@extends('layouts.admin')

@section('title', 'Add Vehicle')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800 ">Add Vehicle Unit</h2>
            <a href="{{ route('admin.vehicles.index') }}" class="text-gray-600  hover:text-gray-900">Back to Fleet</a>
        </div>

        <div class="bg-white  shadow-sm sm:rounded-lg p-6">
            <form action="{{ route('admin.vehicles.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-1 md:col-span-2">
                        <x-input-label for="vehicle_type_id" value="Vehicle Type (Models)" />
                        <select id="vehicle_type_id" name="vehicle_type_id" class="mt-1 block w-full border-gray-300    rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach($vehicleTypes as $type)
                                <option value="{{ $type->id }}" {{ old('vehicle_type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }} ({{ $type->manufacturer }} {{ $type->model }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('vehicle_type_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="registration_number" value="Registration Number" />
                        <x-text-input id="registration_number" name="registration_number" type="text" class="mt-1 block w-full" :value="old('registration_number')" required placeholder="e.g. KCA 123X" />
                        <x-input-error :messages="$errors->get('registration_number')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="year" value="Year of Manufacture" />
                        <x-text-input id="year" name="year" type="number" min="1900" max="{{ date('Y') + 1 }}" class="mt-1 block w-full" :value="old('year')" required />
                        <x-input-error :messages="$errors->get('year')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="status" value="Current Status" />
                        <select id="status" name="status" class="mt-1 block w-full border-gray-300    rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="available">Available</option>
                            <option value="in_use">In Use</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="retired">Retired</option>
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="mileage" value="Current Mileage (km)" />
                        <x-text-input id="mileage" name="mileage" type="number" min="0" class="mt-1 block w-full" :value="old('mileage', 0)" />
                        <x-input-error :messages="$errors->get('mileage')" class="mt-2" />
                    </div>
                </div>

                <div class="flex items-center space-x-2">
                    <input id="is_active" name="is_active" type="checkbox" value="1" class="rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label for="is_active" class="text-sm font-medium text-gray-700 ">Active (Visible in system)</label>
                </div>

                <div>
                    <x-input-label for="notes" value="Internal Notes" />
                    <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full border-gray-300    rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                    <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                        Add Vehicle Unit
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
