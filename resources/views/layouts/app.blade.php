<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'GameSpin') — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full antialiased">
    <div x-data="{ menu: false, user: false }" @keydown.escape.window="menu = false; user = false" class="flex min-h-screen flex-col">
        <header class="sticky top-0 z-40 border-b border-white/5 bg-abyss-950/80 backdrop-blur-xl">
            <div class="mx-auto flex h-16 max-w-7xl items-center gap-4 px-4 sm:px-6">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5" aria-label="GameSpin home">
                    <x-brand-mark class="h-8 w-8 text-accent-400" />
                    <span class="text-lg font-bold tracking-tight text-white">
                        Game<span class="text-accent-400">Spin</span>
                    </span>
                </a>

                <nav class="ml-4 hidden items-center gap-1 md:flex" aria-label="Primary">
                    @auth
                        <x-ui.nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Dashboard</x-ui.nav-link>
                        <x-ui.nav-link :href="route('library.index')" :active="request()->routeIs('library.*')">Library</x-ui.nav-link>
                        <x-ui.nav-link :href="route('picker.index')" :active="request()->routeIs('picker.*')">Roulette</x-ui.nav-link>
                        <x-ui.nav-link :href="route('history.index')" :active="request()->routeIs('history.*')">History</x-ui.nav-link>
                        <x-ui.nav-link :href="route('community.index')" :active="request()->routeIs('community.*')">Community</x-ui.nav-link>
                        @if (auth()->user()->isAdmin())
                            <x-ui.nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">Admin</x-ui.nav-link>
                        @endif
                    @endauth
                </nav>

                <div class="ml-auto flex items-center gap-2">
                    @auth
                        <div class="relative" @click.outside="user = false">
                            <button type="button" @click="user = !user" :aria-expanded="user"
                                class="flex items-center gap-2 rounded-full border border-white/10 bg-white/5 py-1 pl-1 pr-3 text-sm text-slate-200 transition hover:border-accent-500/40 hover:bg-white/10">
                                <span class="grid h-7 w-7 place-items-center rounded-full bg-gradient-to-br from-accent-400 to-accent-600 text-xs font-bold text-abyss-950">
                                    {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                                </span>
                                <span class="hidden max-w-[10rem] truncate sm:inline">{{ auth()->user()->name }}</span>
                                <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4 text-slate-400" aria-hidden="true">
                                    <path d="M5.5 7.5 10 12l4.5-4.5" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round" />
                                </svg>
                            </button>

                            <div x-show="user" x-cloak x-transition.origin.top.right
                                class="absolute right-0 mt-2 w-52 overflow-hidden rounded-xl border border-white/10 bg-abyss-850/95 p-1.5 shadow-2xl shadow-black/50 backdrop-blur-xl">
                                <div class="px-3 py-2">
                                    <p class="truncate text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                                    <p class="truncate text-xs text-slate-500">{{ auth()->user()->email }}</p>
                                </div>
                                <div class="my-1 h-px bg-white/5"></div>
                                @if (auth()->user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="block rounded-lg px-3 py-2 text-sm text-slate-300 hover:bg-white/5 hover:text-white">Admin console</a>
                                @endif
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-red-300 hover:bg-red-500/10">Log out</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="hidden rounded-xl px-4 py-2 text-sm font-medium text-slate-300 transition hover:text-white sm:inline-flex">Log in</a>
                        <x-ui.button :href="route('register')" size="sm">Get started</x-ui.button>
                    @endauth

                    <button type="button" @click="menu = !menu" :aria-expanded="menu"
                        class="grid h-10 w-10 place-items-center rounded-xl border border-white/10 text-slate-300 transition hover:bg-white/5 md:hidden"
                        aria-label="Toggle navigation">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true">
                            <path x-show="!menu" d="M4 7h16M4 12h16M4 17h16" stroke-linecap="round" />
                            <path x-show="menu" x-cloak d="M6 6l12 12M18 6L6 18" stroke-linecap="round" />
                        </svg>
                    </button>
                </div>
            </div>

            <div x-show="menu" x-cloak x-transition class="border-t border-white/5 bg-abyss-900/95 md:hidden">
                <nav class="mx-auto flex max-w-7xl flex-col gap-1 px-4 py-3 text-sm">
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-lg px-3 py-2 text-slate-300 hover:bg-white/5 hover:text-white">Dashboard</a>
                        <a href="{{ route('library.index') }}" class="rounded-lg px-3 py-2 text-slate-300 hover:bg-white/5 hover:text-white">Library</a>
                        <a href="{{ route('picker.index') }}" class="rounded-lg px-3 py-2 text-slate-300 hover:bg-white/5 hover:text-white">Roulette</a>
                        <a href="{{ route('history.index') }}" class="rounded-lg px-3 py-2 text-slate-300 hover:bg-white/5 hover:text-white">History</a>
                        <a href="{{ route('community.index') }}" class="rounded-lg px-3 py-2 text-slate-300 hover:bg-white/5 hover:text-white">Community</a>
                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="rounded-lg px-3 py-2 text-amber-300 hover:bg-white/5">Admin console</a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="rounded-lg px-3 py-2 text-slate-300 hover:bg-white/5 hover:text-white">Log in</a>
                        <a href="{{ route('register') }}" class="rounded-lg px-3 py-2 text-accent-300 hover:bg-white/5">Create an account</a>
                    @endauth
                </nav>
            </div>
        </header>

        <main class="mx-auto w-full max-w-7xl flex-1 px-4 py-8 sm:px-6">
            @include('partials.flash')
            @yield('content')
        </main>

        <footer class="border-t border-white/5">
            <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-2 px-4 py-6 text-xs text-slate-500 sm:flex-row sm:px-6">
                <div class="flex items-center gap-2">
                    <x-brand-mark class="h-4 w-4 text-slate-600" />
                    <span>GameSpin — a school project.</span>
                </div>
                <p>Game data is served from the local database. Steam features are optional.</p>
            </div>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>