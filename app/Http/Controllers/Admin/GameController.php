<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GameController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $games = Game::query()
            ->withCount('userGames')
            ->when($search !== '', function ($query) use ($search): void {
                $escaped = '%'.str_replace(['%', '_'], ['\%', '\_'], $search).'%';
                $query->where(fn ($q) => $q
                    ->where('name', 'like', $escaped)
                    ->orWhere('steam_app_id', 'like', $escaped));
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.games', [
            'games' => $games,
            'search' => $search,
        ]);
    }
}