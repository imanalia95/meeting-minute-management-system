<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-sage-800">Dashboard</h2>
    </x-slot>

    @if (session('success'))
        <div class="mb-5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl border border-sage-300 p-6">
        <h3 class="font-semibold text-sage-800 mb-5">Action Taken</h3>

        <div class="flex gap-8 mb-8 flex-wrap">
            <a href="{{ route('tindakan.index', ['status' => 'belum_mula']) }}" class="flex flex-col items-center gap-2 group">
                <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center text-gray-800 font-bold group-hover:bg-gray-300">
                    {{ $stats['belum_mula'] }}
                </div>
                <span class="text-sm font-medium text-sage-700">Belum Mula</span>
            </a>
            <a href="{{ route('tindakan.index', ['status' => 'dalam_proses']) }}" class="flex flex-col items-center gap-2 group">
                <div class="w-16 h-16 rounded-full bg-amber-200 flex items-center justify-center text-amber-800 font-bold group-hover:bg-amber-300">
                    {{ $stats['dalam_proses'] }}
                </div>
                <span class="text-sm font-medium text-sage-700">Dalam Proses</span>
            </a>
            <a href="{{ route('tindakan.index', ['status' => 'selesai']) }}" class="flex flex-col items-center gap-2 group">
                <div class="w-16 h-16 rounded-full bg-emerald-200 flex items-center justify-center text-emerald-800 font-bold group-hover:bg-emerald-300">
                    {{ $stats['selesai'] }}
                </div>
                <span class="text-sm font-medium text-sage-700">Selesai</span>
            </a>
            <a href="{{ route('tindakan.index', ['status' => 'tertangguh']) }}" class="flex flex-col items-center gap-2 group">
                <div class="w-16 h-16 rounded-full bg-rose-200 flex items-center justify-center text-rose-800 font-bold group-hover:bg-rose-300">
                    {{ $stats['tertangguh'] }}
                </div>
                <span class="text-sm font-medium text-sage-700">Tertangguh</span>
            </a>
        </div>

        <div class="rounded-lg border border-sage-300 overflow-hidden">
            <table class="min-w-full text-sm">
                <thead class="bg-sage-400/60">
                    <tr class="text-left text-sage-800">
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Title</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sage-200">
                    @forelse ($recentMeetings as $meeting)
                        <tr>
                            <td class="px-4 py-3 text-sage-700">{{ $meeting->meeting_date->format('d/n/Y') }}</td>
                            <td class="px-4 py-3 text-sage-800 font-medium">{{ $meeting->title }}</td>
                            <td class="px-4 py-3"><x-status-badge :status="$meeting->status" /></td>
                            <td class="px-4 py-3">
                                <x-action-buttons :meeting="$meeting" />
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-sage-400">Tiada mesyuarat lagi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
