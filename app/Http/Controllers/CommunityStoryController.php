<?php

namespace App\Http\Controllers;

use App\Models\CommunityStory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CommunityStoryController extends Controller
{
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in to share your story.'
            ], 401);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('community/stories', 'public');
            }
        }

        CommunityStory::create([
            'user_id' => Auth::id(),
            'name' => Auth::user()->name,
            'email' => Auth::user()->email,
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'images' => $imagePaths,
            'is_approved' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Your story has been submitted and is awaiting approval!'
        ]);
    }
}
