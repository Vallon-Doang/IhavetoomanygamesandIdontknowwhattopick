@extends('layouts.app')

@section('title', 'Library')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">My library</h1>
            <p class="mt-1 text-sm text-slate-400">{{ $games->total() }} games. Search and exclude games to shape your picks.</p>
        </div>

        <form method="GET" action="{{ route('library.index') }}" class="flex gap-2">
            <input type="search" name="search" value="{{ $search }}" placeholder="Search titles…"
                class="rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-indigo-500 focus:outline-none">
            <button type="submit" class="rounded-lg bg-slate-700 px-4 py-2 text-sm text-white hover:bg-slate-600">Search</button>
            @if ($search !== '')
                <a href="{{ route('library.index') }}" class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 hover:border-slate-500">Clear</a>
            @endif
        </form>
    </div>

    @if ($games->isEmpty())
        <div class="mt-8 rounded-xl border border-dashed border-slate-700 bg-slate-900/40 p-10 text-center">
            <h2 class="font-semibold text-white">No games found</h2>
            <p class="mt-2 text-sm text-slate-400">
                @if ($search !== '')
                    Nothing matches “{{ $search }}”. Try a different search.
                @else
                    Your library is empty. Link Steam and synchronize, or rely on seeded demo games.
                @endif
            </p>
        </div>
    @else
        <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($games as $userGame)
                <article class="overflow-hidden rounded-xl border border-slate-800 bg-slate-900/50">
                    <div class="aspect-[2/1] bg-slate-800">
                        @if ($userGame->game->cover_url)
                            <img src="{{ $userGame->game->cover_url }}" alt="" loading="lazy"
                                class="h-full w-full object-cover" onerror="this.style.display='none'">
                        @else
                            <div class="grid h-full place-items-center text-3xl font-bold text-slate-600">
                                {{ strtoupper(substr($userGame->game->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>

                    <div class="p-4">
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="font-medium text-slate-100">{{ $userGame->game->name }}</h3>
                            <span class="shrink-0 rounded px-2 py-0.5 text-xs font-medium
                                {{ $userGame->source === 'steam' ? 'bg-sky-900/50 text-sky-300' : 'bg-slate-700/60 text-slate-300' }}">
                                {{ $userGame->source }}
                            </span>
                        </div>

                        <p class="mt-1 text-xs text-slate-400">
                            {{ intdiv($userGame->playtime_minutes, 60) }}h {{ $userGame->playtime_minutes % 60 }}m playtime
                        </p>

                        <form method="POST" action="{{ route('library.exclusion', $userGame) }}" class="mt-3">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full rounded-lg border px-3 py-1.5 text-sm
                                {{ $userGame->is_excluded
                                    ? 'border-emerald-700 text-emerald-300 hover:bg-emerald-900/30'
                                    : 'border-slate-700 text-slate-300 hover:bg-slate-800' }}">
                                {{ $userGame->is_excluded ? 'Include in picker' : 'Exclude from picker' }}
                            </button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $games->links() }}
        </div>
    @endif
@endsection