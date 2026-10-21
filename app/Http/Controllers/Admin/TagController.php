<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TagController extends Controller
{
    public function index()
    {
        return view('admin.tags.index', [
            'tags' => Tag::withCount('posts')->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return redirect()->route('admin.tags.index');
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:60|unique:tags,name']);
        Tag::create($data);

        return back()->with('success', 'ট্যাগ যোগ হয়েছে।');
    }

    public function edit(Tag $tag)
    {
        return view('admin.tags.edit', compact('tag'));
    }

    public function update(Request $request, Tag $tag)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:60', Rule::unique('tags', 'name')->ignore($tag->id)],
            'slug' => ['required', 'string', 'max:200', Rule::unique('tags', 'slug')->ignore($tag->id)],
        ]);
        $tag->update($data);

        return redirect()->route('admin.tags.index')->with('success', 'ট্যাগ আপডেট হয়েছে।');
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();

        return back()->with('success', 'ট্যাগ মোছা হয়েছে।');
    }
}