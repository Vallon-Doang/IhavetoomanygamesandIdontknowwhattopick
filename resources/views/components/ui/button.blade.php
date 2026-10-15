@props(['href' => null, 'variant' => 'primary', 'type' => 'button', 'size' => 'md'])

@php
    $base = 'inline-flex items-center justify-center gap-2 rounded-xl font-semibold transition duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent-400/70 focus-visible:ring-offset-2 focus-visible:ring-offset-abyss-950 disabled:cursor-not-allowed disabled:opacity-60';
    $sizes = [
        'sm' => 'px-3.5 py-2 text-sm',
        'md' => 'px-5 py-2.5 text-sm',
        'lg' => 'px-6 py-3 text-base',
    ];
    $variants = [
        'primary' => 'bg-accent-500 text-abyss-950 shadow-lg shadow-accent-500/20 hover:bg-accent-400',
        'secondary' => 'bg-white/5 text-slate-100 ring-1 ring-inset ring-white/10 hover:bg-white/10',
        'ghost' => 'text-slate-300 hover:bg-white/5 hover:text-white',
        'success' => 'bg-emerald-500 text-abyss-950 hover:bg-emerald-400',
        'danger' => 'bg-red-500/90 text-white hover:bg-red-500',
    ];
    $classes = $base.' '.$sizes[$size].' '.$variants[$variant];
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif