@extends('layouts.admin')

@section('title', 'Announcements')

@section('content')
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-white">Announcements</h1>
        <p class="mt-1 text-sm text-slate-400">Publish short updates. Published announcements appear at the top of the community feed.</p>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.4fr)]">
        <div class="rounded-2xl border border-white/5 bg-abyss-900/60 p-5">
            <h2 class="text-sm font-semibold text-white">New announcement</h2>
            <form method="POST" action="{{ route('admin.announcements.store') }}" class="mt-4 space-y-4">
                @csrf

                <div>
                    <label for="title" class="block text-sm font-medium text-slate-200">Title</label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" maxlength="150" required
                        class="mt-1.5 w-full rounded-xl border border-white/10 bg-abyss-950/70 px-3.5 py-2.5 text-sm text-slate-100 placeholder:text-slate-500 focus:border-accent-500/60 focus:outline-none focus:ring-2 focus:ring-accent-500/20">
                </div>

                <div>
                    <label for="body" class="block text-sm font-medium text-slate-200">Message</label>
                    <textarea id="body" name="body" rows="4" maxlength="5000" required
                        class="mt-1.5 w-full rounded-xl border border-white/10 bg-abyss-950/70 px-3.5 py-2.5 text-sm text-slate-100 placeholder:text-slate-500 focus:border-accent-500/60 focus:outline-none focus:ring-2 focus:ring-accent-500/20">{{ old('body') }}</textarea>
                </div>

                <label class="flex items-center gap-2 text-sm text-slate-300">
                    <input type="checkbox" name="is_published" value="1" @checked(old('is_published')) class="rounded border-white/20 bg-abyss-950 text-accent-500 focus:ring-accent-500/40">
                    Publish immediately
                </label>

                <x-ui.button type="submit" size="sm">Create announcement</x-ui.button>
            </form>
        </div>

        <div>
            <div class="overflow-hidden rounded-2xl border border-white/5">
                <table class="w-full text-left text-sm">
                    <thead class="bg-abyss-900/80 text-xs uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-4 py-3 font-medium">Announcement</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 text-right font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse ($announcements as $announcement)
                            <tr class="bg-abyss-900/40 align-top transition hover:bg-abyss-850/60">
                                <td class="px-4 py-3">
                                    <p class="font-medium text-slate-100">{{ $announcement->title }}</p>
                                    <p class="mt-0.5 line-clamp-2 text-xs text-slate-500">{{ $announcement->body }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    @if ($announcement->is_published)
                                        <x-ui.badge tone="emerald">Published</x-ui.badge>
                                        <p class="mt-1 text-xs text-slate-500">{{ $announcement->published_at?->format('M j, Y') }}</p>
                                    @else
                                        <x-ui.badge tone="slate">Draft</x-ui.badge>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('admin.announcements.edit', $announcement) }}" class="text-xs font-medium text-accent-300 hover:text-accent-200">Edit</a>
                                        <form method="POST" action="{{ route('admin.announcements.destroy', $announcement) }}"
                                            x-data @submit.prevent="if (confirm('Delete this announcement?')) $el.submit()">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-medium text-red-300 hover:text-red-200">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-4 py-10 text-center text-slate-500">No announcements yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">{{ $announcements->links() }}</div>
        </div>
    </div>
@endsection
