@php
    $classes = match ($status) {
        'success' => 'bg-emerald-900/50 text-emerald-300',
        'failed' => 'bg-red-900/50 text-red-300',
        'running' => 'bg-sky-900/50 text-sky-300',
        default => 'bg-slate-700/60 text-slate-300',
    };
@endphp
<span class="rounded px-2 py-0.5 text-xs font-medium {{ $classes }}">{{ $status }}</span>