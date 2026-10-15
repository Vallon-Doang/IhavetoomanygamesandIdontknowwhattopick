@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-sm text-slate-400">Welcome back</p>
            <h1 class="text-2xl font-bold tracking-tight text-white sm:text-3xl">{{ $user->name }}</h1>
        </div>
        <div class="flex gap-3">
            <x-ui.button :href="route('library.index')" variant="secondary">Library</x-ui.button>
            <x-ui.button :href="route('picker.index')">
                Spin now
                <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4" aria-hidden="true">
                    <path d="M5 10h10M11 6l4 4-4 4" stroke="currentColor" stroke-width="1.6" fill="none" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </x-ui.button>
        </div>
    </div>

    @if ($steamConflict)
        <section class="mt-6 rounded-2xl border border-amber-500/30 bg-amber-500/5 p-5" role="alert">
            <div class="flex flex-wrap items-center gap-2">
                <x-ui.badge tone="amber">Steam linking conflict</x-ui.badge>
                <span class="text-sm font-semibold text-white">That Steam account is already linked elsewhere</span>
            </div>
            <p class="mt-2 text-sm text-slate-300">
                Steam ID <span class="font-mono text-slate-100">{{ $steamConflict }}</span> is already linked to another
                GameSpin account. If you think this is a mistake, you can ask an administrator to clear the old link.
            </p>

            <form method="POST" action="{{ route('steam.reset-request') }}" class="mt-4"
                x-data="{ sending: false }" @submit="sending = true">
                @csrf
                <label for="message" class="block text-xs font-medium uppercase tracking-wider text-slate-400">Add a note (optional)</label>
                <textarea id="message" name="message" rows="2" maxlength="1000"
                    placeholder="Tell the administrator which account you actually use…"
                    class="mt-1.5 w-full rounded-xl border border-white/10 bg-abyss-950/70 px-3.5 py-2.5 text-sm text-slate-100 placeholder:text-slate-500 transition focus:border-accent-500/60 focus:outline-none focus:ring-2 focus:ring-accent-500/20"></textarea>
                <x-ui.button type="submit" size="sm" variant="secondary" class="mt-3" x-bind:disabled="sending">
                    <span x-show="!sending">Request a Steam reset</span>
                    <span x-show="sending" x-cloak>Submitting…</span>
                </x-ui.button>
            </form>
        </section>
    @endif

    {{-- Featured game --}}
    @if ($topGame && $topGame->game)
        <section class="relative mt-6 overflow-hidden rounded-2xl border border-white/5">
            <div class="absolute inset-0">
                @if ($topGame->game->cover_url)
                    <img src="{{ $topGame->game->cover_url }}" alt="" class="h-full w-full object-cover opacity-40" onerror="this.remove()">
                @endif
                <div class="absolute inset-0 bg-gradient-to-r from-abyss-950 via-abyss-950/85 to-abyss-900/30"></div>
            </div>

            <div class="relative flex flex-col gap-6 p-6 sm:flex-row sm:items-end sm:justify-between sm:p-8">
                <div class="min-w-0">
                    <x-ui.badge tone="cyan">Most played in your library</x-ui.badge>
                    <h2 class="mt-3 truncate text-2xl font-bold text-white sm:text-3xl" title="{{ $topGame->game->name }}">
                        {{ $topGame->game->name }}
                    </h2>
                    <p class="mt-1 text-sm text-slate-400">
                        {{ intdiv($topGame->playtime_minutes, 60) }}h {{ $topGame->playtime_minutes % 60 }}m played
                        @if ($topGame->is_excluded)
                            · <span class="text-amber-300">excluded from picks</span>
                        @endif
                    </p>
                </div>
                <div class="flex flex-none gap-3">
                    <x-ui.button :href="route('picker.index')">Let the wheel decide</x-ui.button>
                </div>
            </div>
        </section>
    @else
        <section class="mt-6 rounded-2xl border border-dashed border-white/10 bg-abyss-900/40 p-8 text-center">
            <h2 class="font-semibold text-white">Your library is empty</h2>
            <p class="mt-2 text-sm text-slate-400">Link Steam and synchronize, or rely on the seeded demo games.</p>
            <div class="mt-4 flex justify-center gap-3">
                <x-ui.button :href="route('steam.connect')">Link Steam</x-ui.button>
            </div>
        </section>
    @endif

    {{-- Stats --}}
    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-ui.stat label="Games in library" :value="$gamesCount" />
        <x-ui.stat label="Excluded from picker" :value="$excludedCount" />
        <x-ui.stat label="Imported from Steam" :value="$steamGamesCount" />
        <div class="rounded-2xl border border-white/5 bg-abyss-900/60 p-5 transition duration-200 hover:border-accent-500/30">
            <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Last picked</p>
            <p class="mt-2 truncate text-lg font-semibold text-accent-300" title="{{ $lastSpin?->game_name ?? '—' }}">
                {{ $lastSpin?->game_name ?? '—' }}
            </p>
            <p class="mt-1 text-xs text-slate-500">{{ $lastSpin?->created_at?->diffForHumans() ?? 'No spins yet' }}</p>
        </div>
    </div>

    <div class="mt-6 grid gap-4 lg:grid-cols-3">
        {{-- Steam --}}
        <section class="rounded-2xl border border-white/5 bg-abyss-900/60 p-6">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-white">Steam account</h2>
                @if ($steamAccount)
                    <x-ui.badge tone="emerald">Linked</x-ui.badge>
                @else
                    <x-ui.badge>Not linked</x-ui.badge>
                @endif
            </div>

            @if ($steamAccount)
                <div class="mt-4 flex items-center gap-3">
                    @if ($steamAccount->avatar_url)
                        <img src="{{ $steamAccount->avatar_url }}" alt="" class="h-12 w-12 rounded-full ring-1 ring-white/10">
                    @else
                        <span class="grid h-12 w-12 place-items-center rounded-full bg-abyss-700 text-sm font-bold text-slate-300">S</span>
                    @endif
                    <div class="min-w-0">
                        <div class="truncate font-medium text-slate-100">{{ $steamAccount->display_name ?? 'Steam user' }}</div>
                        <div class="truncate text-xs text-slate-500">ID {{ $steamAccount->steam_id }}</div>
                    </div>
                </div>
                <p class="mt-3 text-xs text-slate-400">
                    Last synced: {{ $steamAccount->last_synced_at?->diffForHumans() ?? 'never' }}
                </p>

                <form method="POST" action="{{ route('steam.sync') }}" class="mt-4">
                    @csrf
                    <x-ui.button type="submit" variant="success" class="w-full">Synchronize library</x-ui.button>
                </form>

                <form method="POST" action="{{ route('steam.disconnect') }}" class="mt-2"
                    onsubmit="return confirm('Unlink your Steam account? Imported games stay in your library.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full rounded-xl border border-white/10 px-4 py-2.5 text-sm text-slate-300 transition hover:border-red-500/50 hover:text-red-300">
                        Unlink Steam
                    </button>
                </form>
            @else
                <p class="mt-3 text-sm leading-relaxed text-slate-400">
                    Link Steam to import the games you own. Not available right now?
                    The seeded demo library works without Steam.
                </p>
                <x-ui.button :href="route('steam.connect')" variant="secondary" class="mt-4 w-full">Link Steam account</x-ui.button>
            @endif
        </section>

        {{-- Recent spins --}}
        <section class="rounded-2xl border border-white/5 bg-abyss-900/60 p-6 lg:col-span-2">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-white">Recent spins</h2>
                <a href="{{ route('history.index') }}" class="text-sm text-accent-400 hover:text-accent-300">View all</a>
            </div>

            @if ($recentSpins->isEmpty())
                <p class="mt-4 text-sm text-slate-400">No spins yet. Head to the roulette and pick your first game.</p>
            @else
                <ul class="mt-4 space-y-2">
                    @foreach ($recentSpins as $spin)
                        <li class="flex items-center gap-3 rounded-xl border border-white/5 bg-abyss-850/40 p-2.5">
                            <x-game-cover :name="$spin->game_name" :cover="$spin->game?->cover_url" class="h-11 w-20 flex-none rounded-lg" />
                            <span class="min-w-0 flex-1 truncate font-medium text-slate-100">{{ $spin->game_name }}</span>
                            <span class="flex-none text-xs text-slate-500">{{ $spin->created_at->diffForHumans() }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>
@endsection