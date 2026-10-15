<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResolveSteamResetRequest;
use App\Models\SteamAccount;
use App\Models\SteamResetRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class SteamResetRequestController extends Controller
{
    /**
     * List member Steam reset requests for review.
     */
    public function index(Request $request): View
    {
        $filter = (string) $request->query('filter', SteamResetRequest::STATUS_PENDING);

        if (! in_array($filter, ['pending', 'resolved', 'dismissed', 'all'], true)) {
            $filter = SteamResetRequest::STATUS_PENDING;
        }

        $requests = SteamResetRequest::query()
            ->with(['requester', 'resolver'])
            ->when($filter !== 'all', fn ($query) => $query->where('status', $filter))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.steam-requests', [
            'requests' => $requests,
            'filter' => $filter,
            'pendingCount' => SteamResetRequest::pending()->count(),
        ]);
    }

    /**
     * Resolve (unlink the conflicting Steam account) or dismiss a request.
     */
    public function update(ResolveSteamResetRequest $request, SteamResetRequest $steamResetRequest): RedirectResponse
    {
        $admin = $request->user();

        if ($request->validated('action') === 'dismiss') {
            $steamResetRequest->update([
                'status' => SteamResetRequest::STATUS_DISMISSED,
                'resolved_by' => $admin->id,
                'resolved_at' => now(),
            ]);

            Log::info('Admin dismissed a Steam reset request', [
                'admin_id' => $admin->id,
                'request_id' => $steamResetRequest->id,
                'requester_id' => $steamResetRequest->user_id,
            ]);

            return back()->with('status', 'Reset request dismissed.');
        }

        $unlinked = 0;

        if ($steamResetRequest->steam_id) {
            $unlinked = SteamAccount::where('steam_id', $steamResetRequest->steam_id)->delete();
        }

        $steamResetRequest->update([
            'status' => SteamResetRequest::STATUS_RESOLVED,
            'resolved_by' => $admin->id,
            'resolved_at' => now(),
        ]);

        Log::info('Admin resolved a Steam reset request', [
            'admin_id' => $admin->id,
            'request_id' => $steamResetRequest->id,
            'requester_id' => $steamResetRequest->user_id,
            'steam_id' => $steamResetRequest->steam_id,
            'accounts_unlinked' => $unlinked,
        ]);

        return back()->with(
            'status',
            $unlinked > 0
                ? 'Reset request resolved. The conflicting Steam link was removed.'
                : 'Reset request resolved, but no matching Steam link was found.'
        );
    }
}
