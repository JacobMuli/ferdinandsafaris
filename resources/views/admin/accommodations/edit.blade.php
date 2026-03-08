@extends('layouts.admin')

@section('title', 'Edit Accommodation')
@section('subtitle', 'Update accommodation details')

@section('content')
<div class="max-w-4xl">
    <form method="POST" action="{{ route('admin.accommodations.update', $accommodation) }}" enctype="multipart/form-data" class="bg-white rounded-xl border border-gray-200 p-6 space-y-6 shadow-sm">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-form.input name="name" label="Accommodation Name" :value="old('name', $accommodation->name)" required />

            <x-form.select name="type" label="Type">
                <option value="Lodge" @selected(old('type', $accommodation->type) === 'Lodge')>Lodge</option>
                <option value="Hotel" @selected(old('type', $accommodation->type) === 'Hotel')>Hotel</option>
                <option value="Camp" @selected(old('type', $accommodation->type) === 'Camp')>Camp</option>
                <option value="Resort" @selected(old('type', $accommodation->type) === 'Resort')>Resort</option>
            </x-form.select>

            <x-form.select name="category" label="Category">
                <option value="Standard" @selected(old('category', $accommodation->category) === 'Standard')>Standard</option>
                <option value="Deluxe" @selected(old('category', $accommodation->category) === 'Deluxe')>Deluxe</option>
                <option value="Luxury" @selected(old('category', $accommodation->category) === 'Luxury')>Luxury</option>
            </x-form.select>

            <x-form.select name="park_location_id" label="Park Location">
                <option value="">-- No Park Location --</option>
                @foreach($parkLocations as $location)
                    <option value="{{ $location->id }}" @selected(old('park_location_id', $accommodation->park_location_id) == $location->id)>{{ $location->name }}</option>
                @endforeach
            </x-form.select>

            <x-form.input name="total_rooms" label="Total Rooms" type="number" :value="old('total_rooms', $accommodation->total_rooms)" required />
            <x-form.input name="price_per_night_standard" label="Price per Night (Standard)" type="number" step="0.01" :value="old('price_per_night_standard', $accommodation->price_per_night_standard)" required />

            <x-form.input name="city" label="City" :value="old('city', $accommodation->city)" />
            <x-form.input name="country" label="Country" :value="old('country', $accommodation->country)" />

            <x-form.input name="amenities" label="Amenities (Comma separated)" :value="old('amenities', implode(', ', $accommodation->amenities ?? []))" placeholder="Pool, WiFi, Gym" />

            <div class="space-y-2">
                <x-form.file name="main_image" label="Main Image" />
                @if($accommodation->main_image)
                    <div class="text-xs text-gray-500">Current Image:</div>
                    <img src="{{ asset('storage/' . $accommodation->main_image) }}" class="h-16 w-16 object-cover rounded" alt="Current">
                @endif
            </div>
        </div>

        <x-form.textarea name="description" label="Description" rows="4" required>
            {{ old('description', $accommodation->description) }}
        </x-form.textarea>

        <div class="flex justify-end gap-3 pt-4 border-t">
            <a href="{{ route('admin.accommodations.index') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">Update Accommodation</button>
        </div>
    </form>
</div>
@endsection
