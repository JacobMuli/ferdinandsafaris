<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Display settings page.
     */
    public function index()
    {
        $settings = SiteSetting::all()->groupBy('group');
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'nullable|string',
        ]);

        // Special handling for boolean settings (checkboxes not sent if unchecked)
        $booleanSettings = SiteSetting::where('type', 'boolean')->pluck('key')->toArray();
        foreach ($booleanSettings as $key) {
            $value = isset($validated['settings'][$key]) ? '1' : '0';
            SiteSetting::set($key, $value);
        }

        foreach ($validated['settings'] as $key => $value) {
            // Skip already handled booleans
            if (in_array($key, $booleanSettings)) continue;
            
            SiteSetting::set($key, $value);
        }

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }
}
