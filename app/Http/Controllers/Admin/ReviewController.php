<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with(['customer', 'tour'])->latest()->paginate(10);
        return view('admin.reviews.index', compact('reviews'));
    }

    public function toggleStatus(Review $review)
    {
        $review->update(['is_approved' => !$review->is_approved]);
        return back()->with('success', 'Review status updated.');
    }

    public function destroy(Review $review)
    {
        $review->delete();
        return back()->with('success', 'Review deleted.');
    }
}
