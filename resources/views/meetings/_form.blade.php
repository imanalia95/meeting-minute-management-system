@csrf
@if(isset($meeting)) @method('PUT') @endif

@if ($errors->any())
    <div class="rounded-lg bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 text-sm mb-5">
        <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- ── Butiran Mesyuarat ─────────────────────────────────────── --}}
<div class="bg-white rounded-xl border border-stone-300 p-6 mb-5">
    <h3 class="font-semibold text-stone-800 mb-4">Butiran Mesyuarat</h3>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-stone-600 mb-1">Tajuk Mesyuarat</label>
            <input type="text" name="title" value="{{ old('title', $meeting->title ?? '') }}"
                   class="w-full rounded-lg border-stone-300" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-stone-600 mb-1">Tarikh</label>
            <input type="date" name="meeting_date"
                   value="{{ old('meeting_date', isset($meeting) ? $meeting->meeting_date->format('Y-m-d') : now()->format('Y-m-d')) }}"
                   class="w-full rounded-lg border-stone-300" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-stone-600 mb-1">Lokasi</label>
            <input type="text" name="location" value="{{ old('location', $meeting->location ?? '') }}"
                   placeholder="e.g. Bilik Mesyuarat Utama" class="w-full rounded-lg border-stone-300" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-stone-600 mb-1">Masa Mula</label>
            <input type="time" name="start_time"
                value="{{ old('start_time', isset($meeting) && $meeting->start_time ? \Carbon\Carbon::parse($meeting->start_time)->format('H:i') : '') }}"
                class="w-full rounded-lg border-sage-300" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-stone-600 mb-1">Masa Tamat</label>
            <input type="time" name="end_time"
                value="{{ old('end_time', isset($meeting) && $meeting->end_time ? \Carbon\Carbon::parse($meeting->end_time)->format('H:i') : '') }}"
                class="w-full rounded-lg border-sage-300">
            <p class="text-xs text-stone-400 mt-1">Boleh ditinggalkan kosong sehingga mesyuarat selesai.</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-stone-600 mb-1">Pengerusi</label>
            <select name="chairperson_id" class="w-full rounded-lg border-stone-300" required>
                <option value="">Pilih Pengerusi</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" @selected(old('chairperson_id', $meeting->chairperson_id ?? '') == $user->id)>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-stone-600 mb-1">Setiausaha</label>
            <select name="secretary_id" class="w-full rounded-lg border-stone-300" required>
                <option value="">Pilih Setiausaha</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" @selected(old('secretary_id', $meeting->secretary_id ?? '') == $user->id)>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

{{-- ── Kehadiran (Attendance) ────────────────────────────────── --}}
<div class="bg-white rounded-xl border border-stone-300 p-6 mb-5">
    <h3 class="font-semibold text-stone-800 mb-1">Kehadiran</h3>
    <p class="text-xs text-stone-400 mb-4">Tandakan status kehadiran setiap staff untuk mesyuarat ini.</p>

    <div class="rounded-lg border border-stone-200 divide-y divide-stone-100 max-h-72 overflow-y-auto">
        @php
            $existingAttendance = isset($meeting)
                ? $meeting->attendees->pluck('pivot.attendance_status', 'id')
                : collect();
        @endphp
        @foreach ($users as $user)
            @php $current = old("attendance.$user->id", $existingAttendance[$user->id] ?? 'tidak_hadir'); @endphp
            <div class="flex items-center justify-between px-3 py-2 text-sm">
                <div>
                    <p class="text-stone-800 font-medium">{{ $user->name }}</p>
                    @if($user->position) <p class="text-xs text-stone-400">{{ $user->position }}</p> @endif
                </div>
                <select name="attendance[{{ $user->id }}]" class="rounded-lg border-stone-300 text-xs">
                    <option value="hadir" @selected($current === 'hadir')>Hadir</option>
                    <option value="tidak_hadir" @selected($current === 'tidak_hadir')>Tidak Hadir</option>
                    <option value="hadir_lewat" @selected($current === 'hadir_lewat')>Hadir Lewat</option>
                </select>
            </div>
        @endforeach
    </div>
</div>

