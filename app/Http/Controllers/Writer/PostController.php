<?php

namespace App\Http\Controllers\Writer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    /** লেখকের নিজের লেখার তালিকা */
    public function index(Request $request)
    {
        $posts = Post::with(['category'])
            ->where('user_id', auth()->id())
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest()->paginate(15)->withQueryString();

        $counts = Post::where('user_id', auth()->id())
            ->selectRaw('status, count(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status');

        return view('writer.posts.index', compact('posts', 'counts'));
    }

    public function create()
    {
        return view('writer.posts.form', [
            'post'       => new Post(),
            'categories' => Category::orderBy('name')->get(),
            'tags'       => Tag::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['user_id'] = auth()->id();

        // Writer শুধু draft বা pending করতে পারে, publish না
        if ($data['status'] === 'published') {
            $data['status'] = 'pending';
        }

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('posts', 'public');
        }

        $post = Post::create($data);
        $post->tags()->sync($request->input('tags', []));

        return redirect()->route('writer.posts.index')
            ->with('success', 'লেখা সংরক্ষিত হয়েছে। Admin অনুমোদনের পর প্রকাশিত হবে।');
    }

    public function edit(Post $post)
    {
        // শুধু নিজের লেখা edit করতে পারবে
        abort_unless($post->user_id === auth()->id(), 403);
        // প্রকাশিত লেখা writer edit করতে পারবে না
        abort_if($post->status === 'published', 403, 'প্রকাশিত লেখা সম্পাদনা করা যাবে না।');

        return view('writer.posts.form', [
            'post'       => $post,
            'categories' => Category::orderBy('name')->get(),
            'tags'       => Tag::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Post $post)
    {
        abort_unless($post->user_id === auth()->id(), 403);
        abort_if($post->status === 'published', 403, 'প্রকাশিত লেখা সম্পাদনা করা যাবে না।');

        $data = $this->validated($request, $post);

        if (empty($data['slug'])) {
            unset($data['slug']);
        }
        if ($data['status'] === 'published') {
            $data['status'] = 'pending';
        }
        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('posts', 'public');
        }

        $post->update($data);
        $post->tags()->sync($request->input('tags', []));

        return redirect()->route('writer.posts.index')
            ->with('success', 'লেখা আপডেট হয়েছে।');
    }

    public function destroy(Post $post)
    {
        abort_unless($post->user_id === auth()->id(), 403);
        abort_if($post->status === 'published', 403);

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
