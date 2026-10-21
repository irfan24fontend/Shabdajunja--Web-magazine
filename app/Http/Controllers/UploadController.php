<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    /**
     * Editor-এর ভেতর থেকে ছবি আপলোড।
     * Returns JSON: { url: "..." }
     */
    public function image(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|max:3072|mimes:jpg,jpeg,png,webp,gif',
        ]);

        $path = $request->file('image')->store('uploads', 'public');

        return response()->json([
            'url' => asset('storage/' . $path),
        ]);
    }
}
