<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ModeratePostRequest;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PostController extends Controller
{
    /**
     * Moderation queue for community posts.
     */
    public function index(Request $request): View
    {
        $filter = (string) $request->query('filter', 'all');

        if (! in_array($filter, ['all', 'visible', 'hidden'], true)) {
            $filter = 'all';
        }

        $posts = Post::query()
            ->withTrashed()
            ->with(['author', 'hiddenBy'])
            ->when($filter === 'hidden', fn ($query) => $query->where('is_hidden', true))
            ->when($filter === 'visible', fn ($query) => $query->where('is_hidden', false))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.posts', [
            'posts' => $posts,
            'filter' => $filter,
            'hiddenCount' => Post::where('is_hidden', true)->count(),
        ]);
    }

    /**
     * Hide or restore a post for moderation.
     */
    public function moderate(ModeratePostRequest $request, Post $post): RedirectResponse
    {
        if ($request->validated('action') === 'hide') {
            $post->update([
                'is_hidden' => true,
                'hidden_reason' => $request->validated('hidden_reason'),
                'hidden_by' => $request->user()->id,
                'hidden_at' => now(),
            ]);

            return back()->with('status', 'Post hidden from the community feed.');
        }

        $post->update([
            'is_hidden' => false,
            'hidden_reason' => null,
            'hidden_by' => null,
            'hidden_at' => null,
        ]);

        return back()->with('status', 'Post restored.');
    }

    /**
     * Permanently delete a post and its uploaded image.
     */
    public function destroy(Post $post): RedirectResponse
    {
        $this->authorize('delete', $post);

        if ($post->image_path) {
            Storage::disk('public')->delete($post->image_path);
        }

        $post->forceDelete();

        return back()->with('status', 'Post permanently deleted.');
    }
}
