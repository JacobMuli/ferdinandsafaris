<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ParkLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ParkLocationController extends Controller
{
    public function index()
    {
        $locations = ParkLocation::latest()->paginate(10);
        return view('admin.locations.index', compact('locations'));
    }

    public function create()
    {
        return view('admin.locations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'country' => 'required|string',
            'description' => 'required|string',
            'featured_image' => 'nullable|image|max:4096',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('locations', 'public');
            $validated['featured_image'] = $path;
        }

        ParkLocation::create($validated);

        return redirect()->route('admin.locations.index')
            ->with('success', 'Location created successfully.');
    }

    public function edit(ParkLocation $location)
    {
        return view('admin.locations.edit', compact('location'));
    }

    public function update(Request $request, ParkLocation $location)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'country' => 'required|string',
            'description' => 'required|string',
            'featured_image' => 'nullable|image|max:4096',
        ]);

        if ($request->name !== $location->name) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        if ($request->hasFile('featured_image')) {
             if ($location->featured_image && !Str::startsWith($location->featured_image, ['http://', 'https://'])) {
                Storage::disk('public')->delete($location->featured_image);
            }
            $path = $request->file('featured_image')->store('locations', 'public');
            $validated['featured_image'] = $path;
        }

        $location->update($validated);

        return redirect()->route('admin.locations.index')
            ->with('success', 'Location updated successfully.');
    }

    public function destroy(ParkLocation $location)
    {
        if ($location->featured_image && !Str::startsWith($location->featured_image, ['http://', 'https://'])) {
            Storage::disk('public')->delete($location->featured_image);
        }
        $location->delete();

        return redirect()->route('admin.locations.index')
            ->with('success', 'Location deleted successfully.');
    }

    public function export()
    {
        $filename = 'locations-' . date('Y-m-d') . '.csv';
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];
        $columns = ['name', 'slug', 'description', 'type', 'country', 'featured_image'];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach (ParkLocation::all() as $row) {
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
        $headers = fgetcsv($handle); // Get headers

        // Normalize headers to slug format for easier mapping
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
                    'slug' => $row['slug'] ?? \Illuminate\Support\Str::slug($row['name']),
                    'type' => $row['type'] ?? 'National Park',
                    'country' => $row['country'] ?? 'Kenya',
                    'description' => $row['description'] ?? '',
                    'region' => $row['region'] ?? null,
                    'latitude' =>  is_numeric($row['latitude'] ?? null) ? $row['latitude'] : null,
                    'longitude' => is_numeric($row['longitude'] ?? null) ? $row['longitude'] : null,
                    'area_size_km2' => is_numeric($row['area_size_km2'] ?? null) ? $row['area_size_km2'] : null,
                    // Strip non-numeric chars for fees (e.g. "200(Peak)" -> 200)
                    'entry_fee_adult' => (float) preg_replace('/[^0-9.]/', '', $row['adult_fee_day'] ?? ($row['entry_fee_adult'] ?? 0)),
                    'entry_fee_child' => (float) preg_replace('/[^0-9.]/', '', $row['child_fee_day'] ?? ($row['entry_fee_child'] ?? 0)),
                    'featured_image' => $row['featured_image_url'] ?? null,
                ];

                // Parse Arrays
                $arrayFields = ['wildlife', 'activities', 'highlights'];
                foreach ($arrayFields as $field) {
                    $val = $row[$field] ?? '';
                    // Handle ['A', 'B'] style or semicolon separated
                    if (str_contains($val, '[')) {
                         // Simple cleanup of [' '] chars
                         $cleaned = str_replace(['[', ']', "'", '"'], '', $val);
                         $updateData[$field] = array_map('trim', explode(',', $cleaned));
                    } else {
                         $updateData[$field] = array_values(array_filter(array_map('trim', preg_split('/[;,]/', $val))));
                    }
                }

                ParkLocation::updateOrCreate(
                    ['slug' => $updateData['slug']],
                    $updateData
                );
                $imported++;

             } catch (\Exception $e) {
                 \Illuminate\Support\Facades\Log::error("Available import error: " . $e->getMessage());
                 $errors[] = $e->getMessage();
             }
        }
        fclose($handle);

        $msg = "Imported {$imported} locations successfully.";
        if (count($errors)) $msg .= " With " . count($errors) . " errors.";

        return back()->with('success', $msg);
    }
}
