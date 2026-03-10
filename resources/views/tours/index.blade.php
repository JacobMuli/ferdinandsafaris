<x-guest-layout>
    <x-slot name="title">
        Tours - Ferdinand Safaris
    </x-slot>

    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-emerald-50 to-blue-50 pt-24 pb-12 border-b border-gray-100">
        <div class="w-full px-4 md:px-12" x-data="{ filtersOpen: false }">
            <div class="flex items-center justify-between">
                <!-- Hero Content -->
                <div>
                    <h1 class="text-4xl md:text-5xl font-bold text-emerald-900 mb-4">
                        Explore Our Tours
                    </h1>
                    <p class="text-xl text-gray-600">
                        Discover unforgettable adventures across Africa
                    </p>
                </div>

                <!-- Filter Button -->
                <button
                    @click="filtersOpen = !filtersOpen"
                    class="inline-flex items-center px-6 py-3 bg-white border-2 border-emerald-600 rounded-lg text-emerald-600 font-semibold hover:bg-emerald-50 transition shadow-lg"
                >
                    <i class="fas fa-filter mr-2"></i>
                    Filters
                    <i class="fas fa-chevron-down ml-2 transition-transform"
                       :class="filtersOpen ? 'rotate-180' : ''"></i>
                </button>
            </div>

            <!-- Filter Dropdown -->
            <div
                x-show="filtersOpen"
                @click.away="filtersOpen = false"
                x-transition
                class="relative mt-6 w-full bg-white rounded-2xl shadow-2xl border border-gray-200 z-30"
                style="display: none;"
                x-data="tourFilters()"
            >
                <div class="p-6">
                    <div class="flex items-end gap-6">
                        <!-- Header -->
                        <div class="flex items-center gap-3 shrink-0">
                            <i class="fas fa-sliders-h text-emerald-600 text-lg"></i>
                            <h3 class="text-lg font-bold text-gray-900 whitespace-nowrap">
                                Filter Tours
                            </h3>
                        </div>

                        <!-- Filters Form -->
                        <form id="filterForm"
                              action="{{ route('tours.index') }}"
                              method="GET"
                              class="flex-1">
                            <div class="flex flex-wrap items-end gap-4">
                                <!-- Search -->
                                <div class="flex-1 min-w-96">
                                    <input
                                        type="text"
                                        name="search"
                                        value="{{ request('search') }}"
                                        placeholder="Search tours..."
                                        @input="autoSubmit"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition"
                                    >
                                </div>

                                <!-- Category -->
                                <div>
                                    <select
                                        name="category"
                                        @change="autoSubmit"
                                        class="w-64 px-16 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition"
                                    >
                                        <option value="all">All Categories</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat }}" @selected(request('category') === $cat)>
                                                {{ ucfirst($cat) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Min Days -->
                                <div class="w-32">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 shadow-sm">Min Days</label>
                                    <input
                                        type="number"
                                        name="duration_min"
                                        value="{{ request('duration_min') }}"
                                        placeholder="Min"
                                        @input="autoSubmit"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition text-sm"
                                    >
                                </div>

                                <!-- Max Days -->
                                <div class="w-32">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 shadow-sm">Max Days</label>
                                    <input
                                        type="number"
                                        name="duration_max"
                                        value="{{ request('duration_max') }}"
                                        placeholder="Max"
                                        @input="autoSubmit"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition text-sm"
                                    >
                                </div>

                                <!-- Price Range (Disabled per owner request) -->
                                <div class="opacity-40 cursor-not-allowed w-40">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Price Range</label>
                                    <select disabled class="w-full px-4 py-2 border border-gray-200 bg-gray-50 rounded-lg text-sm italic">
                                        <option>Coming Soon</option>
                                    </select>
                                </div>

                                <!-- Level (Disabled per owner request) -->
                                <div class="opacity-40 cursor-not-allowed w-32">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Level</label>
                                    <select disabled class="w-full px-4 py-2 border border-gray-200 bg-gray-50 rounded-lg text-sm italic">
                                        <option>Any</option>
                                    </select>
                                </div>
                            </div>
                        </form>

                        <!-- Clear All Button -->
                        <a href="{{ route('tours.index') }}"
                           class="shrink-0 text-sm font-semibold text-emerald-600 hover:text-emerald-700 whitespace-nowrap flex items-center gap-2">
                            <i class="fas fa-redo"></i>
                            Clear All
                        </a>
                    </div>

                    <!-- Active Filters Indicator -->
                    @if(request()->hasAny(['search','category','duration_min','duration_max']))
                        <div class="mt-4 bg-emerald-50 border border-emerald-200 rounded-lg p-3 flex justify-between items-center">
                            <div class="flex items-center text-emerald-700">
                                <i class="fas fa-info-circle mr-2"></i>
                                <span class="text-sm font-medium">Filters are active</span>
                            </div>
                            <a href="{{ route('tours.index') }}"
                               class="text-sm font-semibold text-emerald-600 hover:text-emerald-800">
                                Reset
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Tours Section -->
    <div class="w-full px-4 md:px-12 py-12">
        <!-- Sort Options and Results Count -->
        <div class="flex justify-between items-center mb-6">
            <p class="text-gray-600">
                <span class="font-semibold text-gray-900">{{ $tours->total() }}</span> tours found
            </p>
            <form action="{{ route('tours.index') }}" method="GET" class="flex items-center space-x-2">
                @foreach(request()->except('sort') as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <label class="text-sm text-gray-700">Sort by:</label>
                <select name="sort"
                        onchange="this.form.submit()"
                        class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                    <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Highest Rated</option>
                    <option value="duration" {{ request('sort') == 'duration' ? 'selected' : '' }}>Duration</option>
                </select>
            </form>
        </div>

        <!-- Tours List -->
        @if($tours->count() > 0)
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($tours as $tour)
                    <x-tour-card :tour="$tour" />
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $tours->links() }}
            </div>
        @else
            <div class="bg-white rounded-lg p-12 text-center">
                <i class="fas fa-search text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">No tours found</h3>
                <p class="text-gray-600 mb-4">Try adjusting your filters to find more tours</p>
                <a href="{{ route('tours.index') }}"
                   class="inline-block bg-emerald-600 text-white px-6 py-2 rounded-lg hover:bg-emerald-700 transition">
                    Clear All Filters
                </a>
            </div>
        @endif
    </div>

    <script>
        window.tourFilters = function () {
            return {
                debounceTimer: null,

                autoSubmit() {
                    clearTimeout(this.debounceTimer);
                    this.debounceTimer = setTimeout(() => {
                        document.getElementById('filterForm').submit();
                    }, 500);
                }
            }
        }
    </script>
</x-guest-layout>
