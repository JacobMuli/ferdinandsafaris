<x-guest-layout>
    <div class="bg-gray-50  min-h-screen">

        @php
            $header = $cmsPage?->sections->where('section_key', 'header')->first()?->content ?? [
                'badge' => "We'd love to hear from you",
                'heading' => 'Get in Touch',
                'subheading' => 'Planning a trip? Have a question about a safari? Our team of experts is ready to help you plan your next adventure.',
                'bg_image' => 'https://images.unsplash.com/photo-1523495862369-12349f43063d?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80',
            ];

            $contactInfo = $cmsPage?->sections->where('section_key', 'contact_info')->first()?->content ?? [
               'heading' => 'Contact Information',
               'description' => 'Fill up the form and our specialized team will get back to you within 24 hours.',
               'items' => [
                   ['icon' => 'fas fa-phone-alt', 'label' => 'Call Us', 'value' => '+254-720-968563'],
                   ['icon' => 'fas fa-envelope', 'label' => 'Email Us', 'value' => 'info@ferdinandsafaris.com'],
                   ['icon' => 'fas fa-map-marker-alt', 'label' => 'Visit Us', 'value' => "Nairobi, Kenya\nWestlands, Delta Towers"],
               ]
            ];

            $faqs = $cmsPage?->sections->where('section_key', 'faqs')->first()?->content['items'] ?? [
                ['q' => 'What is included in the safari price?', 'a' => 'Accommodation, meals, park fees, transport in a 4x4 safari vehicle, and professional guides.'],
                ['q' => 'Can I customize my safari?', 'a' => 'Yes. All our safaris are fully customizable based on your preferences and budget.'],
                ['q' => 'Is it safe to travel on safari?', 'a' => 'Yes. Our guides are experienced professionals and safety is our top priority.'],
                ['q' => 'What is the best time to visit?', 'a' => 'June to October is excellent for wildlife viewing, but safaris run year-round.'],
            ];
        @endphp

        <!-- Header -->
        <div class="relative bg-emerald-900 text-white py-32 overflow-hidden">
            <div class="absolute inset-0 bg-[url('{{ $header['bg_image'] }}')] bg-cover bg-center opacity-30"></div>
            <div class="relative w-full px-4 md:px-12 text-center">
                <span class="text-emerald-400 font-bold tracking-widest uppercase text-sm mb-4 block">{{ $header['badge'] }}</span>
                <h1 class="text-5xl md:text-7xl font-bold mb-6">{{ $header['heading'] }}</h1>
                <p class="text-xl text-gray-300 max-w-2xl mx-auto font-light">
                    {{ $header['subheading'] }}
                </p>
            </div>
        </div>

        <div class="w-full px-4 md:px-12 -mt-20 relative z-10 pb-20">
            <div class="bg-white  rounded-3xl shadow-xl overflow-hidden border border-gray-100 ">
                <div class="grid lg:grid-cols-5 min-h-[600px]">

                    <!-- Contact Info Sidebar -->
                    <div class="lg:col-span-2 bg-emerald-700  text-white p-12 flex flex-col justify-between relative overflow-hidden">
                         <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>

                        <div class="relative z-10">
                            <h3 class="text-2xl font-bold mb-8">{{ $contactInfo['heading'] }}</h3>
                            <p class="text-emerald-100 mb-12 leading-relaxed">
                                {{ $contactInfo['description'] }}
                            </p>

                            <div class="space-y-8">
                                @foreach($contactInfo['items'] as $item)
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                                        <i class="{{ $item['icon'] }} text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-emerald-200 uppercase tracking-wider font-semibold mb-1">{{ $item['label'] }}</p>
                                        <p class="text-lg font-medium">{!! nl2br(e($item['value'])) !!}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="relative z-10 mt-12 flex gap-6">
                             <a href="#" class="w-12 h-12 rounded-full border border-white/30 flex items-center justify-center hover:bg-white hover:text-emerald-700 transition">
                                <i class="fab fa-facebook-f text-xl"></i>
                            </a>
                             <a href="#" class="w-12 h-12 rounded-full border border-white/30 flex items-center justify-center hover:bg-white hover:text-emerald-700 transition">
                                <i class="fab fa-instagram text-xl"></i>
                            </a>
                            <a href="#" class="w-12 h-12 rounded-full border border-white/30 flex items-center justify-center hover:bg-white hover:text-emerald-700 transition">
                                <i class="fab fa-x-twitter text-xl"></i>
                            </a>
                            <a href="#" class="w-12 h-12 rounded-full border border-white/30 flex items-center justify-center hover:bg-white hover:text-emerald-700 transition">
                                <i class="fab fa-linkedin-in text-xl"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Contact Form -->
                    <div class="lg:col-span-3 p-6 md:p-12 bg-white ">
                        <form method="POST" action="{{ route('contact.submit') }}" class="space-y-8">
                            @csrf

                            <div class="grid md:grid-cols-2 gap-8">
                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-gray-900 ">Full Name</label>
                                    <input type="text" name="name" required class="w-full px-4 py-3 rounded-lg bg-gray-50  border border-gray-200  focus:ring-2 focus:ring-emerald-500 transition outline-none" placeholder="John Doe">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-gray-900 ">Email Address</label>
                                    <input type="email" name="email" required class="w-full px-4 py-3 rounded-lg bg-gray-50  border border-gray-200  focus:ring-2 focus:ring-emerald-500 transition outline-none" placeholder="john@example.com">
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 gap-8">
                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-gray-900 ">Phone Number</label>
                                    <input type="tel" name="phone" class="w-full px-4 py-3 rounded-lg bg-gray-50  border border-gray-200  focus:ring-2 focus:ring-emerald-500 transition outline-none" placeholder="+254 700 000 000">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-gray-900 ">Subject</label>
                                    <select name="subject" required class="w-full px-4 py-3 rounded-lg bg-gray-50  border border-gray-200  focus:ring-2 focus:ring-emerald-500 transition outline-none">
                                        <option value="">Select a subject</option>
                                        <option value="General Inquiry">General Inquiry</option>
                                        <option value="Booking Question">Booking Question</option>
                                        <option value="Custom Safari Request">Custom Safari Request</option>
                                        <option value="Partnership">Partnership</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-gray-900 ">Message</label>
                                <textarea name="message" rows="4" required class="w-full px-4 py-3 rounded-lg bg-gray-50  border border-gray-200  focus:ring-2 focus:ring-emerald-500 transition outline-none resize-none" placeholder="Tell us about your dream safari..."></textarea>
                            </div>

                            <div class="pt-4">
                                <button type="submit" class="w-full bg-emerald-600 text-white font-bold py-4 rounded-lg hover:bg-emerald-700 transition shadow-lg transform hover:-translate-y-1">
                                    Send Message
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        <!-- FAQ Section (Simpler) -->
        <div class="w-full px-4 md:px-12 pb-20">
            <h2 class="text-3xl font-bold text-center text-gray-900  mb-12">Frequently Asked Questions</h2>

            <div class="grid gap-6 max-w-4xl mx-auto">
                 @foreach($faqs as $i => $faq)
                    <div x-data="{ open: false }" class="bg-white  rounded-xl shadow-sm border border-gray-100  overflow-hidden">
                        <button @click="open = !open" class="w-full px-8 py-6 flex justify-between items-center text-left">
                            <span class="font-bold text-gray-900  text-lg">{{ $faq['q'] }}</span>
                            <i class="fas fa-plus text-emerald-500 transition-transform duration-300" :class="open ? 'rotate-45' : ''"></i>
                        </button>
                        <div x-show="open" x-collapse>
                            <div class="px-8 pb-6 text-gray-600  leading-relaxed">
                                {{ $faq['a'] }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</x-guest-layout>
