<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCommunityTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_cannot_access_admin_community_pages(): void
    {
        $member = User::factory()->create();

        $this->actingAs($member)->get('/admin/posts')->assertForbidden();
        $this->actingAs($member)->get('/admin/announcements')->assertForbidden();
        $this->actingAs($member)->get('/admin/analytics')->assertForbidden();
    }

    public function test_admin_can_access_admin_community_pages(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/admin/posts')->assertOk();
        $this->actingAs($admin)->get('/admin/announcements')->assertOk();
        $this->actingAs($admin)->get('/admin/analytics')->assertOk();
    }

    public function test_admin_can_hide_and_restore_a_post(): void
    {
        $admin = User::factory()->admin()->create();
        $author = User::factory()->create();
        $post = $author->posts()->create(['body' => 'Rule breaking post']);

        $this->actingAs($admin)
            ->patch(route('admin.posts.moderate', $post), [
                'action' => 'hide',
                'hidden_reason' => 'Off topic',
            ])
            ->assertRedirect();

        $post->refresh();
        $this->assertTrue($post->is_hidden);
        $this->assertSame('Off topic', $post->hidden_reason);
        $this->assertSame($admin->id, $post->hidden_by);

        $this->actingAs($admin)
            ->patch(route('admin.posts.moderate', $post), ['action' => 'restore'])
            ->assertRedirect();

        $post->refresh();
        $this->assertFalse($post->is_hidden);
        $this->assertNull($post->hidden_reason);
    }

    public function test_moderation_requires_a_valid_action(): void
    {
        $admin = User::factory()->admin()->create();
        $post = User::factory()->create()->posts()->create(['body' => 'A post']);

        $this->actingAs($admin)
            ->patch(route('admin.posts.moderate', $post), ['action' => 'nonsense'])
            ->assertSessionHasErrors('action');
    }

    public function test_admin_can_permanently_delete_a_post(): void
    {
        $admin = User::factory()->admin()->create();
        $post = User::factory()->create()->posts()->create(['body' => 'Remove me']);

        $this->actingAs($admin)
            ->delete(route('admin.posts.destroy', $post))
            ->assertRedirect();

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_published_announcements_appear_on_the_feed_and_drafts_do_not(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->create();

        $this->actingAs($admin)->post(route('admin.announcements.store'), [
            'title' => 'Published notice',
            'body' => 'Visible to everyone.',
            'is_published' => '1',
        ]);

        $this->actingAs($admin)->post(route('admin.announcements.store'), [
            'title' => 'Draft notice',
            'body' => 'Still being written.',
        ]);

        $response = $this->actingAs($member)->get('/community');
        $response->assertSee('Published notice');
        $response->assertDontSee('Draft notice');

        $this->assertDatabaseHas('announcements', [
            'title' => 'Published notice',
            'is_published' => true,
        ]);
        $this->assertDatabaseHas('announcements', [
            'title' => 'Draft notice',
            'is_published' => false,
        ]);
    }

    public function test_announcement_validation_is_enforced(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.announcements.store'), ['title' => '', 'body' => ''])
            ->assertSessionHasErrors(['title', 'body']);
    }

    public function test_admin_can_update_and_delete_an_announcement(): void
    {
        $admin = User::factory()->admin()->create();
        $announcement = $admin->announcements()->create([
            'title' => 'Old title',
            'body' => 'Old body',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.announcements.update', $announcement), [
                'title' => 'New title',
                'body' => 'New body',
                'is_published' => '1',
            ])
            ->assertRedirect(route('admin.announcements.index'));

        $this->assertDatabaseHas('announcements', [
            'id' => $announcement->id,
            'title' => 'New title',
            'is_published' => true,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.announcements.destroy', $announcement))
            ->assertRedirect();

        $this->assertDatabaseMissing('announcements', ['id' => $announcement->id]);
    }

    public function test_analytics_page_reflects_real_counts(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->create();
        $member->posts()->create(['body' => 'A counted post']);

        $response = $this->actingAs($admin)->get('/admin/analytics');
        $response->assertOk();
        $response->assertViewHas('totals', fn (array $totals) => $totals['posts'] === 1 && $totals['users'] === 2);
    }
}
