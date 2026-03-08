<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CmsPage;
use App\Models\CmsSection;

class CmsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Home Page
        $home = CmsPage::firstOrCreate(
            ['slug' => 'home'],
            [
                'title' => 'Home Page',
                'is_active' => true,
                'meta_title' => 'Ferdinand Safaris - Discover Africa',
                'meta_description' => 'Experience unforgettable safari adventures with expert guides.',
            ]
        );

        // Hero Section
        CmsSection::firstOrCreate(
            ['cms_page_id' => $home->id, 'section_key' => 'hero'],
            [
                'title' => 'Hero Section',
                'order' => 1,
                'is_active' => true,
                'content' => [
                    'heading' => "Discover Africa's Wonders",
                    'subheading' => "Experience unforgettable safari adventures with expert guides and luxury accommodations",
                    'bg_image' => "https://images.unsplash.com/photo-1516426122078-c23e76319801",
                ]
            ]
        );

        // Stats Section
        CmsSection::firstOrCreate(
            ['cms_page_id' => $home->id, 'section_key' => 'stats'],
            [
                'title' => 'Statistics',
                'order' => 2,
                'is_active' => true,
                'content' => [
                    'items' => [
                        ['value' => '500+', 'label' => 'Happy Travelers'],
                        ['value' => '50+', 'label' => 'Unique Tours'],
                        ['value' => '15+', 'label' => 'Years Experience'],
                        ['value' => '4.9', 'label' => 'Average Rating'],
                    ]
                ]
            ]
        );

        // 2. About Page
        $about = CmsPage::firstOrCreate(
            ['slug' => 'about'],
            [
                'title' => 'About Us',
                'is_active' => true,
                'meta_title' => 'About Ferdinand Safaris - Our Story',
                'meta_description' => 'Learn about our history, values, and the team behind Ferdinand Safaris.',
            ]
        );

        // About - Hero
        CmsSection::firstOrCreate(
            ['cms_page_id' => $about->id, 'section_key' => 'hero'],
            [
                'title' => 'About Hero',
                'order' => 1,
                'is_active' => true,
                'content' => [
                    'badge' => 'UNKNOWN • UNTAMED • UNFORGETTABLE',
                    'heading' => 'We Are <span class="text-emerald-500">Ferdinand.</span>',
                    'subheading' => "Crafting award-winning African safaris since 2010. We don't just show you Africa; we help you feel its heartbeat.",
                    'bg_image' => 'https://images.unsplash.com/photo-1470770841072-f978cf4d019e?auto=format&fit=crop&w=2000&q=80',
                ]
            ]
        );

        // About - Stats
        CmsSection::firstOrCreate(
            ['cms_page_id' => $about->id, 'section_key' => 'stats'],
            [
                'title' => 'Our Stats',
                'order' => 2,
                'is_active' => true,
                'content' => [
                    'items' => [
                        ['value' => '15+', 'label' => 'Years Experience'],
                        ['value' => '5k+', 'label' => 'Happy Travelers'],
                        ['value' => '24/7', 'label' => 'On-Trip Support'],
                        ['value' => '100%', 'label' => 'Local Guides'],
                    ]
                ]
            ]
        );

        // About - Story
        CmsSection::firstOrCreate(
            ['cms_page_id' => $about->id, 'section_key' => 'story'],
            [
                'title' => 'Our Story',
                'order' => 3,
                'is_active' => true,
                'content' => [
                    'heading' => 'Deep roots in <br><span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-500">African Soil.</span>',
                    'body_1' => 'Ferdinand Safaris was born from a simple belief: that the best way to see Africa is through the eyes of those who call it home.',
                    'body_2' => 'We are not just a travel agency; we are custodians of the land and storytellers of the wild. Every itinerary we craft is an invitation to conservation, cultural understanding, and adventure.',
                    'image_1' => 'https://images.unsplash.com/photo-1550953684-257a3e5c9823?auto=format&fit=crop&w=800&q=80',
                    'image_2' => 'https://images.unsplash.com/photo-1516426122078-c23e76319801?auto=format&fit=crop&w=800&q=80',
                ]
            ]
        );

        // About - Values
        CmsSection::firstOrCreate(
            ['cms_page_id' => $about->id, 'section_key' => 'values'],
            [
                'title' => 'Our Values',
                'order' => 4,
                'is_active' => true,
                'content' => [
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
                ]
            ]
        );

        // 3. Contact Page
        $contact = CmsPage::firstOrCreate(
            ['slug' => 'contact'],
            [
                'title' => 'Contact Us',
                'is_active' => true,
                'meta_title' => 'Contact Ferdinand Safaris',
                'meta_description' => 'Get in touch with our team to plan your dream safari.',
            ]
        );

        // Contact - Header
        CmsSection::firstOrCreate(
            ['cms_page_id' => $contact->id, 'section_key' => 'header'],
            [
                'title' => 'Contact Header',
                'order' => 1,
                'is_active' => true,
                'content' => [
                    'badge' => "We'd love to hear from you",
                    'heading' => 'Get in Touch',
                    'subheading' => 'Planning a trip? Have a question about a safari? Our team of experts is ready to help you plan your next adventure.',
                    'bg_image' => 'https://images.unsplash.com/photo-1523495862369-12349f43063d?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80',
                ]
            ]
        );

        // Contact - Info
        CmsSection::firstOrCreate(
            ['cms_page_id' => $contact->id, 'section_key' => 'contact_info'],
            [
                'title' => 'Contact Information',
                'order' => 2,
                'is_active' => true,
                'content' => [
                   'heading' => 'Contact Information',
                   'description' => 'Fill up the form and our specialized team will get back to you within 24 hours.',
                   'items' => [
                       ['icon' => 'fas fa-phone-alt', 'label' => 'Call Us', 'value' => '+254 700 000 000'],
                       ['icon' => 'fas fa-envelope', 'label' => 'Email Us', 'value' => 'info@ferdinandsafaris.com'],
                       ['icon' => 'fas fa-map-marker-alt', 'label' => 'Visit Us', 'value' => "Nairobi, Kenya\nWestlands, Delta Towers"],
                   ]
                ]
            ]
        );

         // Contact - FAQs
        CmsSection::firstOrCreate(
            ['cms_page_id' => $contact->id, 'section_key' => 'faqs'],
            [
                'title' => 'FAQs',
                'order' => 3,
                'is_active' => true,
                'content' => [
                    'heading' => 'Frequently Asked Questions',
                    'items' => [
                        ['q' => 'What is included in the safari price?', 'a' => 'Accommodation, meals, park fees, transport in a 4x4 safari vehicle, and professional guides.'],
                        ['q' => 'Can I customize my safari?', 'a' => 'Yes. All our safaris are fully customizable based on your preferences and budget.'],
                        ['q' => 'Is it safe to travel on safari?', 'a' => 'Yes. Our guides are experienced professionals and safety is our top priority.'],
                        ['q' => 'What is the best time to visit?', 'a' => 'June to October is excellent for wildlife viewing, but safaris run year-round.'],
                    ]
                ]
            ]
        );

        // 4. Community Page
        $community = CmsPage::firstOrCreate(
            ['slug' => 'community'],
            [
                'title' => 'Community',
                'is_active' => true,
                'meta_title' => 'Our Community - Ferdinand Safaris',
                'meta_description' => 'Real experiences from travelers who explored Africa with Ferdinand Safaris.',
            ]
        );

        // Community - Hero
        CmsSection::firstOrCreate(
            ['cms_page_id' => $community->id, 'section_key' => 'hero'],
            [
                'title' => 'Community Hero',
                'order' => 1,
                'is_active' => true,
                'content' => [
                    'heading' => 'Our Community & Guest Stories',
                    'subheading' => 'Real experiences from travelers who explored Africa with Ferdinand Safaris.',
                ]
            ]
        );

        // Community - Reviews
        CmsSection::firstOrCreate(
            ['cms_page_id' => $community->id, 'section_key' => 'reviews'],
            [
                'title' => 'Guest Reviews',
                'order' => 2,
                'is_active' => true,
                'content' => [
                    'items' => [
                        ['name' => 'Sarah M.', 'country' => 'USA', 'text' => 'An unforgettable safari! Our guide was incredible and the wildlife sightings were beyond expectations. We learned so much about the local ecosystem.', 'rating' => 5],
                        ['name' => 'James K.', 'country' => 'UK', 'text' => 'Professional, friendly, and well organized. Ferdinand Safaris exceeded every expectation. The lodges were improved versions of what I imagined.', 'rating' => 5],
                        ['name' => 'Anna L.', 'country' => 'Germany', 'text' => 'The perfect blend of adventure and comfort. Highly recommended for first-time safari travelers. The seamless transfers made everything stress-free.', 'rating' => 4],
                        ['name' => 'Daniel W.', 'country' => 'Canada', 'text' => 'Excellent communication and attention to detail. We felt safe and cared for throughout. Seeing the Big Five was a dream come true.', 'rating' => 5],
                        ['name' => 'Maria R.', 'country' => 'Spain', 'text' => 'A life-changing journey. Seeing Africa through local guides made all the difference. Their passion for conservation is inspiring.', 'rating' => 5],
                    ]
                ]
            ]
        );
    }
}
