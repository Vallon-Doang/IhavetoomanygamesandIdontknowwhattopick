<?php

namespace App\Http\Controllers;

use App\Models\SteamAccount;
use App\Services\SteamLibrarySyncService;
use App\Services\SteamService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SteamController extends Controller
{
    public function __construct(
        private readonly SteamService $steam,
        private readonly SteamLibrarySyncService $sync,
    ) {}

    public function connect(Request $request): RedirectResponse
    {
        if (! $this->steam->isConfigured()) {
            return redirect()->route('dashboard')->with(
                'error',
                'Steam integration is not configured on this server. The seeded demo library is still available.'
            );
        }

        $returnTo = config('services.steam.callback_url') ?: route('steam.callback');

        return redirect()->away($this->steam->getRedirectUrl($returnTo, config('app.url')));
    }

    public function callback(Request $request): RedirectResponse
    {
        $steamId = $this->steam->verifyCallback($request->query());

        if (! $steamId) {
            return redirect()->route('dashboard')->with('error', 'Steam identity verification failed. Please try again.');
        }

        $user = $request->user();

        $conflict = SteamAccount::where('steam_id', $steamId)
            ->where('user_id', '!=', $user->id)
            ->exists();

        if ($conflict) {
            $request->session()->put('steam_conflict', $steamId);

            return redirect()->route('dashboard')->with(
                'error',
                'That Steam account is already linked to another GameSpin user. If this is unexpected, you can request a reset from your dashboard.'
            );
        }

        $request->session()->forget('steam_conflict');

        $account = SteamAccount::updateOrCreate(
            ['user_id' => $user->id],
            ['steam_id' => $steamId]
        );

        $summary = $this->steam->getPlayerSummary($steamId);

        if ($summary) {
            $account->update([
                'display_name' => $summary['personaname'] ?? null,
                'avatar_url' => $summary['avatarfull'] ?? null,
            ]);
        }

        return redirect()->route('dashboard')->with('status', 'Steam account linked successfully.');
    }

    public function sync(Request $request): RedirectResponse
    {
        $user = $request->user();
        $account = $user->steamAccount;

        if (! $account) {
            return redirect()->route('dashboard')->with('error', 'Link a Steam account before synchronizing.');
        }

        if (! $this->steam->isConfigured()) {
            return redirect()->route('dashboard')->with(
                'error',
                'Steam API access is not configured. Your existing library remains available.'
            );
        }

        if ($this->sync->hasActiveRun($user)) {
            return redirect()->route('dashboard')->with('error', 'A synchronization is already in progress.');
        }

        $run = $this->sync->sync($user, $account);

        if ($run->status === \App\Models\SteamSyncRun::STATUS_SUCCESS) {
            return redirect()->route('dashboard')->with(
                'status',
                "Steam synchronization complete. {$run->games_imported} games imported."
            );
        }

        return redirect()->route('dashboard')->with(
            'error',
            $run->error_message ?: 'Steam synchronization failed. Your previous library was preserved.'
        );
    }

    public function disconnect(Request $request): RedirectResponse
    {
        $request->user()->steamAccount?->delete();

        return redirect()->route('dashboard')->with(
            'status',
            'Steam account unlinked. Previously imported games remain in your library.'
        );
    }
}