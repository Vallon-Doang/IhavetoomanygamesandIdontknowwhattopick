<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Post;
use App\Models\SpinHistory;
use App\Models\SteamAccount;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    /**
     * MySQL-backed community and engagement metrics. Nothing here is faked.
     */
    public function index(): View
    {
        $since7 = now()->subDays(7);
        $since30 = now()->subDays(30);

        $totalPosts = Post::withTrashed()->count();

        $dailyPosts = Post::query()
            ->withTrashed()
            ->where('created_at', '>=', now()->subDays(13)->startOfDay())
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day');

        $postSeries = collect(range(13, 0))->map(function (int $offset) use ($dailyPosts): array {
            $date = now()->subDays($offset);
            $key = $date->toDateString();

            return [
                'label' => $date->format('M j'),
                'count' => (int) ($dailyPosts[$key] ?? 0),
            ];
        });

        return view('admin.analytics', [
            'totals' => [
                'users' => User::count(),
                'members' => User::where('role', User::ROLE_MEMBER)->count(),
                'admins' => User::where('role', User::ROLE_ADMIN)->count(),
                'posts' => $totalPosts,
                'hiddenPosts' => Post::where('is_hidden', true)->count(),
                'announcements' => Announcement::count(),
                'publishedAnnouncements' => Announcement::published()->count(),
                'spins' => SpinHistory::count(),
                'linkedSteam' => SteamAccount::count(),
            ],
            'recent' => [
                'newUsers' => User::where('created_at', '>=', $since7)->count(),
                'newPosts' => Post::withTrashed()->where('created_at', '>=', $since7)->count(),
                'spins' => SpinHistory::where('created_at', '>=', $since7)->count(),
                'newUsers30' => User::where('created_at', '>=', $since30)->count(),
                'newPosts30' => Post::withTrashed()->where('created_at', '>=', $since30)->count(),
                'spins30' => SpinHistory::where('created_at', '>=', $since30)->count(),
            ],
            'postSeries' => $postSeries,
            'maxPosts' => max(1, (int) $postSeries->max('count')),
            'topGames' => SpinHistory::query()
                ->selectRaw('game_name, COUNT(*) as picks')
                ->groupBy('game_name')
                ->orderByDesc('picks')
                ->limit(5)
                ->get(),
            'topPosters' => User::query()
                ->whereHas('posts', fn ($query) => $query->published())
                ->withCount(['posts' => fn ($query) => $query->published()])
                ->orderByDesc('posts_count')
                ->limit(5)
                ->get(),
            'generatedAt' => Carbon::now(),
        ]);
    }
}
