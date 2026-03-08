@extends('layouts.admin')

@section('title', 'Edit Destination')

@section('subtitle', 'Update details for {{ $location->name }}')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-800">Edit Destination</h2>
            <p class="text-sm text-gray-500">Update details for {{ $location->name }}</p>
        </div>

        <form action="{{ route('admin.locations.update', $location) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Name -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                    <input type="text" name="name" value="{{ old('name', $location->name) }}" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500" required>
                </div>

                <!-- Country -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                    <select name="country" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                        @foreach(['Kenya', 'Tanzania', 'Uganda', 'Rwanda'] as $country)
                            <option value="{{ $country }}" {{ old('country', $location->country) == $country ? 'selected' : '' }}>{{ $country }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                    <select name="type" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                        @foreach(['National Park', 'Game Reserve', 'Conservancy', 'Beach', 'City'] as $type)
                            <option value="{{ $type }}" {{ old('type', $location->type) == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="4" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500" required>{{ old('description', $location->description) }}</textarea>
                </div>
            </div>

            <!-- Featured Image -->
            <div class="mb-8">
                <label class="block text-sm font-medium text-gray-700 mb-2">Featured Image</label>

                @if($location->featured_image)
                    <div class="mb-4">
                        <img src="{{ $location->featured_image_url }}" alt="Current Image" class="h-32 w-auto rounded-lg object-cover">
                        <p class="text-xs text-gray-500 mt-1">Current image</p>
                    </div>
                @endif



                <input type="file" name="featured_image" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                @error('featured_image') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.locations.index') }}" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition">Update Destination</button>
            </div>
        </form>
    </div>
</div>
@endsection
