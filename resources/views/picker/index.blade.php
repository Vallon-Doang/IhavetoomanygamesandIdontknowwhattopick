@extends('layouts.app')

@section('title', 'Roulette')

@section('content')
    <div class="mx-auto max-w-2xl"
        x-data="{
            spinning: false,
            result: null,
            names: @js($eligibleCount > 0 ? 'spinning…' : 'no eligible games'),
        }">
        <h1 class="text-2xl font-bold text-white">GameSpin roulette</h1>
        <p class="mt-1 text-sm text-slate-400">
            {{ $eligibleCount }} eligible game{{ $eligibleCount === 1 ? '' : 's' }} in your library right now.
        </p>

        @if (session('picked'))
            @php($picked = session('picked'))
            <div class="mt-6 rounded-xl border border-indigo-700 bg-indigo-950/50 p-8 text-center">
                <p class="text-sm uppercase tracking-wide text-indigo-300">Your game is</p>
                <p class="mt-2 text-3xl font-extrabold text-white">{{ $picked['name'] }}</p>
                @if (! empty($picked['filters']))
                    <p class="mt-3 text-xs text-slate-400">
                        Filters applied:
                        @foreach ($picked['filters'] as $key => $value)
                            <span class="mr-2 rounded bg-slate-800 px-2 py-0.5">{{ $key }}: {{ $value }}</span>
                        @endforeach
                    </p>
                @endif
                <div class="mt-6 flex justify-center gap-3">
                    <a href="{{ route('history.index') }}" class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-200 hover:border-slate-500">View history</a>
                    <a href="{{ route('library.index') }}" class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-200 hover:border-slate-500">Library</a>
                </div>
            </div>
        @endif

        <div class="mt-6 overflow-hidden rounded-xl border border-slate-800 bg-slate-900/50">
            <div class="grid h-32 place-items-center bg-gradient-to-r from-slate-800 via-slate-900 to-slate-800"
                :class="spinning && 'animate-pulse'">
                <span class="px-6 text-center text-2xl font-bold text-slate-200" x-text="spinning ? 'Spinning…' : '🎲'"></span>
            </div>

            <form method="POST" action="{{ route('picker.spin') }}" class="space-y-5 p-6" @submit="spinning = true">
                @csrf

                <div>
                    <label for="search" class="block text-sm font-medium text-slate-300">Title contains (optional)</label>
                    <input id="search" name="search" type="text" value="{{ old('search') }}" placeholder="e.g. Portal"
                        class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-slate-100 focus:border-indigo-500 focus:outline-none">
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="min_playtime" class="block text-sm font-medium text-slate-300">Min playtime (minutes)</label>
                        <input id="min_playtime" name="min_playtime" type="number" min="0" value="{{ old('min_playtime') }}"
                            class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-slate-100 focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label for="max_playtime" class="block text-sm font-medium text-slate-300">Max playtime (minutes)</label>
                        <input id="max_playtime" name="max_playtime" type="number" min="0" value="{{ old('max_playtime') }}"
                            class="mt-1 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-slate-100 focus:border-indigo-500 focus:outline-none">
                    </div>
                </div>

                <button type="submit" :disabled="spinning"
                    class="w-full rounded-lg bg-indigo-500 px-4 py-3 font-semibold text-white hover:bg-indigo-400 disabled:cursor-not-allowed disabled:opacity-60">
                    <span x-show="!spinning">Spin the wheel</span>
                    <span x-show="spinning" x-cloak>Choosing…</span>
                </button>
            </form>
        </div>

        <p class="mt-4 text-center text-xs text-slate-500">
            The winner is chosen on the server and saved to your history exactly once.
        </p>
    </div>
@endsection