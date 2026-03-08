<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Redirect Admin to Admin Dashboard
        if ($user->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        $bookings = $user->customer ? $user->customer->bookings()->latest()->take(5)->get() : collect();
        $upcomingTrips = $user->customer ? $user->customer->bookings()->whereIn('status', ['confirmed', 'paid'])->where('tour_date', '>=', now())->count() : 0;
        $pastTrips = $user->customer ? $user->customer->bookings()->where('sort_date', '<', now())->count() : 0; // Simplified logic, ideally check status

        return view('dashboard', compact('bookings', 'upcomingTrips', 'pastTrips'));
    }
}
