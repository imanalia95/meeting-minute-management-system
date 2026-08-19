<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-sage-800">Staff Profile</h2>
    </x-slot>

    @if (session('success'))
        <div class="mb-5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl border border-sage-300 p-8 max-w-xl">
        <div class="flex flex-col items-center mb-6">
            <div class="w-28 h-28 rounded-full bg-sage-300 border border-sage-400"></div>
        </div>

        <form method="POST" action="{{ route('account.update') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-sage-700 mb-1">Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                       class="w-full rounded-lg border-sage-300 bg-sage-100">
            </div>
            <div>
                <label class="block text-sm font-medium text-sage-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                       class="w-full rounded-lg border-sage-300 bg-sage-100">
            </div>
            <div>
                <label class="block text-sm font-medium text-sage-700 mb-1">Position</label>
                <input type="text" name="position" value="{{ old('position', $user->position) }}"
                       placeholder="e.g. Jururawat, Penyelaras Klinikal"
                       class="w-full rounded-lg border-sage-300 bg-sage-100">
            </div>
            <div>
                <label class="block text-sm font-medium text-sage-700 mb-1">Department</label>
                <input type="text" name="department" value="{{ old('department', $user->department) }}"
                       class="w-full rounded-lg border-sage-300 bg-sage-100">
            </div>

            @error('name') <p class="text-sm text-rose-600">{{ $message }}</p> @enderror
            @error('email') <p class="text-sm text-rose-600">{{ $message }}</p> @enderror

            <button class="w-full rounded-lg bg-sage-600 py-2.5 text-sm font-medium text-white hover:bg-sage-700">
                Save Changes
            </button>
        </form>
    </div>
</x-app-layout>
