<x-guest-layout>
    <x-slot name="title">
        Featured Tours - Ferdinand Safaris
    </x-slot>

    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-emerald-900 to-emerald-800 text-white pt-24 pb-12 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight mb-4">
                <i class="fas fa-star text-yellow-400 mr-3"></i>Our Signature Experiences
            </h1>
            <p class="text-xl text-emerald-100 max-w-2xl mx-auto">
                Hand-picked adventures representing the very best of what we offer. Unforgettable journeys, expertly curated for you.
            </p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        @if($tours->count() > 0)
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($tours as $tour)
                    <x-tour-card :tour="$tour" />
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <div class="bg-emerald-50 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-compass text-3xl text-emerald-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">No featured tours at the moment</h3>
                <p class="text-gray-600 mb-6">Check back soon for our latest highlights.</p>
                <a href="{{ route('tours.index') }}" class="btn-primary inline-flex items-center px-6 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition">
                    Browse All Tours
                </a>
            </div>
        @endif
    </div>
</x-guest-layout>
