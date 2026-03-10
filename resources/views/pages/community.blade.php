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

        <!-- Photo Gallery (UGC Grid) -->
        @php
            $stories = \App\Models\CommunityStory::where('is_approved', true)->latest()->take(12)->get();
        @endphp

        @if($stories->count() > 0)
        <div class="w-full px-4 md:px-12 py-16 bg-white">
            <div class="max-w-screen-xl mx-auto text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Guest Perspective Gallery</h2>
                <div class="h-1 w-20 bg-emerald-500 mx-auto"></div>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($stories as $story)
                    @if(isset($story->images[0]))
                        <div class="relative group aspect-square overflow-hidden rounded-xl cursor-pointer">
                            <img src="{{ asset('storage/' . $story->images[0]) }}" alt="{{ $story->title }}" class="w-full h-full object-cover transform group-hover:scale-110 transition duration-700">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center p-4">
                                <p class="text-white text-center text-sm font-medium">{{ $story->title }}</p>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
        @endif

        @auth
        <!-- Share Your Story Form -->
        <div id="share-story" class="w-full px-4 md:px-12 py-24 bg-gray-900 relative">
            <div class="absolute inset-0 pattern-grid-lg opacity-5"></div>
            <div class="max-w-3xl mx-auto relative z-10 bg-white p-8 md:p-12 rounded-3xl shadow-2xl">
                <div class="text-center mb-12">
                    <span class="text-emerald-600 font-bold uppercase tracking-widest text-sm mb-2 block">Tell Your Tale</span>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">Share Your Safari Experience</h2>
                    <p class="text-gray-600">Help others discover the magic of Africa. Upload your photos and share your story.</p>
                </div>

                <form id="storyForm" class="space-y-6" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Story Title</label>
                            <input type="text" name="title" required placeholder="e.g. My Serengeti Sunrise"
                                class="w-full px-5 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Upload Photos (Max 5)</label>
                            <input type="file" name="images[]" multiple accept="image/*"
                                class="w-full px-5 py-2.5 rounded-xl border border-gray-200 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Write Your Story</label>
                        <textarea name="content" rows="6" required placeholder="Describe your unforgettable moments..."
                            class="w-full px-5 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"></textarea>
                    </div>

                    <div class="flex items-center justify-between">
                        <p class="text-xs text-gray-400 max-w-xs">By submitting, you agree to allow Ferdinand Safaris to feature your content on our platform.</p>
                        <button type="submit" class="bg-emerald-600 text-white px-10 py-4 rounded-full font-bold shadow-lg hover:bg-emerald-700 transition transform hover:-translate-y-1 active:scale-95 flex items-center gap-3">
                            <span>Publish Story</span>
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
                <div id="storyMessage" class="mt-6 p-4 rounded-xl hidden text-center font-medium"></div>
            </div>
        </div>
        @else
        <!-- Story Call to Action for Guests -->
        <div class="w-full px-4 md:px-12 py-24 bg-emerald-900 text-white text-center">
            <h2 class="text-3xl font-bold mb-4">Have a Story to Tell?</h2>
            <p class="text-emerald-100 mb-8 max-w-2xl mx-auto">Login to your account to share your safari photos and experiences with our community.</p>
            <div class="flex justify-center gap-4">
                <a href="{{ route('login') }}" class="bg-white text-emerald-800 px-8 py-4 rounded-full font-bold hover:bg-emerald-50 transition">Login to Share</a>
                <a href="{{ route('register') }}" class="bg-emerald-700 border border-emerald-500 text-white px-8 py-4 rounded-full font-bold hover:bg-emerald-600 transition">Create Account</a>
            </div>
        </div>
        @endauth

        <!-- CTA -->
        <div class="mt-8 text-center bg-white rounded-3xl p-8 md:p-12 shadow-lg border border-gray-100 max-w-screen-xl mx-auto mb-16">
            <h2 class="text-3xl font-bold text-emerald-800 mb-4">
                Ready to Join Our Community?
            </h2>
            <p class="text-lg text-gray-600 mb-8 max-w-2xl mx-auto">
                Let us help you create your own unforgettable safari story.
            </p>
            <a href="{{ route('contact') }}" class="inline-block bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-8 py-4 rounded-full shadow-lg hover:shadow-xl transition transform hover:-translate-y-1">
                Plan Your Safari
            </a>
        </div>

        <script>
            // Newsletter Handling
            const newsletterForm = document.getElementById('newsletterForm');
            if (newsletterForm) {
                newsletterForm.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const email = document.getElementById('newsletterEmail').value;
                    const messageEl = document.getElementById('newsletterMessage');
                    
                    try {
                        const response = await fetch('{{ route('newsletter.subscribe') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ email })
                        });
                        
                        const data = await response.json();
                        messageEl.textContent = data.message;
                        messageEl.className = `mt-4 text-sm font-medium ${data.success ? 'text-emerald-400' : 'text-red-400'}`;
                        messageEl.classList.remove('hidden');
                        
                        if (data.success) newsletterForm.reset();
                    } catch (error) {
                        messageEl.textContent = 'Something went wrong. Please try again.';
                        messageEl.className = 'mt-4 text-sm font-medium text-red-400';
                        messageEl.classList.remove('hidden');
                    }
                });
            }

            // Story Submission Handling
            const storyForm = document.getElementById('storyForm');
            if (storyForm) {
                storyForm.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const formData = new FormData(storyForm);
                    const messageEl = document.getElementById('storyMessage');
                    
                    try {
                        const response = await fetch('{{ route('community.stories.store') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: formData
                        });
                        
                        const data = await response.json();
                        messageEl.textContent = data.message;
                        messageEl.className = `mt-6 p-4 rounded-xl text-center font-medium ${data.success ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700'}`;
                        messageEl.classList.remove('hidden');
                        
                        if (data.success) storyForm.reset();
                    } catch (error) {
                        messageEl.textContent = 'Something went wrong. Please try again.';
                        messageEl.className = 'mt-6 p-4 rounded-xl bg-red-50 text-red-700 text-center font-medium';
                        messageEl.classList.remove('hidden');
                    }
                });
            }
        </script>
    </div>
</x-guest-layout>
