@extends('layouts.admin')

@section('title', 'Booking Details')
@section('subtitle', 'Reference: ' . $booking->booking_reference)

@section('actions')
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.bookings.index') }}" class="btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i> Back
        </a>

        @if($booking->status === 'pending')
            <form action="{{ route('admin.bookings.confirm', $booking) }}" method="POST" onsubmit="return confirm('Confirm this booking?')">
                @csrf
                <button type="submit" class="btn-primary bg-emerald-600 hover:bg-emerald-700 shadow-md">
                    <i class="fas fa-check mr-2"></i> Confirm Booking
                </button>
            </form>
        @endif

        @if($booking->status !== 'cancelled' && $booking->status !== 'completed')
            <button onclick="document.getElementById('cancelModal').classList.remove('hidden')"
                    class="btn-secondary text-red-600 hover:bg-red-50 border-red-200">
                <i class="fas fa-times mr-2"></i> Cancel
            </button>
        @endif

         @if(in_array($booking->status, ['confirmed', 'paid']) && $booking->status !== 'completed')
             <form action="{{ route('admin.bookings.complete', $booking) }}" method="POST" onsubmit="return confirm('Mark this booking as completed?')">
                @csrf
                <button type="submit" class="btn-primary bg-blue-600 hover:bg-blue-700 shadow-md">
                    <i class="fas fa-flag-checkered mr-2"></i> Mark Completed
                </button>
            </form>
        @endif
    </div>
@endsection

@section('content')

