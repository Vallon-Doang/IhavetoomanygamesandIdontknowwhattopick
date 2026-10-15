@extends('layouts.admin')

@section('title', 'Game catalog')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Game catalog</h1>
            <p class="mt-1 text-sm text-slate-400">{{ $games->total() }} game{{ $games->total() === 1 ? '' : 's' }} in the shared catalog.</p>
        </div>
        <form method="GET" action="{{ route('admin.games.index') }}" class="flex gap-2">
            <input type="search" name="search" value="{{ $search }}" placeholder="Title or app ID…" aria-label="Search games"
                class="rounded-xl border border-white/10 bg-abyss-900/70 px-3.5 py-2.5 text-sm text-slate-100 placeholder:text-slate-500 transition focus:border-accent-500/60 focus:outline-none focus:ring-2 focus:ring-accent-500/20">
            <x-ui.button type="submit" variant="secondary" size="sm">Search</x-ui.button>
            @if ($search !== '')
                <a href="{{ route('admin.games.index') }}" class="self-center px-2 text-sm text-slate-400 hover:text-white">Clear</a>
            @endif
        </form>
    </div>

    <div class="mt-6 overflow-x-auto rounded-2xl border border-white/5">
        <table class="w-full text-left text-sm">
            <thead class="bg-abyss-900/80 text-xs uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-4 py-3 font-medium">Game</th>
                    <th class="px-4 py-3 font-medium">Steam App ID</th>
                    <th class="px-4 py-3 font-medium">Owners</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse ($games as $game)
                    <tr class="bg-abyss-900/40 transition hover:bg-abyss-850/60">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <x-game-cover :name="$game->name" :cover="$game->cover_url" class="h-10 w-20 flex-none rounded-lg" />
                                <span class="text-slate-100">{{ $game->name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @if ($game->steam_app_id)
                                <span class="font-mono text-slate-400">{{ $game->steam_app_id }}</span>
                            @else
                                <x-ui.badge>demo</x-ui.badge>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-300">{{ $game->user_games_count }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-4 py-10 text-center text-slate-500">No games found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $games->links() }}</div>
@endsection