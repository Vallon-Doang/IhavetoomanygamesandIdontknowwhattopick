@extends('layouts.admin')

@section('title', 'Edit announcement')

@section('content')
    <div class="mx-auto max-w-2xl">
        <a href="{{ route('admin.announcements.index') }}" class="text-sm text-slate-400 hover:text-white">&larr; Back to announcements</a>

        <h1 class="mt-4 text-2xl font-bold tracking-tight text-white">Edit announcement</h1>

        <div class="mt-6 rounded-2xl border border-white/5 bg-abyss-900/60 p-5">
            <form method="POST" action="{{ route('admin.announcements.update', $announcement) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="title" class="block text-sm font-medium text-slate-200">Title</label>
                    <input type="text" id="title" name="title" value="{{ old('title', $announcement->title) }}" maxlength="150" required
                        class="mt-1.5 w-full rounded-xl border border-white/10 bg-abyss-950/70 px-3.5 py-2.5 text-sm text-slate-100 focus:border-accent-500/60 focus:outline-none focus:ring-2 focus:ring-accent-500/20">
                </div>

                <div>
                    <label for="body" class="block text-sm font-medium text-slate-200">Message</label>
                    <textarea id="body" name="body" rows="5" maxlength="5000" required
                        class="mt-1.5 w-full rounded-xl border border-white/10 bg-abyss-950/70 px-3.5 py-2.5 text-sm text-slate-100 focus:border-accent-500/60 focus:outline-none focus:ring-2 focus:ring-accent-500/20">{{ old('body', $announcement->body) }}</textarea>
                </div>

                <label class="flex items-center gap-2 text-sm text-slate-300">
                    <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $announcement->is_published)) class="rounded border-white/20 bg-abyss-950 text-accent-500 focus:ring-accent-500/40">
                    Published
                </label>

                <div class="flex items-center gap-3">
                    <x-ui.button type="submit" size="sm">Save changes</x-ui.button>
                    <a href="{{ route('admin.announcements.index') }}" class="text-sm text-slate-400 hover:text-white">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
