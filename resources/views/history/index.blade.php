<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-sage-800">History</h2>
    </x-slot>

    @if (session('success'))
        <div class="mb-5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl border border-sage-300 p-6">

        <form method="GET" class="flex flex-wrap gap-3 mb-5">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari tajuk mesyuarat..."
                   class="rounded-lg border-sage-300 text-sm flex-1 min-w-[200px]">
            <select name="status" class="rounded-lg border-stone-300 text-sm">
                <option value="">Semua Status</option>
                <option value="draft" @selected(request('status')==='draft')>Draft</option>
                <option value="final" @selected(request('status')==='final')>Final</option>
                <option value="approved" @selected(request('status')==='approved')>Approved</option>
            </select>
            <button class="rounded-lg bg-sage-600 px-4 py-2 text-sm text-white">Filter</button>
        </form>

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
                    @forelse ($meetings as $meeting)
                        <tr>
                            <td class="px-4 py-3 text-sage-700">{{ $meeting->meeting_date->format('d/n/Y') }}</td>
                            <td class="px-4 py-3 text-sage-800 font-medium">{{ $meeting->title }}</td>
                            <td class="px-4 py-3"><x-status-badge :status="$meeting->status" /></td>
                            <td class="px-4 py-3">
                                <x-action-buttons :meeting="$meeting" />
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-sage-400">Tiada mesyuarat dijumpai.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $meetings->links() }}</div>
    </div>
</x-app-layout>
