@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Welcome, {{ $user->name }}</h1>
            <p class="mt-1 text-sm text-slate-400">Your GameSpin overview.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('library.index') }}" class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-200 hover:border-slate-500">Library</a>
            <a href="{{ route('picker.index') }}" class="rounded-lg bg-indigo-500 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-400">Spin now</a>
        </div>
    </div>

    <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-slate-800 bg-slate-900/50 p-5">
            <div class="text-sm text-slate-400">Games in library</div>
            <div class="mt-1 text-3xl font-bold text-white">{{ $gamesCount }}</div>
        </div>
        <div class="rounded-xl border border-slate-800 bg-slate-900/50 p-5">
            <div class="text-sm text-slate-400">Excluded from picker</div>
            <div class="mt-1 text-3xl font-bold text-white">{{ $excludedCount }}</div>
        </div>
        <div class="rounded-xl border border-slate-800 bg-slate-900/50 p-5">
            <div class="text-sm text-slate-400">Imported from Steam</div>
            <div class="mt-1 text-3xl font-bold text-white">{{ $steamGamesCount }}</div>
        </div>
        <div class="rounded-xl border border-slate-800 bg-slate-900/50 p-5">
            <div class="text-sm text-slate-400">Last picked</div>
            <div class="mt-1 truncate text-lg font-semibold text-indigo-300" title="{{ $lastSpin?->game_name ?? '—' }}">
                {{ $lastSpin?->game_name ?? '—' }}
            </div>
        </div>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-3">
        <section class="lg:col-span-1 rounded-xl border border-slate-800 bg-slate-900/50 p-6">
            <h2 class="font-semibold text-white">Steam account</h2>

            @if ($steamAccount)
                <div class="mt-4 flex items-center gap-3">
                    @if ($steamAccount->avatar_url)
                        <img src="{{ $steamAccount->avatar_url }}" alt="" class="h-12 w-12 rounded-full border border-slate-700">
                    @endif
                    <div class="min-w-0">
                        <div class="truncate font-medium text-slate-200">{{ $steamAccount->display_name ?? 'Steam user' }}</div>
                        <div class="truncate text-xs text-slate-500">ID {{ $steamAccount->steam_id }}</div>
                    </div>
                </div>
                <p class="mt-3 text-xs text-slate-400">
                    Last synced:
                    {{ $steamAccount->last_synced_at?->diffForHumans() ?? 'never' }}
                </p>

                <form method="POST" action="{{ route('steam.sync') }}" class="mt-4">
                    @csrf
                    <button type="submit" class="w-full rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500">
                        Synchronize library
                    </button>
                </form>

                <form method="POST" action="{{ route('steam.disconnect') }}" class="mt-2"
                    onsubmit="return confirm('Unlink your Steam account? Imported games stay in your library.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 hover:border-red-500 hover:text-red-300">
                        Unlink Steam
                    </button>
                </form>
            @else
                <p class="mt-2 text-sm text-slate-400">
                    Link Steam to import the games you own. Not available right now?
                    The seeded demo library works without Steam.
                </p>
                <a href="{{ route('steam.connect') }}" class="mt-4 block w-full rounded-lg bg-slate-700 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-slate-600">
                    Link Steam account
                </a>
            @endif
        </section>

        <section class="lg:col-span-2 rounded-xl border border-slate-800 bg-slate-900/50 p-6">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-white">Recent spins</h2>
                <a href="{{ route('history.index') }}" class="text-sm text-indigo-400 hover:text-indigo-300">View all</a>
            </div>

            @if ($recentSpins->isEmpty())
                <p class="mt-4 text-sm text-slate-400">No spins yet. Head to the roulette and pick your first game.</p>
            @else
                <ul class="mt-4 divide-y divide-slate-800">
                    @foreach ($recentSpins as $spin)
                        <li class="flex items-center justify-between py-3">
                            <span class="text-slate-200">{{ $spin->game_name }}</span>
                            <span class="text-xs text-slate-500">{{ $spin->created_at->diffForHumans() }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>
@endsection