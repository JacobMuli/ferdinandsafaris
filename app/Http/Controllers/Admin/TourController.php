<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\ParkLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TourController extends Controller
{
    public function index(Request $request)
    {
        $query = Tour::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
        }

        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $tours = $query->withCount('bookings')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $categories = Tour::distinct()->pluck('category');

        return view('admin.tours.index', compact('tours', 'categories'));
    }

    public function create()
    {
        $locations = ParkLocation::select('id', 'name')->orderBy('name')->get();
        return view('admin.tours.create', compact('locations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'highlights' => 'nullable|string',
            'itinerary' => 'nullable|array',
            'itinerary.*.day' => 'required_with:itinerary|integer',
            'itinerary.*.title' => 'required_with:itinerary|string|max:255',
            'itinerary.*.description' => 'required_with:itinerary|string',
            'duration_days' => 'required|integer|min:1',
            'difficulty_level' => 'required|in:easy,moderate,challenging',
            'price_per_person' => 'required|numeric|min:0',
            'child_price' => 'nullable|numeric|min:0',
            'group_discount_percentage' => 'nullable|numeric|min:0|max:100',
            'min_group_size' => 'required|integer|min:1',
            'corporate_discount_percentage' => 'nullable|numeric|min:0|max:100',
            'max_participants' => 'required|integer|min:1',
            'category' => 'required|string',
            'location' => 'required|string',
            'included_items' => 'nullable|string', // Changed to string for textarea input
            'excluded_items' => 'nullable|string', // Changed to string for textarea input
            'requirements' => 'nullable|string',   // Changed to string for textarea input
            'featured_image' => 'nullable|image|max:2048',
            'gallery_images.*' => 'nullable|image|max:2048',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'available_from' => 'nullable|date',
            'available_to' => 'nullable|date|after_or_equal:available_from',
            'park_locations' => 'nullable|array',
            'park_locations.*' => 'exists:parks_locations,id',
        ]);

        // Helper to convert newline string to array
        $toArray = function ($value) {
            if (empty($value)) return null;
            return array_values(array_filter(array_map('trim', explode("\n", $value))));
        };

        $validated['highlights'] = $toArray($validated['highlights'] ?? '');
        $validated['included_items'] = $toArray($validated['included_items'] ?? '');
        $validated['excluded_items'] = $toArray($validated['excluded_items'] ?? '');
        $validated['requirements'] = $toArray($validated['requirements'] ?? '');

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('tours', 'public');
        }

        // Handle gallery images upload
        if ($request->hasFile('gallery_images')) {
            $galleryPaths = [];
            foreach ($request->file('gallery_images') as $image) {
                $galleryPaths[] = $image->store('tours/gallery', 'public');
            }
            $validated['gallery_images'] = $galleryPaths;
        }

        $validated['slug'] = Str::slug($validated['name']);

        $tour = Tour::create($validated);

        if (isset($validated['park_locations'])) {
            $tour->parkLocations()->sync($validated['park_locations']);
        }

        return redirect()->route('admin.tours.index')
            ->with('success', 'Tour created successfully!');
    }

    public function show(Tour $tour)
    {
        $tour->load(['bookings' => function ($q) {
            $q->latest()->limit(10);
        }]);

        $stats = [
            'total_bookings' => $tour->bookings()->count(),
            'revenue' => $tour->bookings()->whereIn('payment_status', ['paid'])->sum('total_amount'),
            'avg_rating' => $tour->rating,
            'total_reviews' => $tour->reviews_count,
        ];

        return view('admin.tours.show', compact('tour', 'stats'));
    }

    public function edit(Tour $tour)
    {
        $locations = ParkLocation::select('id', 'name')->orderBy('name')->get();
        $tour->load('parkLocations'); // Eager load current locations
        return view('admin.tours.edit', compact('tour', 'locations'));
    }

    public function update(Request $request, Tour $tour)
    {
        \Illuminate\Support\Facades\Log::info('Tour Update Request:', $request->all());

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'highlights' => 'nullable|string',
            'itinerary' => 'nullable|array',
            'itinerary.*.day' => 'required_with:itinerary|integer',
            'itinerary.*.title' => 'required_with:itinerary|string|max:255',
            'itinerary.*.description' => 'required_with:itinerary|string',
            'duration_days' => 'required|integer|min:1',
            'difficulty_level' => 'required|in:easy,moderate,challenging',
            'price_per_person' => 'required|numeric|min:0',
            'child_price' => 'nullable|numeric|min:0',
            'group_discount_percentage' => 'nullable|numeric|min:0|max:100',
            'min_group_size' => 'required|integer|min:1',
            'corporate_discount_percentage' => 'nullable|numeric|min:0|max:100',
            'max_participants' => 'required|integer|min:1',
            'category' => 'required|string',
            'location' => 'required|string',
            'included_items' => 'nullable|string', // Changed to string for textarea input
            'excluded_items' => 'nullable|string', // Changed to string for textarea input
            'requirements' => 'nullable|string',   // Changed to string for textarea input
            'featured_image' => 'nullable|image|max:2048',
            'gallery_images.*' => 'nullable|image|max:2048',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'available_from' => 'nullable|date',
            'available_to' => 'nullable|date|after_or_equal:available_from',
            'park_locations' => 'nullable|array',
            'park_locations.*' => 'exists:parks_locations,id',
        ]);

        // Helper to convert newline string to array
        $toArray = function ($value) {
            if (empty($value)) return null;
            return array_values(array_filter(array_map('trim', explode("\n", $value))));
        };

        $validated['highlights'] = $toArray($validated['highlights'] ?? '');
        $validated['included_items'] = $toArray($validated['included_items'] ?? '');
        $validated['excluded_items'] = $toArray($validated['excluded_items'] ?? '');
        $validated['requirements'] = $toArray($validated['requirements'] ?? '');

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            // Delete old image
            if ($tour->featured_image) {
                Storage::disk('public')->delete($tour->featured_image);
            }
            $validated['featured_image'] = $request->file('featured_image')->store('tours', 'public');
        }

        // Handle gallery images upload
        if ($request->hasFile('gallery_images')) {
            $galleryPaths = $tour->gallery_images ?? [];
            foreach ($request->file('gallery_images') as $image) {
                $galleryPaths[] = $image->store('tours/gallery', 'public');
            }
            $validated['gallery_images'] = $galleryPaths;
        }

        if ($validated['name'] !== $tour->name) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $tour->update($validated);

        // Always sync park locations. If none selected (empty/null), sync empty array to clear.
        $tour->parkLocations()->sync($request->input('park_locations', []));

        return redirect()->route('admin.tours.show', $tour)
            ->with('success', 'Tour updated successfully!');
    }

    public function destroy(Tour $tour)
    {
        // Delete images
        if ($tour->featured_image) {
            Storage::disk('public')->delete($tour->featured_image);
        }

        if ($tour->gallery_images) {
            foreach ($tour->gallery_images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $tour->delete();

        return redirect()->route('admin.tours.index')
            ->with('success', 'Tour deleted successfully!');
    }

    public function toggleStatus(Tour $tour)
    {
        $tour->update(['is_active' => !$tour->is_active]);

        return back()->with('success', 'Tour status updated successfully!');
    }

    public function toggleFeatured(Tour $tour)
    {
        $tour->update(['is_featured' => !$tour->is_featured]);

        return back()->with('success', 'Tour featured status updated successfully!');
    }

    public function deleteGalleryImage(Tour $tour, $index)
    {
        $gallery = $tour->gallery_images ?? [];

        if (isset($gallery[$index])) {
            Storage::disk('public')->delete($gallery[$index]);
            unset($gallery[$index]);
            $tour->update(['gallery_images' => array_values($gallery)]);
        }

        return back()->with('success', 'Image deleted successfully!');
    }

    public function export()
    {
        $filename = 'tours-' . date('Y-m-d') . '.csv';

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = [
            'name', 'slug', 'description', 'highlights', 'itinerary',
            'duration_days', 'difficulty_level', 'price_per_person', 'child_price',
            'group_discount_percentage', 'min_group_size', 'corporate_discount_percentage',
            'max_participants', 'category', 'location', 'included_items',
            'excluded_items', 'requirements', 'is_featured', 'is_active', 'available_from', 'available_to'
        ];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns); // Headers

            foreach (Tour::all() as $tour) {
                $row = [];
                foreach ($columns as $column) {
                    $value = $tour->$column;
                    if (is_array($value)) {
                        $value = json_encode($value);
                    }
                    $row[] = $value;
                }
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request, \App\Services\TourImportService $importer)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:4096',
        ]);

        try {
            $file = $request->file('file');
            $result = $importer->importCsv($file->getPathname());

            $count = $result['count'];
            $errors = $result['errors'];

            if ($count === 0 && count($errors) > 0) {
                 return back()->with('error', 'Import failed: ' . implode(', ', array_slice($errors, 0, 3)));
            }

            $msg = "Imported {$count} tours successfully!";
            if (count($errors) > 0) {
                $msg .= " With " . count($errors) . " errors (check logs).";
                \Illuminate\Support\Facades\Log::warning('Tour import errors:', $errors);
            }

            return back()->with('success', $msg);

        } catch (\Exception $e) {
            return back()->with('error', 'Error during import: ' . $e->getMessage());
        }
    }
}