<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_landing_and_auth_pages(): void
    {
        $this->get('/')->assertOk();
        $this->get('/login')->assertOk();
        $this->get('/register')->assertOk();
    }

    public function test_registration_always_creates_a_member_even_with_forged_role(): void
    {
        $response = $this->post('/register', [
            'name' => 'New Member',
            'email' => 'new@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'admin',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'new@example.com',
            'role' => User::ROLE_MEMBER,
        ]);
    }

    public function test_member_can_log_in_and_reach_member_pages(): void
    {
        $member = User::factory()->create();

        $this->post('/login', [
            'email' => $member->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($member);
        $this->get('/dashboard')->assertOk();
        $this->get('/library')->assertOk();
        $this->get('/picker')->assertOk();
        $this->get('/history')->assertOk();
        $this->get('/community')->assertOk();
    }

    public function test_member_cannot_access_admin_routes(): void
    {
        $member = User::factory()->create();

        $this->actingAs($member)->get('/admin')->assertForbidden();
        $this->actingAs($member)->get('/admin/users')->assertForbidden();
        $this->actingAs($member)->get('/admin/posts')->assertForbidden();
        $this->actingAs($member)->get('/admin/announcements')->assertForbidden();
        $this->actingAs($member)->get('/admin/analytics')->assertForbidden();
        $this->actingAs($member)->get('/admin/steam-requests')->assertForbidden();
    }

    public function test_admin_can_access_admin_routes(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/admin')->assertOk();
        $this->actingAs($admin)->get('/admin/users')->assertOk();
        $this->actingAs($admin)->get('/admin/games')->assertOk();
        $this->actingAs($admin)->get('/admin/syncs')->assertOk();
        $this->actingAs($admin)->get('/admin/posts')->assertOk();
        $this->actingAs($admin)->get('/admin/announcements')->assertOk();
        $this->actingAs($admin)->get('/admin/analytics')->assertOk();
        $this->actingAs($admin)->get('/admin/steam-requests')->assertOk();
    }

    public function test_guest_is_redirected_from_member_pages(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
        $this->get('/picker')->assertRedirect(route('login'));
        $this->post('/picker/spin')->assertRedirect(route('login'));
    }
}