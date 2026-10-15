<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CommunityTest extends TestCase
{
    use RefreshDatabase;

    private function pngBytes(): string
    {
        return base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='
        );
    }

    public function test_guest_is_redirected_from_community(): void
    {
        $this->get('/community')->assertRedirect(route('login'));
        $this->post('/community/posts', ['body' => 'Hello'])->assertRedirect(route('login'));
    }

    public function test_member_can_view_the_feed_with_published_announcements(): void
    {
        $member = User::factory()->create();
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.announcements.store'), [
            'title' => 'Heads up',
            'body' => 'Maintenance tonight.',
            'is_published' => '1',
        ]);

        $response = $this->actingAs($member)->get('/community');
        $response->assertOk();
        $response->assertSee('Heads up');
        $response->assertSee('Maintenance tonight.');
    }

    public function test_member_can_create_a_post(): void
    {
        $member = User::factory()->create();

        $response = $this->actingAs($member)->post(route('community.posts.store'), [
            'body' => 'Playing some Portal 2 this weekend.',
        ]);

        $response->assertRedirect(route('community.index'));
        $this->assertDatabaseHas('posts', [
            'user_id' => $member->id,
            'body' => 'Playing some Portal 2 this weekend.',
            'is_hidden' => false,
        ]);
    }

    public function test_post_body_is_required(): void
    {
        $member = User::factory()->create();

        $this->actingAs($member)
            ->post(route('community.posts.store'), ['body' => ''])
            ->assertSessionHasErrors('body');
    }

    public function test_member_can_upload_a_valid_image(): void
    {
        Storage::fake('public');
        $member = User::factory()->create();

        $file = UploadedFile::fake()->createWithContent('photo.png', $this->pngBytes());

        $this->actingAs($member)
            ->post(route('community.posts.store'), ['body' => 'With image', 'image' => $file])
            ->assertRedirect(route('community.index'));

        $post = Post::where('body', 'With image')->firstOrFail();
        $this->assertNotNull($post->image_path);
        Storage::disk('public')->assertExists($post->image_path);
    }

    public function test_non_image_uploads_are_rejected(): void
    {
        Storage::fake('public');
        $member = User::factory()->create();

        $file = UploadedFile::fake()->createWithContent('notes.txt', 'not an image');

        $this->actingAs($member)
            ->post(route('community.posts.store'), ['body' => 'Bad upload', 'image' => $file])
            ->assertSessionHasErrors('image');

        $this->assertDatabaseMissing('posts', ['body' => 'Bad upload']);
    }

    public function test_author_can_edit_their_own_post(): void
    {
        $member = User::factory()->create();
        $post = $member->posts()->create(['body' => 'Original body']);

        $this->actingAs($member)->get(route('community.posts.edit', $post))->assertOk();

        $this->actingAs($member)
            ->put(route('community.posts.update', $post), ['body' => 'Updated body'])
            ->assertRedirect(route('community.index'));

        $this->assertDatabaseHas('posts', ['id' => $post->id, 'body' => 'Updated body']);
    }

    public function test_member_cannot_edit_another_members_post(): void
    {
        $author = User::factory()->create();
        $other = User::factory()->create();
        $post = $author->posts()->create(['body' => 'Private thoughts']);

        $this->actingAs($other)->get(route('community.posts.edit', $post))->assertForbidden();
        $this->actingAs($other)
            ->put(route('community.posts.update', $post), ['body' => 'Hijacked'])
            ->assertForbidden();

        $this->assertDatabaseHas('posts', ['id' => $post->id, 'body' => 'Private thoughts']);
    }

    public function test_author_can_delete_their_own_post(): void
    {
        $member = User::factory()->create();
        $post = $member->posts()->create(['body' => 'Delete me']);

        $this->actingAs($member)
            ->delete(route('community.posts.destroy', $post))
            ->assertRedirect(route('community.index'));

        $this->assertSoftDeleted('posts', ['id' => $post->id]);
    }

    public function test_member_cannot_delete_another_members_post(): void
    {
        $author = User::factory()->create();
        $other = User::factory()->create();
        $post = $author->posts()->create(['body' => 'Keep me']);

        $this->actingAs($other)
            ->delete(route('community.posts.destroy', $post))
            ->assertForbidden();

        $this->assertDatabaseHas('posts', ['id' => $post->id, 'deleted_at' => null]);
    }

    public function test_hidden_posts_are_not_shown_in_the_feed(): void
    {
        $member = User::factory()->create();
        $member->posts()->create(['body' => 'Visible post body']);
        $member->posts()->create(['body' => 'Hidden post body', 'is_hidden' => true]);

        $response = $this->actingAs($member)->get('/community');
        $response->assertOk();
        $response->assertSee('Visible post body');
        $response->assertDontSee('Hidden post body');
    }
}
