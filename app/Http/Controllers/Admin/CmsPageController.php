<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use Illuminate\Http\Request;

class CmsPageController extends Controller
{
    public function index()
    {
        $pages = CmsPage::latest()->paginate(10);
        return view('admin.cms.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.cms.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:cms_pages,slug',
            'is_active' => 'boolean',
        ]);

        CmsPage::create($validated);

        return redirect()->route('admin.cms-pages.index')
            ->with('success', 'Page created successfully.');
    }

    public function show(CmsPage $cmsPage)
    {
        return view('admin.cms.show', compact('cmsPage'));
    }

    public function edit(CmsPage $cmsPage)
    {
        return view('admin.cms.edit', compact('cmsPage'));
    }

    public function update(Request $request, CmsPage $cmsPage)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:cms_pages,slug,' . $cmsPage->id,
            'is_active' => 'boolean',
        ]);

        $cmsPage->update($validated);

        return redirect()->route('admin.cms-pages.index')
            ->with('success', 'Page updated successfully.');
    }

    public function destroy(CmsPage $cmsPage)
    {
        $cmsPage->delete();
        return redirect()->route('admin.cms-pages.index')
            ->with('success', 'Page deleted successfully.');
    }
}
