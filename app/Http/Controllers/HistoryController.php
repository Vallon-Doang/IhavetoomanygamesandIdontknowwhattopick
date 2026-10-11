<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class HistoryController extends Controller
{
    public function index(Request $request): View
    {
        $history = $request->user()
            ->spinHistory()
            ->with('game')
            ->latest('created_at')
            ->paginate(15);

        return view('history.index', [
            'history' => $history,
        ]);
    }
}