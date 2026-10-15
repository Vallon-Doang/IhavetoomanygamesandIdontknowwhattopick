@extends('layouts.app')

@section('title', 'Roulette')

@section('content')
    @php
        $picked = session('picked');
        $appliedFilters = $picked['filters'] ?? [];
        $steamAppId = $picked['steam_app_id'] ?? null;
        $rouletteConfig = [
            'picked' => $picked,
        ];
    @endphp

    <div class="mx-auto max-w-5xl" x-data="rouletteRig(@js($rouletteConfig))" x-init="boot()">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-white sm:text-3xl">GameSpin roulette</h1>
                <p class="mt-1 text-sm text-slate-400">
                    {{ $eligibleCount }} eligible game{{ $eligibleCount === 1 ? '' : 's' }} in your library right now.
                </p>
            </div>
            <x-ui.button :href="route('library.index')" variant="secondary">Manage library</x-ui.button>
        </div>

        {{-- Roulette reel --}}
        <section x-show="phase !== 'idle'" x-cloak
            class="relative mt-6 overflow-hidden rounded-3xl border border-white/5 bg-abyss-900/60">
            <div class="pointer-events-none absolute -top-24 left-1/2 h-48 w-[36rem] -translate-x-1/2 rounded-full bg-accent-500/15 blur-3xl"></div>

            <div class="relative">
                <div x-ref="viewport" class="relative overflow-hidden py-6">
                    <div x-ref="strip" class="flex gap-3 will-change-transform">
                        <template x-for="(item, index) in reel" :key="index">
                            <div class="cover-frame relative h-24 w-32 flex-none overflow-hidden rounded-xl border bg-abyss-800 transition duration-300"
                                :class="phase === 'result' && index === winnerIndex
                                    ? 'gamespin-win scale-105 border-accent-400 ring-2 ring-accent-400'
                                    : 'border-white/5 opacity-90'">
                                <div class="absolute inset-0 grid place-items-center text-2xl font-bold text-white/20"
                                    x-text="initial(item.name)"></div>
                                <template x-if="item.cover">
                                    <img :src="item.cover" alt="" loading="lazy"
                                        class="relative h-full w-full object-cover" x-on:error="$el.remove()">
                                </template>
                                <div class="absolute inset-x-0 bottom-0 truncate bg-gradient-to-t from-abyss-950/95 to-transparent px-2 pb-1 pt-4 text-[10px] font-medium text-slate-200"
                                    x-text="item.name"></div>
                            </div>
                        </template>
                    </div>

                    <div class="pointer-events-none absolute inset-y-0 left-0 z-10 w-16 bg-gradient-to-r from-abyss-900 to-transparent"></div>
                    <div class="pointer-events-none absolute inset-y-0 right-0 z-10 w-16 bg-gradient-to-l from-abyss-900 to-transparent"></div>
                </div>

                {{-- Center marker --}}
                <div class="pointer-events-none absolute inset-y-0 left-1/2 z-20 -translate-x-1/2">
                    <div class="h-full w-px bg-gradient-to-b from-transparent via-accent-300 to-transparent shadow-[0_0_18px_4px_rgba(56,189,248,0.55)]"></div>
                </div>
                <div class="pointer-events-none absolute -top-0.5 left-1/2 z-20 -translate-x-1/2">
                    <div class="h-0 w-0 border-x-8 border-t-8 border-x-transparent border-t-accent-400"></div>
                </div>
            </div>

            <p class="border-t border-white/5 px-5 py-3 text-center text-xs text-slate-400" x-show="phase === 'spinning'" x-cloak>
                Choosing your game…
            </p>
        </section>

        {{-- Result panel --}}
        @if ($picked)
            <section x-show="showResult" x-cloak
                x-transition:enter="transition ease-out duration-500"
                x-transition:enter-start="opacity-0 translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="relative mt-6 overflow-hidden rounded-3xl border border-accent-500/30 bg-gradient-to-br from-abyss-900 to-abyss-950">
                <div class="pointer-events-none absolute -right-16 -top-16 h-56 w-56 rounded-full bg-accent-500/20 blur-3xl"></div>

                <div class="relative flex flex-col items-center gap-6 p-6 sm:flex-row sm:p-8">
                    <x-game-cover :name="$picked['name']" :cover="$picked['cover'] ?? null"
                        class="h-28 w-48 flex-none rounded-xl shadow-lg ring-1 ring-accent-500/40" />

                    <div class="min-w-0 flex-1 text-center sm:text-left">
                        <p class="text-xs font-semibold uppercase tracking-[0.25em] text-accent-400">Winner</p>
                        <h2 class="mt-1.5 truncate text-3xl font-extrabold tracking-tight text-white sm:text-4xl" title="{{ $picked['name'] }}">
                            {{ $picked['name'] }}
                        </h2>

                        @if (! empty($appliedFilters))
                            <div class="mt-3 flex flex-wrap justify-center gap-2 sm:justify-start">
                                @foreach ($appliedFilters as $key => $value)
                                    <x-ui.badge>{{ str_replace('_', ' ', $key) }}: {{ $value }}</x-ui.badge>
                                @endforeach
                            </div>
                        @endif

                        <div class="mt-6 flex flex-wrap justify-center gap-3 sm:justify-start">
                            @if ($steamAppId)
                                <x-ui.button :href="'steam://run/'.$steamAppId" variant="success">
                                    <svg viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4" aria-hidden="true">
                                        <path d="M11.98 2C5.9 2 1 6.9 1 12.98c0 5.03 3.55 9.24 8.3 10.32l2.9-4.3c.55.24 1.16.38 1.8.38 2.48 0 4.5-2.02 4.5-4.5s-2.02-4.5-4.5-4.5c-.66 0-1.28.15-1.84.4L8.2 13.4c.05-.3.1-.6.1-.92 0-2.9 2.36-5.26 5.26-5.26s5.26 2.36 5.26 5.26-2.36 5.26-5.26 5.26h-.1l-1.6 2.36c.9.2 1.83.3 2.78.3 4.42 0 8-3.58 8-8s-3.58-8-8-8Zm-5.3 14.9 1.6-2.42a3.3 3.3 0 0 1-1.9-2.98c0-1.83 1.49-3.32 3.32-3.32l2.1-1.42c-.3-.1-.62-.16-.95-.16-2.9 0-5.26 2.36-5.26 5.26 0 .8.18 1.56.5 2.24l.6.8Z"/>
                                    </svg>
                                    Open in Steam
                                </x-ui.button>
                                <x-ui.button :href="'https://store.steampowered.com/app/'.$steamAppId.'/'" variant="secondary" target="_blank" rel="noopener">
                                    Steam page
                                </x-ui.button>
                            @endif
                            <x-ui.button type="button" x-on:click="spinAgain()">Spin Again</x-ui.button>
                            <x-ui.button :href="route('library.index')" variant="secondary">View Library</x-ui.button>
                            <x-ui.button :href="route('history.index')" variant="ghost">View History</x-ui.button>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        {{-- Filters --}}
        <form id="spin-form" method="POST" action="{{ route('picker.spin') }}"
            x-show="phase !== 'spinning'"
            class="mt-6 space-y-5 rounded-2xl border border-white/5 bg-abyss-900/60 p-6 sm:p-7"
            x-on:submit="phase = 'spinning'">
            @csrf

            <div>
                <label for="search" class="block text-sm font-medium text-slate-300">Title contains (optional)</label>
                <input id="search" name="search" type="text" value="{{ old('search', $appliedFilters['search'] ?? '') }}"
                    placeholder="e.g. Portal"
                    class="mt-1.5 w-full rounded-xl border border-white/10 bg-abyss-950/70 px-3.5 py-2.5 text-slate-100 placeholder:text-slate-500 transition focus:border-accent-500/60 focus:outline-none focus:ring-2 focus:ring-accent-500/20">
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="min_playtime" class="block text-sm font-medium text-slate-300">Min playtime (minutes)</label>
                    <input id="min_playtime" name="min_playtime" type="number" min="0"
                        value="{{ old('min_playtime', $appliedFilters['min_playtime'] ?? '') }}"
                        class="mt-1.5 w-full rounded-xl border border-white/10 bg-abyss-950/70 px-3.5 py-2.5 text-slate-100 transition focus:border-accent-500/60 focus:outline-none focus:ring-2 focus:ring-accent-500/20">
                </div>
                <div>
                    <label for="max_playtime" class="block text-sm font-medium text-slate-300">Max playtime (minutes)</label>
                    <input id="max_playtime" name="max_playtime" type="number" min="0"
                        value="{{ old('max_playtime', $appliedFilters['max_playtime'] ?? '') }}"
                        class="mt-1.5 w-full rounded-xl border border-white/10 bg-abyss-950/70 px-3.5 py-2.5 text-slate-100 transition focus:border-accent-500/60 focus:outline-none focus:ring-2 focus:ring-accent-500/20">
                </div>
            </div>

            <button type="submit" :disabled="phase === 'spinning'"
                class="w-full rounded-xl bg-accent-500 px-4 py-3 font-semibold text-abyss-950 shadow-lg shadow-accent-500/20 transition hover:bg-accent-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent-400/70 focus-visible:ring-offset-2 focus-visible:ring-offset-abyss-950 disabled:cursor-not-allowed disabled:opacity-60">
                Spin the wheel
            </button>
        </form>

        <p class="mt-4 text-center text-xs text-slate-500">
            The winner is chosen on the server and saved to your history exactly once.
        </p>
    </div>
