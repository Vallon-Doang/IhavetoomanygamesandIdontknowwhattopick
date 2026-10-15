<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Announcement;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CommunityController extends Controller
{
    /**
     * Display the community feed with the latest published announcements.
     */
    public function index(): View
    {
        $posts = Post::query()
            ->published()
            ->with('author')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $announcements = Announcement::query()
            ->published()
            ->with('author')
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('community.index', [
            'posts' => $posts,
            'announcements' => $announcements,
        ]);
    }

    /**
     * Store a newly created post.
     */
    public function store(StorePostRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('posts', 'public')
            : null;

        $request->user()->posts()->create([
            'body' => $data['body'],
            'image_path' => $imagePath,
        ]);

        return redirect()
            ->route('community.index')
            ->with('status', 'Your post is live in the community feed.');
    }

    /**
     * Show the form for editing the author's own post.
     */
    public function edit(Post $post): View
    {
        $this->authorize('update', $post);

        return view('community.edit', ['post' => $post]);
    }

    /**
     * Update the author's own post.
     */
    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($post->image_path) {
                Storage::disk('public')->delete($post->image_path);
            }
            $data['image_path'] = $request->file('image')->store('posts', 'public');
        }

        $post->update([
            'body' => $data['body'],
            'image_path' => $data['image_path'] ?? $post->image_path,
        ]);

        return redirect()
            ->route('community.index')
            ->with('status', 'Post updated.');
    }

    /**
     * Remove the author's own post (administrators may also moderate).
     */
    public function destroy(Post $post): RedirectResponse
    {
        $this->authorize('delete', $post);

        if ($post->image_path) {
            Storage::disk('public')->delete($post->image_path);
        }

        $post->delete();

        return redirect()
            ->route('community.index')
            ->with('status', 'Post removed.');
    }
}
