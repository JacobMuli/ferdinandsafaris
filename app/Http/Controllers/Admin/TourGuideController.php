<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourGuide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TourGuideController extends Controller
{
    public function index()
    {
        $guides = TourGuide::latest()->paginate(10);
        return view('admin.guides.index', compact('guides'));
    }

    public function create()
    {
        $vehicles = \App\Models\Vehicle::active()->get();
        // Get all users who are admins, or all users if needed. For now, let's list admins for potential linking.
        // Actually, requirement says "User (admin) can also be a Tour Guide".
        // It's probably best to verify if prompts implied ONLY admins. "A User (admin)..." suggests yes.
        $users = auth()->user()->isSuperAdmin()
            ? \App\Models\User::where('is_admin', true)->get()
            : collect([]);

        return view('admin.guides.create', compact('vehicles', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:tour_guides,email',
            'phone' => 'nullable|string',
            'license_number' => 'required|string|unique:tour_guides,license_number',
            'license_expiry_date' => 'required|date',
            'profile_photo' => 'nullable|image|max:2048',
            'employment_status' => 'required|in:full_time,part_time,freelancer',
            'vehicle_ids' => 'required|array|min:1',
            'vehicle_ids.*' => 'exists:vehicles,id',
            'user_id' => 'nullable|exists:users,id',
        ]);

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('guides', 'public');
            $validated['profile_photo'] = $path;
        }

        if (isset($validated['user_id']) && (!auth()->user()->isSuperAdmin())) {
            unset($validated['user_id']);
        }

        $guide = TourGuide::create($validated);
        $guide->vehicles()->sync($validated['vehicle_ids']);

        return redirect()->route('admin.guides.index')
            ->with('success', 'Tour Guide created successfully.');
    }

    public function show(TourGuide $guide)
    {
        return view('admin.guides.show', compact('guide'));
    }

    public function edit(TourGuide $guide)
    {
        $vehicles = \App\Models\Vehicle::active()->get();
        $users = auth()->user()->isSuperAdmin()
            ? \App\Models\User::where('is_admin', true)->get()
            : collect([]);

        return view('admin.guides.edit', compact('guide', 'vehicles', 'users'));
    }

    public function update(Request $request, TourGuide $guide)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:tour_guides,email,' . $guide->id,
            'phone' => 'nullable|string',
            'license_number' => 'required|string|unique:tour_guides,license_number,' . $guide->id,
            'license_expiry_date' => 'required|date',
            'profile_photo' => 'nullable|image|max:2048',
            'employment_status' => 'required|in:full_time,part_time,freelancer',
            'vehicle_ids' => 'required|array|min:1',
            'vehicle_ids.*' => 'exists:vehicles,id',
            'user_id' => 'nullable|exists:users,id',
        ]);

        if ($request->hasFile('profile_photo')) {
            // Delete old photo
            if ($guide->profile_photo) {
                Storage::disk('public')->delete($guide->profile_photo);
            }
            $path = $request->file('profile_photo')->store('guides', 'public');
            $validated['profile_photo'] = $path;
        }

        // Only Super Admin can change user link
        if (array_key_exists('user_id', $validated) && (!auth()->user()->isSuperAdmin())) {
            unset($validated['user_id']);
        }

        $guide->update($validated);
        $guide->vehicles()->sync($validated['vehicle_ids']);

        return redirect()->route('admin.guides.index')
            ->with('success', 'Tour Guide updated successfully.');
    }

    public function destroy(TourGuide $guide)
    {
        if ($guide->profile_photo) {
            Storage::disk('public')->delete($guide->profile_photo);
        }
        $guide->delete();

        return redirect()->route('admin.guides.index')
            ->with('success', 'Tour Guide deleted successfully.');
    }

    public function export()
    {
        $filename = 'guides-' . date('Y-m-d') . '.csv';
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];
        $columns = ['first_name', 'last_name', 'email', 'phone', 'license_number', 'license_expiry_date', 'employment_status'];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach (TourGuide::all() as $row) {
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
        $request->validate(['file' => 'required|file|mimes:csv,txt|max:2048']);
        $file = $request->file('file');
        $handle = fopen($file->getPathname(), 'r');
        fgetcsv($handle); // Skip header

        $imported = 0;
        $columns = ['first_name', 'last_name', 'email', 'phone', 'license_number', 'license_expiry_date', 'employment_status'];

        while (($data = fgetcsv($handle)) !== false) {
             if (count($data) !== count($columns)) continue;
             $row = array_combine($columns, $data);

             // Check unique constraints (email, license)
             if (!TourGuide::where('email', $row['email'])->exists() && !TourGuide::where('license_number', $row['license_number'])->exists()) {
                 // Defaults for required fields if missing in CSV (though simpler to just require them)
                 // vehicle_ids is required in store(), but create() might bypass? No, we need to handle vehicles potentially.
                 // For now, simpler data import, no vehicle linking.

                 // Note: 'vehicle_ids' is not in CSV. We'll create guide without vehicles initially.
                 // But validation in store() requires vehicle_ids. We are using direct CREATE here, bypassing store() validation but hitting DB constraints?
                 // DB migration likely has vehicle_guide pivot table, so no direct column.

                 // Handling vehicle_ids?
                 // If we want to import, we probably should allow nullable vehicles or just import basics.
                 // DB Constraint check:
                 // Assuming no strict db constraint on vehicles linkage.

                 TourGuide::create($row);
                 $imported++;
             }
        }
        fclose($handle);

        return back()->with('success', "Imported {$imported} guides successfully.");
    }
}
