@extends('layouts.admin')

@section('title', 'Edit Tour Guide')

@section('subtitle', 'Update details for ' . $guide->first_name . ' ' . $guide->last_name)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Edit Guide Information</h2>
                <p class="text-sm text-gray-500">Update details for {{ $guide->first_name }} {{ $guide->last_name }}</p>
            </div>
            <img src="{{ $guide->profile_photo_url }}"
                 alt="Profile" class="h-12 w-12 rounded-full object-cover">
        </div>

        <form action="{{ route('admin.guides.update', $guide) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                {{-- Personal Info --}}
                <x-form.input name="first_name" label="First Name" :value="old('first_name', $guide->first_name)" required />
                <x-form.input name="last_name" label="Last Name" :value="old('last_name', $guide->last_name)" required />
                <x-form.input name="email" label="Email Address" type="email" :value="old('email', $guide->email)" required />
                <x-form.input name="phone" label="Phone Number" :value="old('phone', $guide->phone)" />

                {{-- License Info --}}
                <x-form.input name="license_number" label="License Number" :value="old('license_number', $guide->license_number)" required />
                <x-form.input name="license_expiry_date" label="License Expiry Date" type="date" :value="old('license_expiry_date', $guide->license_expiry_date ? $guide->license_expiry_date->format('Y-m-d') : '')" required />

                {{-- Employment --}}
                <div>
                    <label for="employment_status" class="block text-sm font-medium text-gray-700 mb-1">Employment Status</label>
                    <select name="employment_status" id="employment_status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        @foreach(['full_time', 'part_time', 'freelancer'] as $status)
                            <option value="{{ $status }}" {{ old('employment_status', $guide->employment_status) == $status ? 'selected' : '' }}>
                                {{ ucwords(str_replace('_', ' ', $status)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Admin User Link (Super Admin Only) --}}
                @if(auth()->user()->isSuperAdmin())
                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Link to System Admin User (Optional)</label>
                    <select name="user_id" id="user_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">-- No User Link --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id', $guide->user_id) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Links this guide to an existing admin login.</p>
                </div>
                @endif
            </div>

            {{-- Vehicles --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Assigned Vehicles (At least one required)</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 max-h-48 overflow-y-auto border p-2 rounded-md">
                    @foreach($vehicles as $vehicle)
                        <div class="flex items-center space-x-2">
                            <input type="checkbox" name="vehicle_ids[]" value="{{ $vehicle->id }}" id="vehicle_{{ $vehicle->id }}"
                                class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                                {{ (is_array(old('vehicle_ids')) && in_array($vehicle->id, old('vehicle_ids'))) || (!old('vehicle_ids') && in_array($vehicle->id, $guide->vehicles->pluck('id')->toArray())) ? 'checked' : '' }}>
                            <label for="vehicle_{{ $vehicle->id }}" class="text-sm text-gray-700">
                                {{ $vehicle->type->name ?? 'Unknown' }} - {{ $vehicle->registration_number }}
                            </label>
                        </div>
                    @endforeach
                </div>
                <x-input-error :messages="$errors->get('vehicle_ids')" class="mt-2" />
            </div>

            <x-form.file name="profile_photo" label="Profile Photo (Leave empty to keep current)" />

            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.guides.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Update Guide</button>
            </div>
        </form>
    </div>
</div>
@endsection
