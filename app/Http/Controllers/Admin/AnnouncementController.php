<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAnnouncementRequest;
use App\Http\Requests\UpdateAnnouncementRequest;
use App\Models\Announcement;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    /**
     * List every announcement, published or draft.
     */
    public function index(): View
    {
        return view('admin.announcements', [
            'announcements' => Announcement::query()
                ->with('author')
                ->latest()
                ->paginate(20),
        ]);
    }

    /**
     * Store a new announcement.
     */
    public function store(StoreAnnouncementRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $published = $request->boolean('is_published');

        $request->user()->announcements()->create([
            'title' => $data['title'],
            'body' => $data['body'],
            'is_published' => $published,
            'published_at' => $published ? now() : null,
        ]);

        return back()->with('status', 'Announcement created.');
    }

    /**
     * Show the form for editing an announcement.
     */
    public function edit(Announcement $announcement): View
    {
        $this->authorize('update', $announcement);

        return view('admin.announcements-edit', ['announcement' => $announcement]);
    }

    /**
     * Update an announcement.
     */
    public function update(UpdateAnnouncementRequest $request, Announcement $announcement): RedirectResponse
    {
        $data = $request->validated();
        $published = $request->boolean('is_published');

        $announcement->update([
            'title' => $data['title'],
            'body' => $data['body'],
            'is_published' => $published,
            'published_at' => $published ? ($announcement->published_at ?? now()) : null,
        ]);

        return redirect()
            ->route('admin.announcements.index')
            ->with('status', 'Announcement updated.');
    }

    /**
     * Delete an announcement.
     */
    public function destroy(Announcement $announcement): RedirectResponse
    {
        $this->authorize('delete', $announcement);

        $announcement->delete();

        return back()->with('status', 'Announcement deleted.');
    }
}
