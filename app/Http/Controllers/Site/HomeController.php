<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;

class HomeController extends Controller
{
    public function index()
    {
        $featured = Post::published()->with(['author', 'category'])
            ->latest('published_at')->first();

        $latest = Post::published()->with(['author', 'category'])
            ->when($featured, fn ($q) => $q->where('id', '!=', $featured->id))
            ->latest('published_at')
            ->paginate(9);

        $popular = Post::published()->popular()->take(5)->get();

        // Prottek category r latest 4 ta lekha
        $sections = Category::orderBy('id')->get()
            ->map(function ($c) {
                $c->setRelation('latestPosts',
                    $c->posts()->published()->with('author')->latest('published_at')->take(4)->get());
                return $c;
            })
            ->filter(fn ($c) => $c->latestPosts->isNotEmpty());

        return view('site.home', compact('featured', 'latest', 'popular', 'sections'));
    }
}