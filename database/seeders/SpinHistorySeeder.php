<?php

namespace Database\Seeders;

use App\Models\SpinHistory;
use App\Models\User;
use Illuminate\Database\Seeder;

class SpinHistorySeeder extends Seeder
{
    public function run(): void
    {
        $member = User::where('email', 'member@gamespin.test')->first();

        if (! $member || $member->spinHistory()->exists()) {
            return;
        }

        $games = $member->games()
            ->wherePivot('is_excluded', false)
            ->orderBy('games.name')
            ->limit(3)
            ->get();

        foreach ($games as $game) {
            SpinHistory::create([
                'user_id' => $member->id,
                'game_id' => $game->id,
                'game_name' => $game->name,
                'filters' => null,
            ]);
        }
    }
}