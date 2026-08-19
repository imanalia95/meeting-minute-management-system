<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-sage-800">Meeting Minutes</h2>
    </x-slot>

    {{-- Delete lives in its own form (siblings, not nested — forms can't nest in HTML) --}}
    <form id="delete-meeting-form" method="POST" action="{{ route('meetings.destroy', $meeting) }}"
          onsubmit="return confirm('Padam mesyuarat ini? Tindakan ini tidak boleh diundur.')" class="hidden">
        @csrf @method('DELETE')
    </form>

    <form method="POST" action="{{ route('meetings.update', $meeting) }}">
        <input type="hidden" name="from" value="{{ request('from') }}">
        @include('meetings._form')

        <div class="flex justify-between items-center">
            <button type="submit" form="delete-meeting-form"
                    class="rounded-lg bg-rose-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-rose-700">
                Delete
            </button>

            <button class="rounded-lg bg-sage-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-sage-700">
                Save
            </button>
        </div>
    </form>
</x-app-layout>
