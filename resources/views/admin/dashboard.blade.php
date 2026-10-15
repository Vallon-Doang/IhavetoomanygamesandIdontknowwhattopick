@extends('layouts.admin')

@section('title', 'Admin overview')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white">System overview</h1>
            <p class="mt-1 text-sm text-slate-400">Live counts from the database.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <x-ui.button :href="route('admin.users.index')" variant="secondary" size="sm">Manage users</x-ui.button>
            <x-ui.button :href="route('admin.games.index')" variant="secondary" size="sm">Game catalog</x-ui.button>
            <x-ui.button :href="route('admin.syncs.index')" variant="secondary" size="sm">Sync activity</x-ui.button>
        </div>
    </div>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-ui.stat label="Registered users" :value="$counts['users']" />
        <x-ui.stat label="Members" :value="$counts['members']" />
        <x-ui.stat label="Admins" :value="$counts['admins']" />
        <x-ui.stat label="Games (catalog)" :value="$counts['games']" />
    </div>

    <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-ui.stat label="Ownership rows" :value="$counts['ownerships']" />
        <x-ui.stat label="Spins recorded" :value="$counts['spins']" />
        <x-ui.stat label="Steam accounts linked" :value="$counts['linkedSteam']" />
        <div class="rounded-2xl border border-white/5 bg-abyss-900/60 p-5">
            <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Sync runs</p>
            <p class="mt-2 flex items-baseline gap-2 text-2xl font-semibold">
                <span class="text-emerald-400">{{ $syncCounts['success'] }}</span>
                <span class="text-xs font-normal text-slate-500">success</span>
                <span class="text-red-400">{{ $syncCounts['failed'] }}</span>
                <span class="text-xs font-normal text-slate-500">failed</span>
            </p>
        </div>
    </div>

    <section class="mt-8">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-white">Latest sync runs</h2>
            <a href="{{ route('admin.syncs.index') }}" class="text-sm text-accent-400 hover:text-accent-300">View all</a>
        </div>

        @if ($recentSyncs->isEmpty())
            <p class="mt-3 rounded-2xl border border-dashed border-white/10 bg-abyss-900/40 p-6 text-center text-sm text-slate-400">
                No synchronization has been attempted yet.
            </p>
        @else
            <ul class="mt-3 divide-y divide-white/5 overflow-hidden rounded-2xl border border-white/5">
                @foreach ($recentSyncs as $run)
                    <li class="flex items-center justify-between gap-4 bg-abyss-900/40 px-4 py-3 text-sm">
                        <span class="truncate text-slate-200">{{ $run->user?->name ?? 'Unknown user' }}</span>
                        <div class="flex items-center gap-3">
                            <span class="text-slate-500">{{ $run->created_at->diffForHumans() }}</span>
                            @include('admin.partials.sync-status', ['status' => $run->status])
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>
@endsection