<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featured = Game::query()
            ->whereNotNull('cover_url')
            ->inRandomOrder()
            ->limit(6)
            ->get();

        return view('home', compact('featured'));
    }
}