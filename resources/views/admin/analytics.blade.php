@extends('layouts.admin')

@section('title', 'Analytics')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Analytics</h1>
            <p class="mt-1 text-sm text-slate-400">Live figures derived from MySQL — nothing here is simulated.</p>
        </div>
        <p class="text-xs text-slate-500">Generated {{ $generatedAt->format('M j, Y H:i') }}</p>
    </div>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-ui.stat label="Users" :value="$totals['users']" :hint="$totals['members'].' members · '.$totals['admins'].' admins'" />
        <x-ui.stat label="Community posts" :value="$totals['posts']" :hint="$totals['hiddenPosts'].' hidden'" />
        <x-ui.stat label="Announcements" :value="$totals['announcements']" :hint="$totals['publishedAnnouncements'].' published'" />
        <x-ui.stat label="Spins recorded" :value="$totals['spins']" :hint="$totals['linkedSteam'].' linked Steam'" />
    </div>

    <div class="mt-4 grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-white/5 bg-abyss-900/60 p-5">
            <p class="text-xs font-medium uppercase tracking-wider text-slate-400">New users</p>
            <p class="mt-2 text-2xl font-semibold text-white">{{ $recent['newUsers'] }} <span class="text-sm font-normal text-slate-500">/ 7d</span></p>
            <p class="mt-1 text-xs text-slate-500">{{ $recent['newUsers30'] }} in 30 days</p>
        </div>
        <div class="rounded-2xl border border-white/5 bg-abyss-900/60 p-5">
            <p class="text-xs font-medium uppercase tracking-wider text-slate-400">New posts</p>
            <p class="mt-2 text-2xl font-semibold text-white">{{ $recent['newPosts'] }} <span class="text-sm font-normal text-slate-500">/ 7d</span></p>
            <p class="mt-1 text-xs text-slate-500">{{ $recent['newPosts30'] }} in 30 days</p>
        </div>
        <div class="rounded-2xl border border-white/5 bg-abyss-900/60 p-5">
            <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Spins</p>
            <p class="mt-2 text-2xl font-semibold text-white">{{ $recent['spins'] }} <span class="text-sm font-normal text-slate-500">/ 7d</span></p>
            <p class="mt-1 text-xs text-slate-500">{{ $recent['spins30'] }} in 30 days</p>
        </div>
    </div>

    <div class="mt-6 rounded-2xl border border-white/5 bg-abyss-900/60 p-5">
        <h2 class="text-sm font-semibold text-white">Posts · last 14 days</h2>
        <div class="mt-5 flex h-40 items-end gap-1.5">
            @foreach ($postSeries as $day)
                <div class="group flex flex-1 flex-col items-center justify-end gap-2" title="{{ $day['label'] }}: {{ $day['count'] }}">
                    <span class="text-[10px] text-slate-500 opacity-0 transition group-hover:opacity-100">{{ $day['count'] }}</span>
                    <div class="w-full rounded-t-md bg-gradient-to-t from-accent-600/60 to-accent-400"
                        style="height: {{ max(2, (int) round(($day['count'] / $maxPosts) * 100)) }}%"></div>
                    <span class="text-[10px] text-slate-600">{{ $day['label'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-white/5 bg-abyss-900/60 p-5">
            <h2 class="text-sm font-semibold text-white">Most-picked games</h2>
            <ul class="mt-4 space-y-3">
                @forelse ($topGames as $game)
                    <li class="flex items-center justify-between gap-3 text-sm">
                        <span class="truncate text-slate-200">{{ $game->game_name }}</span>
                        <x-ui.badge tone="cyan">{{ $game->picks }}</x-ui.badge>
                    </li>
                @empty
                    <li class="text-sm text-slate-500">No spins recorded yet.</li>
                @endforelse
            </ul>
        </div>

        <div class="rounded-2xl border border-white/5 bg-abyss-900/60 p-5">
            <h2 class="text-sm font-semibold text-white">Most active posters</h2>
            <ul class="mt-4 space-y-3">
                @forelse ($topPosters as $poster)
                    <li class="flex items-center justify-between gap-3 text-sm">
                        <span class="truncate text-slate-200">{{ $poster->name }}</span>
                        <x-ui.badge tone="slate">{{ $poster->posts_count }}</x-ui.badge>
                    </li>
                @empty
                    <li class="text-sm text-slate-500">No published posts yet.</li>
                @endforelse
            </ul>
        </div>
    </div>
@endsection
