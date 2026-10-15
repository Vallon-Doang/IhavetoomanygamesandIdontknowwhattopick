@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
    {{-- Cinematic hero --}}
    <section class="relative overflow-hidden rounded-3xl border border-white/5 bg-abyss-900/50">
        <div class="pointer-events-none absolute -left-24 -top-32 h-80 w-80 rounded-full bg-accent-500/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -right-20 bottom-0 h-72 w-72 rounded-full bg-blue-600/10 blur-3xl"></div>

        <div class="relative grid items-center gap-10 p-6 sm:p-10 lg:grid-cols-[1.05fr_0.95fr] lg:p-14">
            <div>
                <span class="inline-flex items-center gap-2 rounded-full border border-accent-500/30 bg-accent-500/10 px-3 py-1 text-xs font-medium text-accent-300">
                    <span class="h-1.5 w-1.5 rounded-full bg-accent-400"></span>
                    Your game library, decided for you
                </span>

                <h1 class="mt-5 text-4xl font-extrabold leading-[1.05] tracking-tight text-white sm:text-5xl lg:text-6xl">
                    Stop staring at<br>
                    <span class="bg-gradient-to-r from-accent-300 via-accent-400 to-blue-400 bg-clip-text text-transparent">
                        your backlog.
                    </span>
                </h1>

                <p class="mt-5 max-w-xl text-base leading-relaxed text-slate-400 sm:text-lg">
                    GameSpin tracks the games you own, filters out what you're not in the mood for,
                    and lets a fair roulette wheel pick your next game — in seconds.
                </p>

                <div class="mt-8 flex flex-wrap items-center gap-3">
                    @guest
                        <x-ui.button :href="route('register')" size="lg">Create your account</x-ui.button>
                        <x-ui.button :href="route('login')" variant="secondary" size="lg">Log in</x-ui.button>
                    @else
                        <x-ui.button :href="route('picker.index')" size="lg">Spin the roulette</x-ui.button>
                        <x-ui.button :href="route('dashboard')" variant="secondary" size="lg">Go to dashboard</x-ui.button>
                    @endguest
                </div>

                <p class="mt-6 text-xs text-slate-500">
                    Works with the seeded demo library even without Steam access.
                </p>
            </div>

            {{-- Cover collage --}}
            <div class="relative">
                <div class="grid grid-cols-3 gap-3">
                    @forelse ($featured as $game)
                        <x-game-cover :name="$game->name" :cover="$game->cover_url"
                            class="aspect-[2/1] rounded-xl ring-1 ring-white/10 transition duration-300 hover:-translate-y-1 hover:ring-accent-400/50 {{ $loop->index % 3 === 1 ? 'translate-y-3' : '' }}" />
                    @empty
                        @for ($i = 0; $i < 6; $i++)
                            <div class="grid aspect-[2/1] place-items-center rounded-xl bg-abyss-800/80 ring-1 ring-white/5">
                                <x-brand-mark class="h-6 w-6 text-white/15" />
                            </div>
                        @endfor
                    @endforelse
                </div>

                <div class="pointer-events-none absolute inset-x-4 bottom-[-1.25rem] flex justify-center">
                    <span class="rounded-full border border-accent-500/30 bg-abyss-900/90 px-4 py-1.5 text-xs font-semibold text-accent-300 shadow-lg shadow-black/40 backdrop-blur">
                        Fair, equal-probability picks
                    </span>
                </div>
            </div>
        </div>
    </section>

    {{-- How it works --}}
    <section class="mt-12">
        <div class="flex items-end justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-white sm:text-2xl">How GameSpin works</h2>
                <p class="mt-1 text-sm text-slate-400">Three steps from a full backlog to a single game.</p>
            </div>
        </div>

        <div class="mt-6 grid gap-4 sm:grid-cols-3">
            @php
                $steps = [
                    ['Build your library', 'Start with seeded demo games, then link Steam to import your own collection.'],
                    ['Filter what matters', 'Search by title, set playtime limits, and exclude games you don\'t want to see.'],
                    ['Spin and play', 'The roulette picks one eligible game at random and records it in your history.'],
                ];
            @endphp

            @foreach ($steps as $step)
                <article class="rounded-2xl border border-white/5 bg-abyss-900/50 p-6 transition duration-200 hover:border-accent-500/30 hover:bg-abyss-850/60">
                    <span class="grid h-9 w-9 place-items-center rounded-xl bg-accent-500/15 text-sm font-bold text-accent-300 ring-1 ring-inset ring-accent-500/25">
                        {{ $loop->iteration }}
                    </span>
                    <h3 class="mt-4 font-semibold text-white">{{ $step[0] }}</h3>
                    <p class="mt-1.5 text-sm leading-relaxed text-slate-400">{{ $step[1] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    {{-- Catalog strip --}}
    @if ($featured->isNotEmpty())
        <section class="mt-12">
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-lg font-semibold text-white">In the catalog right now</h2>
                <a href="{{ auth()->check() ? route('library.index') : route('register') }}" class="text-sm text-accent-400 hover:text-accent-300">
                    Browse library →
                </a>
            </div>
            <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
                @foreach ($featured as $game)
                    <x-game-cover :name="$game->name" :cover="$game->cover_url"
                        class="aspect-[2/1] rounded-xl ring-1 ring-white/5 transition duration-300 hover:ring-accent-400/40" />
                @endforeach
            </div>
        </section>
    @endif
@endsection