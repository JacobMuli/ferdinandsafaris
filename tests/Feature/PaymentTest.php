<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Tour;
use App\Models\User;
use App\Models\Booking;
use App\Models\Customer;
use App\Services\StripeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Mockery\MockInterface;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected $tour;
    protected $user;
    protected $customer;
    protected $booking;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->tour = Tour::create([
            'name' => 'Test Safari',
            'slug' => 'test-safari',
            'description' => 'Test',
            'price_per_person' => 1000,
            'duration_days' => 5,
            'is_active' => true,
            'category' => 'wildlife',
            'location' => 'Kenya',
        ]);

        $this->user = User::factory()->create();
        $this->customer = Customer::create([
            'user_id' => $this->user->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => $this->user->email,
            'phone' => '123456',
            'country' => 'US',
            'customer_type' => 'individual'
        ]);

        $this->booking = Booking::create([
            'booking_reference' => 'FS-TEST123',
            'tour_id' => $this->tour->id,
            'customer_id' => $this->customer->id,
            'customer_type' => 'individual',
            'tour_date' => now()->addDays(10),
            'adults_count' => 1,
            'total_participants' => 1,
            'base_price' => 1000,
            'total_amount' => 1000,
            'status' => 'pending',
            'payment_status' => 'pending',
            'emergency_contact_name' => 'None',
            'emergency_contact_phone' => 'None',
        ]);
    }

    public function test_user_cannot_initiate_payment_if_pending()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('bookings.payment.init', $this->booking->booking_reference));

        $response->assertRedirect(route('bookings.show', $this->booking->booking_reference));
        $response->assertSessionHas('error');
    }

    public function test_user_can_initiate_payment_if_confirmed_and_priced()
    {
        $this->actingAs($this->user);

        // Prepare booking for payment
        $this->booking->update([
            'status' => 'confirmed',
            'actual_price' => 1200
        ]);

        // Mock Stripe Service
        $this->instance(
            StripeService::class,
            Mockery::mock(StripeService::class, function (MockInterface $mock) {
                $mock->shouldReceive('createCheckoutSession')
                    ->once()
                    ->andReturn((object)['url' => 'https://checkout.stripe.com/test']);
            })
        );

        $response = $this->get(route('bookings.payment.init', $this->booking->booking_reference));

        $response->assertRedirect('https://checkout.stripe.com/test');
    }

    public function test_payment_success_callback_updates_booking_and_creates_record()
    {
        \Illuminate\Support\Facades\Mail::fake();

        $this->booking->update([
            'status' => 'confirmed',
            'actual_price' => 1200
        ]);

        // Mock Stripe Service for retrieval
        $this->instance(
            StripeService::class,
            Mockery::mock(StripeService::class, function (MockInterface $mock) {
                $mock->shouldReceive('retrieveSession')
                    ->once()
                    ->andReturn((object)[
                        'payment_status' => 'paid',
                        'client_reference_id' => $this->booking->booking_reference
                    ]);
            })
        );

        $response = $this->get(route('bookings.payment.success', [
            'bookingReference' => $this->booking->booking_reference,
            'session_id' => 'test_session_id'
        ]));

        $response->assertRedirect(route('bookings.show', $this->booking->booking_reference));
        $response->assertSessionHas('success');

        $this->booking->refresh();
        $this->assertEquals('paid', $this->booking->status);
        $this->assertEquals('paid', $this->booking->payment_status);

        $this->assertDatabaseHas('payments', [
            'booking_id' => $this->booking->id,
            'amount' => 1200,
            'payment_method' => 'stripe',
            'status' => 'completed'
        ]);

        \Illuminate\Support\Facades\Mail::assertQueued(\App\Mail\BookingConfirmed::class);
    }

    public function test_payment_cancel_callback_redirects_with_info()
    {
        $response = $this->get(route('bookings.payment.cancel', $this->booking->booking_reference));

        $response->assertRedirect(route('bookings.show', $this->booking->booking_reference));
        $response->assertSessionHas('info');
    }
}
