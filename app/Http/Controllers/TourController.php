<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use Illuminate\Http\Request;

class TourController extends Controller
{
    public function index(Request $request)
    {
        $query = Tour::active()->available()->with(['parkLocations', 'approvedReviews' => function ($q) {
            $q->latest()->limit(3);
        }]);

        // Filters
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->filled('difficulty') && $request->difficulty !== 'all') {
            $query->where('difficulty_level', $request->difficulty);
        }

        if ($request->filled('duration_min')) {
            $query->where('duration_days', '>=', $request->duration_min);
        }

        if ($request->filled('duration_max')) {
            $query->where('duration_days', '<=', $request->duration_max);
        }

        if ($request->filled('price_max')) {
            $query->where('price_per_person', '<=', $request->price_max);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort', 'popular');
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('price_per_person', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price_per_person', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating', 'desc');
                break;
            case 'duration':
                $query->orderBy('duration_days', 'asc');
                break;
            default: // popular
                $query->orderBy('views', 'desc')->orderBy('rating', 'desc');
        }

        $tours = $query->paginate(12);

        $categories = Tour::active()->distinct()->pluck('category');
        $maxPrice = Tour::active()->max('price_per_person') ?? 1000;

        return view('tours.index', compact('tours', 'categories', 'maxPrice'));
    }

    public function show(Tour $tour)
    {
        $tour->load(['approvedReviews.customer']);
        $tour->incrementViews();

        $relatedTours = Tour::active()
            ->available()
            ->where('id', '!=', $tour->id)
            ->where('category', $tour->category)
            ->orderBy('rating', 'desc')
            ->limit(4)
            ->get();

        return view('tours.show', compact('tour', 'relatedTours'));
    }

    public function checkAvailability(Request $request, Tour $tour)
    {
        $request->validate([
            'date' => 'required|date|after:today',
        ]);

        $availableSpots = $tour->getAvailableSpots($request->date);

        return response()->json([
            'available' => $availableSpots > 0,
            'available_spots' => $availableSpots,
            'max_participants' => $tour->max_participants,
        ]);
    }

    public function calculatePrice(Request $request, Tour $tour)
    {
        $request->validate([
            'customer_type' => 'required|in:individual,family,group,corporate',
            'adults_count' => 'required|integer|min:1',
            'children_count' => 'integer|min:0',
            'accommodation_id' => 'nullable|integer|exists:accommodations,id',
            'vehicle_type_id' => 'nullable|integer|exists:vehicle_types,id',
            'resident_status' => 'nullable|in:resident,non_resident,citizen',
        ]);

        $options = [
            'accommodation_id' => $request->accommodation_id,
            'vehicle_type_id' => $request->vehicle_type_id,
            'resident_status' => $request->resident_status,
        ];

        $pricing = $tour->calculatePrice(
            $request->customer_type,
            $request->adults_count,
            $request->children_count ?? 0,
            $options
        );

        return response()->json($pricing);
    }

    public function featured()
    {
        $tours = Tour::active()
            ->available()
            ->featured()
            ->with(['parkLocations', 'approvedReviews' => function ($q) {
                $q->latest()->limit(3);
            }])
            ->orderBy('rating', 'desc')
            ->get();

        return view('tours.featured', compact('tours'));
    }

    public function category($category)
    {
        $tours = Tour::active()
            ->available()
            ->where('category', $category)
            ->with(['parkLocations', 'approvedReviews' => function ($q) {
                $q->latest()->limit(3);
            }])
            ->orderBy('rating', 'desc')
            ->paginate(12);

        return view('tours.category', compact('tours', 'category'));
    }
}