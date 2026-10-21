<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        return view('admin.categories.index', [
            'categories' => Category::withCount('posts')->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return redirect()->route('admin.categories.index');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
            'description' => 'nullable|string|max:500',
        ]);
        Category::create($data);

        return back()->with('success', 'ক্যাটাগরি যোগ হয়েছে।');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('categories', 'name')->ignore($category->id)],
            'slug' => ['required', 'string', 'max:200', Rule::unique('categories', 'slug')->ignore($category->id)],
            'description' => 'nullable|string|max:500',
        ]);
        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'ক্যাটাগরি আপডেট হয়েছে।');
    }

    public function destroy(Category $category)
    {
        if ($category->posts()->withTrashed()->exists()) {
            return back()->with('error', 'এই ক্যাটাগরিতে লেখা আছে, তাই মোছা যাবে না।');
        }
        $category->delete();

        return back()->with('success', 'ক্যাটাগরি মোছা হয়েছে।');
    }
}