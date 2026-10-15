@extends('layouts.admin')

@section('title', 'Steam requests')

@section('content')
    @php
        $filters = [
            'pending' => 'Pending',
            'resolved' => 'Resolved',
            'dismissed' => 'Dismissed',
            'all' => 'All',
        ];
    @endphp

    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Steam reset requests</h1>
            <p class="mt-1 text-sm text-slate-400">
                Members ask an administrator to clear a conflicting Steam link · {{ $pendingCount }} pending.
            </p>
        </div>
        <div class="flex gap-1 rounded-xl border border-white/10 bg-abyss-900/60 p-1">
            @foreach ($filters as $key => $label)
                <a href="{{ route('admin.steam-requests.index', ['filter' => $key]) }}"
                    @class([
                        'rounded-lg px-3 py-1.5 text-sm font-medium transition',
                        'bg-accent-500/15 text-accent-300' => $filter === $key,
                        'text-slate-400 hover:text-white' => $filter !== $key,
                    ])>{{ $label }}</a>
            @endforeach
        </div>
    </div>

    <div class="mt-6 overflow-x-auto rounded-2xl border border-white/5">
        <table class="w-full text-left text-sm">
            <thead class="bg-abyss-900/80 text-xs uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-4 py-3 font-medium">Requested by</th>
                    <th class="px-4 py-3 font-medium">Conflicting Steam ID</th>
                    <th class="px-4 py-3 font-medium">Note</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3 text-right font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse ($requests as $steamRequest)
                    <tr class="bg-abyss-900/40 align-top transition hover:bg-abyss-850/60">
                        <td class="px-4 py-3">
                            <p class="font-medium text-slate-100">{{ $steamRequest->requester?->name ?? 'Deleted user' }}</p>
                            <p class="text-xs text-slate-500">{{ $steamRequest->requester?->email }}</p>
                            <p class="mt-0.5 text-xs text-slate-600">{{ $steamRequest->created_at->format('M j, Y H:i') }}</p>
                        </td>
                        <td class="px-4 py-3 font-mono text-xs text-slate-300">{{ $steamRequest->steam_id ?? '—' }}</td>
                        <td class="max-w-sm px-4 py-3 text-slate-400">
                            {{ $steamRequest->message ?: '—' }}
                        </td>
                        <td class="px-4 py-3">
                            @if ($steamRequest->status === \App\Models\SteamResetRequest::STATUS_PENDING)
                                <x-ui.badge tone="amber">Pending</x-ui.badge>
                            @elseif ($steamRequest->status === \App\Models\SteamResetRequest::STATUS_RESOLVED)
                                <x-ui.badge tone="emerald">Resolved</x-ui.badge>
                            @else
                                <x-ui.badge tone="slate">Dismissed</x-ui.badge>
                            @endif
                            @if ($steamRequest->resolver)
                                <p class="mt-1 text-xs text-slate-600">by {{ $steamRequest->resolver->name }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if ($steamRequest->isPending())
                                <div class="flex items-center justify-end gap-2">
                                    <form method="POST" action="{{ route('admin.steam-requests.update', $steamRequest) }}"
                                        x-data @submit.prevent="if (confirm('Resolve and unlink the conflicting Steam account?')) $el.submit()">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="action" value="resolve">
                                        <x-ui.button type="submit" variant="secondary" size="sm">Resolve &amp; unlink</x-ui.button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.steam-requests.update', $steamRequest) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="action" value="dismiss">
                                        <button type="submit" class="text-xs font-medium text-slate-400 hover:text-white">Dismiss</button>
                                    </form>
                                </div>
                            @else
                                <p class="text-right text-xs text-slate-600">{{ $steamRequest->resolved_at?->format('M j, Y') }}</p>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-10 text-center text-slate-500">No requests match this filter.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $requests->links() }}</div>
@endsection
