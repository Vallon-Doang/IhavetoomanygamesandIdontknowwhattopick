@extends('layouts.app')

@section('title', 'Community')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-white">Community</h1>
                <p class="mt-1 text-sm text-slate-400">Share what you're playing and see what other members are up to.</p>
            </div>
            <x-ui.badge tone="cyan">{{ $posts->total() }} post{{ $posts->total() === 1 ? '' : 's' }}</x-ui.badge>
        </div>

        @if ($announcements->isNotEmpty())
            <div class="mt-6 space-y-3">
                @foreach ($announcements as $announcement)
                    <div class="rounded-2xl border border-accent-500/25 bg-accent-500/5 p-4">
                        <div class="flex items-center gap-2">
                            <x-ui.badge tone="cyan">Announcement</x-ui.badge>
                            <span class="text-xs text-slate-500">{{ $announcement->published_at?->diffForHumans() }}</span>
                        </div>
                        <h2 class="mt-2 text-sm font-semibold text-white">{{ $announcement->title }}</h2>
                        <p class="mt-1 whitespace-pre-line text-sm text-slate-300">{{ $announcement->body }}</p>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="mt-6 rounded-2xl border border-white/5 bg-abyss-900/60 p-5"
            x-data="{
                body: '',
                preview: null,
                submitting: false,
                pick(event) {
                    const file = event.target.files[0];
                    this.preview = file ? URL.createObjectURL(file) : null;
                },
            }">
            <form method="POST" action="{{ route('community.posts.store') }}" enctype="multipart/form-data"
                @submit="submitting = true">
                @csrf

                <label for="body" class="block text-sm font-medium text-slate-200">What are you playing?</label>
                <textarea id="body" name="body" rows="3" maxlength="2000" x-model="body" required
                    placeholder="Tell the community about a game you love…"
                    class="mt-2 w-full rounded-xl border border-white/10 bg-abyss-950/70 px-3.5 py-2.5 text-sm text-slate-100 placeholder:text-slate-500 transition focus:border-accent-500/60 focus:outline-none focus:ring-2 focus:ring-accent-500/20"></textarea>
                <div class="mt-1 flex items-center justify-between text-xs text-slate-500">
                    <span>Images: JPG, PNG or WebP up to 2 MB.</span>
                    <span><span x-text="body.length">0</span> / 2000</span>
                </div>

                <div class="mt-3 flex flex-wrap items-center gap-3">
                    <input type="file" name="image" accept="image/png,image/jpeg,image/webp" @change="pick"
                        class="block w-full max-w-xs text-sm text-slate-400 file:mr-3 file:rounded-lg file:border-0 file:bg-white/5 file:px-3 file:py-2 file:text-sm file:font-medium file:text-slate-200 hover:file:bg-white/10">
                    <x-ui.button type="submit" size="sm" class="ml-auto" x-bind:disabled="submitting">
                        <span x-show="!submitting">Post</span>
                        <span x-show="submitting" x-cloak>Posting…</span>
                    </x-ui.button>
                </div>

                <template x-if="preview">
                    <div class="mt-3 overflow-hidden rounded-xl border border-white/5">
                        <img :src="preview" alt="Selected image preview" class="max-h-64 w-full object-cover">
                    </div>
                </template>
            </form>
        </div>

        <div class="mt-6 space-y-4">
            @forelse ($posts as $post)
                <x-post-card :post="$post" />
            @empty
                <div class="rounded-2xl border border-white/5 bg-abyss-900/40 p-10 text-center">
                    <p class="text-sm text-slate-400">No posts yet. Be the first to share what you're playing.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">{{ $posts->links() }}</div>
    </div>
@endsection
