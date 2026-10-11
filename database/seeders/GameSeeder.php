<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\User;
use App\Models\UserGame;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    /**
     * Seeded demo catalog. Steam App IDs are real so artwork resolves when the
     * network is available, but the library works entirely from local data.
     *
     * @var array<int, array{app_id: int|null, name: string, playtime: int, excluded?: bool}>
     */
    private array $games = [
        ['app_id' => 620, 'name' => 'Portal 2', 'playtime' => 1380],
        ['app_id' => 400, 'name' => 'Portal', 'playtime' => 610],
        ['app_id' => 220, 'name' => 'Half-Life 2', 'playtime' => 1240],
        ['app_id' => 440, 'name' => 'Team Fortress 2', 'playtime' => 3420],
        ['app_id' => 570, 'name' => 'Dota 2', 'playtime' => 9180],
        ['app_id' => 730, 'name' => 'Counter-Strike 2', 'playtime' => 7640],
        ['app_id' => 292030, 'name' => 'The Witcher 3: Wild Hunt', 'playtime' => 5210],
        ['app_id' => 271590, 'name' => 'Grand Theft Auto V', 'playtime' => 2890],
        ['app_id' => 105600, 'name' => 'Terraria', 'playtime' => 4560],
        ['app_id' => 413150, 'name' => 'Stardew Valley', 'playtime' => 3870],
        ['app_id' => 1091500, 'name' => 'Cyberpunk 2077', 'playtime' => 1980],
        ['app_id' => 1145360, 'name' => 'Hades', 'playtime' => 2450],
        ['app_id' => 548430, 'name' => 'Deep Rock Galactic', 'playtime' => 1760],
        ['app_id' => 322330, 'name' => "Don't Starve Together", 'playtime' => 890],
        ['app_id' => null, 'name' => 'GameSpin Demo Exclusive', 'playtime' => 15, 'excluded' => true],
    ];

    public function run(): void
    {
        $member = User::where('email', 'member@gamespin.test')->first();

        if (! $member) {
            return;
        }

        foreach ($this->games as $entry) {
            $attributes = ['name' => $entry['name']];

            $match = $entry['app_id'] !== null
                ? ['steam_app_id' => $entry['app_id']]
                : ['steam_app_id' => null, 'name' => $entry['name']];

            $game = Game::updateOrCreate(
                $match,
                [
                    'name' => $entry['name'],
                    'cover_url' => $entry['app_id'] !== null
                        ? 'https://cdn.cloudflare.steamstatic.com/steam/apps/'.$entry['app_id'].'/header.jpg'
                        : null,
                ]
            );

            UserGame::updateOrCreate(
                ['user_id' => $member->id, 'game_id' => $game->id],
                [
                    'playtime_minutes' => $entry['playtime'],
                    'source' => 'demo',
                    'is_excluded' => $entry['excluded'] ?? false,
                    'last_seen_at' => null,
                ]
            );
        }
    }
}