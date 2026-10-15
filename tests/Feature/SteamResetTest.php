<?php

namespace Tests\Feature;

use App\Models\SteamAccount;
use App\Models\SteamResetRequest;
use App\Models\User;
use App\Services\SteamService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SteamResetTest extends TestCase
{
    use RefreshDatabase;

    private function fakeSteamIdentity(string $steamId): void
    {
        $this->mock(SteamService::class, function ($mock) use ($steamId): void {
            $mock->shouldReceive('isConfigured')->andReturn(true);
            $mock->shouldReceive('verifyCallback')->andReturn($steamId);
        });
    }

    public function test_conflict_is_remembered_and_offers_a_reset(): void
    {
        config(['services.steam.key' => 'test-key']);

        $owner = User::factory()->create();
        SteamAccount::create(['user_id' => $owner->id, 'steam_id' => '76561198000000000']);
        $requester = User::factory()->create();

        $this->fakeSteamIdentity('76561198000000000');

        $this->actingAs($requester)
            ->get('/steam/callback?openid_mode=id_res&openid_claimed_id=http://steamcommunity.com/openid/id/76561198000000000')
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error')
            ->assertSessionHas('steam_conflict', '76561198000000000');

        $this->actingAs($requester)
            ->withSession(['steam_conflict' => '76561198000000000'])
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Request a Steam reset');
    }

    public function test_member_can_request_a_steam_reset(): void
    {
        $member = User::factory()->create();

        $this->actingAs($member)
            ->withSession(['steam_conflict' => '76561198000000000'])
            ->post('/steam/reset-request', ['message' => 'This is my main Steam account.'])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('status')
            ->assertSessionMissing('steam_conflict');

        $this->assertDatabaseHas('steam_reset_requests', [
            'user_id' => $member->id,
            'steam_id' => '76561198000000000',
            'message' => 'This is my main Steam account.',
            'status' => SteamResetRequest::STATUS_PENDING,
        ]);
    }

    public function test_duplicate_pending_requests_are_collapsed(): void
    {
        $member = User::factory()->create();

        foreach (['First note', 'Second note'] as $note) {
            $this->actingAs($member)
                ->withSession(['steam_conflict' => '76561198000000000'])
                ->post('/steam/reset-request', ['message' => $note]);
        }

        $this->assertSame(1, SteamResetRequest::where('user_id', $member->id)->count());
        $this->assertDatabaseHas('steam_reset_requests', ['user_id' => $member->id, 'message' => 'Second note']);
    }

    public function test_reset_request_without_a_conflict_is_rejected(): void
    {
        $member = User::factory()->create();

        $this->actingAs($member)
            ->post('/steam/reset-request', ['message' => 'No conflict here'])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('steam_reset_requests', 0);
    }

    public function test_guest_cannot_request_a_reset(): void
    {
        $this->post('/steam/reset-request')->assertRedirect(route('login'));
    }

    public function test_member_cannot_access_admin_steam_requests(): void
    {
        $member = User::factory()->create();
        $request = SteamResetRequest::create(['user_id' => $member->id, 'steam_id' => '76561198000000000']);

        $this->actingAs($member)->get('/admin/steam-requests')->assertForbidden();
        $this->actingAs($member)->patch(route('admin.steam-requests.update', $request), ['action' => 'resolve'])->assertForbidden();
    }

    public function test_admin_resolving_a_request_unlinks_the_conflicting_account(): void
    {
        $admin = User::factory()->admin()->create();
        $owner = User::factory()->create();
        SteamAccount::create(['user_id' => $owner->id, 'steam_id' => '76561198000000000']);

        $requester = User::factory()->create();
        $resetRequest = SteamResetRequest::create([
            'user_id' => $requester->id,
            'steam_id' => '76561198000000000',
        ]);

        $this->actingAs($admin)->get('/admin/steam-requests')->assertOk();

        $this->actingAs($admin)
            ->patch(route('admin.steam-requests.update', $resetRequest), ['action' => 'resolve'])
            ->assertRedirect();

        $this->assertDatabaseMissing('steam_accounts', ['steam_id' => '76561198000000000']);
        $this->assertDatabaseHas('steam_reset_requests', [
            'id' => $resetRequest->id,
            'status' => SteamResetRequest::STATUS_RESOLVED,
            'resolved_by' => $admin->id,
        ]);
    }

    public function test_admin_dismissing_a_request_keeps_the_steam_link(): void
    {
        $admin = User::factory()->admin()->create();
        $owner = User::factory()->create();
        SteamAccount::create(['user_id' => $owner->id, 'steam_id' => '76561198000000000']);

        $resetRequest = SteamResetRequest::create([
            'user_id' => User::factory()->create()->id,
            'steam_id' => '76561198000000000',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.steam-requests.update', $resetRequest), ['action' => 'dismiss'])
            ->assertRedirect();

        $this->assertDatabaseHas('steam_accounts', ['steam_id' => '76561198000000000']);
        $this->assertDatabaseHas('steam_reset_requests', [
            'id' => $resetRequest->id,
            'status' => SteamResetRequest::STATUS_DISMISSED,
        ]);
    }

    public function test_admin_can_unlink_a_users_steam_account(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();
        SteamAccount::create(['user_id' => $user->id, 'steam_id' => '76561198000000000']);

        $this->actingAs($admin)
            ->delete(route('admin.users.steam.destroy', $user))
            ->assertRedirect();

        $this->assertDatabaseMissing('steam_accounts', ['user_id' => $user->id]);
    }

    public function test_member_cannot_unlink_a_users_steam_account(): void
    {
        $member = User::factory()->create();
        $victim = User::factory()->create();
        SteamAccount::create(['user_id' => $victim->id, 'steam_id' => '76561198000000000']);

        $this->actingAs($member)
            ->delete(route('admin.users.steam.destroy', $victim))
            ->assertForbidden();

        $this->assertDatabaseHas('steam_accounts', ['user_id' => $victim->id]);
    }
}
