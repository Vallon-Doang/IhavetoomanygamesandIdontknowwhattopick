@props(['label', 'value', 'hint' => null])

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-white/5 bg-abyss-900/60 p-5 transition duration-200 hover:border-accent-500/30 hover:bg-abyss-850/70']) }}>
    <p class="text-xs font-medium uppercase tracking-wider text-slate-400">{{ $label }}</p>
    <p class="mt-2 text-3xl font-semibold tracking-tight text-white">{{ $value }}</p>
    @if ($hint)
        <p class="mt-1 text-xs text-slate-500">{{ $hint }}</p>
    @endif
</div>