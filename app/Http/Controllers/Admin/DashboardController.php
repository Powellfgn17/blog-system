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

        $recentPosts = Post::query()
            ->with('user')
            ->recent()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentPosts'));
    }
}
