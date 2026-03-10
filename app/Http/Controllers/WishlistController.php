<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use App\Models\TourLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $ip = request()->ip();

        $query = Tour::active()->available();

        if ($userId) {
            $query->whereHas('likes', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        } else {
            $query->whereHas('likes', function ($q) use ($ip) {
                $q->where('ip_address', $ip)->whereNull('user_id');
            });
        }

        $wishlistItems = $query->withCount('likes')->paginate(12);

        return view('dashboard.wishlist', compact('wishlistItems'));
    }

    public function remove(Tour $tour)
    {
        $userId = Auth::id();
        $ip = request()->ip();

        if ($userId) {
            $tour->likes()->where('user_id', $userId)->delete();
        } else {
            $tour->likes()->where('ip_address', $ip)->whereNull('user_id')->delete();
        }

        return back()->with('success', 'Tour removed from wishlist.');
    }
}
