<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleType;
use Illuminate\Http\Request;

class VehicleTypeController extends Controller
{
    public function index()
    {
        $vehicleTypes = VehicleType::latest()->paginate(10);
        return view('admin.vehicle_types.index', compact('vehicleTypes'));
    }

    public function create()
    {
        return view('admin.vehicle_types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:50',
            'manufacturer' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'default_capacity' => 'required|integer|min:1',
            'features' => 'nullable|array',
            'base_daily_rate' => 'nullable|numeric|min:0',
            'base_cost_per_km' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        VehicleType::create($validated);

        return redirect()->route('admin.vehicle_types.index')
            ->with('success', 'Vehicle Type created successfully.');
    }

    public function edit(VehicleType $vehicleType)
    {
        return view('admin.vehicle_types.edit', compact('vehicleType'));
    }

    public function update(Request $request, VehicleType $vehicleType)
    {
         $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:50',
            'manufacturer' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'default_capacity' => 'required|integer|min:1',
            'features' => 'nullable|array',
            'base_daily_rate' => 'nullable|numeric|min:0',
            'base_cost_per_km' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $vehicleType->update($validated);

        return redirect()->route('admin.vehicle_types.index')
            ->with('success', 'Vehicle Type updated successfully.');
    }

    public function destroy(VehicleType $vehicleType)
    {
        if ($vehicleType->vehicles()->exists()) {
            return back()->with('error', 'Cannot delete Vehicle Type because it has associated vehicles. Please reassign or delete them first.');
        }

        $vehicleType->delete();
        return back()->with('success', 'Vehicle Type deleted successfully.');
    }

    public function export()
    {
        $filename = 'vehicle-types-' . date('Y-m-d') . '.csv';
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];
        $columns = ['name', 'category', 'manufacturer', 'model', 'default_capacity', 'features', 'base_daily_rate', 'base_cost_per_km', 'description'];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach (VehicleType::all() as $row) {
                $data = [];
                foreach ($columns as $col) {
                    $value = $row->$col;
                    if ($col === 'features' && is_array($value)) {
                         $value = json_encode($value);
                    }
                    $data[] = $value;
                }
                fputcsv($file, $data);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt|max:2048']);
        $file = $request->file('file');
        $handle = fopen($file->getPathname(), 'r');
        fgetcsv($handle); // Skip header

        $imported = 0;
        $columns = ['name', 'category', 'manufacturer', 'model', 'default_capacity', 'features', 'base_daily_rate', 'base_cost_per_km', 'description'];

        while (($data = fgetcsv($handle)) !== false) {
             if (count($data) !== count($columns)) continue;
             $row = array_combine($columns, $data);

             if (!VehicleType::where('name', $row['name'])->exists()) {
                 // Handle JSON features
                 if (isset($row['features']) && !empty($row['features'])) {
                     $decoded = json_decode($row['features'], true);
                     $row['features'] = $decoded ?: [];
                 }

                 VehicleType::create($row);
                 $imported++;
             }
        }
        fclose($handle);

        return back()->with('success', "Imported {$imported} vehicle types successfully.");
    }
}
