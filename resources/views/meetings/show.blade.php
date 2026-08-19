<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-stone-800">Meeting Minutes</h2>
            <a href="{{ route('meetings.edit', $meeting) }}"
               class="rounded-lg bg-sage-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-sage-600">
                Edit
            </a>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="mb-5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @php
        $chairperson = $users->firstWhere('id', $meeting->chairperson_id);
        $secretary = $users->firstWhere('id', $meeting->secretary_id);
    @endphp

    {{-- ── Butiran Mesyuarat ─────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-stone-300 p-6 mb-5">
        <div class="flex items-start justify-between mb-4">
            <h3 class="font-semibold text-stone-800">Butiran Mesyuarat</h3>
            <x-status-badge :status="$meeting->status" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div class="sm:col-span-2">
                <p class="text-xs font-medium text-stone-500 mb-1">Tajuk Mesyuarat</p>
                <p class="text-stone-800 font-medium">{{ $meeting->title }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-stone-500 mb-1">Tarikh</p>
                <p class="text-stone-800">{{ $meeting->meeting_date->format('d/n/Y') }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-stone-500 mb-1">Lokasi</p>
                <p class="text-stone-800">{{ $meeting->location }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-stone-500 mb-1">Masa Mula</p>
                <p class="text-stone-800">{{ $meeting->start_time }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-stone-500 mb-1">Masa Tamat</p>
                <p class="text-stone-800">{{ $meeting->end_time ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-stone-500 mb-1">Pengerusi</p>
                <p class="text-stone-800">{{ $chairperson->name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-stone-500 mb-1">Setiausaha</p>
                <p class="text-stone-800">{{ $secretary->name ?? '—' }}</p>
            </div>
        </div>
    </div>

    {{-- ── Kehadiran (Attendance) ────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-stone-300 p-6 mb-5">
        <h3 class="font-semibold text-stone-800 mb-4">Kehadiran</h3>

        @php
            $attendanceLabels = [
                'hadir' => 'Hadir',
                'tidak_hadir' => 'Tidak Hadir',
                'hadir_lewat' => 'Hadir Lewat',
            ];
        @endphp

        <div class="rounded-lg border border-stone-200 divide-y divide-stone-100 max-h-72 overflow-y-auto">
            @forelse ($meeting->attendees as $attendee)
                <div class="flex items-center justify-between px-3 py-2 text-sm">
                    <div>
                        <p class="text-stone-800 font-medium">{{ $attendee->name }}</p>
                        @if($attendee->position) <p class="text-xs text-stone-400">{{ $attendee->position }}</p> @endif
                    </div>
                    <span class="text-xs font-medium text-stone-600">
                        {{ $attendanceLabels[$attendee->pivot->attendance_status] ?? $attendee->pivot->attendance_status }}
                    </span>
                </div>
            @empty
                <p class="px-3 py-4 text-sm text-stone-400 text-center">Tiada rekod kehadiran.</p>
            @endforelse
        </div>
    </div>

    {{-- ── Agenda & Perbincangan ─────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-stone-300 p-6 mb-5">
        <h3 class="font-semibold text-stone-800 mb-4">Agenda &amp; Perbincangan</h3>

        <div class="space-y-4">
            @forelse ($meeting->agendaItems as $item)
                <div class="rounded-lg border border-stone-200 p-4">
                    <p class="text-stone-800 font-medium mb-2">{{ $item->title }}</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                        <div>
                            <p class="text-xs font-medium text-stone-500 mb-1">Ringkasan Perbincangan</p>
                            <p class="text-stone-700 whitespace-pre-line">{{ $item->discussion->summary ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-stone-500 mb-1">Keputusan / Fokus</p>
                            <p class="text-stone-700 whitespace-pre-line">{{ $item->discussion->decision ?? '—' }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-stone-400">Tiada agenda direkodkan.</p>
            @endforelse
        </div>
    </div>

    {{-- ── Tindakan Susulan ──────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-stone-300 p-6 mb-5">
        <h3 class="font-semibold text-stone-800 mb-4">Tindakan Susulan</h3>

        <div class="rounded-lg border border-stone-300 overflow-hidden">
            <table class="min-w-full text-sm">
                <thead class="bg-stone-400/60">
                    <tr class="text-left text-stone-800">
                        <th class="px-4 py-3">Butiran Tindakan</th>
                        <th class="px-4 py-3">Ditugaskan Kepada</th>
                        <th class="px-4 py-3">Tarikh Akhir</th>
                        <th class="px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200">
                    @forelse ($meeting->actionItems as $action)
                        @php $assignee = $users->firstWhere('id', $action->assigned_to); @endphp
                        <tr>
                            <td class="px-4 py-3 text-stone-800">{{ $action->description }}</td>
                            <td class="px-4 py-3 text-stone-700">{{ $assignee->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-stone-700">{{ optional($action->due_date)->format('d/n/Y') ?? '—' }}</td>
                            <td class="px-4 py-3"><x-status-badge :status="$action->status" /></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-stone-400">Tiada tindakan susulan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── Conclusion / Penutupan ────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-stone-300 p-6">
        <h3 class="font-semibold text-stone-800 mb-4">Conclusion</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-xs font-medium text-stone-500 mb-1">Tarikh Mesyuarat Akan Datang</p>
                <p class="text-stone-800">
                    {{ $meeting->next_meeting_date ? $meeting->next_meeting_date->format('d/n/Y') : '—' }}
                </p>
            </div>
            <div>
                <p class="text-xs font-medium text-stone-500 mb-1">Status Mesyuarat</p>
                <x-status-badge :status="$meeting->status" />
            </div>
        </div>
    </div>
</x-app-layout>
