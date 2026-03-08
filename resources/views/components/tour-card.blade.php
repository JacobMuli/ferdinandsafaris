@props(['tour'])

<a href="{{ route('tours.show', $tour) }}" class="block relative h-80 rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition group">
    <img src="{{ $tour->display_image }}"
            alt="{{ $tour->name }}"
            class="absolute inset-0 w-full h-full object-cover transition duration-500 group-hover:scale-110">
    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>

    @if($tour->is_featured ?? false)
        <div class="absolute top-4 right-4 bg-emerald-600 text-white px-3 py-1 rounded-full text-sm font-semibold z-10">
            Featured
        </div>
    @endif
    <div class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-sm font-semibold text-gray-900 z-10">
        {{ ucfirst($tour->category) }}
    </div>

    <div class="absolute bottom-0 left-0 p-6 w-full">
        <h3 class="text-xl font-bold text-white mb-0 leading-tight">{{ $tour->name }}</h3>
    </div>
</a>
