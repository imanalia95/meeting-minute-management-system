<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-sage-800">Meeting Minutes</h2>
    </x-slot>

    <form method="POST" action="{{ route('meetings.store') }}">
        @include('meetings._form')

        <div class="flex justify-end gap-3">
            <a href="{{ route('dashboard') }}" class="px-4 py-2 text-sm text-sage-600">Cancel</a>
            <button class="rounded-lg bg-sage-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-sage-700">
                Save
            </button>
        </div>
    </form>
</x-app-layout>
