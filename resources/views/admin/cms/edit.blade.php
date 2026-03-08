@extends('layouts.admin')

@section('title', 'Edit Page')
@section('subtitle', 'Editing: ' . $cmsPage->title)

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Main Settings --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 mb-4">Page Settings</h3>
                <form action="{{ route('admin.cms-pages.update', $cmsPage) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <x-form.input name="title" label="Page Title" :value="old('title', $cmsPage->title)" required />
                    </div>

                    <div class="mb-4">
                        <x-form.input name="slug" label="Slug" :value="old('slug', $cmsPage->slug)" required />
                    </div>

                    <div class="mb-6">
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" {{ old('is_active', $cmsPage->is_active) ? 'checked' : '' }}>
                            <span class="text-gray-700 font-medium">Active</span>
                        </label>
                    </div>

                    <div class="pt-4 border-t border-gray-100">
                        <button type="submit" class="btn-primary w-full">Update Settings</button>
                    </div>
                </form>
            </div>

            {{-- Delete Page --}}
            <div class="bg-white rounded-xl shadow-sm border border-red-100 p-6">
                 <h3 class="font-bold text-red-600 mb-2">Danger Zone</h3>
                 <p class="text-sm text-gray-500 mb-4">Deleting a page cannot be undone.</p>
                 <form action="{{ route('admin.cms-pages.destroy', $cmsPage) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this page?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full py-2 px-4 border border-red-200 text-red-600 rounded-lg hover:bg-red-50 transition text-sm font-medium">
                        Delete Page
                    </button>
                </form>
            </div>
        </div>

        {{-- Content Sections --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-gray-800">Content Sections</h3>
                    <!-- Ideally, a button to add a new section would go here, possibly modal -->
                    <button class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded-lg transition"
                            onclick="alert('To add sections, please contact the developer to define the schema first, or use a seeder.')">
                        <i class="fas fa-plus mr-1"></i> Add Section
                    </button>
                </div>

                @if($cmsPage->sections->count() > 0)
                    <div class="space-y-4">
                        @foreach($cmsPage->sections as $section)
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-emerald-200 transition">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <h4 class="font-semibold text-gray-800">{{ $section->title ?? $section->section_key }}</h4>
                                        <span class="text-xs font-mono text-gray-400 bg-gray-50 px-1 rounded">{{ $section->section_key }}</span>
                                    </div>
                                    <span class="text-xs text-gray-400">Order: {{ $section->order }}</span>
                                </div>
                                <div class="bg-gray-50 rounded p-3 font-mono text-xs text-gray-600 overflow-x-auto whitespace-pre-wrap">
                                    {{ json_encode($section->content, JSON_PRETTY_PRINT) }}
                                </div>
                                <!-- Edit Section Button (Placeholder) -->
                                <div class="mt-2 text-right">
                                    <button class="text-xs text-blue-600 hover:text-blue-800 font-medium">Edit Content</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 bg-gray-50 rounded-lg border border-dashed border-gray-200">
                        <p class="text-gray-500">No content sections defined for this page.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
