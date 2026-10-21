<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;

class ArchiveController extends Controller
{
    private function base()
    {
        return Post::published()->with(['author', 'category'])->latest('published_at');
    }

    public function category(Request $request, Category $category)
{
    $query = $this->base()->where('category_id', $category->id);

    if ($request->filled('tag')) {
        $query->whereHas('tags', fn ($q) => $q->where('slug', $request->tag));
    }

    $sort = $request->sort === 'popular' ? 'popular' : 'latest';
    if ($sort === 'popular') {
        $query->reorder()->orderByDesc('views');
    }

    // Ei category r lekhay jei tag gulo ache, sheigulo chip hishebe dekhabe
    $tags = Tag::whereHas('posts', fn ($q) => $q->published()->where('category_id', $category->id))
        ->orderBy('name')->get();

    return view('site.archive', [
        'heading'   => $category->name,
        'sub'       => $category->description,
        'category'  => $category,
        'tags'      => $tags,
        'activeTag' => $request->tag,
        'sort'      => $sort,
        'posts'     => $query->paginate(10)->withQueryString(),
    ]);
}

    public function tag(Tag $tag)
    {
        return view('site.archive', [
            'heading' => '#' . $tag->name,
            'sub' => null,
            'posts' => $this->base()->whereHas('tags', fn ($q) => $q->where('tags.id', $tag->id))->paginate(12),
        ]);
    }

    public function author(User $user)
    {
        return view('site.archive', [
            'heading' => $user->name,
            'sub' => $user->bio,
            'posts' => $this->base()->where('user_id', $user->id)->paginate(12),
        ]);
    }

    public function search(Request $request)
    {
        $q = trim((string) $request->q);

        $posts = $this->base()
            ->when($q !== '', fn ($query) => $query->where(function ($w) use ($q) {
                $w->where('title', 'like', "%{$q}%")
                  ->orWhere('excerpt', 'like', "%{$q}%")
                  ->orWhere('body', 'like', "%{$q}%");
            }), fn ($query) => $query->whereRaw('1 = 0'))
            ->paginate(12)->withQueryString();

        return view('site.archive', [
            'heading' => $q !== '' ? "“{$q}” এর ফলাফল" : 'খুঁজুন',
            'sub' => null,
            'posts' => $posts,
        ]);
    }
}