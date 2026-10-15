<?php

namespace App\Http\Controllers;

use App\Http\Requests\SpinRequest;
use App\Services\GamePickerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PickerController extends Controller
{
    public function __construct(private readonly GamePickerService $picker) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        return view('picker.index', [
            'eligibleCount' => $this->picker->eligibleQuery($user)->count(),
        ]);
    }

    public function spin(SpinRequest $request): RedirectResponse
    {
        $filters = $request->filters();
        $result = $this->picker->pick($request->user(), $filters);

        if (! $result) {
            return back()
                ->withInput()
                ->with('error', 'No eligible game matched your filters. Adjust the filters or include more games in your library.');
        }

        $games = $this->picker->eligibleGames($request->user(), $filters);
        $winnerGame = $games->firstWhere('id', $result->game_id);
        $reel = $this->picker->buildReel(
            $games,
            (int) $result->game_id,
            $result->game_name,
            $winnerGame?->cover_url,
        );

        return redirect()
            ->route('picker.index')
            ->with('picked', [
                'id' => $result->game_id,
                'name' => $result->game_name,
                'cover' => $winnerGame?->cover_url,
                'filters' => $result->filters,
                'reel' => $reel['items'],
                'winner_index' => $reel['winner_index'],
            ]);
    }
}