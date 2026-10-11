<?php

namespace App\Http\Controllers;

use App\Models\UserGame;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LibraryController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $search = trim((string) $request->query('search', ''));

        $games = $user->userGames()
            ->with('game')
            ->when($search !== '', function ($query) use ($search): void {
                $escaped = '%'.str_replace(['%', '_'], ['\%', '\_'], $search).'%';
                $query->whereHas('game', fn ($q) => $q->where('name', 'like', $escaped));
            })
            ->join('games', 'games.id', '=', 'user_games.game_id')
            ->orderBy('games.name')
            ->select('user_games.*')
            ->paginate(12)
            ->withQueryString();

        return view('library.index', [
            'games' => $games,
            'search' => $search,
        ]);
    }

    public function toggleExclusion(Request $request, UserGame $userGame): RedirectResponse
    {
        $this->authorize('update', $userGame);

        $userGame->update(['is_excluded' => ! $userGame->is_excluded]);

        return back()->with('status', $userGame->is_excluded
            ? "{$userGame->game->name} excluded from the picker."
            : "{$userGame->game->name} included in the picker.");
    }
}