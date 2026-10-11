<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\SteamAccount;
use App\Models\SteamSyncRun;
use App\Models\User;
use App\Models\UserGame;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SteamSyncTest extends TestCase
{
    use RefreshDatabase;

    private function linkedUser(): User
    {
        $user = User::factory()->create();
        SteamAccount::create(['user_id' => $user->id, 'steam_id' => '76561198000000000']);

        return $user;
    }

    private function fakeOwnedGames(array $games): void
    {
        Http::fake([
            'api.steampowered.com/IPlayerService/GetOwnedGames/*' => Http::response([
                'response' => ['games' => $games],
            ]),
            'api.steampowered.com/ISteamUser/GetPlayerSummaries/*' => Http::response([
                'response' => ['players' => []],
            ]),
        ]);
    }

    public function test_sync_imports_games_and_is_idempotent(): void
    {
        config(['services.steam.key' => 'test-key']);
        $user = $this->linkedUser();

        $this->fakeOwnedGames([
            ['appid' => 620, 'name' => 'Portal 2', 'playtime_forever' => 120],
            ['appid' => 400, 'name' => 'Portal', 'playtime_forever' => 60],
        ]);

        $this->actingAs($user)->post('/steam/sync')->assertRedirect(route('dashboard'));

        $this->assertSame(2, Game::count());
        $this->assertSame(2, UserGame::where('user_id', $user->id)->count());

        $this->actingAs($user)->post('/steam/sync')->assertRedirect(route('dashboard'));

        $this->assertSame(2, Game::count(), 'Catalog games must not be duplicated.');
        $this->assertSame(2, UserGame::where('user_id', $user->id)->count(), 'Ownership rows must not be duplicated.');
        $this->assertSame(2, SteamSyncRun::where('status', SteamSyncRun::STATUS_SUCCESS)->count());
    }

    public function test_failed_sync_preserves_existing_library(): void
    {
        config(['services.steam.key' => 'test-key']);
        $user = $this->linkedUser();

        $existing = Game::create(['name' => 'Previously Imported', 'steam_app_id' => 999]);
        UserGame::create([
            'user_id' => $user->id,
            'game_id' => $existing->id,
            'playtime_minutes' => 10,
            'source' => 'steam',
        ]);

        Http::fake([
            'api.steampowered.com/IPlayerService/GetOwnedGames/*' => Http::response('server error', 500),
        ]);

        $this->actingAs($user)->post('/steam/sync')->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('user_games', ['user_id' => $user->id, 'game_id' => $existing->id]);
        $this->assertSame(1, SteamSyncRun::where('status', SteamSyncRun::STATUS_FAILED)->count());
    }

    public function test_private_library_is_reported_without_deleting_games(): void
    {
        config(['services.steam.key' => 'test-key']);
        $user = $this->linkedUser();

        Http::fake([
            'api.steampowered.com/IPlayerService/GetOwnedGames/*' => Http::response(['response' => []]),
        ]);

        $this->actingAs($user)
            ->post('/steam/sync')
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');

        $this->assertSame(1, SteamSyncRun::where('status', SteamSyncRun::STATUS_FAILED)->count());
    }

    public function test_sync_without_linked_account_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/steam/sync')
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('steam_sync_runs', 0);
    }
}