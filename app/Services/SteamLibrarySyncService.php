<?php

namespace App\Services;

use App\Exceptions\SteamApiException;
use App\Models\Game;
use App\Models\SteamAccount;
use App\Models\SteamSyncRun;
use App\Models\User;
use App\Models\UserGame;
use Illuminate\Support\Str;
use Throwable;

class SteamLibrarySyncService
{
    public function __construct(private readonly SteamService $steam) {}

    /**
     * Synchronize a member's accessible Steam library.
     *
     * Existing library entries are never deleted here; an incomplete or failed
     * sync preserves the last valid local library.
     */
    public function sync(User $user, SteamAccount $account): SteamSyncRun
    {
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

            $imported = 0;

            foreach ($games as $entry) {
                $appId = $entry['appid'] ?? null;
                if (blank($appId)) {
                    continue;
                }

                $game = Game::updateOrCreate(
                    ['steam_app_id' => $appId],
                    [
                        'name' => $entry['name'] ?? ('Steam App '.$appId),
                        'cover_url' => 'https://cdn.cloudflare.steamstatic.com/steam/apps/'.$appId.'/header.jpg',
                    ]
                );

                UserGame::updateOrCreate(
                    ['user_id' => $user->id, 'game_id' => $game->id],
                    [
                        'playtime_minutes' => (int) ($entry['playtime_forever'] ?? 0),
                        'source' => 'steam',
                        'last_seen_at' => now(),
                    ]
                );

                $imported++;
            }

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
        }

        return $run->refresh();
    }

    public function hasActiveRun(User $user): bool
    {
        return $user->steamSyncRuns()
            ->whereIn('status', [SteamSyncRun::STATUS_PENDING, SteamSyncRun::STATUS_RUNNING])
            ->exists();
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