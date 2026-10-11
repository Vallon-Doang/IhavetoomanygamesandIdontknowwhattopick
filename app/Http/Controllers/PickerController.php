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

        return redirect()
            ->route('picker.index')
            ->with('picked', [
                'id' => $result->game_id,
                'name' => $result->game_name,
                'filters' => $result->filters,
            ]);
    }
}