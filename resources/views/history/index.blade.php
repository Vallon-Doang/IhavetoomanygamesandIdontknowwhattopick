@extends('layouts.app')

@section('title', 'History')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Spin history</h1>
            <p class="mt-1 text-sm text-slate-400">Your previous picks, newest first.</p>
        </div>
        <a href="{{ route('picker.index') }}" class="rounded-lg bg-indigo-500 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-400">Spin again</a>
    </div>

    @if ($history->isEmpty())
        <div class="mt-8 rounded-xl border border-dashed border-slate-700 bg-slate-900/40 p-10 text-center">
            <h2 class="font-semibold text-white">No history yet</h2>
            <p class="mt-2 text-sm text-slate-400">Your roulette picks will appear here.</p>
        </div>
    @else
        <div class="mt-8 overflow-hidden rounded-xl border border-slate-800">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-900 text-slate-400">
                    <tr>
                        <th class="px-4 py-3 font-medium">Game</th>
                        <th class="px-4 py-3 font-medium">Filters</th>
                        <th class="px-4 py-3 font-medium">Picked at</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @foreach ($history as $spin)
                        <tr class="bg-slate-900/40">
                            <td class="px-4 py-3 font-medium text-slate-100">{{ $spin->game_name }}</td>
                            <td class="px-4 py-3 text-slate-400">
                                @if ($spin->filters)
                                    @foreach ($spin->filters as $key => $value)
                                        <span class="mr-1 inline-block rounded bg-slate-800 px-2 py-0.5 text-xs">{{ $key }}: {{ $value }}</span>
                                    @endforeach
                                @else
                                    <span class="text-slate-600">none</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-400" title="{{ $spin->created_at->toDateTimeString() }}">
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