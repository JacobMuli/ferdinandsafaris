<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsSection;
use Illuminate\Http\Request;

class CmsSectionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cms_page_id' => 'required|exists:cms_pages,id',
            'section_key' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|json', // Or array if we cast in request, assuming simple text for now or raw json
            'order' => 'integer',
        ]);

        if (isset($validated['content']) && is_string($validated['content'])) {
             $validated['content'] = json_decode($validated['content'], true);
        }

        CmsSection::create($validated);

        return back()->with('success', 'Section added successfully.');
    }

    public function update(Request $request, CmsSection $cmsSection)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
             // We allow updating content via raw JSON for now for flexibility
            'content' => 'nullable|string',
            'order' => 'integer',
            'is_active' => 'boolean',
        ]);

        if (isset($validated['content'])) {
             // Validate if it is valid JSON
             json_decode($validated['content']);
             if (json_last_error() !== JSON_ERROR_NONE) {
                 return back()->withErrors(['content' => 'Invalid JSON format']);
             }
             $validated['content'] = json_decode($validated['content'], true);
        }

        $cmsSection->update($validated);

        return back()->with('success', 'Section updated successfully.');
    }

    public function destroy(CmsSection $cmsSection)
    {
        $cmsSection->delete();
        return back()->with('success', 'Section deleted successfully.');
    }
}