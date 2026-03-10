@extends('tour-guide.layouts.guide')

@section('content')
    <div class="mb-4">
        <a href="{{ route('guide.dashboard') }}" class="text-emerald-600 text-sm font-semibold mb-2 inline-flex items-center">
            <i class="fas fa-chevron-left mr-1"></i> Back to Dashboard
        </a>
        <h1 class="text-2xl font-bold text-gray-900 heading-font">Assignment Details</h1>
    </div>

    <!-- Tour Banner Card -->
    <div class="premium-card mb-6">
        <div class="relative h-40">
            <img src="{{ $assignment->booking->tour->featured_image ?? 'https://images.unsplash.com/photo-1516426122078-c23e76319801' }}" alt="Tour" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-black/40"></div>
            <div class="absolute inset-0 p-4 flex flex-col justify-end">
                <span class="status-badge {{ 'status-'.$assignment->status }} self-start mb-2">
                    {{ $assignment->status }}
                </span>
                <h2 class="text-white text-xl font-bold">{{ $assignment->booking->tour->name }}</h2>
            </div>
        </div>
        <div class="p-4 grid grid-cols-2 gap-4 text-center border-b">
            <div>
                <p class="text-xs text-gray-500 uppercase font-bold">Date</p>
                <p class="font-bold text-gray-800">{{ $assignment->booking->tour_date->format('M d, Y') }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-bold">Participants</p>
                <p class="font-bold text-gray-800">{{ $assignment->booking->total_participants }} Total</p>
            </div>
        </div>
    </div>

    <!-- Status Actions -->
    @if($assignment->status === 'pending')
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 mb-8 text-center">
            <h3 class="font-bold text-amber-800 mb-2">New Invitation</h3>
            <p class="text-sm text-amber-700 mb-6">You have been offered this tour with a payment of <strong class="text-lg">${{ number_format($assignment->offered_payment, 2) }}</strong>. Would you like to accept?</p>
            
            <div class="flex flex-col space-y-3">
                <form action="{{ route('guide.assignments.accept', $assignment->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full py-4 bg-emerald-600 text-white rounded-xl font-bold shadow-lg shadow-emerald-200">
                        Accept Assignment
                    </button>
                </form>
                <a href="{{ route('guide.assignments.decline', $assignment->id) }}" class="w-full py-3 bg-white border border-red-200 text-red-600 rounded-xl font-bold">
                    Decline
                </a>
            </div>
        </div>
    @endif

    <!-- Manifest Section (Mobile Optimized) -->
    @if($assignment->status === 'accepted' || $assignment->status === 'confirmed')
        <h3 class="font-bold text-gray-800 mb-4 heading-font flex items-center">
            <i class="fas fa-clipboard-list mr-2 text-emerald-600"></i> Passenger Manifest
        </h3>
        
        <div class="premium-card mb-8">
            <div class="p-4 border-b bg-gray-50">
                <h4 class="font-bold text-gray-900">Lead Customer</h4>
                <div class="mt-2 flex items-center justify-between">
                    <div>
                        <p class="text-gray-800 font-medium">{{ $booking->customer->first_name }} {{ $booking->customer->last_name }}</p>
                        <p class="text-xs text-gray-500">{{ $booking->customer->phone }}</p>
                    </div>
                    <div class="flex space-x-2">
                        <a href="tel:{{ $booking->customer->phone }}" class="w-10 h-10 bg-emerald-100 text-emerald-700 rounded-full flex items-center justify-center">
                            <i class="fas fa-phone"></i>
                        </a>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $booking->customer->phone) }}" class="w-10 h-10 bg-green-100 text-green-700 rounded-full flex items-center justify-center">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="p-4 border-b">
                <div class="flex justify-between text-sm mb-2">
                    <span class="text-gray-500 font-medium italic">Emergency Contact</span>
                    <span class="text-emerald-700 font-bold uppercase text-[10px] tracking-widest bg-emerald-50 px-2 py-0.5 rounded">Critical Info</span>
                </div>
                <p class="text-gray-800 font-bold">{{ $booking->emergency_contact_name }}</p>
                <p class="text-sm text-gray-600 font-medium">{{ $booking->emergency_contact_phone }}</p>
                <a href="tel:{{ $booking->emergency_contact_phone }}" class="mt-3 inline-flex items-center text-emerald-600 text-sm font-bold">
                    <i class="fas fa-phone-alt mr-2"></i> Call Emergency Contact
                </a>
            </div>

            @if($booking->special_requests)
                <div class="p-4 bg-amber-50">
                    <h4 class="text-xs font-bold text-amber-800 uppercase mb-2">Special Requests / Alerts</h4>
                    <p class="text-sm text-amber-900 leading-relaxed">{{ $booking->special_requests }}</p>
                </div>
            @endif
        </div>

        <!-- Logistics -->
        <h3 class="font-bold text-gray-800 mb-4 heading-font flex items-center">
            <i class="fas fa-shuttle-van mr-2 text-emerald-600"></i> Logistics
        </h3>
        <div class="premium-card p-4 space-y-4 mb-8">
            <div class="flex items-start">
                <i class="fas fa-truck-pickup w-8 text-gray-400 mt-1"></i>
                <div>
                    <h4 class="text-xs font-bold text-gray-500 uppercase">Vehicle Requirement</h4>
                    <p class="text-gray-900 font-medium">{{ $booking->vehicleType->name ?? 'Standard Safari Van' }}</p>
                </div>
            </div>
            <div class="flex items-start">
                <i class="fas fa-bed w-8 text-gray-400 mt-1"></i>
                <div>
                    <h4 class="text-xs font-bold text-gray-500 uppercase">Accommodations</h4>
                    <p class="text-gray-900 font-medium">{{ $booking->accommodation->name ?? 'Mixed/To be confirmed' }}</p>
                </div>
            </div>
        </div>
    @endif

    @if($assignment->status === 'declined')
        <div class="premium-card p-8 text-center bg-red-50 border-red-100 mb-8">
            <i class="fas fa-times-circle text-4xl text-red-300 mb-4"></i>
            <h3 class="font-bold text-red-900">Assignment Declined</h3>
            <p class="text-red-700 text-sm mt-2">You declined this assignment on {{ $assignment->updated_at->format('M d, Y') }}.</p>
            @if($assignment->notes)
                <div class="mt-4 p-3 bg-white/50 rounded-lg text-sm text-red-800 italic text-left">
                    "{{ $assignment->notes }}"
                </div>
            @endif
        </div>
    @endif
@endsection
