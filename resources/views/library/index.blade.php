@extends('layouts.app')

@section('title', 'Library')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white sm:text-3xl">My library</h1>
            <p class="mt-1 text-sm text-slate-400">
                {{ $games->total() }} game{{ $games->total() === 1 ? '' : 's' }} · exclude titles to shape your picks.
            </p>
        </div>

        <form method="GET" action="{{ route('library.index') }}" class="flex w-full items-center gap-2 sm:w-auto">
            <div class="relative flex-1 sm:w-64">
                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6"
                    class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-500" aria-hidden="true">
                    <circle cx="9" cy="9" r="5.25" />
                    <path d="m13 13 3.5 3.5" stroke-linecap="round" />
                </svg>
                <input type="search" name="search" value="{{ $search }}" placeholder="Search titles…"
                    aria-label="Search library"
                    class="w-full rounded-xl border border-white/10 bg-abyss-900/70 py-2.5 pl-9 pr-3 text-sm text-slate-100 placeholder:text-slate-500 transition focus:border-accent-500/60 focus:outline-none focus:ring-2 focus:ring-accent-500/20">
            </div>
            <x-ui.button type="submit" variant="secondary">Search</x-ui.button>
            @if ($search !== '')
                <a href="{{ route('library.index') }}" class="rounded-xl px-3 py-2.5 text-sm text-slate-400 transition hover:text-white">Clear</a>
            @endif
        </form>
    </div>

    @if ($games->isEmpty())
        <div class="mt-8 rounded-2xl border border-dashed border-white/10 bg-abyss-900/40 p-12 text-center">
            <div class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-accent-500/10 text-accent-300">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="h-6 w-6" aria-hidden="true">
                    <rect x="3" y="5" width="18" height="14" rx="2.5" />
                    <path d="m10 12 4 0M12 10v4" stroke-linecap="round" />
                </svg>
            </div>
            <h2 class="mt-4 font-semibold text-white">No games found</h2>
            <p class="mx-auto mt-2 max-w-md text-sm text-slate-400">
                @if ($search !== '')
                    Nothing matches “{{ $search }}”. Try a different search.
                @else
                    Your library is empty. Link Steam and synchronize, or rely on the seeded demo games.
                @endif
            </p>
            @if ($search !== '')
                <x-ui.button :href="route('library.index')" variant="secondary" class="mt-5">Clear search</x-ui.button>
            @endif
        </div>
    @else
        <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
            @foreach ($games as $userGame)
                <article class="group relative">
                    <div class="relative overflow-hidden rounded-xl ring-1 ring-white/5 transition duration-300 group-hover:ring-accent-400/50 group-hover:shadow-lg group-hover:shadow-accent-500/10">
                        <x-game-cover :name="$userGame->game->name" :cover="$userGame->game->cover_url"
                            class="aspect-[2/1] w-full {{ $userGame->is_excluded ? 'opacity-50 saturate-50' : '' }}" />

                        <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-abyss-950/95 via-abyss-950/25 to-transparent"></div>

                        <div class="absolute left-2 top-2">
                            <x-ui.badge :tone="$userGame->source === 'steam' ? 'sky' : 'slate'">{{ $userGame->source }}</x-ui.badge>
                        </div>

                        <form method="POST" action="{{ route('library.exclusion', $userGame) }}" class="absolute right-2 top-2">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                aria-label="{{ $userGame->is_excluded ? 'Include '.$userGame->game->name.' in picker' : 'Exclude '.$userGame->game->name.' from picker' }}"
                                title="{{ $userGame->is_excluded ? 'Include in picker' : 'Exclude from picker' }}"
                                class="grid h-8 w-8 place-items-center rounded-full border border-white/15 bg-abyss-950/70 text-slate-200 backdrop-blur transition hover:border-accent-400/60 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-accent-400/70">
                                @if ($userGame->is_excluded)
                                    <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" class="h-4 w-4" aria-hidden="true">
                                        <path d="M2.5 10S5.5 5 10 5s7.5 5 7.5 5-3 5-7.5 5-7.5-5-7.5-5Z" stroke-linejoin="round" />
                                        <circle cx="10" cy="10" r="2.2" />
                                    </svg>
                                @else
                                    <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" class="h-4 w-4" aria-hidden="true">
                                        <path d="M2.5 10S5.5 5 10 5s7.5 5 7.5 5-3 5-7.5 5-7.5-5-7.5-5Z" stroke-linejoin="round" />
                                        <path d="m4 4 12 12" stroke-linecap="round" />
                                    </svg>
                                @endif
                            </button>
                        </form>

                        <div class="absolute inset-x-0 bottom-0 p-3">
                            <h3 class="truncate text-sm font-semibold text-white" title="{{ $userGame->game->name }}">
                                {{ $userGame->game->name }}
                            </h3>
                            <p class="mt-0.5 text-[11px] text-slate-400">
                                {{ intdiv($userGame->playtime_minutes, 60) }}h {{ $userGame->playtime_minutes % 60 }}m
                                @if ($userGame->is_excluded)
                                    · <span class="text-amber-300">excluded</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $games->links() }}
        </div>
    @endif
@endsection