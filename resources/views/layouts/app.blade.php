<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'GameSpin') — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-slate-950 text-slate-100 antialiased">
    <header class="border-b border-slate-800 bg-slate-900/70 backdrop-blur">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center gap-x-6 gap-y-2 px-4 py-3">
            <a href="{{ route('home') }}" class="flex items-center gap-2 text-lg font-bold text-indigo-400">
                <span class="grid h-8 w-8 place-items-center rounded-lg bg-indigo-500 text-white">G</span>
                GameSpin
            </a>

            <nav class="flex flex-1 flex-wrap items-center gap-x-4 gap-y-1 text-sm">
                @auth
                    <a href="{{ route('dashboard') }}" class="hover:text-indigo-300 {{ request()->routeIs('dashboard') ? 'text-indigo-300' : 'text-slate-300' }}">Dashboard</a>
                    <a href="{{ route('library.index') }}" class="hover:text-indigo-300 {{ request()->routeIs('library.*') ? 'text-indigo-300' : 'text-slate-300' }}">Library</a>
                    <a href="{{ route('picker.index') }}" class="hover:text-indigo-300 {{ request()->routeIs('picker.*') ? 'text-indigo-300' : 'text-slate-300' }}">Roulette</a>
                    <a href="{{ route('history.index') }}" class="hover:text-indigo-300 {{ request()->routeIs('history.*') ? 'text-indigo-300' : 'text-slate-300' }}">History</a>
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="rounded bg-amber-500/20 px-2 py-0.5 text-amber-300 hover:bg-amber-500/30">Admin</a>
                    @endif
                @endauth
            </nav>

            <div class="flex items-center gap-4 text-sm">
                @auth
                    <span class="hidden text-slate-400 sm:inline">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-md border border-slate-700 px-3 py-1.5 text-slate-200 hover:border-slate-500">Log out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-slate-300 hover:text-indigo-300">Log in</a>
                    <a href="{{ route('register') }}" class="rounded-md bg-indigo-500 px-3 py-1.5 font-medium text-white hover:bg-indigo-400">Register</a>
                @endauth
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-8">
        @include('partials.flash')
        @yield('content')
    </main>

    <footer class="mx-auto max-w-6xl px-4 py-8 text-center text-xs text-slate-600">
        GameSpin — a school project. Game data shown from the local database.
    </footer>
</body>
</html>