@props(['tour'])

<a href="{{ route('tours.show', $tour) }}" class="block relative h-80 rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition group">
    <img src="{{ $tour->display_image }}"
            alt="{{ $tour->name }}"
            class="absolute inset-0 w-full h-full object-cover transition duration-500 group-hover:scale-110">
    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>

    <!-- Like Button -->
    <button 
        onclick="event.preventDefault(); toggleLike(this, {{ $tour->id }})"
        class="absolute top-4 right-4 z-20 w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 {{ $tour->likes()->where(function($q) { $q->where('user_id', auth()->id())->orWhere('ip_address', request()->ip()); })->exists() ? 'bg-red-500 text-white' : 'bg-white/20 backdrop-blur-md text-white hover:bg-white hover:text-red-500 shadow-sm' }}"
        data-liked="{{ $tour->likes()->where(function($q) { $q->where('user_id', auth()->id())->orWhere('ip_address', request()->ip()); })->exists() ? 'true' : 'false' }}"
    >
        <i class="fas fa-heart"></i>
    </button>

    @if($tour->is_featured ?? false)
        <div class="absolute top-4 left-4 bg-emerald-600 text-white px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest z-10 shadow-lg shadow-emerald-500/20">
            Featured
        </div>
    @endif
    
    <div class="absolute top-16 left-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-[9px] font-black text-gray-900 uppercase tracking-widest z-10">
        {{ $tour->category }}
    </div>

    <div class="absolute bottom-0 left-0 p-6 w-full">
        <div class="flex flex-col gap-1">
             <div class="flex items-center gap-3 text-white/80 text-[10px] font-black uppercase tracking-widest mb-1">
                <span class="flex items-center gap-1.5"><i class="fas fa-clock text-emerald-400"></i> {{ $tour->duration_days }} Days</span>
                <span class="flex items-center gap-1.5"><i class="fas fa-map-marker-alt text-emerald-400"></i> {{ Str::limit($tour->location, 15) }}</span>
            </div>
            <h3 class="text-xl font-bold text-white mb-0 leading-tight group-hover:text-emerald-400 transition-colors">{{ $tour->name }}</h3>
            @if(\App\Models\SiteSetting::get('show_public_pricing', false))
                <p class="text-emerald-400 font-bold mt-2 flex items-baseline gap-1">
                    <span class="text-[10px] opacity-60 uppercase">From</span>
                    <span>{{ config('safaris.currency', 'USD') }} {{ number_format($tour->price_per_person) }}</span>
                </p>
            @endif
        </div>
    </div>
</a>

<script>
if (typeof toggleLike !== 'function') {
    function toggleLike(btn, tourId) {
        const icon = btn.querySelector('i');
        const isLiked = btn.getAttribute('data-liked') === 'true';
        
        // Optimistic UI
        if (isLiked) {
            btn.classList.remove('bg-red-500');
            btn.classList.add('bg-white/20', 'text-white');
            btn.setAttribute('data-liked', 'false');
        } else {
            btn.classList.remove('bg-white/20', 'text-white');
            btn.classList.add('bg-red-500', 'text-white');
            btn.setAttribute('data-liked', 'true');
        }

        fetch(`/tours/${tourId}/like`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Update with actual server state if needed
            btn.setAttribute('data-liked', data.liked ? 'true' : 'false');
        })
        .catch(error => {
            console.error('Error:', error);
            // Revert on error
            if (isLiked) {
                btn.classList.add('bg-red-500');
                btn.classList.remove('bg-white/20', 'text-white');
                btn.setAttribute('data-liked', 'true');
            } else {
                btn.classList.add('bg-white/20', 'text-white');
                btn.classList.remove('bg-red-500');
                btn.setAttribute('data-liked', 'false');
            }
        });
    }
}
</script>
