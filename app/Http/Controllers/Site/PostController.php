<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function show(Request $request, Post $post)
    {
        $isLive = $post->status === 'published'
            && $post->published_at !== null
            && $post->published_at->lte(now());
        $canPreview = auth()->check() && auth()->user()->isEditor();

        abort_unless($isLive || $canPreview, 404);

        // Ek session e ekbar-i view count hobe (preview te hobe na)
        if ($isLive) {
            $key = 'viewed_post_' . $post->id;
            if (!$request->session()->has($key)) {
                $post->incrementViews();
                $request->session()->put($key, true);
            }
        }

        $post->load(['author', 'category', 'tags']);

        $comments = $post->comments()->approved()
            ->whereNull('parent_id')
            ->with(['author', 'replies' => fn ($q) => $q->approved()->with('author')])
            ->latest()->get();

        $related = Post::published()->with('author')
            ->where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->latest('published_at')->take(3)->get();

        return view('site.post', compact('post', 'comments', 'related'));
    }

    public function comment(Request $request, Post $post)
    {
        abort_unless($post->status === 'published', 404);

        $rules = [
            'body' => 'required|string|min:2|max:2000',
            'parent_id' => 'nullable|exists:comments,id',
        ];
        if (!auth()->check()) {
            $rules['guest_name'] = 'required|string|max:60';
        }
        $data = $request->validate($rules);

        $user = auth()->user();

        $post->comments()->create([
            'user_id' => $user?->id,
            'guest_name' => $user ? null : $data['guest_name'],
            'parent_id' => $data['parent_id'] ?? null,
            'body' => $data['body'],
            // Editor/admin er comment shorashori approved, baki der moderation
            'approved' => $user?->isEditor() ?? false,
        ]);

        return back()->with('success', $user?->isEditor()
            ? 'মন্তব্য যোগ হয়েছে।'
            : 'মন্তব্য জমা হয়েছে, অনুমোদনের পর দেখা যাবে।');
    }
}