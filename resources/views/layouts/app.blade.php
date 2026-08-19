<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'As Salam Management System') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="flex min-h-screen bg-sage-100">

        {{-- Sidebar --}}
        <aside class="w-64 shrink-0 bg-sage-300 flex flex-col">
            <div class="px-5 py-5">
                <a href="{{ route('dashboard') }}"
                    class="block font-bold text-stone-800 text-sm leading-tight hover:text-stone-600">As Salam Management System
                </a>
            </div>

            <div class="flex flex-col items-center py-4">
                <div class="w-20 h-20 rounded-full bg-sage-100 border border-sage-400"></div>
                <p class="mt-2 max-w-full text-center font-medium text-stone-800 text-sm break-words">
                    {{ auth()->user()->name }}
                </p>
            </div>

            <nav class="mt-4 px-3 space-y-1 text-sm">
                <a href="{{ route('dashboard') }}"
                   class="block rounded-lg px-3 py-2 font-medium {{ request()->routeIs('dashboard') ? 'bg-sage-500 text-white' : 'text-sage-800 hover:bg-sage-400/50' }}">
                    Dashboard
                </a>
                <a href="{{ route('account.show') }}"
                   class="block rounded-lg px-3 py-2 font-medium {{ request()->routeIs('account.*') ? 'bg-sage-500 text-white' : 'text-sage-800 hover:bg-sage-400/50' }}">
                    Account
                </a>
                <a href="{{ route('history.index') }}"
                   class="block rounded-lg px-3 py-2 font-medium {{ request()->routeIs('history.*') ? 'bg-sage-500 text-white' : 'text-sage-800 hover:bg-sage-400/50' }}">
                    History
                </a>
            </nav>

            <div class="mt-4 mx-3 border-t border-sage-400"></div>

            <nav class="mt-4 px-3 space-y-1 text-sm">
                <a href="{{ route('meetings.create') }}"
                   class="block rounded-lg px-3 py-2 font-medium {{ request()->routeIs('meetings.create') ? 'bg-sage-500 text-white' : 'text-sage-800 hover:bg-sage-400/50' }}">
                    Create New Meeting
                </a>
            </nav>

            <div class="px-3 mt-4 pb-5">
                <div class="border-t border-stone-400 mb-4"></div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button
                        type="submit"
                        class="ml-auto flex items-center justify-center gap-2 rounded-lg bg-sage-600 px-3 py-2 text-sm font-medium text-white hover:bg-sage-700 transition"
                    >
                        <span>Sign Out</span>

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.8"
                            stroke="currentColor"
                            class="w-5 h-5"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6A2.25 2.25 0 0 0 5.25 5.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3-3H9m9 0-3-3m3 3-3 3"
                            />
                        </svg>
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main content --}}
        <div class="flex-1 flex flex-col">
            <header class="bg-sage-100 border-b border-sage-300 px-8 py-5">
                {{ $header ?? '' }}
            </header>
            <main class="flex-1 p-8">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
