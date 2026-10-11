<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-slate-950 text-slate-100 antialiased">
    <header class="border-b border-amber-900/40 bg-amber-950/30">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center gap-x-6 gap-y-2 px-4 py-3">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 text-lg font-bold text-amber-400">
                <span class="grid h-8 w-8 place-items-center rounded-lg bg-amber-500 text-slate-900">A</span>
                GameSpin Admin
            </a>

            <nav class="flex flex-1 flex-wrap items-center gap-x-4 gap-y-1 text-sm">
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'text-amber-300' : 'text-slate-300' }} hover:text-amber-300">Overview</a>
                <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'text-amber-300' : 'text-slate-300' }} hover:text-amber-300">Users</a>
                <a href="{{ route('admin.games.index') }}" class="{{ request()->routeIs('admin.games.*') ? 'text-amber-300' : 'text-slate-300' }} hover:text-amber-300">Games</a>
                <a href="{{ route('admin.syncs.index') }}" class="{{ request()->routeIs('admin.syncs.*') ? 'text-amber-300' : 'text-slate-300' }} hover:text-amber-300">Syncs</a>
                <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-200">← Member area</a>
            </nav>

            <form method="POST" action="{{ route('logout') }}" class="flex items-center gap-3 text-sm">
                @csrf
                <span class="hidden text-slate-400 sm:inline">{{ auth()->user()->name }}</span>
                <button type="submit" class="rounded-md border border-slate-700 px-3 py-1.5 text-slate-200 hover:border-slate-500">Log out</button>
            </form>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-8">
        @include('partials.flash')
        @yield('content')
    </main>
</body>
</html>