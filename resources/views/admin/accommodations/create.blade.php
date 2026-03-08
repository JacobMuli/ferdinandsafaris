@extends('layouts.admin')

@section('title', 'Add Accommodation')
@section('subtitle', 'Create a new accommodation listing')

@section('content')
<div class="max-w-4xl">
    <form method="POST" action="{{ route('admin.accommodations.store') }}" enctype="multipart/form-data" class="bg-white rounded-xl border border-gray-200 p-6 space-y-6 shadow-sm">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-form.input name="name" label="Accommodation Name" required />

            <x-form.select name="type" label="Type">
                <option value="Lodge">Lodge</option>
                <option value="Hotel">Hotel</option>
                <option value="Camp">Camp</option>
                <option value="Resort">Resort</option>
            </x-form.select>

            <x-form.select name="category" label="Category">
                <option value="Standard">Standard</option>
                <option value="Deluxe">Deluxe</option>
                <option value="Luxury">Luxury</option>
            </x-form.select>

            <x-form.select name="park_location_id" label="Park Location">
                <option value="">-- No Park Location --</option>
                @foreach($parkLocations as $location)
                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                @endforeach
            </x-form.select>

            <x-form.input name="total_rooms" label="Total Rooms" type="number" required />
            <x-form.input name="price_per_night_standard" label="Price per Night (Standard)" type="number" step="0.01" required />

            <x-form.input name="city" label="City" />
            <x-form.input name="country" label="Country" value="Kenya" />

            <x-form.input name="amenities" label="Amenities (Comma separated)" placeholder="Pool, WiFi, Gym" />

            <x-form.file name="main_image" label="Main Image" />
        </div>

        <x-form.textarea name="description" label="Description" rows="4" required />

        <div class="flex justify-end gap-3 pt-4 border-t">
            <a href="{{ route('admin.accommodations.index') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">Create Accommodation</button>
        </div>
    </form>
</div>
@endsection
