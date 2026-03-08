@extends('layouts.admin')

@section('title', 'Add New Destination')

@section('subtitle', 'Add a new park or destination')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-800">Destination Details</h2>
            <p class="text-sm text-gray-500">Add a new park or destination.</p>
        </div>

        <form action="{{ route('admin.locations.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Name -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="e.g. Serengeti National Park" required>
                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Country -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                    <select name="country" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="Kenya">Kenya</option>
                        <option value="Tanzania">Tanzania</option>
                        <option value="Uganda">Uganda</option>
                        <option value="Rwanda">Rwanda</option>
                    </select>
                </div>

                <!-- Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                    <select name="type" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="National Park">National Park</option>
                        <option value="Game Reserve">Game Reserve</option>
                        <option value="Conservancy">Conservancy</option>
                        <option value="Beach">Beach</option>
                        <option value="City">City</option>
                    </select>
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="4" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500" required>{{ old('description') }}</textarea>
                    @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Featured Image -->
            <div class="mb-8">
                <label class="block text-sm font-medium text-gray-700 mb-2">Featured Image</label>
                <div class="mb-4">

                </div>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-emerald-500 transition">
                    <div class="space-y-1 text-center">
                        <i class="fas fa-image text-gray-400 text-3xl mb-3"></i>
                        <div class="flex text-sm text-gray-600">
                            <label class="relative cursor-pointer bg-white rounded-md font-medium text-emerald-600 hover:text-emerald-500 focus-within:outline-none">
                                <span>Upload a file</span>
                                <input name="featured_image" type="file" class="sr-only">
                            </label>
                            <p class="pl-1">or drag and drop</p>
                        </div>
                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB</p>
                    </div>
                </div>
                @error('featured_image') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.locations.index') }}" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition">Save Destination</button>
            </div>
        </form>
    </div>
</div>
@endsection
