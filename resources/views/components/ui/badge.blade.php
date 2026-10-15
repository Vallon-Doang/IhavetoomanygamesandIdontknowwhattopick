@props(['tone' => 'slate'])

@php
    $tones = [
        'slate' => 'bg-white/5 text-slate-300 ring-white/10',
        'cyan' => 'bg-accent-500/15 text-accent-300 ring-accent-500/30',
        'sky' => 'bg-sky-500/15 text-sky-300 ring-sky-500/30',
        'emerald' => 'bg-emerald-500/15 text-emerald-300 ring-emerald-500/30',
        'amber' => 'bg-amber-500/15 text-amber-300 ring-amber-500/30',
        'red' => 'bg-red-500/15 text-red-300 ring-red-500/30',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] font-medium ring-1 ring-inset '.$tones[$tone]]) }}>
    {{ $slot }}
</span>