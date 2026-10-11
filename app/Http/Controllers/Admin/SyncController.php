<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SteamSyncRun;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SyncController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $runs = SteamSyncRun::query()
            ->with('user')
            ->when(in_array($status, ['pending', 'running', 'success', 'failed'], true), fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.syncs', [
            'runs' => $runs,
            'status' => $status,
        ]);
    }
}