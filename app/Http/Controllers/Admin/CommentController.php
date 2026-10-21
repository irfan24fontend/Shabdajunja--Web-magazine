<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $comments = Comment::with(['post', 'author'])
            ->when($request->filter === 'pending', fn ($q) => $q->where('approved', false))
            ->when($request->filter === 'approved', fn ($q) => $q->where('approved', true))
            ->latest()->paginate(20)->withQueryString();

        return view('admin.comments.index', compact('comments'));
    }

    public function approve(Comment $comment)
    {
        $comment->update(['approved' => true]);

        return back()->with('success', 'মন্তব্য অনুমোদিত হয়েছে।');
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();

        return back()->with('success', 'মন্তব্য মোছা হয়েছে।');
    }
}