@extends('layouts.admin')

@section('title', 'Game catalog')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h1 class="text-2xl font-bold text-white">Game catalog</h1>
        <form method="GET" action="{{ route('admin.games.index') }}" class="flex gap-2">
            <input type="search" name="search" value="{{ $search }}" placeholder="Title or app ID…"
                class="rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-amber-500 focus:outline-none">
            <button type="submit" class="rounded-lg bg-slate-700 px-4 py-2 text-sm text-white hover:bg-slate-600">Search</button>
            @if ($search !== '')
                <a href="{{ route('admin.games.index') }}" class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 hover:border-slate-500">Clear</a>
            @endif
        </form>
    </div>

    <div class="mt-6 overflow-x-auto rounded-xl border border-slate-800">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-900 text-slate-400">
                <tr>
                    <th class="px-4 py-3 font-medium">Game</th>
                    <th class="px-4 py-3 font-medium">Steam App ID</th>
                    <th class="px-4 py-3 font-medium">Owners</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
                @forelse ($games as $game)
                    <tr class="bg-slate-900/40">
                        <td class="px-4 py-3 text-slate-100">{{ $game->name }}</td>
                        <td class="px-4 py-3 text-slate-400">{{ $game->steam_app_id ?? 'demo' }}</td>
                        <td class="px-4 py-3 text-slate-300">{{ $game->user_games_count }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-4 py-6 text-center text-slate-500">No games found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $games->links() }}</div>
@endsection