@endsection

@push('scripts')
    <script>
        window.rouletteRig = function (config) {
            const picked = config.picked || null;
            const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            return {
                phase: picked ? 'ready' : 'idle',
                reel: picked ? picked.reel : [],
                winnerIndex: picked ? picked.winner_index : 0,
                result: picked,
                showResult: false,
                duration: reduceMotion ? 0 : 6200,

                boot() {
                    if (!picked || !this.reel.length) {
                        return;
                    }
                    this.$nextTick(() => {
                        window.setTimeout(() => this.runReel(), reduceMotion ? 0 : 200);
                    });
                },

                initial(name) {
                    return (name || '?').trim().charAt(0).toUpperCase();
                },

                runReel() {
                    if (this.phase === 'spinning') {
                        return;
                    }

                    this.phase = 'spinning';
                    this.showResult = false;

                    const strip = this.$refs.strip;
                    const viewport = this.$refs.viewport;
                    if (!strip || !viewport) {
                        return;
                    }

                    const card = strip.children[this.winnerIndex] || strip.children[0];
                    const cardWidth = card ? card.getBoundingClientRect().width : 128;
                    const computed = getComputedStyle(strip);
                    const gap = parseFloat(computed.columnGap || computed.gap) || 12;
                    const step = cardWidth + gap;
                    const viewportWidth = viewport.clientWidth;

                    // Keep the random stop point inside the winning card so it lands exactly on it.
                    const jitter = (Math.random() - 0.5) * (cardWidth * 0.5);
                    const target = (this.winnerIndex * step) + (cardWidth / 2) - (viewportWidth / 2) + jitter;

                    if (this.duration === 0) {
                        strip.style.transition = 'none';
                        strip.style.transform = `translateX(${-target}px)`;
                        this.phase = 'result';
                        this.showResult = true;
                        return;
                    }

                    strip.style.transition = 'none';
                    strip.style.transform = 'translateX(0px)';
                    void strip.offsetWidth; // force reflow so the reset is committed

                    strip.style.transition = `transform ${this.duration}ms cubic-bezier(0.08, 0.82, 0.17, 1)`;

                    requestAnimationFrame(() => {
                        strip.style.transform = `translateX(${-target}px)`;
                    });

                    window.setTimeout(() => {
                        this.phase = 'result';
                        this.showResult = true;
                    }, this.duration + 150);
                },

                spinAgain() {
                    this.showResult = false;
                    const form = document.getElementById('spin-form');
                    if (form) {
                        form.requestSubmit();
                    }
                },
            };
        };
    </script>
@endpush