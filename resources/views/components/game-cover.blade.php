@props(['name', 'cover' => null])

<div {{ $attributes->merge(['class' => 'cover-frame relative overflow-hidden bg-abyss-800']) }}>
    <div class="absolute inset-0 grid place-items-center bg-gradient-to-br from-abyss-700 to-abyss-900 text-2xl font-bold text-white/20"
        aria-hidden="true">
        {{ mb_strtoupper(mb_substr($name, 0, 1)) }}
    </div>

    @if ($cover)
        <img src="{{ $cover }}" alt="" loading="lazy" class="relative h-full w-full object-cover" onerror="this.remove()">
    @endif

    {{ $slot }}
</div>