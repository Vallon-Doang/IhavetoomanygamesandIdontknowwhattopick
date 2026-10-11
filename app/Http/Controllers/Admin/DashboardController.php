<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\SpinHistory;
use App\Models\SteamAccount;
use App\Models\SteamSyncRun;
use App\Models\User;
use App\Models\UserGame;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'counts' => [
                'users' => User::count(),
                'members' => User::where('role', User::ROLE_MEMBER)->count(),
                'admins' => User::where('role', User::ROLE_ADMIN)->count(),
                'games' => Game::count(),
                'ownerships' => UserGame::count(),
                'spins' => SpinHistory::count(),
                'linkedSteam' => SteamAccount::count(),
            ],
            'syncCounts' => [
                'success' => SteamSyncRun::where('status', SteamSyncRun::STATUS_SUCCESS)->count(),
                'failed' => SteamSyncRun::where('status', SteamSyncRun::STATUS_FAILED)->count(),
            ],
            'recentSyncs' => SteamSyncRun::with('user')->latest()->limit(5)->get(),
        ]);
    }
}