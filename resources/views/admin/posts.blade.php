@extends('layouts.admin')

@section('title', 'Moderation')

@section('content')
    @php
        $filters = [
            'all' => 'All',
            'visible' => 'Visible',
            'hidden' => 'Hidden',
        ];
    @endphp

    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Post moderation</h1>
            <p class="mt-1 text-sm text-slate-400">
                {{ $posts->total() }} post{{ $posts->total() === 1 ? '' : 's' }} in view ·
                {{ $hiddenCount }} hidden.
            </p>
        </div>
        <div class="flex gap-1 rounded-xl border border-white/10 bg-abyss-900/60 p-1">
            @foreach ($filters as $key => $label)
                <a href="{{ route('admin.posts.index', ['filter' => $key]) }}"
                    @class([
                        'rounded-lg px-3 py-1.5 text-sm font-medium transition',
                        'bg-accent-500/15 text-accent-300' => $filter === $key,
                        'text-slate-400 hover:text-white' => $filter !== $key,
                    ])>{{ $label }}</a>
            @endforeach
        </div>
    </div>

    <div class="mt-6 overflow-x-auto rounded-2xl border border-white/5">
        <table class="w-full text-left text-sm">
            <thead class="bg-abyss-900/80 text-xs uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-4 py-3 font-medium">Author</th>
                    <th class="px-4 py-3 font-medium">Post</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3 font-medium">Posted</th>
                    <th class="px-4 py-3 text-right font-medium">Moderation</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse ($posts as $post)
                    <tr class="bg-abyss-900/40 align-top transition hover:bg-abyss-850/60">
                        <td class="px-4 py-3">
                            <p class="font-medium text-slate-100">{{ $post->author?->name ?? 'Unknown' }}</p>
                            <p class="text-xs text-slate-500">{{ $post->author?->email }}</p>
                        </td>
                        <td class="max-w-md px-4 py-3">
                            <p class="line-clamp-3 text-slate-300">{{ $post->body }}</p>
                            @if ($post->image_path)
                                <x-ui.badge tone="slate" class="mt-2">image</x-ui.badge>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if ($post->trashed())
                                <x-ui.badge tone="red">Deleted</x-ui.badge>
                            @elseif ($post->is_hidden)
                                <x-ui.badge tone="amber">Hidden</x-ui.badge>
                                @if ($post->hidden_reason)
                                    <p class="mt-1 text-xs text-slate-500">{{ $post->hidden_reason }}</p>
                                @endif
                                @if ($post->hiddenBy)
                                    <p class="mt-0.5 text-xs text-slate-600">by {{ $post->hiddenBy->name }}</p>
                                @endif
                            @else
                                <x-ui.badge tone="emerald">Visible</x-ui.badge>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-slate-400">{{ $post->created_at->format('M j, Y') }}</td>
                        <td class="px-4 py-3">
                            <div class="flex flex-col items-end gap-2">
                                @if (! $post->trashed())
                                    <form method="POST" action="{{ route('admin.posts.moderate', $post) }}" class="flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        @if ($post->is_hidden)
                                            <input type="hidden" name="action" value="restore">
                                            <x-ui.button type="submit" variant="secondary" size="sm">Restore</x-ui.button>
                                        @else
                                            <input type="hidden" name="action" value="hide">
                                            <input type="text" name="hidden_reason" maxlength="255" placeholder="Reason (optional)"
                                                class="w-40 rounded-lg border border-white/10 bg-abyss-950/70 px-2.5 py-1.5 text-xs text-slate-100 placeholder:text-slate-500 focus:border-accent-500/60 focus:outline-none">
                                            <x-ui.button type="submit" variant="secondary" size="sm">Hide</x-ui.button>
                                        @endif
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('admin.posts.destroy', $post) }}"
                                    x-data @submit.prevent="if (confirm('Permanently delete this post?')) $el.submit()">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-medium text-red-300 hover:text-red-200">Delete permanently</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-10 text-center text-slate-500">No posts match this filter.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $posts->links() }}</div>
@endsection
