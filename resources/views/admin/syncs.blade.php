@extends('layouts.admin')

@section('title', 'Sync activity')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Steam sync activity</h1>
            <p class="mt-1 text-sm text-slate-400">Recent synchronization runs and their outcomes.</p>
        </div>
        <form method="GET" action="{{ route('admin.syncs.index') }}" class="flex gap-2">
            <select name="status" aria-label="Filter by status"
                class="rounded-xl border border-white/10 bg-abyss-900/70 px-3.5 py-2.5 text-sm text-slate-100 transition focus:border-accent-500/60 focus:outline-none focus:ring-2 focus:ring-accent-500/20">
                <option value="">All statuses</option>
                @foreach (['pending', 'running', 'success', 'failed'] as $option)
                    <option value="{{ $option }}" @selected($status === $option)>{{ ucfirst($option) }}</option>
                @endforeach
            </select>
            <x-ui.button type="submit" variant="secondary" size="sm">Filter</x-ui.button>
        </form>
    </div>

    <div class="mt-6 overflow-x-auto rounded-2xl border border-white/5">
        <table class="w-full text-left text-sm">
            <thead class="bg-abyss-900/80 text-xs uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-4 py-3 font-medium">User</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3 font-medium">Imported</th>
                    <th class="px-4 py-3 font-medium">Started</th>
                    <th class="px-4 py-3 font-medium">Error</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse ($runs as $run)
                    <tr class="bg-abyss-900/40 transition hover:bg-abyss-850/60">
                        <td class="px-4 py-3 text-slate-100">{{ $run->user?->name ?? 'Unknown' }}</td>
                        <td class="px-4 py-3">@include('admin.partials.sync-status', ['status' => $run->status])</td>
                        <td class="px-4 py-3 text-slate-300">{{ $run->games_imported }}</td>
                        <td class="px-4 py-3 text-slate-400">{{ $run->started_at?->diffForHumans() ?? '—' }}</td>
                        <td class="max-w-xs truncate px-4 py-3 text-slate-400" title="{{ $run->error_message }}">{{ $run->error_message ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-10 text-center text-slate-500">No sync runs recorded.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $runs->links() }}</div>
@endsection