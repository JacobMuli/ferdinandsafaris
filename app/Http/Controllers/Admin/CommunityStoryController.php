<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommunityStory;
use Illuminate\Http\Request;

class CommunityStoryController extends Controller
{
    public function index()
    {
        $stories = CommunityStory::with('user')->latest()->paginate(20);
        return view('admin.community.index', compact('stories'));
    }

    public function approve(CommunityStory $story)
    {
        $story->update(['is_approved' => true]);
        return back()->with('success', 'Story approved successfully.');
    }

    public function reject(CommunityStory $story)
    {
        $story->update(['is_approved' => false]);
        return back()->with('success', 'Story unapproved.');
    }

    public function destroy(CommunityStory $story)
    {
        $story->delete();
        return back()->with('success', 'Story deleted.');
    }

    public function toggleFeatured(CommunityStory $story)
    {
        $story->update(['is_featured' => !$story->is_featured]);
        return back()->with('success', 'Featured status updated.');
    }
}
