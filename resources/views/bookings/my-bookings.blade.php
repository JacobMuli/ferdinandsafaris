<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800  leading-tight">
            {{ __('My Bookings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if($bookings->count() > 0)
                <div class="space-y-6">
                    @foreach($bookings as $booking)
                        <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition-all duration-300 border border-gray-100">
                            <div class="grid lg:grid-cols-12 gap-0">
                                <!-- Tour Image -->
                                <div class="lg:col-span-4 relative h-64 lg:h-auto">
                                    <img src="{{ $booking->tour->display_image }}"
                                         alt="{{ $booking->tour->name }}"
                                         class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                                    
                                    <!-- Status Badge on Image -->
                                    <div class="absolute top-4 right-4">
                                        @if($booking->status === 'paid')
                                            <span class="px-3 py-1.5 bg-green-500 text-white rounded-full text-xs font-bold shadow-lg">
                                                <i class="fas fa-check-circle mr-1"></i>Confirmed
                                            </span>
                                        @elseif($booking->status === 'confirmed')
                                            <span class="px-3 py-1.5 bg-blue-500 text-white rounded-full text-xs font-bold shadow-lg">
                                                <i class="fas fa-check mr-1"></i>Confirmed
                                            </span>
                                        @elseif($booking->status === 'pending')
                                            <span class="px-3 py-1.5 bg-yellow-500 text-white rounded-full text-xs font-bold shadow-lg">
                                                <i class="fas fa-clock mr-1"></i>Pending
                                            </span>
                                        @elseif($booking->status === 'completed')
                                            <span class="px-3 py-1.5 bg-gray-500 text-white rounded-full text-xs font-bold shadow-lg">
                                                <i class="fas fa-flag-checkered mr-1"></i>Completed
                                            </span>
                                        @else
                                            <span class="px-3 py-1.5 bg-red-500 text-white rounded-full text-xs font-bold shadow-lg">
                                                <i class="fas fa-times-circle mr-1"></i>Cancelled
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Category Badge -->
                                    <div class="absolute top-4 left-4">
                                        <span class="px-3 py-1 bg-white/90 backdrop-blur-sm text-gray-900 rounded-full text-xs font-semibold">
                                            {{ ucfirst($booking->tour->category) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Booking Details -->
                                <div class="lg:col-span-8 p-6">
                                    <div class="flex flex-col h-full">
                                        <!-- Header -->
                                        <div class="mb-4">
                                            <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $booking->tour->name }}</h3>
                                            <p class="text-sm text-gray-500">
                                                <i class="fas fa-tag mr-1"></i>
                                                Reference: <span class="font-semibold text-gray-700">{{ $booking->booking_reference }}</span>
                                            </p>
                                        </div>

                                        <!-- Info Grid -->
                                        <div class="grid md:grid-cols-2 gap-4 mb-6">
                                            <div class="flex items-center text-gray-700">
                                                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center mr-3">
                                                    <i class="fas fa-calendar text-emerald-600"></i>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-500">Tour Dates</p>
                                                    <p class="font-semibold text-sm">
                                                        {{ $booking->tour_date->format('M d, Y') }} - {{ $booking->tour_date->copy()->addDays($booking->tour->duration_days - 1)->format('M d, Y') }}
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="flex items-center text-gray-700">
                                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                                    <i class="fas fa-users text-blue-600"></i>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-500">Participants</p>
                                                    <p class="font-semibold text-sm">
                                                        {{ $booking->total_participants }} 
                                                        ({{ $booking->adults_count }} adult{{ $booking->adults_count > 1 ? 's' : '' }}
                                                        @if($booking->children_count > 0)
                                                            , {{ $booking->children_count }} child{{ $booking->children_count > 1 ? 'ren' : '' }}
                                                        @endif)
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="flex items-center text-gray-700">
                                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                                    <i class="fas fa-clock text-purple-600"></i>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-500">Duration</p>
                                                    <p class="font-semibold text-sm">{{ $booking->tour->duration_days }} Days</p>
                                                </div>
                                            </div>

                                            <div class="flex items-center text-gray-700">
                                                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                                                    @if($booking->actual_price)
                                                        <i class="fas fa-dollar-sign text-orange-600"></i>
                                                    @else
                                                        <i class="fas fa-hourglass-half text-orange-600"></i>
                                                    @endif
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-500">Price</p>
                                                    @if($booking->actual_price)
                                                        <p class="font-bold text-lg text-gray-900">${{ number_format($booking->actual_price, 2) }}</p>
                                                    @else
                                                        <p class="font-semibold text-sm text-yellow-600">Pending Confirmation</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Payment Status -->
                                        <div class="mb-4">
                                            @if($booking->payment_status === 'paid')
                                                <div class="flex items-center text-green-600 bg-green-50 rounded-lg px-4 py-2">
                                                    <i class="fas fa-check-circle mr-2"></i>
                                                    <span class="text-sm font-semibold">Payment Completed</span>
                                                </div>
                                            @else
                                                <div class="flex items-center text-yellow-600 bg-yellow-50 rounded-lg px-4 py-2">
                                                    <i class="fas fa-clock mr-2"></i>
                                                    <span class="text-sm font-semibold">Payment Pending</span>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Actions -->
                                        <div class="flex flex-wrap gap-3 mt-auto">
                                            <button type="button"
                                                    onclick="openBookingModal('{{ $booking->booking_reference }}')"
                                                    class="flex-1 min-w-[140px] bg-emerald-600 text-white px-4 py-2.5 rounded-lg font-semibold hover:bg-emerald-700 transition text-sm">
                                                <i class="fas fa-eye mr-2"></i>View Details
                                            </button>

                                            @if($booking->payment_status !== 'paid' && $booking->actual_price && in_array($booking->status, ['pending', 'confirmed']))
                                                <a href="{{ route('bookings.payment', $booking->booking_reference) }}"
                                                   class="flex-1 min-w-[140px] bg-blue-600 text-white px-4 py-2.5 rounded-lg font-semibold hover:bg-blue-700 transition text-sm text-center">
                                                    <i class="fas fa-credit-card mr-2"></i>Pay Now
                                                </a>
                                            @endif

                                            @if($booking->canBeCancelled())
                                                <form action="{{ route('bookings.cancel', $booking->booking_reference) }}" method="POST"
                                                      onsubmit="return confirm('Are you sure you want to cancel this booking?')"
                                                      class="flex-1 min-w-[140px]">
                                                    @csrf
                                                    <button type="submit" class="w-full bg-red-600 text-white px-4 py-2.5 rounded-lg font-semibold hover:bg-red-700 transition text-sm">
                                                        <i class="fas fa-times mr-2"></i>Cancel Booking
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $bookings->links() }}
                </div>
            @else
                <div class="bg-white  rounded-xl p-12 text-center shadow-sm border border-gray-100 ">
                    <i class="fas fa-calendar-times text-gray-300  text-6xl mb-4"></i>
                    <h3 class="text-2xl font-bold text-gray-900  mb-2">No bookings yet</h3>
                    <p class="text-gray-600  mb-6">Start your adventure by booking a tour</p>
                    <a href="{{ route('tours.index') }}" class="inline-block bg-emerald-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-emerald-700 transition">
                        Browse Tours
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Booking Details Modal -->
    <div id="bookingModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop with blur -->
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="closeBookingModal()"></div>
        
        <!-- Modal Content -->
        <div class="flex min-h-screen items-center justify-center p-4">
            <div id="modalContent" class="relative bg-white rounded-xl shadow-2xl max-w-6xl w-full max-h-[90vh] overflow-y-auto">
                <!-- Loading spinner -->
                <div class="flex items-center justify-center p-12">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-emerald-600"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openBookingModal(bookingReference) {
            const modal = document.getElementById('bookingModal');
            const modalContent = document.getElementById('modalContent');
            
            // Show modal
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            
            // Fetch booking details
            fetch(`/bookings/${bookingReference}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.text())
                .then(html => {
                    // Add close button
                    const closeBtn = document.createElement('button');
                    closeBtn.onclick = closeBookingModal;
                    closeBtn.className = 'absolute top-4 right-4 z-10 bg-white/90 hover:bg-white rounded-full p-2 shadow-lg transition';
                    closeBtn.innerHTML = '<i class="fas fa-times text-gray-600 text-xl"></i>';
                    
                    modalContent.innerHTML = '';
                    modalContent.appendChild(closeBtn);
                    modalContent.innerHTML += html;
                })
                .catch(error => {
                    console.error('Error loading booking details:', error);
                    modalContent.innerHTML = '<div class="p-8 text-center text-red-600">Error loading booking details</div>';
                });
        }
        
        function closeBookingModal() {
            const modal = document.getElementById('bookingModal');
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
        
        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeBookingModal();
            }
        });
    </script>
</x-app-layout>