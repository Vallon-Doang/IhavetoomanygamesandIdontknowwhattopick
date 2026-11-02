<?php

namespace App\Services;

use App\Exceptions\SteamApiException;
use App\Exceptions\SynchronizationInProgressException;
use App\Models\Game;
use App\Models\SteamAccount;
use App\Models\SteamSyncRun;
use App\Models\User;
use App\Models\UserGame;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Throwable;

class SteamLibrarySyncService
{
    /** Lock TTL sized to cover a full sync plus a safety margin. */
    public const RUN_LOCK_TTL = 300;

    /** Runs older than this are treated as dead and closed automatically. */
    public const RUN_MAX_AGE_MINUTES = 15;

    public function __construct(private readonly SteamService $steam) {}

    /**
     * Synchronize a member's accessible Steam library.
     *
     * Existing library entries are never deleted here; an incomplete or failed
     * sync preserves the last valid local library.
     */
    public function sync(User $user, SteamAccount $account): SteamSyncRun
    {
        $this->reapStaleRuns($user);

        $lock = Cache::lock("steam-sync:{$user->id}", self::RUN_LOCK_TTL);

        if (! $lock->get()) {
            throw new SynchronizationInProgressException;
        }

        $run = SteamSyncRun::create([
            'user_id' => $user->id,
            'status' => SteamSyncRun::STATUS_RUNNING,
            'started_at' => now(),
        ]);

        try {
            $games = $this->steam->getOwnedGames($account->steam_id);

            if (is_null($games)) {
                throw new SteamApiException('The Steam library is private or unavailable. Set your game details to public and retry.');
            }

            $imported = $this->importGames($user, $games);

            $account->update(['last_synced_at' => now()]);
            $this->refreshProfile($user, $account);

            $run->update([
                'status' => SteamSyncRun::STATUS_SUCCESS,
                'games_imported' => $imported,
                'finished_at' => now(),
            ]);
        } catch (Throwable $e) {
            $run->update([
                'status' => SteamSyncRun::STATUS_FAILED,
                'error_message' => Str::limit($e->getMessage(), 500),
                'finished_at' => now(),
            ]);
        } finally {
            $lock->release();
        }

        return $run->refresh();
    }

    /**
     * Import a Steam library in bulk (one catalog upsert + one ownership
     * upsert) instead of two queries per game, so large libraries finish
     * well inside a serverless execution window.
     *
     * Existing library entries are never deleted here; a failed or private
     * sync preserves the last valid local library.
     *
     * @param  array<int, array<string, mixed>>  $games
     * @return int Number of games imported (deduplicated by app ID).
     */
    private function importGames(User $user, array $games): int
    {
        // De-duplicate by app ID: the Steam payload may repeat an entry, and
        // upsert() cannot touch the same unique row twice in one statement.
        $entries = collect($games)
            ->filter(fn (array $entry): bool => filled($entry['appid'] ?? null))
            ->keyBy(fn (array $entry): string|int => $entry['appid']);

        if ($entries->isEmpty()) {
            return 0;
        }

        $now = now();

        $catalogRows = $entries
            ->map(function (array $entry) use ($now): array {
                $appId = $entry['appid'];

                return [
                    'steam_app_id' => $appId,
                    'name' => $entry['name'] ?? 'Steam App '.$appId,
                    'cover_url' => 'https://cdn.cloudflare.steamstatic.com/steam/apps/'.$appId.'/header.jpg',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            })
            ->all();

        Game::upsert($catalogRows, ['steam_app_id'], ['name', 'cover_url', 'updated_at']);

        $appIds = $entries->keys()->all();
        $catalog = Game::query()
            ->whereIn('steam_app_id', $appIds)
            ->get(['id', 'steam_app_id', 'name'])
            ->keyBy('steam_app_id');

        $ownershipRows = $entries
            ->map(function (array $entry) use ($user, $catalog, $now): ?array {
                $appId = $entry['appid'];
                $game = $catalog->get($appId);

                if (! $game) {
                    return null;
                }

                return [
                    'user_id' => $user->id,
                    'game_id' => $game->id,
                    'playtime_minutes' => (int) ($entry['playtime_forever'] ?? 0),
                    'source' => 'steam',
                    'is_excluded' => false,
                    'last_seen_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            })
            ->filter()
            ->values()
            ->all();

        if ($ownershipRows !== []) {
            UserGame::upsert(
                $ownershipRows,
                ['user_id', 'game_id'],
                ['playtime_minutes', 'source', 'is_excluded', 'last_seen_at', 'updated_at']
            );
        }

        return count($ownershipRows);
    }

    public function hasActiveRun(User $user): bool
    {
        return $user->steamSyncRuns()
            ->whereIn('status', [SteamSyncRun::STATUS_PENDING, SteamSyncRun::STATUS_RUNNING])
            ->where('started_at', '>=', now()->subMinutes(self::RUN_MAX_AGE_MINUTES))
            ->exists();
    }

    /**
     * Serverless runtimes may kill a request mid-sync. Close out runs that
     * never finished so a stale "running" row cannot block future
     * synchronisations forever.
     */
    private function reapStaleRuns(User $user): void
    {
        $user->steamSyncRuns()
            ->whereIn('status', [SteamSyncRun::STATUS_PENDING, SteamSyncRun::STATUS_RUNNING])
            ->where('started_at', '<', now()->subMinutes(self::RUN_MAX_AGE_MINUTES))
            ->update([
                'status' => SteamSyncRun::STATUS_FAILED,
                'error_message' => 'Synchronization timed out and was closed automatically.',
                'finished_at' => now(),
            ]);
    }

    private function refreshProfile(User $user, SteamAccount $account): void
    {
        $summary = $this->steam->getPlayerSummary($account->steam_id);

        if ($summary) {
            $account->update([
                'display_name' => $summary['personaname'] ?? $account->display_name,
                'avatar_url' => $summary['avatarfull'] ?? $account->avatar_url,
            ]);
        }
    }
}
