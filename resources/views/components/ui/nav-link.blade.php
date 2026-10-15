@props(['href', 'active' => false])

<a href="{{ $href }}" @if ($active) aria-current="page" @endif
    @class([
        'relative rounded-lg px-3 py-2 text-sm font-medium transition duration-200',
        'text-white' => $active,
        'text-slate-400 hover:bg-white/5 hover:text-white' => ! $active,
    ])>
    {{ $slot }}
    @if ($active)
        <span class="absolute inset-x-2 -bottom-px h-px bg-gradient-to-r from-transparent via-accent-400 to-transparent"></span>
    @endif
</a>