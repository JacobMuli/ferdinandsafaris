<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Booking {{ $booking->booking_reference }} - Ferdinand Safaris</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-6xl w-full mx-auto">
            <!-- Success Message -->
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-600 text-2xl mr-3"></i>
                        <div>
                            <h3 class="text-base font-bold text-green-800">{{ session('success') }}</h3>
                            <p class="text-sm text-green-700">Reference: <strong>{{ $booking->booking_reference }}</strong></p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Booking Details -->
            @include('bookings.partials.details', ['booking' => $booking])

            <div class="mt-6 px-6 pb-6">
                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-3 mb-4">
                    <a href="/" class="flex-1 bg-gray-600 text-white text-center py-2.5 rounded-lg font-semibold hover:bg-gray-700 transition text-sm">
                        <i class="fas fa-home mr-2"></i>
                        Back to Home
                    </a>

                    @if($booking->payment_status !== 'paid' && $booking->total_amount > 0)
                        <a href="{{ route('bookings.payment', $booking->booking_reference) }}"
                           class="flex-1 bg-emerald-600 text-white text-center py-2.5 rounded-lg font-semibold hover:bg-emerald-700 transition text-sm">
                            <i class="fas fa-credit-card mr-2"></i>
                            Complete Payment
                        </a>
                    @elseif($booking->payment_status === 'paid')
                        <button onclick="window.print()"
                                class="flex-1 bg-emerald-600 text-white text-center py-2.5 rounded-lg font-semibold hover:bg-emerald-700 transition text-sm">
                            <i class="fas fa-print mr-2"></i>
                            Print Confirmation
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>