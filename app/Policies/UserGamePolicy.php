<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserGame;

class UserGamePolicy
{
    /**
     * Determine whether the user may view the ownership record.
     */
    public function view(User $user, UserGame $userGame): bool
    {
        return $user->id === $userGame->user_id;
    }

    /**
     * Determine whether the user may update the ownership record.
     */
    public function update(User $user, UserGame $userGame): bool
    {
        return $user->id === $userGame->user_id;
    }
}