{{-- ── Agenda & Perbincangan (repeatable) ────────────────────── --}}
<div class="bg-white rounded-xl border border-stone-300 p-6 mb-5">
    <h3 class="font-semibold text-stone-800 mb-4">Agenda &amp; Perbincangan</h3>

    <div id="agenda-list" class="space-y-4">
        @php $agendaOld = old('agenda', isset($meeting) ? $meeting->agendaItems->map(fn($a) => [
            'id' => $a->id, 'title' => $a->title,
            'summary' => $a->discussion->summary ?? '', 'decision' => $a->discussion->decision ?? '',
        ])->toArray() : []); @endphp

        @foreach ($agendaOld as $i => $item)
            <div class="agenda-row rounded-lg border border-stone-200 p-4 relative">
                <button type="button"
                        onclick="this.closest('.agenda-row').remove()"
                        class="absolute top-3 right-3 p-1.5 text-stone-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition"
                        title="Padam agenda"
                        aria-label="Padam agenda">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-4 h-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3m-4 0h14" />
                    </svg>
                </button>
                @if(!empty($item['id']))
                    <input type="hidden" name="agenda[{{ $i }}][id]" value="{{ $item['id'] }}">
                @endif
                <label class="block text-xs font-medium text-stone-500 mb-1">Tajuk Agenda</label>
                <input type="text" name="agenda[{{ $i }}][title]" value="{{ $item['title'] }}"
                       class="w-full rounded-lg border-stone-300 text-sm mb-3">
                <label class="block text-xs font-medium text-stone-500 mb-1">Ringkasan Perbincangan</label>
                <textarea name="agenda[{{ $i }}][summary]" rows="2"
                          class="w-full rounded-lg border-sage-300 text-sm mb-3 resize-none overflow-hidden agenda-textarea">{{ $item['summary'] }}</textarea>
                <label class="block text-xs font-medium text-stone-500 mb-1">Keputusan / Fokus</label>
                <textarea name="agenda[{{ $i }}][decision]"
                          class="w-full rounded-lg border-sage-300 text-sm resize-none overflow-hidden agenda-textarea">{{ $item['decision'] }}</textarea>
            </div>
        @endforeach
    </div>

    <button type="button" id="add-agenda"
            class="mt-4 rounded-lg bg-stone-200 px-4 py-2 text-sm font-medium text-stone-700 hover:bg-stone-300">
        + Tambah Agenda
    </button>

    <template id="agenda-template">
        <div class="agenda-row rounded-lg border border-stone-200 p-4 relative">
            <button type="button"
                    onclick="this.closest('.agenda-row').remove()"
                    class="absolute top-3 right-3 p-1.5 text-stone-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition"
                    title="Padam agenda"
                    aria-label="Padam agenda">
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="w-4 h-4"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3m-4 0h14" />
                </svg>
            </button>
            <label class="block text-xs font-medium text-stone-500 mb-1">Tajuk Agenda</label>
            <input type="text" name="agenda[__INDEX__][title]" class="w-full rounded-lg border-stone-300 text-sm mb-3">
            <label class="block text-xs font-medium text-stone-500 mb-1">Ringkasan Perbincangan</label>
            <textarea name="agenda[__INDEX__][summary]" rows="2" class="w-full rounded-lg border-sage-300 text-sm mb-3 resize-none overflow-hidden agenda-textarea"></textarea>
            <label class="block text-xs font-medium text-stone-500 mb-1">Keputusan / Fokus</label>
            <textarea name="agenda[__INDEX__][decision]" rows="2" class="w-full rounded-lg border-sage-300 text-sm resize-none overflow-hidden agenda-textarea"></textarea>
        </div>
    </template>
</div>

