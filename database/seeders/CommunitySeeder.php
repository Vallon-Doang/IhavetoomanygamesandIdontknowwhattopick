<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommunitySeeder extends Seeder
{
    /**
     * Deterministic demo community content. Safe to rerun without duplicating.
     */
    public function run(): void
    {
        $member = User::where('email', 'member@gamespin.test')->first();
        $admin = User::where('email', 'admin@gamespin.test')->first();

        if (! $member || ! $admin) {
            return;
        }

        $this->upsertPost($member, 'Finally cleared my backlog roulette and landed on Hades — the picker has good taste.');
        $this->upsertPost($member, 'Anyone else use GameSpin before deciding what to play on a Friday night? It has saved me from choice paralysis.');
        $this->upsertPost($admin, 'Welcome to the GameSpin community feed! Share what you are playing and keep it friendly.');

        $hidden = $this->upsertPost($member, 'This demo post is hidden to show how admin moderation works in the feed.');

        if (! $hidden->is_hidden) {
            $hidden->update([
                'is_hidden' => true,
                'hidden_reason' => 'Example moderation - hidden from the public feed.',
                'hidden_by' => $admin->id,
                'hidden_at' => now(),
            ]);
        }

        $this->upsertAnnouncement(
            $admin,
            'Community feed is live',
            'You can now post updates, attach an image, and edit your own posts. Administrators moderate anything that breaks the rules.',
            true,
        );

        $this->upsertAnnouncement(
            $admin,
            'Steam syncing is optional',
            'The demo library works without Steam credentials. Link a public Steam account when you want to sync your real owned games.',
            true,
        );
    }

    private function upsertPost(User $author, string $body): Post
    {
        return Post::withTrashed()->firstOrCreate(
            ['user_id' => $author->id, 'body' => $body],
        );
    }

    private function upsertAnnouncement(User $author, string $title, string $body, bool $published): Announcement
    {
        return Announcement::updateOrCreate(
            ['title' => $title],
            [
                'user_id' => $author->id,
                'body' => $body,
                'is_published' => $published,
                'published_at' => $published ? now() : null,
            ],
        );
    }
}
