<x-guest-layout>
    <x-slot name="header">
        <style>
            [x-cloak] {
                display: none !important;
            }

            .hero-gradient {
                background: linear-gradient(135deg, #1e3a8a 0%, #059669 100%);
            }

            .card-hover {
                transition: all 0.3s ease;
            }

            .card-hover:hover {
                transform: translateY(-8px);
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            }
        </style>
    </x-slot>

    <!-- Hero Section with Image Background -->
    <section class="relative w-full min-h-screen overflow-hidden flex items-center justify-center">
        <!-- Image Background -->
        <div class="absolute inset-0 z-0">
            <img src="{{ $hero['bg_image'] ?? 'https://images.unsplash.com/photo-1516426122078-c23e76319801' }}"
                alt="Safari Background" class="w-full h-full object-cover">
            <!-- Dark overlay for text readability -->
            <div class="absolute inset-0 bg-black/50"></div>
        </div>

        <!-- Hero Content -->
        <div class="relative z-10 w-full px-4 md:px-12 py-20 md:py-32">
            <div class="text-center" x-data="{ show: false }" x-init="setTimeout(() => show = true, 100)">
                <div x-show="show" x-transition:enter="transition ease-out duration-700"
                    x-transition:enter-start="opacity-0 transform translate-y-8"
                    x-transition:enter-end="opacity-100 transform translate-y-0">
                    <h1 class="text-5xl md:text-7xl font-bold text-white mb-6 drop-shadow-2xl">
                        {{ $hero['heading'] ?? "Discover Africa's Wonders" }}
                    </h1>
                    <p class="text-xl md:text-2xl text-white mb-8 max-w-3xl mx-auto drop-shadow-lg">
                        {{ $hero['subheading'] ?? "Experience unforgettable safari adventures with expert guides and luxury accommodations" }}
                    </p>


                    <!-- Enhanced Search Bar with Autocomplete -->
                    <div class="max-w-2xl mx-auto" x-data="searchAutocomplete()">
                        <form action="{{ route('tours.index') }}" method="GET" class="relative">
                            <input type="text" name="search" x-model="searchQuery" @input.debounce.300ms="fetchResults"
                                @focus="showResults = true" @click.away="showResults = false"
                                placeholder="Search for tours, parks, or locations..."
                                   class="w-full pl-6 pr-20 py-4 rounded-full text-gray-900 text-lg border-2 border-transparent focus:border-emerald-500 focus:ring-0 shadow-2xl outline-none transition-all placeholder-gray-500"
                                autocomplete="off">
                            <button type="submit"
                                    class="absolute right-2 top-2 bottom-2 bg-emerald-600 text-white rounded-full px-4 md:px-6 hover:bg-emerald-700 transition flex items-center justify-center">
                                <i class="fas fa-search text-xl"></i>
                            </button>

                            <!-- Autocomplete Dropdown -->
                            <div x-show="showResults && results.length > 0" x-transition
                                class="absolute top-full mt-2 left-0 right-16 bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden z-50 max-h-80 overflow-y-auto">
                                <template x-for="(result, index) in results" :key="index">
                                    <a :href="result.url"
                                        class="flex items-center gap-3 px-4 py-3 hover:bg-emerald-50 transition cursor-pointer border-b border-gray-100 last:border-0">
                                        <div class="text-xs font-semibold px-2 py-1 rounded-full flex-shrink-0" :class="{
                                                 'bg-emerald-100 text-emerald-700': result.type === 'tour',
                                                 'bg-green-100 text-green-700': result.type === 'park',
                                                 'bg-blue-100 text-blue-700': result.type === 'location'
                                             }" x-text="result.category">
                                        </div>
                                        <div class="font-medium text-gray-900 truncate flex-1" x-text="result.title">
                                        </div>
                                    </a>
                                </template>
                            </div>

                            <!-- Loading Indicator -->
                            <div x-show="loading" class="absolute right-20 top-1/2 -translate-y-1/2">
                                <i class="fas fa-spinner fa-spin text-emerald-600"></i>
                            </div>
                        </form>
                    </div>

                </div>
            </div>

            <!-- Stats -->
            <div class="mt-20 grid grid-cols-2 md:grid-cols-4 gap-8 text-center text-white">
                @foreach($stats as $stat)
                    <div>
                        <div class="text-4xl font-bold mb-2">{{ $stat['value'] }}</div>
                        <div class="text-white/80">{{ $stat['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 bg-white " id="features">
        <div class="w-full px-4 md:px-12">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900  mb-4">Why Choose Ferdinand Safaris</h2>
                <p class="text-xl text-gray-600 ">Experience the difference with our premium services</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="bg-emerald-100  w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user-tie text-emerald-600  text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900  mb-2">Expert Guides</h3>
                    <p class="text-gray-600 ">Professional guides with decades of experience</p>
                </div>

                <div class="text-center">
                    <div class="bg-blue-100  w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shield-alt text-blue-600  text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900  mb-2">Safety First</h3>
                    <p class="text-gray-600 ">Comprehensive insurance and safety measures</p>
                </div>

                <div class="text-center">
                    <div class="bg-purple-100  w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-home text-purple-600  text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900  mb-2">Luxury Lodges</h3>
                    <p class="text-gray-600 ">Stay in premium accommodations throughout</p>
                </div>

                <div class="text-center">
                    <div class="bg-orange-100  w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-percent text-orange-600  text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900  mb-2">Best Prices</h3>
                    <p class="text-gray-600 ">Group and corporate discounts available</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Top Destinations Section -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 md:px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Top Destinations</h2>
                <div class="h-1 w-24 bg-emerald-600 mx-auto rounded-full"></div>
                <p class="mt-4 text-xl text-gray-600">Explore the most iconic parks and reserves in Kenya</p>
            </div>

            <!-- Top Destinations Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                @foreach($topDestinations->take(8) as $destination)
                    <div class="group relative h-72 sm:h-80 rounded-xl overflow-hidden shadow-lg cursor-pointer transform transition-all duration-500 hover:-translate-y-2">
                        <img src="{{ $destination->featured_image_url }}" alt="{{ $destination->name }}"
                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent">
                        </div>

                        <!-- Content -->
                        <div class="absolute bottom-0 left-0 right-0 p-4 sm:p-6">
                            <h3 class="text-lg sm:text-xl font-bold text-white mb-2">
                                {{ $destination->name }}
                            </h3>
                            <p class="text-sm font-medium text-emerald-300">
                                <i class="fas fa-route mr-1"></i>
                                <span>{{ $destination->tours_count }} Tours</span>
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-12 text-center">
                <a href="{{ route('tours.index') }}"
                    class="inline-block border-2 border-emerald-600 text-emerald-600 px-6 sm:px-8 py-3 rounded-full font-bold hover:bg-emerald-600 hover:text-white transition-colors duration-300 text-sm sm:text-base">
                    View All Destinations
                </a>
            </div>
        </div>
    </section>

    <!-- Featured Tours Section -->
    <section class="py-20 bg-white" id="tours">
        <div class="w-full px-4 md:px-12">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900  mb-4">Featured Tours</h2>
                <p class="text-xl text-gray-600 ">Handpicked adventures for your next journey</p>
            </div>

            <!-- Dynamic Carousel -->
            <div x-data="{
                scrollContainer: null,
                autoScrollInterval: null,
                init() {
                    this.scrollContainer = this.$refs.container;
                    this.startAutoScroll();
                },
                startAutoScroll() {
                    // Clear existing interval just in case
                    this.stopAutoScroll();
                    this.autoScrollInterval = setInterval(() => {
                        this.scroll('right');
                    }, 5000); // Scroll every 5 seconds
                },
                stopAutoScroll() {
                    if (this.autoScrollInterval) {
                        clearInterval(this.autoScrollInterval);
                        this.autoScrollInterval = null;
                    }
                },
                scroll(direction) {
                    const container = this.scrollContainer;
                    if (!container) return;

                    const scrollAmount = container.offsetWidth; // Scroll one screen width
                    const maxScroll = container.scrollWidth - container.clientWidth;

                    if (direction === 'right') {
                        if (container.scrollLeft + container.clientWidth >= container.scrollWidth - 10) {
                             // Reset to start smoothly
                             container.scrollTo({ left: 0, behavior: 'smooth' });
                        } else {
                            container.scrollBy({ left: scrollAmount, behavior: 'smooth' });
                        }
                    } else {
                        container.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
                    }
                }
            }" @mouseenter="stopAutoScroll()" @mouseleave="startAutoScroll()" class="relative group">

                <!-- Controls -->
                <button @click="scroll('left')"
                    class="absolute left-0 top-1/2 -translate-y-1/2 -ml-4 z-10 w-12 h-12 bg-white  text-emerald-600 rounded-full shadow-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 focus:outline-none hover:bg-gray-50  hover:scale-110 transform">
                    <i class="fas fa-chevron-left text-xl"></i>
                </button>
                <button @click="scroll('right')"
                    class="absolute right-0 top-1/2 -translate-y-1/2 -mr-4 z-10 w-12 h-12 bg-white  text-emerald-600 rounded-full shadow-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 focus:outline-none hover:bg-gray-50  hover:scale-110 transform">
                    <i class="fas fa-chevron-right text-xl"></i>
                </button>

                <!-- Carousel Container -->
                <div x-ref="container" class="flex overflow-x-auto gap-8 pb-8 snap-x snap-mandatory hide-scrollbar">
                    @forelse($featuredTours as $tour)
                        <div class="flex-shrink-0 w-full md:w-[calc(50%-1rem)] lg:w-[calc(33.333%-2rem)] snap-center">
                            <div
                                class="bg-white  rounded-2xl overflow-hidden shadow-lg h-full flex flex-col transition-all duration-300 hover:-translate-y-3 hover:shadow-2xl border border-transparent hover:border-emerald-500/20">
                                <div class="relative h-64 overflow-hidden">
                                    <img src="{{ $tour->display_image }}" alt="{{ $tour->name }}"
                                        class="w-full h-full object-cover transition-transform duration-500 hover:scale-110">
                                    <div
                                        class="absolute top-4 right-4 bg-emerald-600 text-white px-3 py-1 rounded-full text-sm font-semibold shadow-md">
                                        Featured
                                    </div>
                                </div>
                                <div class="p-6 flex-1 flex flex-col">
                                    <div class="flex items-center justify-between mb-2">
                                        <span
                                            class="text-emerald-600 font-semibold text-sm uppercase tracking-wide">{{ $tour->category }}</span>
                                        <div class="flex items-center">
                                            <i class="fas fa-star text-yellow-400"></i>
                                            <span class="ml-1 text-gray-600  text-sm font-medium">New</span>
                                        </div>
                                    </div>
                                    <h3 class="text-2xl font-bold text-gray-900  mb-2 line-clamp-1">{{ $tour->name }}</h3>
                                    <p class="text-gray-600  mb-4 line-clamp-2 text-sm flex-1">
                                        {{ Str::limit($tour->description, 100) }}</p>

                                    <div class="space-y-4 mt-auto">
                                        <div
                                            class="flex items-center justify-between text-sm text-gray-500  border-t border-gray-100  pt-4">
                                            <span><i class="fas fa-clock mr-1 text-emerald-500"></i>
                                                {{ $tour->duration_days }} Days</span>
                                            <span><i class="fas fa-users mr-1 text-emerald-500"></i> Max
                                                {{ $tour->max_participants }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <div>
                                                @if(\App\Models\SiteSetting::get('show_public_pricing', true))
                                                    <span class="text-2xl font-bold text-gray-900 ">${{ number_format($tour->price_per_person) }}</span>
                                                    <span class="text-gray-500  text-xs">/person</span>
                                                @else
                                                    <span class="text-sm font-semibold text-emerald-600">Contact for Pricing</span>
                                                @endif
                                            </div>
                                            <a href="{{ route('tours.show', $tour) }}"
                                                class="bg-emerald-600 text-white px-6 py-2 rounded-full hover:bg-emerald-700 transition shadow-md hover:shadow-lg transform active:scale-95 duration-200">
                                                View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="w-full text-center py-12">
                            <p class="text-gray-500 ">No featured tours available at the moment.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <style>
                .hide-scrollbar::-webkit-scrollbar {
                    display: none;
                }

                .hide-scrollbar {
                    -ms-overflow-style: none;
                    scrollbar-width: none;
                }
            </style>

            <div class="text-center mt-12">
                <a href="/tours"
                    class="inline-flex items-center bg-emerald-600 text-white px-8 py-4 rounded-full font-semibold text-lg hover:bg-emerald-700 transition">
                    View All Tours
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Testimonials Carousel -->
    <section class="py-20 bg-gray-50 ">
        <div class="w-full px-4 md:px-12">

            <!-- Section Header -->
            <div class="text-center mb-14">
                <h2 class="text-4xl font-bold text-gray-900  mb-4">
                    What Our Travelers Say
                </h2>
                <p class="text-xl text-gray-600  max-w-3xl mx-auto">
                    Real experiences from guests who explored Africa with Ferdinand Safaris
                </p>
            </div>

            <!-- Carousel -->
            <div x-data="{
                    container: null,
                    interval: null,
                    scrollSpeed: 1, // pixels per tick
                    isHovered: false,
                    init() {
                        this.container = this.$refs.track;
                        // Clone the content for infinite scroll illusion if needed,
                        // but CSS animation or JS scroll adjustment is cleaner.
                        // Im implementing a smooth continuous scroll that pauses on hover.
                        this.start();
                    },
                    start() {
                        this.stop();
                        this.interval = setInterval(() => {
                            if (!this.isHovered && this.container) {
                                this.container.scrollLeft += this.scrollSpeed;
                                // Reset if reached the end of the first set (approx midpoint of scrollWidth)
                                // We rely on the cloned items being present.
                                if (this.container.scrollLeft >= (this.container.scrollWidth / 2)) {
                                    this.container.scrollLeft = 0;
                                }
                            }
                        }, 20); // Smooth update rate
                    },
                    stop() {
                        if (this.interval) clearInterval(this.interval);
                    },
                    pause() {
                        this.isHovered = true;
                    },
                    resume() {
                        this.isHovered = false;
                        this.start(); // Ensure it's running
                    },
                    next() {
                         const cardWidth = this.container.firstElementChild.offsetWidth + 32; // width + gap
                         this.container.scrollBy({ left: cardWidth, behavior: 'smooth' });
                    },
                    prev() {
                         const cardWidth = this.container.firstElementChild.offsetWidth + 32;
                         this.container.scrollBy({ left: -cardWidth, behavior: 'smooth' });
                    }
                }" x-init="init()" @mouseenter="pause()" @mouseleave="resume()" class="relative group">

                <!-- Controls -->
                <button @click="prev"
                    class="absolute left-0 top-1/2 -translate-y-1/2 z-10 w-12 h-12 bg-white  text-emerald-600 rounded-full shadow-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition hover:scale-110">
                    <i class="fas fa-chevron-left"></i>
                </button>

                <button @click="next"
                    class="absolute right-0 top-1/2 -translate-y-1/2 z-10 w-12 h-12 bg-white  text-emerald-600 rounded-full shadow-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition hover:scale-110">
                    <i class="fas fa-chevron-right"></i>
                </button>

                <!-- Track -->
                <div x-ref="track" class="flex overflow-x-hidden gap-8 pb-8 select-none" style="scroll-behavior: auto;">
                    {{-- Original Set --}}
                    @foreach($testimonials as $t)
                        <div class="flex-shrink-0 w-full md:w-1/2 lg:w-1/3 min-w-[300px]">
                            <div
                                class="bg-white  rounded-2xl p-8 shadow-lg h-full flex flex-col border border-gray-100  hover:-translate-y-2 transition duration-300">
                                <!-- Stars -->
                                <div class="flex text-yellow-400 mb-4">
                                    @for($i = 0; $i < $t->rating; $i++)
                                        <i class="fas fa-star"></i>
                                    @endfor
                                </div>
                                <!-- Text -->
                                <p class="text-gray-600  text-lg mb-6 flex-1 italic">
                                    “{{ $t->message }}”
                                </p>
                                <!-- Author -->
                                <div class="flex items-center mt-auto">
                                    <div
                                        class="h-12 w-12 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold mr-4 text-xl">
                                        {{ strtoupper(substr($t->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 ">
                                            {{ $t->name }}
                                        </p>
                                        <p class="text-sm text-gray-500 ">
                                            {{ $t->country ?? 'International Guest' }}
                                        </p>
                                    </div>
                                    @if($t->source === 'google')
                                        <div class="ml-auto text-gray-400" title="Posted on Google">
                                            <i class="fab fa-google"></i>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach

                    {{-- Duplicated Set for Infinite Loop --}}
                    @foreach($testimonials as $t)
                        <div class="flex-shrink-0 w-full md:w-1/2 lg:w-1/3 min-w-[300px]" aria-hidden="true">
                            <div
                                class="bg-white  rounded-2xl p-8 shadow-lg h-full flex flex-col border border-gray-100  hover:-translate-y-2 transition duration-300">
                                <!-- Stars -->
                                <div class="flex text-yellow-400 mb-4">
                                    @for($i = 0; $i < $t->rating; $i++)
                                        <i class="fas fa-star"></i>
                                    @endfor
                                </div>
                                <!-- Text -->
                                <p class="text-gray-600  text-lg mb-6 flex-1 italic">
                                    “{{ $t->message }}”
                                </p>
                                <!-- Author -->
                                <div class="flex items-center mt-auto">
                                    <div
                                        class="h-12 w-12 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold mr-4 text-xl">
                                        {{ strtoupper(substr($t->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 ">
                                            {{ $t->name }}
                                        </p>
                                        <p class="text-sm text-gray-500 ">
                                            {{ $t->country ?? 'International Guest' }}
                                        </p>
                                    </div>
                                    @if($t->source === 'google')
                                        <div class="ml-auto text-gray-400" title="Posted on Google">
                                            <i class="fab fa-google"></i>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>

            <!-- Trust Line -->
            <div class="mt-12 text-center">
                <p class="text-gray-500 ">
                    ⭐ Rated 4.9/5 by travelers from over 30 countries
                </p>
            </div>
        </div>

        <style>
            .hide-scrollbar::-webkit-scrollbar {
                display: none;
            }

            .hide-scrollbar {
                scrollbar-width: none;
                -ms-overflow-style: none;
            }
        </style>
    </section>


    <!-- FAQ Section -->
    <section class="py-24 bg-gray-50">
        <div class="w-full px-4 md:px-12 max-w-4xl mx-auto">
            <h2 class="text-3xl font-bold text-gray-900 mb-12 text-center underline decoration-emerald-500 decoration-4 underline-offset-8">Frequently Asked Questions</h2>
            
            <div x-data="{ active: null }" class="space-y-4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <button @click="active = active === 0 ? null : 0" class="w-full px-6 py-4 text-left flex justify-between items-center hover:bg-gray-50 transition">
                        <span class="font-semibold text-gray-900">What is the best time to visit East Africa for a safari?</span>
                        <i :class="active === 0 ? 'fa-minus' : 'fa-plus'" class="fas text-emerald-600"></i>
                    </button>
                    <div x-show="active === 0" x-collapse class="px-6 py-4 text-gray-600 border-t border-gray-50">
                        The best time is during the dry season from June to October, which also coincides with the Great Migration in the Serengeti and Masai Mara.
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <button @click="active = active === 1 ? null : 1" class="w-full px-6 py-4 text-left flex justify-between items-center hover:bg-gray-50 transition">
                        <span class="font-semibold text-gray-900">What should I pack for my safari trip?</span>
                        <i :class="active === 1 ? 'fa-minus' : 'fa-plus'" class="fas text-emerald-600"></i>
                    </button>
                    <div x-show="active === 1" x-collapse class="px-6 py-4 text-gray-600 border-t border-gray-50">
                        Pack light, breathable clothing in neutral colors (khaki, olive, tan). Don't forget a warm jacket for morning drives, a hat, sunscreen, and good binoculars.
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <button @click="active = active === 2 ? null : 2" class="w-full px-6 py-4 text-left flex justify-between items-center hover:bg-gray-50 transition">
                        <span class="font-semibold text-gray-900">Do I need vaccinations before traveling?</span>
                        <i :class="active === 2 ? 'fa-minus' : 'fa-plus'" class="fas text-emerald-600"></i>
                    </button>
                    <div x-show="active === 2" x-collapse class="px-6 py-4 text-gray-600 border-t border-gray-50">
                        Yes, typically Yellow Fever, Typhoid, and Hepatitis A/B are recommended. Malaria prophylaxis is also essential. Please consult your travel clinic 6-8 weeks before departure.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="py-24 relative overflow-hidden bg-emerald-900">
        <div class="absolute inset-0 pattern-grid-lg opacity-10"></div>
        <div class="w-full px-4 md:px-12 relative z-10 text-center">
            <div class="max-w-3xl mx-auto bg-white/10 backdrop-blur-md p-12 rounded-3xl border border-white/20">
                <h2 class="text-4xl font-bold text-white mb-4">Join the Adventure</h2>
                <p class="text-emerald-100 mb-8 text-lg">Subscribe to our newsletter for exclusive safari tips, wildlife updates, and limited-time offers.</p>
                
                <form id="newsletterForm" class="flex flex-col md:flex-row gap-4 max-w-xl mx-auto">
                    <input type="email" id="newsletterEmail" placeholder="Enter your email address" required
                        class="flex-1 px-6 py-4 rounded-full bg-white/20 border border-white/30 text-white placeholder-emerald-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <button type="submit" class="bg-white text-emerald-800 px-8 py-4 rounded-full font-bold hover:bg-emerald-50 transition transform hover:scale-105 active:scale-95">
                        Subscribe Now
                    </button>
                </form>
                <div id="newsletterMessage" class="mt-4 text-sm font-medium hidden"></div>
            </div>
        </div>
    </section>

    <!-- Trust Symbols Section -->
    <section class="py-12 border-b border-gray-100 bg-white">
        <div class="w-full px-4 md:px-12">
            <div class="flex flex-wrap justify-center items-center gap-12 opacity-50 grayscale hover:grayscale-0 transition-all duration-500">
                <img src="https://www.katokenya.org/images/logo.png" alt="KATO" class="h-12 object-contain">
                <img src="https://atta.travel/app/themes/atta/dist/images/atta-logo.svg" alt="ATTA" class="h-12 object-contain">
                <img src="https://www.ktb.go.ke/templates/ktb/images/logo.png" alt="Magical Kenya" class="h-12 object-contain">
                <div class="flex items-center gap-2 border-l border-gray-300 pl-12">
                    <i class="fas fa-certificate text-emerald-600 text-2xl"></i>
                    <span class="font-bold text-gray-800 text-lg">Licensed Safari Tour Operator</span>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="bg-emerald-600 py-20">
        <div class="w-full px-4 md:px-12 text-center">
            <h2 class="text-4xl font-bold text-white mb-6">Ready for Your Adventure?</h2>
            <p class="text-xl text-white/90 mb-8 max-w-2xl mx-auto">
                Our team is ready to craft your perfect safari itinerary. Get in touch with us today to start planning
                your dream trip.
            </p>
            <a href="{{ route('contact') }}"
                class="inline-flex items-center bg-white text-emerald-600 px-8 py-4 rounded-full font-semibold text-lg hover:bg-gray-100 transition">
                Contact Us
                <i class="fas fa-envelope ml-2"></i>
            </a>
        </div>
    </section>

    <script>
        function searchAutocomplete() {
            return {
                searchQuery: '',
                results: [],
                showResults: false,
                loading: false,

                async fetchResults() {
                    if (this.searchQuery.length < 2) {
                        this.results = [];
                        return;
                    }

                    this.loading = true;

                    try {
                        const response = await fetch(`/api/search/autocomplete?q=${encodeURIComponent(this.searchQuery)}`);
                        const data = await response.json();
                        this.results = data;
                        this.showResults = true;
                    } catch (error) {
                        console.error('Search error:', error);
                        this.results = [];
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</x-guest-layout>