<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Category, Comment, Post, User};

class DashboardController extends Controller
{
    public function index()
    {
        // শেষ ১৪ দিনের চার্ট
        $days = collect(range(13, 0))->map(fn ($i) => now()->subDays($i)->startOfDay());

        $postsByDay = Post::where('created_at', '>=', $days->first())->get()
            ->groupBy(fn ($p) => $p->created_at->format('Y-m-d'));
        $commentsByDay = Comment::where('created_at', '>=', $days->first())->get()
            ->groupBy(fn ($c) => $c->created_at->format('Y-m-d'));

        $chart = [
            'labels'   => $days->map(fn ($d) => $d->format('d/m'))->all(),
            'posts'    => $days->map(fn ($d) => $postsByDay->get($d->format('Y-m-d'), collect())->count())->all(),
            'comments' => $days->map(fn ($d) => $commentsByDay->get($d->format('Y-m-d'), collect())->count())->all(),
        ];

        return view('admin.dashboard', [
            'totalPosts'      => Post::count(),
            'publishedPosts'  => Post::where('status', 'published')->count(),
            'totalViews'      => (int) Post::sum('views'),
            'pendingCount'    => Post::where('status', 'pending')->count(),
            'pendingComments' => Comment::where('approved', false)->count(),
            'usersCount'      => User::count(),
            'newThisWeek'     => Post::where('created_at', '>=', now()->subDays(7))->count(),
            'chart'           => $chart,
            'topCategories'   => Category::withCount(['posts' => fn ($q) => $q->where('status', 'published')])
                                    ->orderByDesc('posts_count')->take(5)->get(),
            'pendingPosts'    => Post::with('author')->where('status', 'pending')->latest()->take(5)->get(),
            'recentPosts'     => Post::with(['author', 'category'])->latest()->take(6)->get(),
            'popular'         => Post::published()->popular()->take(5)->get(),
            'recentComments'  => Comment::with('post')->latest()->take(5)->get(),
        ]);
    }
}