<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $users = User::query()
            ->withCount(['userGames', 'spinHistory'])
            ->when($search !== '', function ($query) use ($search): void {
                $escaped = '%'.str_replace(['%', '_'], ['\%', '\_'], $search).'%';
                $query->where(fn ($q) => $q
                    ->where('name', 'like', $escaped)
                    ->orWhere('email', 'like', $escaped));
            })
            ->with('steamAccount')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users', [
            'users' => $users,
            'search' => $search,
        ]);
    }

    /**
     * Remove a user's linked Steam account, guarded behind the admin middleware
     * and recorded in the application log.
     */
    public function unlinkSteam(Request $request, User $user): RedirectResponse
    {
        $account = $user->steamAccount;

        if (! $account) {
            return back()->with('error', 'That user has no linked Steam account.');
        }

        Log::info('Admin unlinked a user Steam account', [
            'admin_id' => $request->user()->id,
            'user_id' => $user->id,
            'steam_id' => $account->steam_id,
        ]);

        $account->delete();

        return back()->with('status', "Unlinked the Steam account from {$user->name}.");
    }
}