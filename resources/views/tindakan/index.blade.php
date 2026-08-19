<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-sage-800">Tindakan Susulan</h2>
    </x-slot>

    @if (session('success'))
        <div class="mb-5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl border border-sage-300 p-6">

        <form method="GET" class="flex flex-wrap gap-3 mb-5">
            <select name="status" class="rounded-lg border-sage-300 text-sm">
                <option value="">Semua Status</option>
                <option value="belum_mula" @selected(request('status')==='belum_mula')>Belum Mula</option>
                <option value="dalam_proses" @selected(request('status')==='dalam_proses')>Dalam Proses</option>
                <option value="selesai" @selected(request('status')==='selesai')>Selesai</option>
                <option value="tertangguh" @selected(request('status')==='tertangguh')>Tertangguh</option>
            </select>
            <button class="rounded-lg bg-sage-600 px-4 py-2 text-sm text-white">Filter</button>
        </form>

        <div class="rounded-lg border border-sage-300 overflow-hidden">
            <table class="min-w-full text-sm">
                <thead class="bg-sage-400/60">
                    <tr class="text-left text-sage-800">
                        <th class="px-4 py-3">Tarikh Akhir</th>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Tindakan</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sage-200">
                    @forelse ($actionItems as $item)
                        <tr>
                            <td class="px-4 py-3 text-sage-700">{{ optional($item->due_date)->format('d/n/Y') ?? '—' }}</td>
                            <td class="px-4 py-3 text-sage-800 font-medium">{{ $item->assignee->name }}</td>
                            <td class="px-4 py-3 text-sage-700">{{ $item->description }}</td>
                            <td class="px-4 py-3"><x-status-badge :status="$item->status" /></td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <a href="{{ route('meetings.edit', $item->meeting) }}?from=tindakan"
                                        class="rounded-lg bg-sage-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-sage-600">Edit</a>
                                    <form method="POST" action="{{ route('tindakan.destroy', $item) }}"
                                          onsubmit="return confirm('Padam tindakan ini?')">
                                        @csrf @method('DELETE')
                                        <button class="rounded-lg bg-rose-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-rose-700">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-sage-400">Tiada tindakan dijumpai.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $actionItems->links() }}</div>
    </div>
</x-app-layout>