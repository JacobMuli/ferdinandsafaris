@php
    $statusClasses = [
        'paid' => 'bg-green-500',
        'confirmed' => 'bg-blue-500',
        'pending' => 'bg-yellow-500'
    ];
    $statusClass = $statusClasses[$booking->status] ?? 'bg-gray-500';
@endphp

<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <!-- Tour Image Header -->
    <div class="relative h-64 overflow-hidden">
        <img src="{{ $booking->tour->display_image }}" 
             alt="{{ $booking->tour->name }}" 
             class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
        <div class="absolute bottom-0 left-0 right-0 p-6">
            <div class="flex items-end justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-white mb-1">{{ $booking->tour->name }}</h1>
                    <p class="text-white/90 text-sm">Reference: {{ $booking->booking_reference }}</p>
                </div>
                <div>
                    <span class="{{ $statusClass }} px-3 py-1 rounded-full text-sm font-semibold text-white">
                        {{ ucfirst($booking->status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="p-6">
        <div class="grid md:grid-cols-2 gap-6 mb-6">
            <!-- Tour Information -->
            <div>
                <h2 class="text-lg font-bold text-gray-900 mb-3">Tour Details</h2>
                <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-calendar text-emerald-600 mr-2 w-4"></i>
                            <div>
                                <div class="text-xs text-gray-500">Date</div>
                                <div class="font-semibold">{{ \Carbon\Carbon::parse($booking->tour_date)->format('M d, Y') }}</div>
                            </div>
                        </div>
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-clock text-emerald-600 mr-2 w-4"></i>
                            <div>
                                <div class="text-xs text-gray-500">Duration</div>
                                <div class="font-semibold">{{ $booking->tour->duration_days }} Days</div>
                            </div>
                        </div>
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-users text-emerald-600 mr-2 w-4"></i>
                            <div>
                                <div class="text-xs text-gray-500">Participants</div>
                                <div class="font-semibold">
                                    {{ $booking->adults_count }} Adult{{ $booking->adults_count > 1 ? 's' : '' }}
                                    @if($booking->children_count > 0)
                                        + {{ $booking->children_count }} Child{{ $booking->children_count > 1 ? 'ren' : '' }}
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-map-marker-alt text-emerald-600 mr-2 w-4"></i>
                            <div>
                                <div class="text-xs text-gray-500">Location</div>
                                <div class="font-semibold">{{ $booking->tour->location }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Information -->
            <div>
                <h2 class="text-lg font-bold text-gray-900 mb-3">Customer Information</h2>
                <div class="bg-gray-50 rounded-lg p-4 space-y-2 text-sm">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <div class="text-xs text-gray-500">Name</div>
                            <div class="font-semibold text-gray-900">{{ $booking->customer->full_name }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Email</div>
                            <div class="font-semibold text-gray-900">{{ $booking->customer->email }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Phone</div>
                            <div class="font-semibold text-gray-900">{{ $booking->customer->phone }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Country</div>
                            <div class="font-semibold text-gray-900">{{ $booking->customer->country }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Section -->
        @if(in_array($booking->status, ['confirmed', 'paid']) && $booking->actual_price > 0)
            <div class="mb-6">
                <h2 class="text-lg font-bold text-gray-900 mb-3">Payment Information</h2>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex justify-between items-center text-xl font-bold text-gray-900 mb-3">
                        <span>Total Amount</span>
                        <span>${{ number_format($booking->actual_price, 2) }}</span>
                    </div>
                    
                    @if($booking->payment_status === 'paid')
                        <div class="flex items-center text-green-600">
                            <i class="fas fa-check-circle text-xl mr-2"></i>
                            <span class="font-semibold">Payment Completed</span>
                        </div>
                    @else
                        <div class="flex items-center text-yellow-600">
                            <i class="fas fa-clock text-xl mr-2"></i>
                            <span class="font-semibold">Payment Pending</span>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <h3 class="font-bold text-yellow-900 mb-2 flex items-center">
                    <i class="fas fa-clock mr-2"></i>
                    Awaiting Price Confirmation
                </h3>
                <p class="text-sm text-yellow-800">
                    Our team is reviewing your booking and will send you a quote shortly. You'll receive an email once the price is confirmed.
                </p>
            </div>
        @endif

        <!-- Important Information -->
        <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <h3 class="font-bold text-blue-900 mb-2 flex items-center text-sm">
                <i class="fas fa-info-circle mr-2"></i>
                Important Information
            </h3>
            <ul class="space-y-1 text-xs text-blue-800">
                <li>• A confirmation email has been sent to {{ $booking->customer->email }}</li>
                <li>• Please arrive 30 minutes before the tour start time</li>
                <li>• Bring a valid ID and this booking reference</li>
                <li>• For questions, contact us at info@ferdinandsafaris.com</li>
            </ul>
        </div>
    </div>
</div>