{{-- ── Tindakan Susulan (repeatable) ─────────────────────────── --}}
<div class="bg-white rounded-xl border border-stone-300 p-6 mb-5">
    <h3 class="font-semibold text-stone-800 mb-4">Tindakan Susulan</h3>

    <div id="action-list" class="space-y-3">
        @php $actionOld = old('actions', isset($meeting) ? $meeting->actionItems->map(fn($a) => [
            'id' => $a->id, 'description' => $a->description,
            'assigned_to' => $a->assigned_to, 'due_date' => optional($a->due_date)->format('Y-m-d'),
            'status' => $a->status, 'discussion_id' => $a->discussion_id,
        ])->toArray() : []); @endphp

        @foreach ($actionOld as $i => $action)
            <div class="action-row grid grid-cols-1 sm:grid-cols-12 gap-2 items-start rounded-lg border border-stone-200 p-3">
                @if(!empty($action['id']))
                    <input type="hidden" name="actions[{{ $i }}][id]" value="{{ $action['id'] }}">
                        @if(isset($meeting) && $meeting->agendaItems->isNotEmpty())
                            <select name="actions[{{ $i }}][discussion_id]" class="sm:col-span-12 rounded-lg border-stone-300 text-xs mb-1">
                                <option value="">— Tindakan Umum (tiada agenda dikaitkan) —</option>
                                @foreach ($meeting->agendaItems as $agendaItem)
                                    @if ($agendaItem->discussion)
                                        <option value="{{ $agendaItem->discussion->id }}" @selected(($action['discussion_id'] ?? null) == $agendaItem->discussion->id)>
                                            Agenda: {{ $agendaItem->title }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        @endif
                @endif
                <input type="text" name="actions[{{ $i }}][description]" value="{{ $action['description'] }}"
                       placeholder="Butiran tindakan" class="sm:col-span-5 rounded-lg border-stone-300 text-sm">
                <select name="actions[{{ $i }}][assigned_to]" class="sm:col-span-3 rounded-lg border-stone-300 text-sm">
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected($action['assigned_to'] == $user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>
                <input type="date" name="actions[{{ $i }}][due_date]" value="{{ $action['due_date'] }}"
                       class="sm:col-span-2 rounded-lg border-stone-300 text-sm">
                <select name="actions[{{ $i }}][status]" class="sm:col-span-1 rounded-lg border-stone-300 text-xs">
                    <option value="belum_mula" @selected($action['status']==='belum_mula')>Belum Mula</option>
                    <option value="dalam_proses" @selected($action['status']==='dalam_proses')>Proses</option>
                    <option value="selesai" @selected($action['status']==='selesai')>Selesai</option>
                    <option value="tertangguh" @selected($action['status']==='tertangguh')>Tunda</option>
                </select>
                <button type="button"
                        onclick="this.closest('.action-row').remove()"
                        class="sm:col-span-1 flex items-center justify-center p-1.5 text-stone-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition"
                        title="Padam tindakan"
                        aria-label="Padam tindakan">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-4 h-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3m-4 0h14" />
                    </svg>
                </button>
            </div>
        @endforeach
    </div>

    <button type="button" id="add-action"
            class="mt-4 rounded-lg bg-stone-200 px-4 py-2 text-sm font-medium text-stone-700 hover:bg-stone-300">
        + Tambah Tindakan
    </button>

    <template id="action-template">
        <div class="action-row grid grid-cols-1 sm:grid-cols-12 gap-2 items-start rounded-lg border border-stone-200 p-3">
            @if(isset($meeting) && $meeting->agendaItems->isNotEmpty())
                <select name="actions[__INDEX__][discussion_id]" class="sm:col-span-12 rounded-lg border-stone-300 text-xs mb-1">
                    <option value="">— Tindakan Umum (tiada agenda dikaitkan) —</option>
                    @foreach ($meeting->agendaItems as $agendaItem)
                        @if ($agendaItem->discussion)
                            <option value="{{ $agendaItem->discussion->id }}">Agenda: {{ $agendaItem->title }}</option>
                        @endif
                    @endforeach
                </select>
                <p class="sm:col-span-12 text-[11px] text-stone-400 -mt-1 mb-1">
                    Boleh pilih agenda yang sama untuk beberapa tindakan jika lebih daripada seorang staff terlibat.
                </p>
            @endif
            <input type="text" name="actions[__INDEX__][description]" placeholder="Butiran tindakan"
                class="sm:col-span-5 rounded-lg border-stone-300 text-sm">
            <select name="actions[__INDEX__][assigned_to]" class="sm:col-span-3 rounded-lg border-stone-300 text-sm">
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
            <input type="date" name="actions[__INDEX__][due_date]" class="sm:col-span-2 rounded-lg border-stone-300 text-sm">
            <select name="actions[__INDEX__][status]" class="sm:col-span-1 rounded-lg border-stone-300 text-xs">
                <option value="belum_mula">Belum Mula</option>
                <option value="dalam_proses">Proses</option>
                <option value="selesai">Selesai</option>
                <option value="tertangguh">Tunda</option>
            </select>
            <button type="button" onclick="this.closest('.action-row').remove()"
                    class="sm:col-span-1 text-xs text-rose-600 hover:underline">Buang</button>
        </div>
    </template>
</div>

{{-- ── Conclusion / Penutupan ────────────────────────────────── --}}
<div class="bg-white rounded-xl border border-stone-300 p-6 mb-5">
    <h3 class="font-semibold text-stone-800 mb-4">Conclusion</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-stone-600 mb-1">Tarikh Mesyuarat Akan Datang</label>
            <input type="date" name="next_meeting_date"
                   value="{{ old('next_meeting_date', isset($meeting) && $meeting->next_meeting_date ? $meeting->next_meeting_date->format('Y-m-d') : '') }}"
                   class="w-full rounded-lg border-stone-300">
        </div>
        <div>
            <label class="block text-sm font-medium text-stone-600 mb-1">Status Mesyuarat</label>
            <select name="status" class="w-full rounded-lg border-sage-300">
                <option value="draft" @selected(old('status', $meeting->status ?? 'draft')==='draft')>Draft</option>
                <option value="final" @selected(old('status', $meeting->status ?? '')==='final')>Final</option>
                <option value="approved" @selected(old('status', $meeting->status ?? '')==='approved')>Approved</option>
            </select>
        </div>
    </div>
</div>

<script>
    function autoResizeTextarea(el) {
        el.style.height = 'auto';
        el.style.height = el.scrollHeight + 'px';
    }

    document.addEventListener('input', function (e) {
        if (e.target.classList.contains('agenda-textarea')) {
            autoResizeTextarea(e.target);
        }
    });

    document.querySelectorAll('.agenda-textarea').forEach(autoResizeTextarea);

    (function () {
        function makeAdder(buttonId, listId, templateId, prefix) {
            const button = document.getElementById(buttonId);
            const list = document.getElementById(listId);
            const template = document.getElementById(templateId);
            let index = list.children.length;

            button.addEventListener('click', function () {
                const html = template.innerHTML.replaceAll('__INDEX__', index);
                const wrapper = document.createElement('div');
                wrapper.innerHTML = html.trim();
                list.appendChild(wrapper.firstElementChild);
                index++;
            });
        }

        makeAdder('add-agenda', 'agenda-list', 'agenda-template', 'agenda');
        makeAdder('add-action', 'action-list', 'action-template', 'actions');
    })();
</script>
