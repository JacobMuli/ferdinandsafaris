<x-guest-layout>
    <div class="bg-white  overflow-hidden">

        @php
            $hero = $cmsPage?->sections->where('section_key', 'hero')->first()?->content ?? [
                'badge' => 'UNKNOWN • UNTAMED • UNFORGETTABLE',
                'heading' => 'We Are <span class="text-emerald-500">Ferdinand.</span>',
                'subheading' => "Crafting award-winning African safaris since 2010. We don't just show you Africa; we help you feel its heartbeat.",
                'bg_image' => 'https://images.unsplash.com/photo-1470770841072-f978cf4d019e?auto=format&fit=crop&w=2000&q=80',
            ];

            $stats = $cmsPage?->sections->where('section_key', 'stats')->first()?->content['items'] ?? [
                ['value' => '15+', 'label' => 'Years Experience'],
                ['value' => '5k+', 'label' => 'Happy Travelers'],
                ['value' => '24/7', 'label' => 'On-Trip Support'],
                ['value' => '100%', 'label' => 'Local Guides'],
            ];

            $story = $cmsPage?->sections->where('section_key', 'story')->first()?->content ?? [
                'heading' => 'Deep roots in <br><span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-500">African Soil.</span>',
                'body_1' => 'Ferdinand Safaris was born from a simple belief: that the best way to see Africa is through the eyes of those who call it home.',
                'body_2' => 'We are not just a travel agency; we are custodians of the land and storytellers of the wild. Every itinerary we craft is an invitation to conservation, cultural understanding, and adventure.',
                'image_1' => 'https://images.unsplash.com/photo-1550953684-257a3e5c9823?auto=format&fit=crop&w=800&q=80',
                'image_2' => 'https://images.unsplash.com/photo-1516426122078-c23e76319801?auto=format&fit=crop&w=800&q=80',
            ];

            $values = $cmsPage?->sections->where('section_key', 'values')->first()?->content ?? [
                'heading' => 'Our Core Values',
                'subheading' => 'The principles that guide every journey we lead.',
                'items' => [
                     [
                        'icon_class' => 'fas fa-hand-holding-heart',
                        'title' => 'Community First',
                        'description' => 'We ensure that local communities benefit directly from your visit, creating a sustainable ecosystem for tourism.',
                        'color' => 'emerald'
                    ],
                    [
                        'icon_class' => 'fas fa-paw',
                        'title' => 'Active Conservation',
                        'description' => 'We partner with conservation trusts to protect endangered species and habitats for future generations.',
                        'color' => 'teal'
                    ],
                    [
                        'icon_class' => 'fas fa-gem',
                        'title' => 'Uncompromised Quality',
                        'description' => 'From vehicles to lodges, we select only the best to ensure your comfort and safety in the wild.',
                        'color' => 'orange'
                    ]
                ]
            ];
        @endphp

        <!-- Modern Hero Section -->
        <div class="relative min-h-[60vh] flex items-center justify-center">
            <!-- Background Image with Overlay -->
            <div class="absolute inset-0 z-0">
                <img src="{{ $hero['bg_image'] }}" alt="Safari Landscape" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-b from-black/60 to-gray-900/90 mix-blend-multiply"></div>
            </div>

            <div class="relative z-10 w-full px-4 md:px-12 text-center">
                <span class="inline-block py-1 px-3 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-sm font-semibold tracking-wider mb-6 backdrop-blur-sm">
                    {{ $hero['badge'] }}
                </span>
                <h1 class="text-6xl md:text-8xl font-black text-white tracking-tighter mb-8 shadow-sm">
                    {!! $hero['heading'] !!}
                </h1>
                <p class="text-xl md:text-2xl text-gray-200 max-w-3xl mx-auto font-light leading-relaxed">
                    {{ $hero['subheading'] }}
                </p>
            </div>
        </div>

        <!-- Stats Section (Floating) -->
        <div class="relative -mt-16 z-20 w-full px-4 md:px-12">
            <div class="bg-white  rounded-3xl shadow-2xl border border-gray-100  p-8 md:p-12 grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                @foreach($stats as $stat)
                <div class="space-y-2">
                    <div class="text-4xl md:text-5xl font-black text-emerald-600 ">{{ $stat['value'] }}</div>
                    <div class="text-gray-500  text-sm font-bold uppercase tracking-wide">{{ $stat['label'] }}</div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Our Story / Philosophy -->
        <div class="w-full px-4 md:px-12 py-24 md:py-32">
            <div class="grid lg:grid-cols-2 gap-20 items-center">
                <div class="space-y-8">
                    <h2 class="text-4xl md:text-5xl font-bold text-gray-900  leading-tight">
                        {!! $story['heading'] !!}
                    </h2>
                    <div class="prose prose-lg ">
                        <p class="text-gray-600 ">
                            {{ $story['body_1'] }}
                        </p>
                        <p class="text-gray-600 ">
                            {{ $story['body_2'] }}
                        </p>
                    </div>
                </div>
                <div class="relative">
                    <div class="aspect-square rounded-3xl overflow-hidden shadow-2xl rotate-3">
                        <img src="{{ $story['image_1'] }}" alt="Lion Encounter" class="w-full h-full object-cover">
                    </div>
                    <!-- Secondary Image -->
                     <div class="absolute -bottom-10 -left-10 w-1/2 aspect-square rounded-3xl overflow-hidden shadow-2xl -rotate-6 border-4 border-white ">
                        <img src="{{ $story['image_2'] }}" alt="Guide with Guests" class="w-full h-full object-cover">
                    </div>
                </div>
            </div>
        </div>

        <!-- Values Section -->
        <div class="bg-gray-50  py-24">
            <div class="w-full px-4 md:px-12">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900  mb-4">{{ $values['heading'] }}</h2>
                    <p class="text-gray-600 ">{{ $values['subheading'] }}</p>
                </div>

                <div class="grid md:grid-cols-3 gap-10">
                    @foreach($values['items'] as $value)
                    <div class="bg-white  p-8 rounded-2xl shadow-sm hover:shadow-lg transition duration-300 border-t-4 border-{{ $value['color'] ?? 'emerald' }}-500">
                        <div class="w-12 h-12 bg-{{ $value['color'] ?? 'emerald' }}-100  rounded-xl flex items-center justify-center text-{{ $value['color'] ?? 'emerald' }}-600 mb-6">
                            <i class="{{ $value['icon_class'] }} text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900  mb-3">{{ $value['title'] }}</h3>
                        <p class="text-gray-600 ">
                            {{ $value['description'] }}
                        </p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Company Information Section -->
        <div class="bg-white py-20">
            <div class="w-full px-4 md:px-12">
                <div class="max-w-5xl mx-auto">
                    <!-- About Company -->
                    <div class="text-center mb-16">
                        <span class="inline-block px-4 py-2 bg-emerald-100 text-emerald-700 rounded-full text-sm font-bold uppercase tracking-wider mb-4">
                            Who We Are
                        </span>
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                            Ferdinand Kenya Tours and Safaris
                        </h2>
                        <p class="text-lg text-gray-600 leading-relaxed max-w-3xl mx-auto">
                            A premier travel company based in Mombasa, Kenya, specializing in creating unforgettable safari experiences across East Africa with professional service, deep local knowledge, and passion for wildlife conservation.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Our Services -->
        <div class="bg-gradient-to-br from-gray-50 to-emerald-50/30 py-20">
            <div class="w-full px-4 md:px-12">
                <div class="max-w-6xl mx-auto">
                    <div class="text-center mb-12">
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Our Services</h2>
                        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                            Comprehensive travel solutions tailored for our valued clients worldwide
                        </p>
                    </div>

                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="group bg-white p-6 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-emerald-200">
                            <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                <i class="fas fa-hotel text-white text-xl"></i>
                            </div>
                            <h4 class="font-bold text-gray-900 mb-2 text-lg">Accommodation Services</h4>
                            <p class="text-sm text-gray-600">Hotels, resorts, and apartment reservations</p>
                        </div>

                        <div class="group bg-white p-6 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-blue-200">
                            <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                <i class="fas fa-users text-white text-xl"></i>
                            </div>
                            <h4 class="font-bold text-gray-900 mb-2 text-lg">Event Planning</h4>
                            <p class="text-sm text-gray-600">Conferences, meetings, and banquet coordination</p>
                        </div>

                        <div class="group bg-white p-6 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-purple-200">
                            <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                <i class="fas fa-gift text-white text-xl"></i>
                            </div>
                            <h4 class="font-bold text-gray-900 mb-2 text-lg">Group Incentives</h4>
                            <p class="text-sm text-gray-600">Special packages for group travel experiences</p>
                        </div>

                        <div class="group bg-white p-6 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-orange-200">
                            <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                <i class="fas fa-handshake text-white text-xl"></i>
                            </div>
                            <h4 class="font-bold text-gray-900 mb-2 text-lg">VIP Services</h4>
                            <p class="text-sm text-gray-600">Personalized meet and greet assistance</p>
                        </div>

                        <div class="group bg-white p-6 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-teal-200">
                            <div class="w-14 h-14 bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                <i class="fas fa-plane text-white text-xl"></i>
                            </div>
                            <h4 class="font-bold text-gray-900 mb-2 text-lg">Airport Transfers</h4>
                            <p class="text-sm text-gray-600">Reliable transportation to and from airports</p>
                        </div>

                        <div class="group bg-white p-6 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-green-200">
                            <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                <i class="fas fa-route text-white text-xl"></i>
                            </div>
                            <h4 class="font-bold text-gray-900 mb-2 text-lg">Safari & Adventures</h4>
                            <p class="text-sm text-gray-600">Tours, safaris, mountain climbing, and trekking</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Safari Vehicles & Guides Combined -->
        <div class="bg-white py-20">
            <div class="w-full px-4 md:px-12">
                <div class="max-w-6xl mx-auto">
                    <div class="grid lg:grid-cols-2 gap-12">

                        <!-- Safari Vehicles -->
                        <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-3xl p-8 md:p-10 border border-emerald-100">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-12 h-12 bg-emerald-600 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-car text-white text-xl"></i>
                                </div>
                                <h2 class="text-2xl md:text-3xl font-bold text-gray-900">Our Safari Fleet</h2>
                            </div>

                            <p class="text-gray-700 mb-6 leading-relaxed">
                                We maintain a diverse fleet of well-equipped safari vehicles to suit different group sizes and preferences, ensuring comfort and optimal wildlife viewing experiences.
                            </p>

                            <div class="space-y-3">
                                <div class="flex items-start gap-3 bg-white/60 backdrop-blur-sm rounded-lg p-3">
                                    <div class="w-6 h-6 bg-emerald-600 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <i class="fas fa-check text-white text-xs"></i>
                                    </div>
                                    <span class="text-gray-800 font-medium">Multiple vehicle types for all group sizes</span>
                                </div>
                                <div class="flex items-start gap-3 bg-white/60 backdrop-blur-sm rounded-lg p-3">
                                    <div class="w-6 h-6 bg-emerald-600 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <i class="fas fa-check text-white text-xs"></i>
                                    </div>
                                    <span class="text-gray-800 font-medium">Advanced radio communication systems</span>
                                </div>
                                <div class="flex items-start gap-3 bg-white/60 backdrop-blur-sm rounded-lg p-3">
                                    <div class="w-6 h-6 bg-emerald-600 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <i class="fas fa-check text-white text-xs"></i>
                                    </div>
                                    <span class="text-gray-800 font-medium">Pop-up roofs for photography & viewing</span>
                                </div>
                                <div class="flex items-start gap-3 bg-white/60 backdrop-blur-sm rounded-lg p-3">
                                    <div class="w-6 h-6 bg-emerald-600 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <i class="fas fa-check text-white text-xs"></i>
                                    </div>
                                    <span class="text-gray-800 font-medium">Comfortable seating & climate control</span>
                                </div>
                            </div>
                        </div>

                        <!-- Expert Guides -->
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-3xl p-8 md:p-10 border border-blue-100">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-user-tie text-white text-xl"></i>
                                </div>
                                <h2 class="text-2xl md:text-3xl font-bold text-gray-900">Expert Guides</h2>
                            </div>

                            <p class="text-gray-700 mb-8 leading-relaxed">
                                Our English-speaking guides are indigenous Africans with extensive knowledge of Kenya's flora and fauna, highly trained through Kenya's tourism institutions.
                            </p>

                            <div class="grid gap-4">
                                <div class="flex items-center gap-4 bg-white/60 backdrop-blur-sm rounded-xl p-4">
                                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-graduation-cap text-blue-600 text-lg"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900">Certified Professionals</h4>
                                        <p class="text-sm text-gray-600">Tourism institution trained</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4 bg-white/60 backdrop-blur-sm rounded-xl p-4">
                                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-leaf text-green-600 text-lg"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900">Wildlife Experts</h4>
                                        <p class="text-sm text-gray-600">Deep ecosystem knowledge</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4 bg-white/60 backdrop-blur-sm rounded-xl p-4">
                                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-language text-purple-600 text-lg"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900">Fluent Communication</h4>
                                        <p class="text-sm text-gray-600">English speaking guides</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
</x-guest-layout>
