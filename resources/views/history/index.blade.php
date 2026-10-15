@extends('layouts.app')

@section('title', 'History')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white sm:text-3xl">Spin history</h1>
            <p class="mt-1 text-sm text-slate-400">Your previous picks, newest first.</p>
        </div>
        <x-ui.button :href="route('picker.index')">Spin again</x-ui.button>
    </div>

    @if ($history->isEmpty())
        <div class="mt-8 rounded-2xl border border-dashed border-white/10 bg-abyss-900/40 p-12 text-center">
            <div class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-accent-500/10 text-accent-300">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="h-6 w-6" aria-hidden="true">
                    <circle cx="12" cy="12" r="8.5" />
                    <path d="M12 7.5V12l3 2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <h2 class="mt-4 font-semibold text-white">No history yet</h2>
            <p class="mt-2 text-sm text-slate-400">Your roulette picks will appear here.</p>
        </div>
    @else
        <div class="mt-6 overflow-hidden rounded-2xl border border-white/5">
            <table class="w-full text-left text-sm">
                <thead class="bg-abyss-900/80 text-xs uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-4 py-3 font-medium">Game</th>
                        <th class="hidden px-4 py-3 font-medium sm:table-cell">Filters</th>
                        <th class="px-4 py-3 text-right font-medium">Picked</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach ($history as $spin)
                        <tr class="bg-abyss-900/40 transition hover:bg-abyss-850/60">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <x-game-cover :name="$spin->game_name" :cover="$spin->game?->cover_url" class="h-11 w-20 flex-none rounded-lg" />
                                    <span class="font-medium text-slate-100">{{ $spin->game_name }}</span>
                                </div>
                            </td>
                            <td class="hidden px-4 py-3 sm:table-cell">
                                @if ($spin->filters)
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach ($spin->filters as $key => $value)
                                            <x-ui.badge>{{ str_replace('_', ' ', $key) }}: {{ $value }}</x-ui.badge>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-slate-600">none</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right text-slate-400" title="{{ $spin->created_at->toDateTimeString() }}">
                                {{ $spin->created_at->diffForHumans() }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-8">{{ $history->links() }}</div>
    @endif
@endsection