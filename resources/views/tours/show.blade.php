<x-guest-layout>
    @push('meta')
        <title>{{ $tour->name }} - Ferdinand Safaris</title>
        <meta name="description" content="{{ Str::limit($tour->description, 160) }}">
        <meta property="og:title" content="{{ $tour->name }} | Ferdinand Safaris">
        <meta property="og:description" content="{{ Str::limit($tour->description, 160) }}">
        <meta property="og:image" content="{{ $tour->featured_image }}">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:type" content="website">
        <meta name="twitter:card" content="summary_large_image">
    @endpush
    <!-- Tour Header & Gallery -->
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10" 
         x-data="{ 
            isLiked: false,
            shareTour() {
                if (navigator.share) {
                    navigator.share({
                        title: '{{ $tour->name }}',
                        text: 'Check out this amazing safari: {{ $tour->name }}',
                        url: window.location.href
                    }).catch(console.error);
                } else {
                    navigator.clipboard.writeText(window.location.href);
                    alert('Link copied to clipboard!');
                }
            }
         }">
        <!-- Title and Category -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-4">
                    <span class="px-4 py-1.5 rounded-full text-[11px] font-black bg-emerald-100 text-emerald-700 uppercase tracking-[0.2em] border border-emerald-200/50 shadow-sm">
                        {{ $tour->category }}
                    </span>
                    @if($tour->is_featured)
                        <span class="px-4 py-1.5 rounded-full text-[11px] font-black bg-amber-100 text-amber-700 uppercase tracking-[0.2em] border border-amber-200/50 shadow-sm">
                            <i class="fas fa-star mr-1"></i> Featured
                        </span>
                    @endif
                </div>
                
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-black text-gray-900 leading-tight tracking-tight">
                    {{ $tour->name }}
                </h1>
                
                <div class="flex flex-wrap items-center gap-x-8 gap-y-3 mt-5 text-gray-600 font-bold text-sm">
                    <div class="flex items-center gap-2.5 hover:text-emerald-600 transition duration-300 group cursor-default">
                        <div class="w-8 h-8 rounded-full bg-emerald-50 flex items-center justify-center group-hover:bg-emerald-100 transition">
                            <i class="fas fa-map-marker-alt text-emerald-500"></i>
                        </div>
                        <span class="underline decoration-emerald-200 decoration-2 underline-offset-4">{{ $tour->location }}</span>
                    </div>
                    
                    <div class="flex items-center gap-2.5 hover:text-emerald-600 transition duration-300 group cursor-default">
                        <div class="w-8 h-8 rounded-full bg-emerald-50 flex items-center justify-center group-hover:bg-emerald-100 transition">
                            <i class="fas fa-clock text-emerald-500"></i>
                        </div>
                        <span>{{ $tour->duration_days }} Days Adventure</span>
                    </div>
                    
                    @if($tour->reviews_count > 0)
                        <div class="flex items-center gap-2.5">
                            <div class="flex items-center gap-1.5 px-3 py-1.5 bg-yellow-50 text-yellow-700 rounded-lg border border-yellow-100">
                                <i class="fas fa-star"></i>
                                <span class="font-black">{{ number_format($tour->rating, 1) }}</span>
                            </div>
                            <span class="text-gray-400 font-medium underline underline-offset-4 decoration-gray-200 group cursor-pointer hover:text-gray-600">({{ $tour->reviews_count }} reviews)</span>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <button @click="shareTour()" 
                        title="Share this tour"
                        class="flex items-center gap-2 px-5 py-3 rounded-xl hover:bg-gray-50 transition-all bg-white shadow-sm hover:text-emerald-600 group active:scale-95">
                    <i class="fas fa-share-alt group-hover:rotate-12 transition-transform"></i>
                    <span class="font-black text-xs uppercase tracking-widest hidden sm:inline">Share</span>
                </button>
                <button @click="isLiked = !isLiked" 
                        title="Add to favorites"
                        class="flex items-center gap-2 px-5 py-3 rounded-xl hover:bg-gray-50 transition-all bg-white shadow-sm hover:text-red-600 group active:scale-95">
                    <i :class="isLiked ? 'fas fa-heart text-red-500' : 'far fa-heart group-hover:scale-110 transition-transform'"></i>
                    <span class="font-black text-xs uppercase tracking-widest hidden sm:inline" :class="isLiked ? 'text-red-600' : ''" x-text="isLiked ? 'Saved' : 'Save'"></span>
                </button>
            </div>
        </div>

        <!-- Bento Grid Gallery -->
        @if($tour->gallery_images && count($tour->gallery_images) > 0)
            <div class="grid grid-cols-1 md:grid-cols-4 md:grid-rows-2 gap-3 h-[400px] md:h-[600px] rounded-2xl overflow-hidden shadow-sm border border-gray-100">
                <!-- Main Large Image -->
                <div class="md:col-span-2 md:row-span-2 relative group overflow-hidden">
                    <img src="{{ Str::startsWith($tour->gallery_images[0], 'http') ? $tour->gallery_images[0] : Storage::url($tour->gallery_images[0]) }}"
                         alt="{{ $tour->name }}"
                         class="w-full h-full object-cover transition duration-700 group-hover:scale-105 cursor-pointer"
                         onclick="window.open(this.src, '_blank')">
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/5 transition pointer-events-none"></div>
                </div>

                <!-- Smaller Images -->
                @foreach(array_slice($tour->gallery_images, 1, 4) as $index => $image)
                    <div class="hidden md:block relative group overflow-hidden">
                        <img src="{{ Str::startsWith($image, 'http') ? $image : Storage::url($image) }}"
                             alt="{{ $tour->name }}"
                             class="w-full h-full object-cover transition duration-700 group-hover:scale-105 cursor-pointer"
                             onclick="window.open(this.src, '_blank')">
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/5 transition pointer-events-none"></div>
                        
                        @if($index === 3 && count($tour->gallery_images) > 5)
                            <div class="absolute bottom-5 right-5 bg-white/95 backdrop-blur-md text-gray-900 px-5 py-2.5 rounded-xl text-sm font-black shadow-2xl flex items-center gap-2 cursor-pointer hover:bg-white hover:scale-105 transition active:scale-95 border border-gray-100"
                                 onclick="window.open('{{ Str::startsWith($tour->gallery_images[5], 'http') ? $tour->gallery_images[5] : Storage::url($tour->gallery_images[5]) }}', '_blank')">
                                <i class="fas fa-th"></i>
                                Show all photos
                            </div>
                        @endif
                    </div>
                @endforeach

                <!-- Mobile Swipe Overlay Hint (if needed) -->
                <div class="md:hidden grid grid-cols-2 gap-2 h-40">
                    @foreach(array_slice($tour->gallery_images, 1, 2) as $image)
                        <div class="rounded-xl overflow-hidden">
                            <img src="{{ Str::startsWith($image, 'http') ? $image : Storage::url($image) }}"
                                 class="w-full h-full object-cover">
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="aspect-video md:aspect-[21/9] rounded-2xl overflow-hidden shadow-lg border border-gray-100">
                <img src="{{ $tour->display_image }}"
                     alt="{{ $tour->name }}"
                     class="w-full h-full object-cover">
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
        <div class="grid lg:grid-cols-3 gap-6 sm:gap-10">

            <!-- Left Column: Details -->
            <div class="lg:col-span-2 space-y-6 sm:space-y-10">

                <!-- Quick Stats Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    <div
                        class="bg-white rounded-2xl p-6 shadow-md hover:shadow-xl transition-shadow border border-gray-100">
                        <div class="flex items-center gap-4 mb-2">
                            <div
                                class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                <i class="fas fa-clock text-xl"></i>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 uppercase font-bold tracking-wider mb-1">Duration</p>
                        <p class="font-bold text-gray-900 text-2xl">{{ $tour->duration_days }} Days</p>
                    </div>

                    <div
                        class="bg-white rounded-2xl p-6 shadow-md hover:shadow-xl transition-shadow border border-gray-100">
                        <div class="flex items-center gap-4 mb-2">
                            <div
                                class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                                <i class="fas fa-users text-xl"></i>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 uppercase font-bold tracking-wider mb-1">Group Size</p>
                        <p class="font-bold text-gray-900 text-2xl">Max {{ $tour->max_participants }}</p>
                    </div>

                    <div
                        class="bg-white rounded-2xl p-6 shadow-md hover:shadow-xl transition-shadow border border-gray-100 col-span-2 md:col-span-1">
                        <div class="flex items-center gap-4 mb-2">
                            <div
                                class="w-12 h-12 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center">
                                <i class="fas fa-map-marked-alt text-xl"></i>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 uppercase font-bold tracking-wider mb-1">Location</p>
                        <p class="font-bold text-gray-900 text-xl">{{ $tour->location }}</p>
                    </div>
                </div>


                <!-- Description -->
                <div class="bg-white rounded-2xl p-8 shadow-md border border-gray-100">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4 flex items-center gap-3">
                        <i class="fas fa-info-circle text-emerald-600"></i>
                        About this Tour
                    </h3>
                    <p class="text-gray-700 leading-relaxed text-lg">{{ $tour->description }}</p>
                </div>

                <!-- Highlights -->
                @if($tour->highlights)
                    <div class="bg-emerald-50/50 rounded-2xl p-8 border border-emerald-100">
                        <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                            <i class="fas fa-camera text-emerald-600"></i> Tour Highlights
                        </h3>
                        @if(is_array($tour->highlights))
                            <div class="grid md:grid-cols-2 gap-4">
                                @foreach($tour->highlights as $highlight)
                                    <div class="flex items-start gap-3">
                                        <div
                                            class="mt-1 w-5 h-5 rounded-full bg-emerald-200 flex items-center justify-center shrink-0">
                                            <i class="fas fa-check text-xs text-emerald-800"></i>
                                        </div>
                                        <span class="text-gray-700 font-medium">{{ $highlight }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="prose prose-emerald text-gray-700">
                                {!! nl2br(e((string) $tour->highlights)) !!}
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Itinerary -->
                @if($tour->itinerary)
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-8 flex items-center gap-3">
                            <i class="fas fa-route text-emerald-600"></i> Daily Itinerary
                        </h3>

                        <div class="relative border-l-2 border-emerald-100 ml-3 space-y-12 pb-4">
                            @if(is_array($tour->itinerary))
                                @foreach($tour->itinerary as $item)
                                    @php
                                        $day = $item['day'] ?? $loop->iteration;
                                        $title = $item['title'] ?? (is_string($item) ? '' : 'Day ' . $loop->iteration);
                                        $description = $item['description'] ?? (is_string($item) ? $item : '');

                                        if (is_string($item) && !isset($item['description'])) {
                                            $description = $item;
                                            $title = '';
                                        }
                                    @endphp
                                    <div class="relative pl-10 group">
                                        <!-- Dot -->
                                        <div
                                            class="absolute -left-[9px] top-1 h-[18px] w-[18px] rounded-full border-4 border-white bg-emerald-200 group-hover:bg-emerald-500 transition-colors shadow-sm">
                                        </div>

                                        <!-- Content Box -->
                                        <div
                                            class="bg-gray-50/50 hover:bg-white rounded-xl p-5 border border-gray-100 hover:border-emerald-100 transition-all shadow-sm hover:shadow-md">
                                            <div
                                                class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3 pb-3 border-b border-gray-100/50">
                                                <div class="flex items-center gap-3">
                                                    <span
                                                        class="inline-flex items-center justify-center h-7 px-3 rounded-full text-xs font-bold bg-emerald-600 text-white shadow-sm uppercase tracking-wide">
                                                        Day {{ $day }}
                                                    </span>
                                                    @if($title && $title !== "Day $day" && $title !== "Day " . $day)
                                                        <h4 class="text-base font-bold text-gray-900 leading-tight">
                                                            {{ $title }}
                                                        </h4>
                                                    @endif
                                                </div>
                                            </div>

                                            <div
                                                class="prose prose-sm prose-emerald max-w-none text-gray-600 leading-relaxed font-light">
                                                <p>{{ $description }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="pl-10">
                                    <div class="bg-white rounded-xl p-6 border border-gray-100 text-gray-600">
                                        {!! nl2br(e((string) $tour->itinerary)) !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Inclusions/Exclusions -->
                @if(!empty($tour->included_items) || !empty($tour->excluded_items))
                    <div class="grid md:grid-cols-2 gap-8">
                        @if(!empty($tour->included_items))
                            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                                <h4 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                                    <i class="fas fa-check-circle text-emerald-500"></i> What's Included
                                </h4>
                                @if(is_array($tour->included_items))
                                    <ul class="space-y-3">
                                        @foreach($tour->included_items as $item)
                                            <li class="flex items-start gap-3 text-sm text-gray-600">
                                                <i class="fas fa-check text-emerald-500 mt-0.5"></i>
                                                <span>{{ $item }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <div class="prose prose-sm text-gray-600">
                                        {!! nl2br(e((string) $tour->included_items)) !!}
                                    </div>
                                @endif
                            </div>
                        @endif

                        @if(!empty($tour->excluded_items))
                            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                                <h4 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                                    <i class="fas fa-times-circle text-red-500"></i> What's Not Included
                                </h4>
                                @if(is_array($tour->excluded_items))
                                    <ul class="space-y-3">
                                        @foreach($tour->excluded_items as $item)
                                            <li class="flex items-start gap-3 text-sm text-gray-600">
                                                <i class="fas fa-times text-red-400 mt-0.5"></i>
                                                <span>{{ $item }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <div class="prose prose-sm text-gray-600">
                                        {!! nl2br(e((string) $tour->excluded_items)) !!}
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endif


            </div>

            <!-- Right Column: Sticky Booking Card -->
            <div class="lg:col-span-1">
                <div class="sticky top-24 space-y-6">
                    <!-- Pricing Information (Conditional) -->
                    @if(\App\Models\SiteSetting::get('show_public_pricing', false))
                    <div class="bg-emerald-900 rounded-2xl p-8 mb-6 text-white shadow-xl shadow-emerald-900/20 relative overflow-hidden">
                        <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                        <div class="relative z-10">
                            <div class="flex items-center gap-2 text-emerald-300 text-[10px] font-black uppercase tracking-widest mb-2">
                                <i class="fas fa-tag"></i> Starting From
                            </div>
                            <div class="flex items-baseline gap-2">
                                <span class="text-4xl font-black">{{ config('safaris.currency', 'USD') }} {{ number_format($tour->price_per_person) }}</span>
                                <span class="text-emerald-300/80 text-xs font-bold">/ person</span>
                            </div>
                            <p class="text-emerald-200/60 text-[10px] mt-4 font-medium italic border-t border-white/10 pt-4">
                                * Final price depends on season, group size, and vehicle choice.
                            </p>
                        </div>
                    </div>
                    @endif

                    <div
                        class="bg-white rounded-3xl shadow-2xl shadow-gray-200/50 border border-gray-100 overflow-hidden transform transition hover:-translate-y-1 duration-300">
                        <div class="p-8">
                            <div class="mb-8">
                                <h3 class="text-2xl font-black text-gray-900 tracking-tight">Check Availability</h3>
                                <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest mt-1">Select your preferred date</p>
                            </div>

                            <div class="space-y-6">
                                <form action="{{ route('bookings.create', $tour) }}" method="GET">
                                    <div class="mb-6">
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Proposed Start Date</label>
                                        <input type="date" name="date" required min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                               class="w-full px-5 py-4 rounded-2xl bg-gray-50 border-0 focus:ring-2 focus:ring-emerald-500 outline-none transition font-bold text-gray-700 shadow-inner">
                                    </div>
                                    <button type="submit"
                                        class="block w-full bg-emerald-600 hover:bg-emerald-700 text-white text-center py-5 rounded-2xl font-black uppercase tracking-[0.2em] text-[11px] transition-all shadow-xl shadow-emerald-200/50 active:scale-95">
                                        Book This Adventure
                                    </button>
                                </form>

                                <a href="{{ route('contact') }}"
                                    class="block w-full bg-white border border-gray-100 text-gray-700 hover:bg-gray-50 text-center py-4 rounded-2xl font-black uppercase tracking-[0.2em] text-[10px] transition active:scale-95 shadow-sm">
                                    <i class="fas fa-envelope mr-2 text-emerald-500"></i> Request Information
                                </a>
                            </div>
                        </div>
                        <div
                            class="bg-gray-50/80 p-6 text-center text-[9px] text-gray-400 font-black uppercase tracking-widest border-t border-gray-100 flex justify-center gap-8">
                            <span class="flex items-center"><i class="fas fa-shield-alt mr-2 text-emerald-500/50 text-xs"></i> Secure</span>
                            <span class="flex items-center"><i class="fas fa-user-check mr-2 text-emerald-500/50 text-xs"></i> Expert Led</span>
                        </div>
                    </div>

                    <!-- Contact/Support Card -->
                    <div class="bg-blue-50/50 rounded-2xl p-6 border border-blue-100 text-center">
                        <div
                            class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-headset text-xl"></i>
                        </div>
                        <h4 class="font-bold text-gray-900 mb-1">Need Help?</h4>
                        <p class="text-sm text-gray-600 mb-4">Our safari experts are here to help you plan your trip.
                        </p>
                        <a href="tel:+254720968563" class="text-blue-600 font-bold hover:underline">
                            +254-720-968563
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-guest-layout>