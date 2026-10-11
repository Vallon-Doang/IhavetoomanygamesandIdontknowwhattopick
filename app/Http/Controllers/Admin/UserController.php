<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
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
}