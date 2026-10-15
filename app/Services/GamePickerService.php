<?php

namespace App\Services;

use App\Models\SpinHistory;
use App\Models\User;
use App\Models\UserGame;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class GamePickerService
{
    public const REEL_LENGTH = 60;

    public const REEL_WINNER_INDEX = 55;

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

    /**
     * All eligible catalog games for the member, used to populate the reel.
     *
     * @param  array{search?: string|null, min_playtime?: int|null, max_playtime?: int|null}  $filters
     * @return Collection<int, \App\Models\Game>
     */
    public function eligibleGames(User $user, array $filters = []): Collection
    {
        return $this->eligibleQuery($user, $filters)
            ->with('game')
            ->get()
            ->pluck('game')
            ->filter()
            ->values();
    }

    /**
     * Build the roulette reel: a random strip of covers with the server-chosen
     * winner placed at a fixed index so the animation can land on it exactly.
     *
     * @param  Collection<int, \App\Models\Game>  $games
     * @return array{items: array<int, array{id: int, name: string, cover: string|null}>, winner_index: int}
     */
    public function buildReel(Collection $games, int $winnerId, string $winnerName, ?string $winnerCover): array
    {
        $pool = $games->map(fn ($game) => [
            'id' => $game->id,
            'name' => $game->name,
            'cover' => $game->cover_url,
        ])->all();

        if ($pool === []) {
            $pool = [['id' => $winnerId, 'name' => $winnerName, 'cover' => $winnerCover]];
        }

        $items = [];
        for ($i = 0; $i < self::REEL_LENGTH; $i++) {
            $items[] = $pool[array_rand($pool)];
        }

        $items[self::REEL_WINNER_INDEX] = [
            'id' => $winnerId,
            'name' => $winnerName,
            'cover' => $winnerCover,
        ];

        return [
            'items' => $items,
            'winner_index' => self::REEL_WINNER_INDEX,
        ];
    }
}