@extends('layouts.app')

@section('title', 'Edit post')

@section('content')
    <div class="mx-auto max-w-2xl">
        <a href="{{ route('community.index') }}" class="text-sm text-slate-400 hover:text-white">&larr; Back to community</a>

        <h1 class="mt-4 text-2xl font-bold tracking-tight text-white">Edit post</h1>
        <p class="mt-1 text-sm text-slate-400">Only you can edit your own post. Administrators may hide posts but not rewrite them.</p>

        <div class="mt-6 rounded-2xl border border-white/5 bg-abyss-900/60 p-5"
            data-body="{{ old('body', $post->body) }}"
            x-data="{
                body: $el.dataset.body || '',
                preview: null,
                submitting: false,
                pick(event) {
                    const file = event.target.files[0];
                    this.preview = file ? URL.createObjectURL(file) : null;
                },
            }">
            <form method="POST" action="{{ route('community.posts.update', $post) }}" enctype="multipart/form-data"
                @submit="submitting = true">
                @csrf
                @method('PUT')

                <label for="body" class="block text-sm font-medium text-slate-200">Post content</label>
                <textarea id="body" name="body" rows="4" maxlength="2000" x-model="body" required
                    class="mt-2 w-full rounded-xl border border-white/10 bg-abyss-950/70 px-3.5 py-2.5 text-sm text-slate-100 transition focus:border-accent-500/60 focus:outline-none focus:ring-2 focus:ring-accent-500/20"></textarea>
                <div class="mt-1 text-right text-xs text-slate-500"><span x-text="body.length">0</span> / 2000</div>

                @if ($post->image_path)
                    <div class="mt-3 flex items-center gap-3 rounded-xl border border-white/5 bg-abyss-950/50 p-3">
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($post->image_path) }}" alt="Current image" class="h-16 w-24 rounded-lg object-cover">
                        <p class="text-xs text-slate-400">Uploading a new image replaces this one.</p>
                    </div>
                @endif

                <div class="mt-3 flex flex-wrap items-center gap-3">
                    <input type="file" name="image" accept="image/png,image/jpeg,image/webp" @change="pick"
                        class="block w-full max-w-xs text-sm text-slate-400 file:mr-3 file:rounded-lg file:border-0 file:bg-white/5 file:px-3 file:py-2 file:text-sm file:font-medium file:text-slate-200 hover:file:bg-white/10">
                    <x-ui.button type="submit" size="sm" class="ml-auto" x-bind:disabled="submitting">Save changes</x-ui.button>
                </div>

                <template x-if="preview">
                    <div class="mt-3 overflow-hidden rounded-xl border border-white/5">
                        <img :src="preview" alt="New image preview" class="max-h-64 w-full object-cover">
                    </div>
                </template>
            </form>
        </div>
    </div>
@endsection
