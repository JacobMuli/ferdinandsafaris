<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Tour;
use App\Models\User;
use App\Models\Customer;
use App\Mail\BookingConfirmationMail;
use App\Notifications\NewBookingNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_validation_requires_necessary_fields()
    {
        // 1. Arrange: Create Tour
        $tour = Tour::create([
            'name' => 'Safari Adventure',
            'slug' => 'safari-adventure',
            'description' => 'Test Tour',
            'price_per_person' => 1000,
            'duration_days' => 5,
            'is_active' => true,
            'category' => 'wildlife',
            'location' => 'Kenya',
        ]);

        // 2. Act: Send empty payload
        $response = $this->post(route('bookings.store'), []);

        // 3. Assert: Session has errors for required fields
        $response->assertSessionHasErrors([
            'tour_id',
            'tour_date',
            'customer_type',
            'adults_count',
            'first_name',
            'email',
            'phone',
        ]);
    }

    public function test_guest_can_book_and_emails_are_sent()
    {
        Mail::fake();
        Notification::fake();

        $tour = Tour::create([
            'name' => 'Safari Adventure 2',
            'slug' => 'safari-adventure-2',
            'description' => 'Test Tour 2',
            'price_per_person' => 1000,
            'duration_days' => 5,
            'is_active' => true,
            'min_group_size' => 1,
            'category' => 'wildlife',
            'location' => 'Kenya',
        ]);
        
        // Create an Admin to receive notification
        $admin = User::factory()->create(['is_admin' => true, 'email' => 'admin@ferdinand.com']);

        $payload = [
            'tour_id' => $tour->id,
            'tour_date' => now()->addDays(10)->format('Y-m-d'),
            'customer_type' => 'individual',
            'adults_count' => 1,
            'first_name' => 'Guest',
            'last_name' => 'User',
            'email' => 'guest@example.com',
            'phone' => '+1234567890',
            'country' => 'USA',
            'emergency_contact_name' => 'Mom',
            'emergency_contact_phone' => '+111222333',
        ];

        $response = $this->post(route('bookings.store'), $payload);

        $response->assertRedirect();
        
        // Check Customer table for email
        $this->assertDatabaseHas('customers', ['email' => 'guest@example.com']);

        $customer = Customer::where('email', 'guest@example.com')->first();

        // Check Booking table for customer_id
        $this->assertDatabaseHas('bookings', [
            'tour_id' => $tour->id,
            'customer_id' => $customer->id,
        ]);

        // Assert Customer Email Queued
        Mail::assertQueued(BookingConfirmationMail::class);

        // Assert Admin Notification Sent
        Notification::assertSentTo(
            [$admin],
            NewBookingNotification::class
        );
    }

    public function test_authenticated_user_booking_appears_in_my_bookings()
    {
        $user = User::factory()->create();
        $tour = Tour::create([
            'name' => 'Safari Adventure 3',
            'slug' => 'safari-adventure-3',
            'description' => 'Test Tour 3',
            'price_per_person' => 1000,
            'duration_days' => 5,
            'is_active' => true,
            'min_group_size' => 1,
            'category' => 'wildlife',
            'location' => 'Kenya',
        ]);

        // Login as User
        $this->actingAs($user);

        $payload = [
            'tour_id' => $tour->id,
            'tour_date' => now()->addDays(5)->format('Y-m-d'),
            'customer_type' => 'individual',
            'adults_count' => 1,
            'first_name' => $user->name,
            'last_name' => 'Test',
            'email' => $user->email,
            'phone' => '+1234567890',
            'country' => 'UK',
            'emergency_contact_name' => 'Dad',
            'emergency_contact_phone' => '+444555666',
        ];

        $response = $this->post(route('bookings.store'), $payload);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        // Verify Database Link
        $customer = Customer::where('email', $user->email)->firstOrFail();
        $this->assertEquals($user->id, $customer->user_id);
        
        // Verify "My Bookings" Page contains the booking
        $response = $this->get(route('my-bookings'));
        $response->assertStatus(200);
        $response->assertSee($tour->name);
        $response->assertSee('Pending'); // Status
    }

    public function test_admin_can_see_new_booking_in_dashboard()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $tour = Tour::create([
            'name' => 'Hidden Gem Safari',
            'slug' => 'hidden-gem',
            'description' => 'Hidden Description',
            'price_per_person' => 1500,
            'duration_days' => 7,
            'is_active' => true,
            'min_group_size' => 1,
            'category' => 'wildlife',
            'location' => 'Kenya',
        ]);
        
        // Create a booking first (as a guest)
        $this->post(route('bookings.store'), [
            'tour_id' => $tour->id,
            'tour_date' => now()->addDays(20)->format('Y-m-d'),
            'customer_type' => 'individual',
            'adults_count' => 1,
            'first_name' => 'Random',
            'last_name' => 'Stranger',
            'email' => 'stranger@example.com',
            'phone' => '+999888777',
            'country' => 'Canada',
            'emergency_contact_name' => 'Friend',
            'emergency_contact_phone' => '+111',
        ]);

        // Login as Admin
        $this->actingAs($admin);

        // Visit Admin Booking Index
        $response = $this->get(route('admin.bookings.index'));
        
        $response->assertStatus(200);
        $response->assertSee('Hidden Gem Safari');
        $response->assertSee('stranger@example.com');
    }
    public function test_admin_confirmation_logic()
    {
        Mail::fake();
        $admin = User::factory()->create(['is_admin' => true]);
        $tour = Tour::create(['name' => 'T', 'slug' => 't', 'description' => 'd', 'price_per_person' => 100, 'duration_days' => 1, 'is_active' => 1, 'category' => 'c', 'location' => 'l']);
        
        $response = $this->post(route('bookings.store'), [
            'tour_id' => $tour->id,
            'tour_date' => now()->addDays(20)->format('Y-m-d'),
            'customer_type' => 'individual',
            'adults_count' => 1,
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'phone' => '123',
            'country' => 'US',
            'emergency_contact_name' => 'Em',
            'emergency_contact_phone' => '000',
        ]);
        
        $booking = \App\Models\Booking::first();
        $this->actingAs($admin);

        // 1. Try to confirm without actual_price -> Should fail/redirect back
        $response = $this->post(route('admin.bookings.confirm', $booking));
        $response->assertSessionHasErrors(['error']);
        $this->assertEquals('pending', $booking->fresh()->status);

        // 2. Set Actual Price
        $this->post(route('admin.bookings.update-price', $booking), ['actual_price' => 150]);
        $this->assertEquals(150, $booking->fresh()->actual_price);

        // 3. Confirm now -> Should succeed
        $response = $this->post(route('admin.bookings.confirm', $booking));
        $response->assertSessionHasNoErrors();
        
        $booking->refresh();
        $this->assertEquals('confirmed', $booking->status);
        $this->assertEquals('pending', $booking->payment_status); 

        Mail::assertQueued(\App\Mail\BookingConfirmed::class);
    }
}
