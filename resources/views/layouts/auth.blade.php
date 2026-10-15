<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Account') — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full antialiased">
    <div class="grid min-h-screen lg:grid-cols-2">
        <aside class="relative hidden flex-col justify-between overflow-hidden border-r border-white/5 bg-abyss-900/40 p-10 lg:flex">
            <div class="pointer-events-none absolute -left-20 top-10 h-72 w-72 rounded-full bg-accent-500/20 blur-3xl"></div>
            <div class="pointer-events-none absolute -right-16 bottom-0 h-64 w-64 rounded-full bg-blue-600/10 blur-3xl"></div>

            <a href="{{ route('home') }}" class="relative flex items-center gap-2.5">
                <x-brand-mark class="h-8 w-8 text-accent-400" />
                <span class="text-lg font-bold tracking-tight text-white">Game<span class="text-accent-400">Spin</span></span>
            </a>

            <div class="relative">
                <h2 class="max-w-sm text-3xl font-extrabold leading-tight tracking-tight text-white">
                    One wheel.<br>Your next game.
                </h2>
                <p class="mt-3 max-w-sm text-sm leading-relaxed text-slate-400">
                    Bring your Steam library or start with the seeded demo games, then let GameSpin decide.
                </p>

                <div class="mt-8 flex items-center gap-3">
                    @for ($i = 0; $i < 5; $i++)
                        <div class="grid aspect-[2/1] w-20 place-items-center rounded-lg bg-abyss-800 ring-1 ring-white/5
                            {{ $i === 2 ? '-translate-y-2 ring-accent-400/40' : '' }}">
                            <x-brand-mark class="h-5 w-5 text-white/15" />
                        </div>
                    @endfor
                </div>
            </div>

            <p class="relative text-xs text-slate-500">A school project. Steam features are optional.</p>
        </aside>

        <main class="flex items-center justify-center p-6 sm:p-10">
            <div class="w-full max-w-md">
                <a href="{{ route('home') }}" class="mb-8 flex items-center gap-2.5 lg:hidden">
                    <x-brand-mark class="h-8 w-8 text-accent-400" />
                    <span class="text-lg font-bold tracking-tight text-white">Game<span class="text-accent-400">Spin</span></span>
                </a>

                @include('partials.flash')
                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>