<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::with(['author', 'category'])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->q, fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->latest()->paginate(15)->withQueryString();

        $counts = Post::selectRaw('status, count(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status');

        return view('admin.posts.index', compact('posts', 'counts'));
    }

    public function create()
    {
        return view('admin.posts.form', [
            'post'       => new Post(),
            'categories' => Category::orderBy('name')->get(),
            'tags'       => Tag::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['user_id'] = auth()->id();

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('posts', 'public');
        }
        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $post = Post::create($data);
        $post->tags()->sync($request->input('tags', []));

        return redirect()->route('admin.posts.index')->with('success', 'লেখা সংরক্ষিত হয়েছে।');
    }

    public function edit(Post $post)
    {
        return view('admin.posts.form', [
            'post'       => $post,
            'categories' => Category::orderBy('name')->get(),
            'tags'       => Tag::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Post $post)
    {
        $data = $this->validated($request, $post);

        if (empty($data['slug'])) {
            unset($data['slug']);
        }
        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('posts', 'public');
        }
        if ($data['status'] === 'published' && !$post->published_at && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $post->update($data);
        $post->tags()->sync($request->input('tags', []));

        return redirect()->route('admin.posts.index')->with('success', 'লেখা আপডেট হয়েছে।');
    }

    public function publish(Post $post)
    {
        $post->update([
            'status'       => 'published',
            'published_at' => $post->published_at ?? now(),
        ]);

        return back()->with('success', 'লেখা প্রকাশিত হয়েছে।');
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return back()->with('success', 'লেখা মোছা হয়েছে।');
    }

    private function validated(Request $request, ?Post $post = null): array
    {
        return $request->validate([
            'title'          => 'required|string|max:255',
            'slug'           => ['nullable', 'string', 'max:255',
                Rule::unique('posts', 'slug')->ignore($post?->id)],
            'category_id'    => 'required|exists:categories,id',
            'excerpt'        => 'nullable|string|max:500',
            'body'           => 'required|string',
            'status'         => 'required|in:draft,pending,published',
            'published_at'   => 'nullable|date',
            'featured_image' => 'nullable|image|max:2048',
        ]);
    }
}
