@extends('layouts.admin')

@section('title', 'Add Vehicle Type')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800 ">Add Vehicle Type</h2>
            <a href="{{ route('admin.vehicle-types.index') }}" class="text-gray-600  hover:text-gray-900">Back to List</a>
        </div>

        <div class="bg-white  shadow-sm sm:rounded-lg p-6">
            <form action="{{ route('admin.vehicle-types.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="name" value="Display Name" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required placeholder="e.g. Standard Safari Land Cruiser" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="category" value="Category" />
                        <select id="category" name="category" class="mt-1 block w-full border-gray-300    rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="safari_van">Safari Van</option>
                            <option value="land_cruiser">Land Cruiser</option>
                            <option value="overland_truck">Overland Truck</option>
                            <option value="transfer_van">Transfer Van</option>
                            <option value="suv">SUV</option>
                            <option value="sedan">Sedan</option>
                        </select>
                        <x-input-error :messages="$errors->get('category')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="manufacturer" value="Manufacturer" />
                        <x-text-input id="manufacturer" name="manufacturer" type="text" class="mt-1 block w-full" :value="old('manufacturer')" required placeholder="e.g. Toyota" />
                        <x-input-error :messages="$errors->get('manufacturer')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="model" value="Model" />
                        <x-text-input id="model" name="model" type="text" class="mt-1 block w-full" :value="old('model')" required placeholder="e.g. Land Cruiser 70 Series" />
                        <x-input-error :messages="$errors->get('model')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="default_capacity" value="Default Capacity (Pax)" />
                        <x-text-input id="default_capacity" name="default_capacity" type="number" min="1" class="mt-1 block w-full" :value="old('default_capacity')" required />
                        <x-input-error :messages="$errors->get('default_capacity')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-gray-200  pt-6">
                    <div>
                        <x-input-label for="base_daily_rate" value="Base Daily Rate ($)" />
                        <x-text-input id="base_daily_rate" name="base_daily_rate" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('base_daily_rate')" />
                        <x-input-error :messages="$errors->get('base_daily_rate')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="base_cost_per_km" value="Base Cost per KM ($)" />
                        <x-text-input id="base_cost_per_km" name="base_cost_per_km" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('base_cost_per_km')" />
                        <x-input-error :messages="$errors->get('base_cost_per_km')" class="mt-2" />
                    </div>
                </div>

                <div>
                    <x-input-label for="description" value="Description" />
                    <textarea id="description" name="description" rows="4" class="mt-1 block w-full border-gray-300    rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                        Create Vehicle Type
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
