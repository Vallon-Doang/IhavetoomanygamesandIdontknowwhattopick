@extends('layouts.admin')

@section('title', 'Admin overview')

@section('content')
    <h1 class="text-2xl font-bold text-white">System overview</h1>
    <p class="mt-1 text-sm text-slate-400">Live counts from the database.</p>

    <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ([
            'Registered users' => $counts['users'],
            'Members' => $counts['members'],
            'Admins' => $counts['admins'],
            'Games (catalog)' => $counts['games'],
        ] as $label => $value)
            <div class="rounded-xl border border-slate-800 bg-slate-900/50 p-5">
                <div class="text-sm text-slate-400">{{ $label }}</div>
                <div class="mt-1 text-3xl font-bold text-white">{{ $value }}</div>
            </div>
        @endforeach
    </div>

    <div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ([
            'Ownership rows' => $counts['ownerships'],
            'Spins recorded' => $counts['spins'],
            'Steam accounts linked' => $counts['linkedSteam'],
        ] as $label => $value)
            <div class="rounded-xl border border-slate-800 bg-slate-900/50 p-5">
                <div class="text-sm text-slate-400">{{ $label }}</div>
                <div class="mt-1 text-3xl font-bold text-white">{{ $value }}</div>
            </div>
        @endforeach
        <div class="rounded-xl border border-slate-800 bg-slate-900/50 p-5">
            <div class="text-sm text-slate-400">Sync runs</div>
            <div class="mt-1 text-lg">
                <span class="font-bold text-emerald-400">{{ $syncCounts['success'] }}</span> success ·
                <span class="font-bold text-red-400">{{ $syncCounts['failed'] }}</span> failed
            </div>
        </div>
    </div>

    <div class="mt-8 flex flex-wrap gap-3">
        <a href="{{ route('admin.users.index') }}" class="rounded-lg bg-slate-700 px-4 py-2 text-sm text-white hover:bg-slate-600">Manage users</a>
        <a href="{{ route('admin.games.index') }}" class="rounded-lg bg-slate-700 px-4 py-2 text-sm text-white hover:bg-slate-600">Game catalog</a>
        <a href="{{ route('admin.syncs.index') }}" class="rounded-lg bg-slate-700 px-4 py-2 text-sm text-white hover:bg-slate-600">Sync activity</a>
    </div>

    <section class="mt-10">
        <h2 class="font-semibold text-white">Latest sync runs</h2>
        @if ($recentSyncs->isEmpty())
            <p class="mt-3 text-sm text-slate-400">No synchronization has been attempted yet.</p>
        @else
            <ul class="mt-3 divide-y divide-slate-800 rounded-xl border border-slate-800">
                @foreach ($recentSyncs as $run)
                    <li class="flex items-center justify-between gap-4 px-4 py-3 text-sm">
                        <span class="text-slate-200">{{ $run->user?->name ?? 'Unknown user' }}</span>
                        <span class="text-slate-500">{{ $run->created_at->diffForHumans() }}</span>
                        @include('admin.partials.sync-status', ['status' => $run->status])
                    </li>
                @endforeach
            </ul>
        @endif
    </section>
@endsection