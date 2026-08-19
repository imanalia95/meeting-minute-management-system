<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class MeetingController extends Controller
{
    public function create()
    {
        $users = User::orderBy('name')->get();

        return view('meetings.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateMeeting($request);

        $meeting = DB::transaction(function () use ($validated, $request) {
            $meeting = Meeting::create([
                'title' => $validated['title'],
                'meeting_date' => $validated['meeting_date'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'] ?? null,
                'location' => $validated['location'],
                'next_meeting_date' => $validated['next_meeting_date'] ?? null,
                'status' => $validated['status'] ?? 'draft',
                'chairperson_id' => $validated['chairperson_id'],
                'secretary_id' => $validated['secretary_id'],
                'created_by' => Auth::id(),
            ]);

            $this->syncAttendance($meeting, $request->input('attendance', []));
            $this->syncAgenda($meeting, $request->input('agenda', []));
            $this->syncActions($meeting, $request->input('actions', []));

            return $meeting;
        });

        return redirect()->route('history.index')->with('success', 'Mesyuarat berjaya dicipta.');
    }

    public function show(Meeting $meeting)
    {
        $users = User::orderBy('name')->get();
        $meeting->load(['attendees', 'agendaItems.discussion', 'actionItems']);

        return view('meetings.show', compact('meeting', 'users'));
    }

    public function edit(Meeting $meeting)
    {
        $users = User::orderBy('name')->get();
        $meeting->load(['attendees', 'agendaItems.discussion', 'actionItems']);

        return view('meetings.edit', compact('meeting', 'users'));
    }

    public function exportPdf(Meeting $meeting)
    {
        $meeting->load([
            'chairperson', 'secretary',
            'attendees',
            'agendaItems.discussion',
            'actionItems.assignee',
        ]);

        $pdf = Pdf::loadView('meetings.pdf', compact('meeting'));

        $filename = 'minit-mesyuarat-' . $meeting->meeting_date->format('Y-m-d') . '-' . $meeting->id . '.pdf';

        return $pdf->download($filename);
    }

    public function update(Request $request, Meeting $meeting)
    {
        $validated = $this->validateMeeting($request);

        DB::transaction(function () use ($validated, $request, $meeting) {
            $meeting->update([
                'title' => $validated['title'],
                'meeting_date' => $validated['meeting_date'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'] ?? null,
                'location' => $validated['location'],
                'next_meeting_date' => $validated['next_meeting_date'] ?? null,
                'status' => $validated['status'] ?? $meeting->status,
                'chairperson_id' => $validated['chairperson_id'],
                'secretary_id' => $validated['secretary_id'],
            ]);

            $this->syncAttendance($meeting, $request->input('attendance', []));
            $this->syncAgenda($meeting, $request->input('agenda', []));
            $this->syncActions($meeting, $request->input('actions', []));
        });

        if ($request->input('from') === 'tindakan') {
            return redirect()->route('tindakan.index')->with('success', 'Mesyuarat Berjaya Dikemaskini');
        }

        return redirect()->route('history.index')->with('success', 'Mesyuarat berjaya dikemaskini.');
    }

    public function destroy(Meeting $meeting)
    {
        $meeting->delete();

        return redirect()->route('history.index')->with('success', 'Mesyuarat berjaya dipadam.');
    }

    // ── Helpers ──────────────────────────────────────────────────

    private function validateMeeting(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'meeting_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'location' => ['required', 'string', 'max:255'],
            'next_meeting_date' => ['nullable', 'date'],
            'status' => ['nullable', 'in:draft,final,approved'],
            'chairperson_id' => ['required', 'exists:users,id'],
            'secretary_id' => ['required', 'exists:users,id'],

            'attendance' => ['nullable', 'array'],
            'attendance.*' => ['in:hadir,tidak_hadir,hadir_lewat'],

            'agenda' => ['nullable', 'array'],
            'agenda.*.id' => ['nullable', 'integer'],
            'agenda.*.title' => ['nullable', 'string', 'max:255'],
            'agenda.*.summary' => ['nullable', 'string'],
            'agenda.*.decision' => ['nullable', 'string'],

            'actions' => ['nullable', 'array'],
            'actions.*.id' => ['nullable', 'integer'],
            'actions.*.description' => ['nullable', 'string'],
            'actions.*.assigned_to' => ['nullable', 'exists:users,id'],
            'actions.*.due_date' => ['nullable', 'date'],
            'actions.*.status' => ['nullable', 'in:belum_mula,dalam_proses,selesai,tertangguh'],
            'actions.*.discussion_id' => ['nullable', 'integer', 'exists:discussions,id'],
        ]);
    }

    /**
     * Every staff member gets an explicit attendance row — a proper register, not just a checklist.
     * The form always submits every user in the system, so sync() (not syncWithoutDetaching)
     * is correct here — it keeps the pivot table exactly matching what was submitted.
     */
    private function syncAttendance(Meeting $meeting, array $attendance): void
    {
        $pivotData = collect($attendance)
            ->mapWithKeys(fn ($status, $userId) => [$userId => ['attendance_status' => $status]]);

        $meeting->attendees()->sync($pivotData);
    }

    /** Agenda items + their 1:1 discussion, added/updated/removed based on what was submitted */
    private function syncAgenda(Meeting $meeting, array $agendaRows): void
    {
        $submittedIds = [];

        foreach ($agendaRows as $index => $row) {
            if (empty($row['title'])) {
                continue; // skip empty rows silently rather than erroring
            }

            if (!empty($row['id'])) {
                $agendaItem = $meeting->agendaItems()->find($row['id']);
                if ($agendaItem) {
                    $agendaItem->update(['title' => $row['title'], 'sequence' => $index + 1]);
                    $submittedIds[] = $agendaItem->id;
                    $agendaItem->discussion()->updateOrCreate([], [
                        'summary' => $row['summary'] ?? '',
                        'decision' => $row['decision'] ?? '',
                    ]);
                    continue;
                }
            }

            $agendaItem = $meeting->agendaItems()->create([
                'title' => $row['title'],
                'sequence' => $index + 1,
            ]);
            $agendaItem->discussion()->create([
                'summary' => $row['summary'] ?? '',
                'decision' => $row['decision'] ?? '',
            ]);
            $submittedIds[] = $agendaItem->id;
        }

        // Anything not resubmitted was removed in the UI — delete it (discussion cascades)
        $meeting->agendaItems()->whereNotIn('id', $submittedIds)->delete();
    }

    /** Action items, added/updated/removed based on what was submitted */
    private function syncActions(Meeting $meeting, array $actionRows): void
    {
        $submittedIds = [];

        foreach ($actionRows as $row) {
            if (empty($row['description']) || empty($row['assigned_to'])) {
                continue;
            }

            $data = [
                'description' => $row['description'],
                'assigned_to' => $row['assigned_to'],
                'due_date' => $row['due_date'] ?? null,
                'status' => $row['status'] ?? 'belum_mula',
                'completed_at' => ($row['status'] ?? null) === 'selesai' ? now() : null,
                'discussion_id' => $row['discussion_id'] ?? null,
            ];

            if (!empty($row['id'])) {
                $actionItem = $meeting->actionItems()->find($row['id']);
                if ($actionItem) {
                    $actionItem->update($data);
                    $submittedIds[] = $actionItem->id;
                    continue;
                }
            }

            $actionItem = $meeting->actionItems()->create($data);
            $submittedIds[] = $actionItem->id;
        }

        $meeting->actionItems()->whereNotIn('id', $submittedIds)->delete();
    }
}
