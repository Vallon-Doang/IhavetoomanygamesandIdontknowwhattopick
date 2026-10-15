<?php

namespace App\Policies;

use App\Models\Announcement;
use App\Models\User;

class AnnouncementPolicy
{
    /**
     * Determine whether the user may view the announcement index.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user may view a single announcement.
     */
    public function view(User $user, Announcement $announcement): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user may create announcements.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user may update announcements.
     */
    public function update(User $user, Announcement $announcement): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user may delete announcements.
     */
    public function delete(User $user, Announcement $announcement): bool
    {
        return $user->isAdmin();
    }
}
