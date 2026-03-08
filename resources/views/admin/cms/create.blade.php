@extends('layouts.admin')

@section('title', 'New Page')
@section('subtitle', 'Create a new content page')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('admin.cms-pages.store') }}" method="POST">
            @csrf

            <div class="mb-6">
                <x-form.input name="title" label="Page Title" required />
            </div>

            <div class="mb-6">
                <x-form.input name="slug" label="Slug (Optional - Auto generated)" />
                <p class="text-xs text-gray-500 mt-1">Unique identifier for URL (e.g. 'home', 'about-us')</p>
            </div>

            <div class="mb-6">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" checked>
                    <span class="text-gray-700 font-medium">Active</span>
                </label>
            </div>

            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.cms-pages.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Create Page</button>
            </div>
        </form>
    </div>
</div>
@endsection
