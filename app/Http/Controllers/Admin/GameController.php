<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GameController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        // Cast target for the numeric App ID search: PostgreSQL wants TEXT
        // (CHAR means CHAR(1) there and would truncate), while MySQL only
        // knows CHAR (no TEXT cast). SQLite accepts both spellings.
        $castType = DB::connection()->getDriverName() === 'pgsql' ? 'TEXT' : 'CHAR';

        $games = Game::query()
            ->withCount('userGames')
            ->when($search !== '', function ($query) use ($search, $castType): void {
                $escaped = '%'.str_replace(['%', '_'], ['\%', '\_'], $search).'%';
                // Case-insensitive title search across MySQL/PostgreSQL/SQLite.
                $query->where(fn ($q) => $q
                    ->whereLike('name', $escaped)
                    // steam_app_id is numeric, so cast it before the pattern
                    // match (PostgreSQL has no LIKE operator for integers).
                    ->orWhereRaw("LOWER(CAST(steam_app_id AS {$castType})) LIKE LOWER(?)", [$escaped]));
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.games', [
            'games' => $games,
            'search' => $search,
        ]);
    }
}