<!-- Top Stats Row -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Status Card -->
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4
        {{ $booking->status === 'confirmed' ? 'border-emerald-500' :
          ($booking->status === 'pending' ? 'border-yellow-500' :
          ($booking->status === 'cancelled' ? 'border-red-500' : 'border-blue-500')) }}">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Booking Status</p>
                <p class="text-xl font-bold text-gray-900 capitalize">{{ $booking->status }}</p>
            </div>
            <div class="p-3 rounded-full
                {{ $booking->status === 'confirmed' ? 'bg-emerald-100 text-emerald-600' :
                  ($booking->status === 'pending' ? 'bg-yellow-100 text-yellow-600' :
                  ($booking->status === 'cancelled' ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-600')) }}">
                <i class="fas fa-info-circle text-lg"></i>
            </div>
        </div>
    </div>

    <!-- Payment Stats -->
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 {{ $booking->payment_status === 'paid' ? 'border-emerald-500' : 'border-yellow-500' }}">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Payment Status</p>
                <p class="text-xl font-bold text-gray-900 capitalize">{{ ucfirst($booking->payment_status) }}</p>
            </div>
            <div class="p-3 rounded-full {{ $booking->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-600' : 'bg-yellow-100 text-yellow-600' }}">
                <i class="fas fa-credit-card text-lg"></i>
            </div>
        </div>
    </div>

    <!-- Participants -->
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-indigo-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Total Guests</p>
                <p class="text-xl font-bold text-gray-900">{{ $booking->adults_count + $booking->children_count }}</p>
                <p class="text-xs text-gray-500">{{ $booking->adults_count }} Ad / {{ $booking->children_count }} Ch</p>
            </div>
            <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                <i class="fas fa-users text-lg"></i>
            </div>
        </div>
    </div>

    <!-- Total Cost -->
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-gray-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Total Value</p>
                <p class="text-xl font-bold text-gray-900">${{ number_format($booking->actual_price ?? $booking->total_amount, 2) }}</p>
            </div>
            <div class="p-3 rounded-full bg-gray-100 text-gray-600">
                <i class="fas fa-dollar-sign text-lg"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <!-- Left Column: Tour & Financials -->
    <div class="lg:col-span-2 space-y-8">

        <!-- Tour Card -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
            <div class="h-48 w-full bg-gray-200 relative">
                 <img src="{{ $booking->tour->display_image }}" alt="{{ $booking->tour->name }}" class="w-full h-full object-cover">
                 <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                 <div class="absolute bottom-4 left-6 text-white">
                     <h2 class="text-2xl font-bold shadow-sm">{{ $booking->tour->name }}</h2>
                     <p class="text-sm opacity-90"><i class="fas fa-map-marker-alt mr-1"></i> {{ $booking->tour->location }}</p>
                 </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-indigo-50 text-indigo-600 rounded-lg">
                            <i class="fas fa-calendar-alt text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Tour Dates</p>
                            <p class="text-gray-900 font-semibold mt-1">
                                {{ optional($booking->tour_date)->format('M d, Y') ?? 'N/A' }}
                                <span class="text-gray-400 mx-2">to</span>
                                {{ optional($booking->tour_date)->copy()->addDays($booking->tour->duration_days - 1)->format('M d, Y') ?? '' }}
                            </p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $booking->tour->duration_days }} Days / {{ $booking->tour->duration_days - 1 }} Nights</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                         <div class="p-3 bg-purple-50 text-purple-600 rounded-lg">
                            <i class="fas fa-route text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Itinerary Category</p>
                            <p class="text-gray-900 font-semibold mt-1 capitalize">{{ $booking->tour->category }}</p>
                            <a href="{{ route('admin.tours.show', $booking->tour_id) }}" class="text-xs text-indigo-600 hover:text-indigo-800 hover:underline mt-1 inline-block">View Full Itinerary</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financials Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-900">Financial Overview</h3>
                <span class="text-xs bg-gray-100 text-gray-600 px-3 py-1 rounded-full font-medium">System ID: {{ $booking->id }}</span>
            </div>

            <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                     <!-- Left: System Estimate -->
                     <div>
                         <p class="text-sm text-gray-500 mb-2">System Calculated Estimate</p>
                         <p class="text-3xl font-bold text-gray-400">${{ number_format((float) $booking->total_amount, 2) }}</p>
                         <p class="text-xs text-gray-400 mt-1 italic">Currently calculates vehicle costs only.</p>
                     </div>

                     <!-- Right: Actual Billed -->
                     <div class="relative pl-8 md:border-l border-gray-200">
                         <form action="{{ route('admin.bookings.update-price', $booking) }}" method="POST">
                            @csrf
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                <i class="fas fa-tag text-emerald-600 mr-1"></i> Actual Billed Price
                            </label>
                            <div class="flex gap-2">
                                <div class="relative flex-1">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 font-bold">$</span>
                                    </div>
                                    <input type="number" name="actual_price" step="0.01"
                                           value="{{ $booking->actual_price ?? '' }}"
                                           class="pl-8 block w-full rounded-lg border-gray-300 bg-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-lg font-bold text-gray-900"
                                           placeholder="0.00" required>
                                </div>
                                <button type="submit" class="bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 font-medium shadow-sm transition">
                                    Update
                                </button>
                            </div>
                         </form>

                         <!-- Difference Indicator -->
                         @if($booking->actual_price)
                             @php
                                 $diff = $booking->actual_price - $booking->total_amount;
                                 $diffColor = $diff >= 0 ? 'text-emerald-600' : 'text-red-500';
                                 $arrow = $diff >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
                             @endphp
                             <div class="mt-3 flex items-center gap-2 {{ $diffColor }} font-medium text-sm">
                                 <i class="fas {{ $arrow }}"></i>
                                 <span>${{ number_format(abs($diff), 2) }} {{ $diff >= 0 ? 'Extra Profit' : 'Discount Applied' }}</span>
                             </div>
                         @endif
                     </div>
                </div>
            </div>

            <!-- Payment History Table -->
            <h4 class="text-sm font-bold text-gray-900 mt-8 mb-4 uppercase tracking-wide">Transaction History</h4>
            @if($booking->payments && $booking->payments->count() > 0)
                <div class="overflow-x-auto border border-gray-100 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Method</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach($booking->payments as $payment)
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $payment->created_at->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">${{ number_format($payment->amount, 2) }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ ucfirst($payment->payment_method) }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-6 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                    <p class="text-gray-400 text-sm">No payments recorded yet.</p>
                </div>
            @endif
        </div>

    </div>

    <!-- Right Column: Sidebar -->
    <div class="space-y-6">

        <!-- Customer Profile Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
            <div class="w-20 h-20 bg-indigo-100 rounded-full mx-auto flex items-center justify-center mb-4 ring-4 ring-white shadow-md">
                <span class="text-3xl font-bold text-indigo-600">{{ substr($booking->customer->first_name ?? 'G', 0, 1) }}</span>
            </div>

            <h3 class="text-lg font-bold text-gray-900">{{ $booking->customer->first_name ?? 'Guest' }} {{ $booking->customer->last_name ?? '' }}</h3>
            <p class="text-sm text-indigo-600 font-medium mb-6 uppercase tracking-wider">{{ $booking->customer_type }}</p>

            <div class="space-y-4 text-left">
                <div class="flex items-center p-3 bg-gray-50 rounded-lg gap-3">
                    <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-gray-400 shadow-sm"><i class="fas fa-envelope text-xs"></i></div>
                    <div class="overflow-hidden">
                        <p class="text-xs text-gray-500">Email Address</p>
                        <a href="mailto:{{ $booking->customer->email ?? $booking->email }}" class="text-sm font-medium text-gray-900 hover:text-indigo-600 truncate block">{{ $booking->customer->email ?? $booking->email }}</a>
                    </div>
                </div>

                <div class="flex items-center p-3 bg-gray-50 rounded-lg gap-3">
                    <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-gray-400 shadow-sm"><i class="fas fa-phone text-xs"></i></div>
                    <div>
                        <p class="text-xs text-gray-500">Phone Number</p>
                        <p class="text-sm font-medium text-gray-900">{{ $booking->customer->phone ?? $booking->phone ?? 'N/A' }}</p>
                    </div>
                </div>

                <div class="flex items-center p-3 bg-gray-50 rounded-lg gap-3">
                    <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-gray-400 shadow-sm"><i class="fas fa-flag text-xs"></i></div>
                    <div>
                        <p class="text-xs text-gray-500">Nationality</p>
                        <p class="text-sm font-medium text-gray-900">{{ $booking->customer->country ?? 'Unknown' }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 border-t border-gray-100 pt-6">
                <a href="#" class="block w-full py-2 px-4 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    View Full Profile
                </a>
            </div>
        </div>

        <!-- Emergency Contact -->
        @if($booking->emergency_contact_name)
        <div class="bg-red-50 rounded-xl border border-red-100 p-6">
            <h4 class="text-red-800 font-bold mb-3 flex items-center gap-2">
                <i class="fas fa-first-aid"></i> Emergency Contact
            </h4>
            <p class="text-red-900 font-medium">{{ $booking->emergency_contact_name }}</p>
            <p class="text-red-700 text-sm mt-1">{{ $booking->emergency_contact_phone }}</p>
        </div>
        @endif

        <!-- Special Requests -->
        @if($booking->special_requests)
        <div class="bg-yellow-50 rounded-xl border border-yellow-200 p-6">
            <h4 class="text-yellow-800 font-bold mb-3 flex items-center gap-2">
                <i class="fas fa-comment-alt"></i> Special Requests
            </h4>
            <p class="text-yellow-900 text-sm italic">"{{ $booking->special_requests }}"</p>
        </div>
        @endif

    </div>
</div>

<!-- Cancel Modal -->
<div id="cancelModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full z-50 backdrop-blur-sm">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-2xl rounded-xl bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-lg"></i>
            </div>
            <h3 class="text-lg leading-6 font-bold text-gray-900">Cancel Booking</h3>
            <div class="mt-2 px-2 py-3">
                <p class="text-sm text-gray-500 mb-4">
                    Are you sure you want to cancel this booking? This action is irreversible and will trigger a cancellation email.
                </p>
                <form action="{{ route('admin.bookings.cancel', $booking) }}" method="POST" class="mt-4">
                    @csrf
                    <textarea name="cancellation_reason" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-red-500 focus:ring-red-500 text-sm mb-4" rows="3" placeholder="Please provide a reason..." required></textarea>

                    <div class="flex justify-center gap-3">
                        <button type="button" onclick="document.getElementById('cancelModal').classList.add('hidden')" class="px-4 py-2 bg-white text-gray-700 border border-gray-300 text-sm font-medium rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none">
                            Keep Booking
                        </button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Confirm Cancellation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
