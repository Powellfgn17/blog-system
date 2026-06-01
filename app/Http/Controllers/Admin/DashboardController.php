<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Report;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'users' => User::count(),
            'posts_blog' => Post::blog()->count(),
            'posts_community' => Post::community()->count(),
            'comments' => Comment::count(),
            'reports_pending' => Report::pending()->count(),
            'users_blocked' => User::where('is_blocked', true)->count(),
        ];

        $flaggedItems = Report::query()
            ->pending()
            ->select('reportable_type', 'reportable_id', \Illuminate\Support\Facades\DB::raw('COUNT(*) as reports_count'))
            ->groupBy('reportable_type', 'reportable_id')
            ->orderByDesc('reports_count')
            ->get()
            ->map(function ($row) {
                // Determine model
                $modelClass = str_ends_with($row->reportable_type, 'Post') || $row->reportable_type === 'post' 
                    ? Post::class 
                    : Comment::class;
                $model = $modelClass::with('user')->find($row->reportable_id);

                return [
                    'reportable' => $model,
                    'reports_count' => (int) $row->reports_count,
                    'reports' => Report::query()
                        ->pending()
                        ->where('reportable_type', $row->reportable_type)
                        ->where('reportable_id', $row->reportable_id)
                        ->with('user')
                        ->get(),
                ];
            })
            ->filter(fn ($item) => $item['reportable'] !== null);

        // Aggregate flagged users based on the flagged items
        $userFlags = [];
        foreach ($flaggedItems as $item) {
            $userId = $item['reportable']->user_id;
            if (!isset($userFlags[$userId])) {
                $userFlags[$userId] = [
                    'user' => $item['reportable']->user,
                    'reports_count' => 0,
                ];
            }
            $userFlags[$userId]['reports_count'] += $item['reports_count'];
        }
        
        $flaggedUsers = collect($userFlags)->sortByDesc('reports_count')->take(5)->values();

        // Limit flagged items for the dashboard queue
        $flaggedItems = $flaggedItems->take(5);

        return view('admin.dashboard', compact('stats', 'flaggedItems', 'flaggedUsers'));
    }
}
