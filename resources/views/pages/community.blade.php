<x-guest-layout>
    <div class="bg-gray-50  min-h-screen">

        @php
            $hero = $cmsPage?->sections->where('section_key', 'hero')->first()?->content ?? [
                'heading' => 'Our Community & Guest Stories',
                'subheading' => 'Real experiences from travelers who explored Africa with Ferdinand Safaris.',
            ];

            $reviews = $cmsPage?->sections->where('section_key', 'reviews')->first()?->content['items'] ?? [
                ['name' => 'Sarah M.', 'country' => 'USA', 'text' => 'An unforgettable safari! Our guide was incredible and the wildlife sightings were beyond expectations. We learned so much about the local ecosystem.', 'rating' => 5],
                ['name' => 'James K.', 'country' => 'UK', 'text' => 'Professional, friendly, and well organized. Ferdinand Safaris exceeded every expectation. The lodges were improved versions of what I imagined.', 'rating' => 5],
                ['name' => 'Anna L.', 'country' => 'Germany', 'text' => 'The perfect blend of adventure and comfort. Highly recommended for first-time safari travelers. The seamless transfers made everything stress-free.', 'rating' => 4],
                ['name' => 'Daniel W.', 'country' => 'Canada', 'text' => 'Excellent communication and attention to detail. We felt safe and cared for throughout. Seeing the Big Five was a dream come true.', 'rating' => 5],
                ['name' => 'Maria R.', 'country' => 'Spain', 'text' => 'A life-changing journey. Seeing Africa through local guides made all the difference. Their passion for conservation is inspiring.', 'rating' => 5],
            ];
        @endphp

        <!-- Hero -->
        <div class="relative bg-gradient-to-r from-emerald-800 to-emerald-600   text-white py-32 overflow-hidden">
            <div class="absolute inset-0 bg-black/20 pattern-grid-lg opacity-10"></div>
            <div class="relative w-full px-4 md:px-12 text-center z-10">
                <h1 class="text-5xl md:text-6xl font-extrabold mb-6 tracking-tight drop-shadow-lg">
                    {{ $hero['heading'] }}
                </h1>
                <p class="text-xl md:text-2xl opacity-90 max-w-3xl mx-auto font-light leading-relaxed drop-shadow-md">
                    {{ $hero['subheading'] }}
                </p>
            </div>
        </div>

        <!-- Testimonials -->
        <!-- Guest Stories Thread -->
        <div class="w-full px-4 md:px-12 py-16">

            <div class="relative space-y-12 max-w-screen-xl mx-auto">
                <!-- Thread Line (Visual connector) -->
                <div class="absolute left-8 top-8 bottom-8 w-0.5 bg-emerald-200  hidden md:block"></div>

                @foreach($reviews as $review)
                    <div class="relative flex items-start gap-6 group">

                        <!-- Avatar -->
                        <div class="relative z-10 shrink-0">
                            <div class="h-16 w-16 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-2xl shadow-lg ring-4 ring-white  group-hover:scale-110 transition duration-300">
                                {{ strtoupper(substr($review['name'], 0, 1)) }}
                            </div>
                        </div>

                        <!-- Content Bubble -->
                        <div class="flex-1 bg-white p-6 md:p-12 rounded-2xl shadow-md border border-gray-100  hover:shadow-xl transition duration-300 relative">
                            <!-- Arrow -->
                            <div class="absolute top-6 -left-3 w-6 h-6 bg-white  border-l border-b border-gray-100  transform rotate-45"></div>

                            <div class="relative z-10">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900 ">
                                            {{ $review['name'] }}
                                        </h3>
                                        <p class="text-sm text-emerald-600  font-medium">
                                            Traveling from {{ $review['country'] }}
                                        </p>
                                    </div>
                                    <div class="flex text-yellow-400 text-sm">
                                        @for($i = 0; $i < $review['rating']; $i++)
                                            <i class="fas fa-star"></i>
                                        @endfor
                                    </div>
                                </div>

                                <p class="text-gray-700  text-lg leading-relaxed italic">
                                    “{{ $review['text'] }}”
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Load More (Visual only for now) -->
            <div class="text-center mt-12 pb-8">
                 <button class="px-6 py-2 border-2 border-emerald-600 text-emerald-600  rounded-full font-semibold hover:bg-emerald-600 hover:text-white transition">
                    Load More Stories
                </button>
            </div>

            <!-- CTA -->
            <div class="mt-8 text-center bg-white  rounded-3xl p-8 md:p-12 shadow-lg border border-gray-100 max-w-screen-xl mx-auto">
                <h2 class="text-3xl font-bold text-emerald-800  mb-4">
                    Ready to Join Our Community?
                </h2>
                <p class="text-lg text-gray-600  mb-8 max-w-2xl mx-auto">
                    Let us help you create your own unforgettable safari story.
                </p>
                <a href="{{ route('contact') }}" class="inline-block bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-8 py-4 rounded-full shadow-lg hover:shadow-xl transition transform hover:-translate-y-1">
                    Plan Your Safari
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>
