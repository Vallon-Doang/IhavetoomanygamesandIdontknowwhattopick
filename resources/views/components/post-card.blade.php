@props(['post'])

@php
    $author = $post->author;
    $canEdit = auth()->check() && auth()->id() === $post->user_id;
    $canDelete = auth()->check() && auth()->user()->can('delete', $post);
    $imageUrl = $post->image_path ? \Illuminate\Support\Facades\Storage::disk('public')->url($post->image_path) : null;
@endphp

<article class="rounded-2xl border border-white/5 bg-abyss-900/60 p-5 transition duration-200 hover:border-accent-500/20">
    <header class="flex items-center gap-3">
        <span class="grid h-10 w-10 flex-none place-items-center rounded-full bg-gradient-to-br from-accent-400 to-accent-600 text-sm font-bold text-abyss-950">
            {{ mb_strtoupper(mb_substr($author?->name ?? '?', 0, 1)) }}
        </span>
        <div class="min-w-0 flex-1">
            <p class="truncate text-sm font-semibold text-white">{{ $author?->name ?? 'Unknown member' }}</p>
            <p class="text-xs text-slate-500">{{ $post->created_at->diffForHumans() }}</p>
        </div>

        @if ($post->is_hidden)
            <x-ui.badge tone="red">Hidden</x-ui.badge>
        @endif
    </header>

    <p class="mt-4 whitespace-pre-line break-words text-sm leading-relaxed text-slate-200">{{ $post->body }}</p>

    @if ($imageUrl)
        <div class="mt-4 overflow-hidden rounded-xl border border-white/5">
            <img src="{{ $imageUrl }}" alt="Image attached to {{ $author?->name }}'s post" loading="lazy" class="max-h-96 w-full object-cover">
        </div>
    @endif

    <footer class="mt-4 flex items-center gap-3">
        @if ($canEdit)
            <a href="{{ route('community.posts.edit', $post) }}" class="text-xs font-medium text-accent-300 hover:text-accent-200">Edit</a>
        @endif

        @if ($canDelete)
            <form method="POST" action="{{ route('community.posts.destroy', $post) }}"
                x-data @submit.prevent="if (confirm('Remove this post?')) $el.submit()">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-xs font-medium text-red-300 hover:text-red-200">Delete</button>
            </form>
        @endif

        @if ($post->is_hidden && $post->hidden_reason)
            <span class="ml-auto text-xs text-slate-500">Reason: {{ $post->hidden_reason }}</span>
        @endif
    </footer>
</article>
