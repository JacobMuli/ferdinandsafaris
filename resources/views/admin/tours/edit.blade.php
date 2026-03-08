@extends('layouts.admin')

@section('title', 'Edit Tour')

@section('subtitle', 'Update tour information')

@section('content')
<div class="max-w-4xl">
    <form method="POST"
          action="{{ route('admin.tours.update', $tour) }}"
          enctype="multipart/form-data"
          class="bg-white rounded-xl border p-6 space-y-6 shadow-sm">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-form.input name="name" label="Tour Name" :value="old('name', $tour->name)" />
            <x-form.input name="location" label="Location" :value="old('location', $tour->location)" />

            <x-form.input name="price_per_person" label="Price per Person" type="number" step="0.01" :value="old('price_per_person', $tour->price_per_person)" />
            <x-form.input name="duration_days" label="Duration (Days)" type="number" :value="old('duration_days', $tour->duration_days)" />

            <x-form.select name="difficulty_level" label="Difficulty Level">
                <option value="easy" @selected(old('difficulty_level', $tour->difficulty_level) === 'easy')>Easy</option>
                <option value="moderate" @selected(old('difficulty_level', $tour->difficulty_level) === 'moderate')>Moderate</option>
                <option value="challenging" @selected(old('difficulty_level', $tour->difficulty_level) === 'challenging')>Challenging</option>
            </x-form.select>

            <x-form.input name="max_participants" label="Max Participants" type="number" :value="old('max_participants', $tour->max_participants)" />
            <x-form.input name="min_group_size" label="Min Group Size" type="number" :value="old('min_group_size', $tour->min_group_size)" />

            <x-form.input name="child_price" label="Child Price" type="number" step="0.01" :value="old('child_price', $tour->child_price)" />

            <div class="grid grid-cols-2 gap-4">
                 <x-form.input name="group_discount_percentage" label="Group Discount (%)" type="number" step="0.1" :value="old('group_discount_percentage', $tour->group_discount_percentage)" />
                 <x-form.input name="corporate_discount_percentage" label="Corp. Discount (%)" type="number" step="0.1" :value="old('corporate_discount_percentage', $tour->corporate_discount_percentage)" />
            </div>

            <x-form.input name="available_from" label="Available From" type="date" :value="old('available_from', $tour->available_from?->format('Y-m-d'))" />
            <x-form.input name="available_to" label="Available To" type="date" :value="old('available_to', $tour->available_to?->format('Y-m-d'))" />

            <div class="md:col-span-2 flex gap-6">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $tour->is_featured)) class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                    <span class="text-sm font-medium text-gray-700">Featured Tour</span>
                </label>

                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $tour->is_active)) class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                    <span class="text-sm font-medium text-gray-700">Active (Published)</span>
                </label>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Associated Park Locations</label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2 bg-gray-50 p-4 rounded-lg border border-gray-200 max-h-60 overflow-y-auto">
                    @foreach($locations as $location)
                        <label class="flex items-center space-x-2 p-2 hover:bg-white rounded cursor-pointer transition-colors">
                            <input type="checkbox" name="park_locations[]" value="{{ $location->id }}"
                                @checked(
                                    is_array(old('park_locations'))
                                    ? in_array($location->id, old('park_locations'))
                                    : $tour->parkLocations->contains($location->id)
                                )
                                class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                            <span class="text-sm text-gray-700">{{ $location->name }}</span>
                        </label>
                    @endforeach
                </div>
                <p class="text-xs text-gray-500 mt-1">Select all parks included in this tour.</p>
            </div>

            <x-form.select name="category" label="Category">
                <option value="Safari" @selected(old('category', $tour->category) === 'Safari')>Safari</option>
                <option value="Adventure" @selected(old('category', $tour->category) === 'Adventure')>Adventure</option>
                <option value="Cultural" @selected(old('category', $tour->category) === 'Cultural')>Cultural</option>
            </x-form.select>

            <x-form.file name="featured_image" label="Change Image" />
        </div>

        <x-form.textarea name="description" label="Description" rows="5">
            {{ old('description', $tour->description) }}
        </x-form.textarea>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-form.textarea name="highlights" label="Highlights" rows="4" placeholder="Enter each highlight on a new line">
                {{ old('highlights', implode("\n", $tour->highlights ?? [])) }}
            </x-form.textarea>

            <x-form.textarea name="requirements" label="Requirements" rows="4" placeholder="Enter each requirement on a new line">
                {{ old('requirements', implode("\n", $tour->requirements ?? [])) }}
            </x-form.textarea>

            <x-form.textarea name="included_items" label="Included Items" rows="4" placeholder="Enter each item on a new line">
                {{ old('included_items', implode("\n", $tour->included_items ?? [])) }}
            </x-form.textarea>

            <x-form.textarea name="excluded_items" label="Excluded Items" rows="4" placeholder="Enter each item on a new line">
                {{ old('excluded_items', implode("\n", $tour->excluded_items ?? [])) }}
            </x-form.textarea>
        </div>

        <!-- Itinerary Repeater -->
        <div class="space-y-4" x-data="{
            days: {{ json_encode(old('itinerary', $tour->formatted_itinerary ?? [['day' => 1, 'title' => '', 'description' => '']])) }},
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
            <button class="btn-primary">Save Changes</button>
        </div>
    </form>
</div>
@endsection
