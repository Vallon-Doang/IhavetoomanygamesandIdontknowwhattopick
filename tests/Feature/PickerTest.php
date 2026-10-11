<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\User;
use App\Models\UserGame;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PickerTest extends TestCase
{
    use RefreshDatabase;

    private function own(User $user, string $name, array $attributes = []): UserGame
    {
        $game = Game::create(['name' => $name]);

        return UserGame::create(array_merge([
            'user_id' => $user->id,
            'game_id' => $game->id,
            'playtime_minutes' => 100,
            'source' => 'demo',
            'is_excluded' => false,
        ], $attributes));
    }

    public function test_spin_records_exactly_one_history_entry_for_an_eligible_game(): void
    {
        $user = User::factory()->create();
        $this->own($user, 'Eligible Game');

        $this->actingAs($user)
            ->post('/picker/spin', [])
            ->assertRedirect(route('picker.index'));

        $this->assertDatabaseCount('spin_history', 1);
        $this->assertDatabaseHas('spin_history', [
            'user_id' => $user->id,
            'game_name' => 'Eligible Game',
        ]);
    }

    public function test_spin_never_selects_an_excluded_game(): void
    {
        $user = User::factory()->create();
        $this->own($user, 'Included Game');
        $this->own($user, 'Excluded Game', ['is_excluded' => true]);

        foreach (range(1, 15) as $ignored) {
            $this->actingAs($user)->post('/picker/spin', []);
        }

        $this->assertSame(15, $user->spinHistory()->count());
        $this->assertDatabaseMissing('spin_history', ['game_name' => 'Excluded Game']);
    }

    public function test_spin_with_empty_eligible_set_writes_no_history(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/picker/spin', [])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseCount('spin_history', 0);
    }

    public function test_title_search_filter_is_applied(): void
    {
        $user = User::factory()->create();
        $this->own($user, 'Portal 2');
        $this->own($user, 'Terraria');

        $this->actingAs($user)->post('/picker/spin', ['search' => 'Portal'])
            ->assertRedirect(route('picker.index'));

        $this->assertDatabaseHas('spin_history', ['user_id' => $user->id, 'game_name' => 'Portal 2']);
    }

    public function test_member_cannot_toggle_another_members_exclusion(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $userGame = $this->own($owner, 'Private Game');

        $this->actingAs($other)
            ->patch(route('library.exclusion', $userGame))
            ->assertForbidden();

        $this->assertFalse($userGame->fresh()->is_excluded);
    }
}