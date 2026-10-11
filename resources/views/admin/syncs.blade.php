@extends('layouts.admin')

@section('title', 'Sync activity')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h1 class="text-2xl font-bold text-white">Steam sync activity</h1>
        <form method="GET" action="{{ route('admin.syncs.index') }}" class="flex gap-2">
            <select name="status" class="rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-amber-500 focus:outline-none">
                <option value="">All statuses</option>
                @foreach (['pending', 'running', 'success', 'failed'] as $option)
                    <option value="{{ $option }}" @selected($status === $option)>{{ $option }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-lg bg-slate-700 px-4 py-2 text-sm text-white hover:bg-slate-600">Filter</button>
        </form>
    </div>

    <div class="mt-6 overflow-x-auto rounded-xl border border-slate-800">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-900 text-slate-400">
                <tr>
                    <th class="px-4 py-3 font-medium">User</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3 font-medium">Imported</th>
                    <th class="px-4 py-3 font-medium">Started</th>
                    <th class="px-4 py-3 font-medium">Error</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
                @forelse ($runs as $run)
                    <tr class="bg-slate-900/40">
                        <td class="px-4 py-3 text-slate-100">{{ $run->user?->name ?? 'Unknown' }}</td>
                        <td class="px-4 py-3">@include('admin.partials.sync-status', ['status' => $run->status])</td>
                        <td class="px-4 py-3 text-slate-300">{{ $run->games_imported }}</td>
                        <td class="px-4 py-3 text-slate-400">{{ $run->started_at?->diffForHumans() ?? '—' }}</td>
                        <td class="max-w-xs truncate px-4 py-3 text-slate-400" title="{{ $run->error_message }}">{{ $run->error_message ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-slate-500">No sync runs recorded.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $runs->links() }}</div>
@endsection