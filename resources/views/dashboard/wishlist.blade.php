<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Wishlist') }}
            </h2>
            <a href="{{ route('tours.index') }}" class="text-emerald-600 hover:text-emerald-700 font-medium text-sm flex items-center gap-1">
                <i class="fas fa-plus"></i> Explore More Tours
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($wishlistItems->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($wishlistItems as $tour)
                                <div class="relative group border rounded-xl overflow-hidden hover:shadow-md transition-shadow">
                                    <x-tour-card :tour="$tour" />
                                    <!-- Remove from Wishlist Button Overlay -->
                                    <div class="absolute top-4 left-4 z-30">
                                         <form action="{{ route('wishlist.remove', $tour) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-white/80 backdrop-blur-sm p-2 rounded-full text-red-600 hover:bg-white hover:text-red-700 transition" title="Remove from wishlist">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-8">
                            {{ $wishlistItems->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="w-20 h-20 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-heart text-3xl"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Your wishlist is empty</h3>
                            <p class="text-gray-500 mb-6">Discover amazing safari experiences and save your favorites here.</p>
                            <a href="{{ route('tours.index') }}" class="inline-flex items-center px-6 py-3 bg-emerald-600 border border-transparent rounded-lg font-bold text-white uppercase tracking-widest hover:bg-emerald-700 transition">
                                Browse Tours
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
