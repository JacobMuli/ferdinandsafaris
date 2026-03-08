<x-guest-layout>
    <x-slot name="title">
        {{ ucfirst($category) }} Tours - Ferdinand Safaris
    </x-slot>

    <!-- Hero Section -->
    <div class="relative bg-gray-900 h-[40vh] min-h-[300px] flex items-center">
        <!-- Background Image (Optional: Could be dynamic based on category if images existed) -->
        <div class="absolute inset-0 overflow-hidden">
            <img src="https://images.unsplash.com/photo-1516426122078-c23e76319801?w=1600" alt="Background" class="w-full h-full object-cover opacity-40">
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
            <h1 class="text-4xl md:text-6xl font-extrabold text-white mb-4 tracking-tight">
                {{ ucfirst($category) }} Adventures
            </h1>
            <p class="text-xl text-gray-200 max-w-2xl mx-auto">
                Explore our curated collection of {{ strtolower($category) }} tours designed to create memories that last a lifetime.
            </p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <!-- Breadcrumbs -->
        <nav class="flex mb-8 text-sm text-gray-500 ">
            <a href="{{ route('home') }}" class="hover:text-emerald-600">Home</a>
            <span class="mx-2">/</span>
            <a href="{{ route('tours.index') }}" class="hover:text-emerald-600">Tours</a>
            <span class="mx-2">/</span>
            <span class="text-gray-900  font-medium">{{ ucfirst($category) }}</span>
        </nav>

        @if($tours->count() > 0)
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($tours as $tour)
                    <x-tour-card :tour="$tour" />
                @endforeach
            </div>

            <div class="mt-12">
                {{ $tours->links() }}
            </div>
        @else
            <div class="text-center py-20 bg-gray-50  rounded-2xl">
                <i class="fas fa-search text-gray-300  text-6xl mb-4"></i>
                <h3 class="text-2xl font-bold text-gray-900  mb-2">No tours found in this category</h3>
                <p class="text-gray-600  mb-8">We're constantly updating our offerings. Check out our other adventures!</p>
                <a href="{{ route('tours.index') }}" class="btn-primary inline-flex items-center px-6 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition">
                    View All Tours
                </a>
            </div>
        @endif
    </div>
</x-guest-layout>
