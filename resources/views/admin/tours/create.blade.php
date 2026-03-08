@extends('layouts.admin')

@section('title', 'Create Tour')

@section('subtitle', 'Add a new tour package')

@section('content')
<div class="max-w-4xl">
    <form method="POST"
          action="{{ route('admin.tours.store') }}"
          enctype="multipart/form-data"
          class="bg-white rounded-xl border p-6 space-y-6 shadow-sm">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-form.input name="name" label="Tour Name" required />
            <x-form.input name="location" label="Location" required />

            <x-form.input name="price_per_person" label="Price per Person" type="number" step="0.01" required />
            <x-form.input name="duration_days" label="Duration (Days)" type="number" required />

            <div class="md:col-span-2">
                <x-form.select name="park_locations[]" label="Associated Park Locations (Hold Ctrl/Cmd to select multiple)" multiple class="h-32">
                    @foreach($locations as $location)
                        <option value="{{ $location->id }}" @selected(in_array($location->id, old('park_locations', [])))>
                            {{ $location->name }}
                        </option>
                    @endforeach
                </x-form.select>
            </div>

            <x-form.select name="category" label="Category">
                <option value="Safari">Safari</option>
                <option value="Adventure">Adventure</option>
                <option value="Cultural">Cultural</option>
            </x-form.select>

            <x-form.file name="featured_image" label="Featured Image" />
        </div>

        <x-form.textarea name="description" label="Description" rows="5" />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-form.textarea name="highlights" label="Highlights" rows="4" placeholder="Enter each highlight on a new line" />
            <x-form.textarea name="requirements" label="Requirements" rows="4" placeholder="Enter each requirement on a new line" />

            <x-form.textarea name="included_items" label="Included Items" rows="4" placeholder="Enter each item on a new line" />
            <x-form.textarea name="excluded_items" label="Excluded Items" rows="4" placeholder="Enter each item on a new line" />
        </div>

        <!-- Itinerary Repeater -->
        <div class="space-y-4" x-data="{
            days: [{ day: 1, title: '', description: '' }],
            addDay() {
                this.days.push({
                    day: this.days.length + 1,
                    title: '',
                    description: ''
                });
            },
            removeDay(index) {
                this.days.splice(index, 1);
                // Re-index days
                this.days.forEach((day, idx) => {
                    day.day = idx + 1;
                });
            }
        }">
            <div class="flex items-center justify-between border-b pb-2">
                <h3 class="text-lg font-medium text-gray-900">Daily Itinerary</h3>
                <button type="button" @click="addDay()" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                    + Add Day
                </button>
            </div>

            <div class="space-y-6">
                <template x-for="(day, index) in days" :key="index">
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 relative">
                        <div class="absolute top-4 right-4">
                            <button type="button" @click="removeDay(index)" class="text-red-500 hover:text-red-700">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>

                        <div class="grid gap-4">
                            <div class="flex gap-4">
                                <div class="w-24">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Day</label>
                                    <input type="number" :name="'itinerary[' + index + '][day]'" x-model="day.day" readonly
                                           class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                                    <input type="text" :name="'itinerary[' + index + '][title]'" x-model="day.title" placeholder="e.g. Arrival & Transfer to Park" required
                                           class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea :name="'itinerary[' + index + '][description]'" x-model="day.description" rows="3" required
                                          class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5"></textarea>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

             <p x-show="days.length === 0" class="text-sm text-gray-500 italic text-center py-4">
                No itinerary days added yet. Click "Add Day" to start.
            </p>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t">
            <a href="{{ route('admin.tours.index') }}" class="btn-secondary">Cancel</a>
            <button class="btn-primary">Create Tour</button>
        </div>
    </form>
</div>
@endsection
