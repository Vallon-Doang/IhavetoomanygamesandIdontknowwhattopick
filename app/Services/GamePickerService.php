<?php

namespace App\Services;

use App\Models\SpinHistory;
use App\Models\User;
use App\Models\UserGame;
use Illuminate\Database\Eloquent\Builder;

class GamePickerService
{
    /**
     * Build the query of games eligible for a pick.
     *
     * @param  array{search?: string|null, min_playtime?: int|null, max_playtime?: int|null}  $filters
     */
    public function eligibleQuery(User $user, array $filters = []): Builder
    {
        return UserGame::query()
            ->where('user_id', $user->id)
            ->where('is_excluded', false)
            ->when(filled($filters['search'] ?? null), function (Builder $query) use ($filters): void {
                $search = '%'.str_replace(['%', '_'], ['\%', '\_'], $filters['search']).'%';
                $query->whereHas('game', fn (Builder $q) => $q->where('name', 'like', $search));
            })
            ->when(filled($filters['min_playtime'] ?? null), function (Builder $query) use ($filters): void {
                $query->where('playtime_minutes', '>=', (int) $filters['min_playtime']);
            })
            ->when(filled($filters['max_playtime'] ?? null), function (Builder $query) use ($filters): void {
                $query->where('playtime_minutes', '<=', (int) $filters['max_playtime']);
            });
    }

    /**
     * Pick a random eligible game and record it once in history.
     *
     * @param  array{search?: string|null, min_playtime?: int|null, max_playtime?: int|null}  $filters
     */
    public function pick(User $user, array $filters = []): ?SpinHistory
    {
        $winner = $this->eligibleQuery($user, $filters)
            ->with('game')
            ->inRandomOrder()
            ->first();

        if (! $winner) {
            return null;
        }

        return SpinHistory::create([
            'user_id' => $user->id,
            'game_id' => $winner->game_id,
            'game_name' => $winner->game->name,
            'filters' => $this->filterSnapshot($filters),
        ]);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>|null
     */
    protected function filterSnapshot(array $filters): ?array
    {
        $snapshot = array_filter([
            'search' => filled($filters['search'] ?? null) ? $filters['search'] : null,
            'min_playtime' => filled($filters['min_playtime'] ?? null) ? (int) $filters['min_playtime'] : null,
            'max_playtime' => filled($filters['max_playtime'] ?? null) ? (int) $filters['max_playtime'] : null,
        ]);

        return $snapshot ?: null;
    }
}