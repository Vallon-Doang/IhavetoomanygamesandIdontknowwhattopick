<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSteamResetRequest;
use App\Models\SteamResetRequest;
use Illuminate\Http\RedirectResponse;

class SteamResetRequestController extends Controller
{
    /**
     * Record a member's request for an administrator to clear a conflicting
     * Steam link. The conflicting SteamID comes from the server-side session
     * set during the failed link attempt, never from the request payload.
     */
    public function store(StoreSteamResetRequest $request): RedirectResponse
    {
        $steamId = $request->session()->get('steam_conflict');

        if (! $steamId) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'There is no Steam linking conflict to report right now.');
        }

        SteamResetRequest::updateOrCreate(
            ['user_id' => $request->user()->id, 'status' => SteamResetRequest::STATUS_PENDING],
            ['steam_id' => $steamId, 'message' => $request->validated('message')],
        );

        $request->session()->forget('steam_conflict');

        return redirect()
            ->route('dashboard')
            ->with('status', 'Reset request submitted. An administrator will review it shortly.');
    }
}
