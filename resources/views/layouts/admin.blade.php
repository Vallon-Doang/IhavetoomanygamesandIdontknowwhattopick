<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full antialiased">
    @php
        $adminNav = [
            ['route' => 'admin.dashboard', 'label' => 'Overview', 'pattern' => 'admin.dashboard'],
            ['route' => 'admin.users.index', 'label' => 'Users', 'pattern' => 'admin.users.*'],
            ['route' => 'admin.games.index', 'label' => 'Games', 'pattern' => 'admin.games.*'],
            ['route' => 'admin.syncs.index', 'label' => 'Syncs', 'pattern' => 'admin.syncs.*'],
            ['route' => 'admin.posts.index', 'label' => 'Moderation', 'pattern' => 'admin.posts.*'],
            ['route' => 'admin.announcements.index', 'label' => 'Announcements', 'pattern' => 'admin.announcements.*'],
            ['route' => 'admin.analytics.index', 'label' => 'Analytics', 'pattern' => 'admin.analytics.*'],
            ['route' => 'admin.steam-requests.index', 'label' => 'Steam requests', 'pattern' => 'admin.steam-requests.*'],
        ];
    @endphp

    <div x-data="{ nav: false }" @keydown.escape.window="nav = false" class="min-h-screen lg:grid lg:grid-cols-[256px_minmax(0,1fr)]">
        <aside class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col border-r border-white/5 bg-abyss-900/95 backdrop-blur-xl transition-transform duration-300 lg:static lg:translate-x-0"
            :class="nav && 'translate-x-0!'"
            aria-label="Admin navigation">
            <div class="flex h-16 items-center gap-2.5 border-b border-white/5 px-5">
                <x-brand-mark class="h-7 w-7 text-accent-400" />
                <span class="font-bold tracking-tight text-white">Game<span class="text-accent-400">Spin</span></span>
                <x-ui.badge tone="amber" class="ml-auto">Admin</x-ui.badge>
            </div>

            <nav class="flex flex-1 flex-col gap-1 p-4">
                @foreach ($adminNav as $item)
                    <a href="{{ route($item['route']) }}"
                        @class([
                            'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition',
                            'bg-accent-500/10 text-accent-300 ring-1 ring-inset ring-accent-500/20' => request()->routeIs($item['pattern']),
                            'text-slate-400 hover:bg-white/5 hover:text-white' => ! request()->routeIs($item['pattern']),
                        ])>
                        <span class="h-1.5 w-1.5 rounded-full bg-current opacity-70"></span>
                        {{ $item['label'] }}
                    </a>
                @endforeach

                <a href="{{ route('dashboard') }}" class="mt-auto flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-400 transition hover:bg-white/5 hover:text-white">
                    <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" class="h-4 w-4" aria-hidden="true">
                        <path d="M12 5l-5 5 5 5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Member area
                </a>
            </nav>

            <div class="border-t border-white/5 p-4">
                <div class="flex items-center gap-3">
                    <span class="grid h-9 w-9 place-items-center rounded-full bg-gradient-to-br from-accent-400 to-accent-600 text-sm font-bold text-abyss-950">
                        {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                        <p class="truncate text-xs text-slate-500">Administrator</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="w-full rounded-lg border border-white/10 px-3 py-2 text-sm text-slate-300 transition hover:border-red-500/50 hover:text-red-300">
                        Log out
                    </button>
                </form>
            </div>
        </aside>

        <div x-show="nav" x-cloak @click="nav = false" class="fixed inset-0 z-40 bg-black/60 backdrop-blur-sm lg:hidden"></div>

        <div class="flex min-w-0 flex-col">
            <header class="sticky top-0 z-30 flex h-16 items-center gap-3 border-b border-white/5 bg-abyss-950/80 px-4 backdrop-blur-xl lg:hidden">
                <button type="button" @click="nav = true" class="grid h-10 w-10 place-items-center rounded-xl border border-white/10 text-slate-300" aria-label="Open navigation">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true">
                        <path d="M4 7h16M4 12h16M4 17h16" stroke-linecap="round" />
                    </svg>
                </button>
                <span class="font-bold tracking-tight text-white">Game<span class="text-accent-400">Spin</span></span>
                <x-ui.badge tone="amber" class="ml-auto">Admin</x-ui.badge>
            </header>

            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                @include('partials.flash')
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>