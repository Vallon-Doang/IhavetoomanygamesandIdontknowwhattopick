<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['steam_app_id', 'name', 'cover_url'])]
class Game extends Model
{
    public function userGames(): HasMany
    {
        return $this->hasMany(UserGame::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_games');
    }
}