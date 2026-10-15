<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Determine whether the user may browse the community feed.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user may view a single post.
     */
    public function view(User $user, Post $post): bool
    {
        return ! $post->is_hidden
            || $user->id === $post->user_id
            || $user->isAdmin();
    }

    /**
     * Determine whether the user may create a post.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Only the author may edit their own post content.
     */
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    /**
     * The author may remove their own post; administrators may moderate any post.
     */
    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id || $user->isAdmin();
    }

    /**
     * Administrators may hide or restore a post for moderation.
     */
    public function moderate(User $user, Post $post): bool
    {
        return $user->isAdmin();
    }

    /**
     * Administrators may restore a soft-deleted post.
     */
    public function restore(User $user, Post $post): bool
    {
        return $user->isAdmin();
    }
}
