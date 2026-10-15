<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('dashboard', [
            'user' => $user,
            'steamAccount' => $user->steamAccount,
            'gamesCount' => $user->userGames()->count(),
            'excludedCount' => $user->userGames()->where('is_excluded', true)->count(),
            'steamGamesCount' => $user->userGames()->where('source', 'steam')->count(),
            'topGame' => $user->userGames()->with('game')->orderByDesc('playtime_minutes')->first(),
            'lastSpin' => $user->spinHistory()->with('game')->latest('created_at')->first(),
            'recentSpins' => $user->spinHistory()->with('game')->latest('created_at')->limit(5)->get(),
            'steamConflict' => session('steam_conflict'),
        ]);
    }
}