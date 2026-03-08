<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Accommodation;

class AccommodationController extends Controller
{
    public function index()
    {
        $accommodations = Accommodation::with('parkLocation')->latest()->paginate(10);
        return view('admin.accommodations.index', compact('accommodations'));
    }

    public function create()
    {
        $parkLocations = \App\Models\ParkLocation::all();
        return view('admin.accommodations.create', compact('parkLocations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'category' => 'required|string',
            'description' => 'required|string',
            'park_location_id' => 'nullable|exists:park_locations,id',
            'total_rooms' => 'required|integer|min:0',
            'price_per_night_standard' => 'required|numeric|min:0',
            'city' => 'nullable|string',
            'country' => 'nullable|string',
            'amenities' => 'nullable|string', // Comma separated
            'main_image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('main_image')) {
            $path = $request->file('main_image')->store('accommodations', 'public');
            $validated['main_image'] = $path;
        }

        // Amenities processing
        if (!empty($validated['amenities'])) {
            $validated['amenities'] = array_map('trim', explode(',', $validated['amenities']));
        } else {
             $validated['amenities'] = [];
        }

        Accommodation::create($validated);

        return redirect()->route('admin.accommodations.index')->with('success', 'Accommodation created successfully.');
    }

    public function show(Accommodation $accommodation)
    {
        return view('admin.accommodations.show', compact('accommodation'));
    }

    public function edit(Accommodation $accommodation)
    {
        $parkLocations = \App\Models\ParkLocation::all();
        return view('admin.accommodations.edit', compact('accommodation', 'parkLocations'));
    }

    public function update(Request $request, Accommodation $accommodation)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'category' => 'required|string',
            'description' => 'required|string',
            'park_location_id' => 'nullable|exists:park_locations,id',
            'total_rooms' => 'required|integer|min:0',
            'price_per_night_standard' => 'required|numeric|min:0',
             'city' => 'nullable|string',
            'country' => 'nullable|string',
            'amenities' => 'nullable|string',
            'main_image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('main_image')) {
             if ($accommodation->main_image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($accommodation->main_image);
            }
            $path = $request->file('main_image')->store('accommodations', 'public');
            $validated['main_image'] = $path;
        }

        if (!empty($validated['amenities'])) {
            $validated['amenities'] = array_map('trim', explode(',', $validated['amenities']));
        } else {
             $validated['amenities'] = [];
        }

        $accommodation->update($validated);

        return redirect()->route('admin.accommodations.index')->with('success', 'Accommodation updated successfully.');
    }

    public function destroy(Accommodation $accommodation)
    {
        if ($accommodation->main_image) {
             \Illuminate\Support\Facades\Storage::disk('public')->delete($accommodation->main_image);
        }
        $accommodation->delete();
        return redirect()->route('admin.accommodations.index')->with('success', 'Accommodation deleted successfully.');
    }

    public function export()
    {
        $filename = 'accommodations-' . date('Y-m-d') . '.csv';
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];
        $columns = ['name', 'type', 'category', 'description', 'city', 'country', 'price_per_night_low_season', 'price_per_night_high_season', 'price_per_night_standard', 'total_rooms'];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach (Accommodation::all() as $row) {
                $data = [];
                foreach ($columns as $col) {
                    $data[] = $row->$col;
                }
                fputcsv($file, $data);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt|max:4096']);
        $file = $request->file('file');

        $handle = fopen($file->getPathname(), 'r');
        $headers = fgetcsv($handle);

        // Normalize headers
        $headerMap = array_map(function($h) { return \Illuminate\Support\Str::slug($h, '_'); }, $headers);

        $imported = 0;
        $errors = [];

        while (($data = fgetcsv($handle)) !== false) {
             if (count($data) !== count($headers)) continue;

             $row = array_combine($headerMap, $data);

             try {
                // Map CSV specific columns to DB columns
                $updateData = [
                    'name' => $row['name'],
                    'slug' => \Illuminate\Support\Str::slug($row['name']),
                    'type' => strtolower($row['type'] ?? 'lodge'),
                    'category' => strtolower($row['category'] ?? 'standard'),
                    'total_rooms' => (int)($row['rooms'] ?? 0),
                    'price_per_night_standard' => (float)($row['price_per_night_standard'] ?? 0),
                    'park_location_id' => !empty($row['park_location_id']) ? (int)$row['park_location_id'] : null,
                    'city' => $row['city'] ?? '',
                    'country' => $row['country'] ?? 'Kenya',
                    'description' => $row['description'] ?? "Accommodation at " . ($row['name'] ?? 'location'),
                ];

                // Amenities parsing
                $amenitiesVal = $row['amenities'] ?? '';
                if (str_contains($amenitiesVal, '[')) {
                     $cleaned = str_replace(['[', ']', "'", '"'], '', $amenitiesVal);
                     $updateData['amenities'] = array_map('trim', explode(',', $cleaned));
                } else {
                     $updateData['amenities'] = array_values(array_filter(array_map('trim', preg_split('/[;,]/', $amenitiesVal))));
                }

                Accommodation::updateOrCreate(
                    ['name' => $updateData['name']],
                    $updateData
                );
                $imported++;

             } catch (\Exception $e) {
                 \Illuminate\Support\Facades\Log::error("Accommodation import error: " . $e->getMessage());
                 $errors[] = $e->getMessage();
             }
        }
        fclose($handle);

        $msg = "Imported {$imported} accommodations successfully.";
        if (count($errors)) $msg .= " With " . count($errors) . " errors.";

        return back()->with('success', $msg);
    }
}
