<?php

namespace Database\Factories;

use App\Models\Accommodation;
use App\Models\ParkLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccommodationFactory extends Factory
{
    protected $model = Accommodation::class;

    public function definition(): array
    {
        $realAccommodations = [
            [
                'name' => 'Serena Safari Lodge Maasai Mara',
                'type' => 'lodge',
                'category' => 'luxury',
                'city' => 'Maasai Mara',
                'country' => 'Kenya',
                'description' => 'Perched high on the slopes of a bush-cloaked hill, with panoramic views over the vast rolling plains of the legendary Maasai Mara National Reserve, the lodge overlooks the Mara River and sits in the middle of the annual wildebeest migration route.',
                'amenities' => ['Restaurant', 'Bar', 'Swimming Pool', 'Spa', 'WiFi', 'Gift Shop', 'Laundry', 'Game Drives'],
                'room_types' => ['Standard Room', 'Superior Room', 'Family Room', 'Suite'],
                'total_rooms' => 74,
                'max_guests' => 148,
                'rating' => 4.7,
            ],
            [
                'name' => 'Ngorongoro Crater Lodge',
                'type' => 'lodge',
                'category' => 'ultra_luxury',
                'city' => 'Ngorongoro',
                'country' => 'Tanzania',
                'description' => 'Ngorongoro Crater Lodge is one of the most luxurious safari camps in Africa. Perched on the rim of the magnificent Ngorongoro Crater, this unique lodge offers unrivaled views into the crater below.',
                'amenities' => ['Fine Dining', 'Butler Service', 'Spa', 'WiFi', 'Lounge', 'Private Balconies'],
                'room_types' => ['Suite'],
                'total_rooms' => 30,
                'max_guests' => 60,
                'rating' => 4.9,
            ],
            [
                'name' => 'Tarangire Sopa Lodge',
                'type' => 'lodge',
                'category' => 'mid_range',
                'city' => 'Tarangire',
                'country' => 'Tanzania',
                'description' => 'Nestled amongst ancient baobab trees and overlooking the Tarangire River, this elegant lodge offers comfortable accommodation with stunning views of elephants and other wildlife.',
                'amenities' => ['Restaurant', 'Bar', 'Swimming Pool', 'WiFi', 'Conference Facilities'],
                'room_types' => ['Standard Room', 'Family Room'],
                'total_rooms' => 75,
                'max_guests' => 150,
                'rating' => 4.3,
            ],
            [
                'name' => 'Mara Intrepids Tented Camp',
                'type' => 'tented_camp',
                'category' => 'luxury',
                'city' => 'Maasai Mara',
                'country' => 'Kenya',
                'description' => 'Experience authentic safari living in luxury tented accommodation set along the banks of the Talek River. Each tent features en-suite facilities and private verandas overlooking the river.',
                'amenities' => ['Restaurant', 'Bar', 'Swimming Pool', 'WiFi', 'Laundry', 'Game Drives', 'Bush Dinners'],
                'room_types' => ['Luxury Tent', 'Family Tent'],
                'total_rooms' => 30,
                'max_guests' => 60,
                'rating' => 4.6,
            ],
            [
                'name' => 'Amboseli Serena Safari Lodge',
                'type' => 'lodge',
                'category' => 'mid_range',
                'city' => 'Amboseli',
                'country' => 'Kenya',
                'description' => 'Set in the heart of Amboseli National Park with breathtaking views of Mount Kilimanjaro, this lodge features Maasai-inspired architecture and décor.',
                'amenities' => ['Restaurant', 'Bar', 'Swimming Pool', 'WiFi', 'Gift Shop', 'Cultural Performances'],
                'room_types' => ['Standard Room', 'Superior Room'],
                'total_rooms' => 96,
                'max_guests' => 192,
                'rating' => 4.4,
            ],
        ];

        $accommodation = $this->faker->randomElement($realAccommodations);
        
        $prices = [
            'budget' => [80, 150, null, null],
            'mid_range' => [150, 250, 350, null],
            'luxury' => [250, 400, 550, 800],
            'ultra_luxury' => [null, 600, 900, 1500],
        ];

        $selectedPrices = $prices[$accommodation['category']];

        return [
            'name' => $accommodation['name'],
            'type' => $accommodation['type'],
            'category' => $accommodation['category'],
            'description' => $accommodation['description'],
            'amenities' => $accommodation['amenities'],
            'room_types' => $accommodation['room_types'],
            'address' => $this->faker->streetAddress(),
            'city' => $accommodation['city'],
            'region' => $accommodation['city'] . ' Region',
            'country' => $accommodation['country'],
            'latitude' => $this->faker->latitude(-4, 3),
            'longitude' => $this->faker->longitude(33, 41),
            'park_location_id' => ParkLocation::where('name', 'like', '%' . $accommodation['city'] . '%')->first()?->id,
            'phone' => '+254' . $this->faker->numerify(' ### ### ###'),
            'email' => strtolower(str_replace(' ', '', $accommodation['name'])) . '@example.com',
            'website' => 'https://www.' . strtolower(str_replace(' ', '', $accommodation['name'])) . '.com',
            'total_rooms' => $accommodation['total_rooms'],
            'max_guests' => $accommodation['max_guests'],
            'price_per_night_budget' => $selectedPrices[0],
            'price_per_night_standard' => $selectedPrices[1],
            'price_per_night_deluxe' => $selectedPrices[2],
            'price_per_night_suite' => $selectedPrices[3],
            'breakfast_included' => true,
            'lunch_included' => $accommodation['category'] !== 'budget',
            'dinner_included' => in_array($accommodation['category'], ['luxury', 'ultra_luxury']),
            'board_basis' => $accommodation['category'] === 'budget' ? 'bed_breakfast' : 'full_board',
            'rating' => $accommodation['rating'],
            'review_count' => $this->faker->numberBetween(50, 500),
            'main_image' => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=800',
            'gallery_images' => [
                'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?w=800',
                'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=800',
                'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800',
            ],
            'family_friendly' => true,
            'pet_friendly' => false,
            'wheelchair_accessible' => $accommodation['category'] !== 'budget',
            'airport_transfer_available' => true,
            'is_available' => true,
            'requires_deposit' => true,
            'min_nights' => 1,
            'max_nights' => null,
            'cancellation_policy' => 'Free cancellation up to 7 days before check-in. 50% refund for cancellations 3-7 days before. No refund for cancellations within 3 days.',
            'house_rules' => 'Check-in: 2:00 PM | Check-out: 11:00 AM | No smoking indoors | Pets not allowed',
            'check_in_time' => '14:00:00',
            'check_out_time' => '11:00:00',
        ];
    }
}