<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_game_search_is_case_insensitive_and_matches_app_ids(): void
    {
        User::factory()->admin()->create();

        Game::create(['name' => 'HALF-LIFE 2', 'steam_app_id' => 220]);

        $this->actingAs(User::factory()->admin()->create())
            ->get('/admin/games?search=half-life')
            ->assertOk()
            ->assertSee('HALF-LIFE 2');

        $this->actingAs(User::factory()->admin()->create())
            ->get('/admin/games?search=22')
            ->assertOk()
            ->assertSee('HALF-LIFE 2');
    }

    public function test_user_search_is_case_insensitive(): void
    {
        User::factory()->create(['name' => 'Alice Cooper', 'email' => 'alice@example.test']);

        $this->actingAs(User::factory()->admin()->create())
            ->get('/admin/users?search=ALICE')
            ->assertOk()
            ->assertSee('Alice Cooper');
    }
}
