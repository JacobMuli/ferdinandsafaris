<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\ParkLocation;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function autocomplete(Request $request)
    {
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $results = [];

        // Search Tours
        $tours = Tour::where('name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->orWhere('location', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get(['id', 'name', 'location', 'category', 'slug']);

        foreach ($tours as $tour) {
            $results[] = [
                'type' => 'tour',
                'id' => $tour->id,
                'title' => $tour->name,
                'subtitle' => $tour->location,
                'category' => ucfirst($tour->category),
                'url' => route('tours.show', $tour->slug),
                'icon' => 'fa-route'
            ];
        }

        // Search Park Locations
        $parks = ParkLocation::where('name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get(['id', 'name', 'description']);

        foreach ($parks as $park) {
            $results[] = [
                'type' => 'park',
                'id' => $park->id,
                'title' => $park->name,
                'subtitle' => 'National Park',
                'category' => 'Park',
                'url' => route('tours.index', ['search' => $park->name]),
                'icon' => 'fa-tree'
            ];
        }

        // Search by unique locations from tours
        $locations = Tour::select('location')
            ->where('location', 'LIKE', "%{$query}%")
            ->distinct()
            ->limit(3)
            ->pluck('location');

        foreach ($locations as $location) {
            $results[] = [
                'type' => 'location',
                'title' => $location,
                'subtitle' => 'Browse tours in this location',
                'category' => 'Location',
                'url' => route('tours.index', ['search' => $location]),
                'icon' => 'fa-map-marker-alt'
            ];
        }

        return response()->json($results);
    }
}
