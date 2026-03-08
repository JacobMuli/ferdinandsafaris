<x-app-layout>
<div class="py-12 pt-24 bg-gray-50 min-h-screen"
     x-data="bookingForm({
         tourId: {{ $tour->id }},
         calculatePriceUrl: '{{ route('tours.calculate-price', $tour) }}',
         checkAvailabilityUrl: '{{ route('tours.check-availability', $tour) }}',
         initial: {{ json_encode([
             'firstName' => old('first_name', optional(auth()->user()?->customer)->first_name ?? explode(' ', auth()->user()?->name ?? '')[0] ?? ''),
             'lastName' => old('last_name', optional(auth()->user()?->customer)->last_name ?? explode(' ', auth()->user()?->name ?? '', 2)[1] ?? ''),
             'email' => old('email', auth()->user()?->email ?? ''),
             'phone' => old('phone', optional(auth()->user()?->customer)->phone ?? ''),
             'country' => old('country', optional(auth()->user()?->customer)->country ?? ''),
             'emergencyContactName' => old('emergency_contact_name', optional(auth()->user()?->customer)->emergency_contact_name ?? ''),
             'emergencyContactPhone' => old('emergency_contact_phone', optional(auth()->user()?->customer)->emergency_contact_phone ?? ''),
         ]) }}
     })">

    <div class="max-w-7xl mx-auto px-4">
        <div class="grid lg:grid-cols-3 gap-8">

            <!-- Booking Form (Left Column, 2/3) -->
            <div class="lg:col-span-2">
                <a href="{{ route('tours.show', $tour) }}" class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-medium mb-4">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Tour
                </a>
                <div class="bg-white rounded-xl shadow-lg p-4 sm:p-8">

                    <form method="POST"
                          action="{{ route('bookings.store') }}"
                          @submit="loading = true"
                          class="space-y-6">
                        @csrf
                        <input type="hidden" name="tour_id" value="{{ $tour->id }}">

                        @if ($errors->any())
                            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-exclamation-circle text-red-500"></i>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800">
                                            There were problems with your submission:
                                        </h3>
                                        <div class="mt-2 text-sm text-red-700">
                                            <ul class="list-disc pl-5 space-y-1">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Date -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <span class="bg-emerald-100 text-emerald-600 w-8 h-8 sm:w-10 sm:h-10 rounded-full flex items-center justify-center text-sm mr-2">1</span>
                                <i class="fas fa-calendar-alt text-emerald-600 mr-2"></i>
                                Tour Date
                            </h3>
                            <x-input-label for="tour_date" :value="__('Tour Date')" class="mb-2 text-base" />
                            <div class="relative" @click="$refs.dateInput.showPicker()">
                                <x-text-input
                                       id="tour_date"
                                       type="date"
                                       name="tour_date"
                                       x-model="tourDate"
                                       x-ref="dateInput"
                                       min="{{ now()->addDays(1)->format('Y-m-d') }}"
                                       value="{{ old('tour_date') }}"
                                       @change="checkAvailability"
                                       required
                                       class="w-full cursor-pointer focus:border-emerald-500 focus:ring-emerald-500 text-base py-3" />
                            </div>
                            <div class="mt-2">
                                <template x-if="availableSpots !== null">
                                    <div :class="availableSpots > 0 ? 'text-emerald-600' : 'text-red-600'" class="text-sm font-medium">
                                        <i :class="availableSpots > 0 ? 'fas fa-check-circle' : 'fas fa-times-circle'" class="mr-1"></i>
                                        <span x-text="availableSpots > 0 ? availableSpots + ' spots available' : 'No spots available for this date'"></span>
                                    </div>
                                </template>
                            </div>
                            <p class="text-sm text-gray-500 mt-2">Select your preferred tour start date</p>
                        </div>

                        <!-- Vehicle Preference -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <span class="bg-emerald-100 text-emerald-600 w-8 h-8 sm:w-10 sm:h-10 rounded-full flex items-center justify-center text-sm mr-2">2</span>
                                <i class="fas fa-car text-emerald-600 mr-2"></i>
                                Vehicle Preference
                            </h3>
                            <x-input-label for="vehicle_type_id" :value="__('Select Vehicle Type')" class="mb-2 text-base" />
                            <select name="vehicle_type_id" x-model="vehicleTypeId" id="vehicle_type_id"
                                    @change="updatePrice"
                                    class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-base py-3">
                                <option value="">Standard (Included)</option>
                                @foreach($vehicleTypes as $type)
                                    <option value="{{ $type->id }}"
                                            data-upgrade-cost="{{ $type->upgrade_cost_per_day }}"
                                            {{ old('vehicle_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                        @if($type->upgrade_cost_per_day > 0)
                                            (+${{ number_format($type->upgrade_cost_per_day) }}/day)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-sm text-gray-500 mt-2">Upgrade costs are estimated for the full tour duration.</p>
                        </div>


                        <x-booking.traveler-inputs :tour="$tour" :customerTypes="$customerTypes"/>
                        <x-booking.contact-details :countries="$countries"/>

                        <textarea name="special_requests"
                                  class="w-full px-4 py-3 rounded-lg border border-gray-300"
                                  placeholder="Special requests"></textarea>

                        <button type="submit"
                                :disabled="!isValid || loading"
                                class="w-full bg-emerald-600 text-white py-4 rounded-lg font-bold transition-colors duration-200 flex items-center justify-center"
                                :class="(!isValid || loading) ? 'bg-gray-400 cursor-not-allowed' : 'bg-emerald-600 hover:bg-emerald-700'">
                            <span x-text="loading ? 'Processing...' : 'Request Quote'">Request Quote</span>
                        </button>
                    </form>

                </div>
            </div>

            <!-- Tour Summary (Right Column, 1/3) -->
            <div class="lg:col-span-1">
                 <div class="bg-white rounded-xl shadow-lg sticky top-24 overflow-hidden">
                    <div class="h-48 relative">
                        <img src="{{ $tour->display_image }}"
                             alt="{{ $tour->name }}"
                             class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        <h2 class="absolute bottom-4 left-4 text-white text-xl font-bold">{{ $tour->name }}</h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3 text-sm text-gray-700 mb-6">
                            <div class="flex items-center">
                                <i class="fas fa-clock w-6 text-emerald-600"></i>
                                <span>{{ $tour->duration_days }} Days</span>
                            </div>
                        </div>

                        <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 mb-4">
                            <p class="text-sm text-blue-800">
                                <i class="fas fa-info-circle mr-1"></i>
                                The final price will be calculated based on your preferences. We will send you a quote shortly.
                            </p>
                        </div>

                        <div class="text-sm text-gray-500">
                            <p class="mb-2"><i class="fas fa-info-circle mr-1"></i> Payment required after confirmation</p>
                            <p><i class="fas fa-check-circle mr-1"></i> Free cancellation 48h prior</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</x-app-layout>
