<?php

namespace App\Http\Controllers;

use App\Services\PricingService;
use App\Models\Tour;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\VehicleType;
use Illuminate\Http\Request;
use App\Http\Requests\StoreBookingRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Services\StripeService;

class BookingController extends Controller
{
    protected $pricingService;
    protected $systemDataService;

    public function __construct(PricingService $pricingService, \App\Services\SystemDataService $systemDataService)
    {
        $this->pricingService = $pricingService;
        $this->systemDataService = $systemDataService;
    }

    public function create(Tour $tour)
    {
        $vehicleTypes = VehicleType::where('is_active', true)->orderBy('base_daily_rate')->get();
        $countries = $this->systemDataService->getGenericCountries();
        $accommodationTypes = $this->systemDataService->getAccommodationTypes();
        $customerTypes = $this->systemDataService->getCustomerTypes();

        return view('bookings.create', compact('tour', 'vehicleTypes', 'countries', 'accommodationTypes', 'customerTypes'));
    }

    public function store(StoreBookingRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $tour = Tour::findOrFail($validated['tour_id']);

            // Check if tour is available for the selected date
            if (!$tour->isAvailableForDate($validated['tour_date'])) {
                return back()->withErrors([
                    'tour_date' => 'This tour is not available on the selected date. Please check availability.'
                ])->withInput();
            }

            // Check availability
            $totalParticipants = $validated['adults_count'] + ($validated['children_count'] ?? 0);
            $availableSpots = $tour->getAvailableSpots($validated['tour_date']);

            if ($availableSpots < $totalParticipants) {
                return back()->withErrors(['tour_date' => 'Not enough spots available for this date.'])->withInput();
            }

            // Find or create customer (and update details)
            $customerData = [
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'phone' => $validated['phone'],
                'country' => $validated['country'],
                'customer_type' => $validated['customer_type'],
                'company_name' => $validated['company_name'] ?? null,
                'tax_id' => $validated['tax_id'] ?? null,
            ];

            if (auth()->check()) {
                $customerData['user_id'] = auth()->id();
            }

            $customer = Customer::updateOrCreate(
                ['email' => $validated['email']],
                $customerData
            );

            // Calculate pricing using Service (Backend Source of Truth)
            $pricing = $this->pricingService->calculateTourPrice(
                $tour,
                $validated['adults_count'],
                $validated['children_count'] ?? 0,
                $validated['customer_type'],
                $request->input('vehicle_type_id'),
                $request->input('accommodation_type', 'standard'),
                $request->input('accommodation_id') // Pass ID if available
            );

            // Prepare Special Requests with Accommodation Preference
            $specialRequests = $validated['special_requests'] ?? '';
            if ($request->filled('accommodation_type') && $request->input('accommodation_type') !== 'standard') {
                $pref = ucfirst($request->input('accommodation_type'));
                $specialRequests .= ($specialRequests ? "\n" : "") . "Accommodation Preference: {$pref}";
            }

            // Create booking
            $booking = Booking::create([
                'booking_reference' => config('safaris.reference_prefix', 'FS-') . strtoupper(uniqid()),
                'tour_id' => $tour->id,
                'customer_id' => $customer->id,
                'customer_type' => $validated['customer_type'],
                'tour_date' => $validated['tour_date'],
                'vehicle_type_id' => $request->input('vehicle_type_id'),
                'accommodation_id' => $request->input('accommodation_id'), // Store ID
                'adults_count' => $validated['adults_count'],
                'children_count' => $validated['children_count'] ?? 0,
                'total_participants' => $totalParticipants,
                'base_price' => $pricing['base_price'],
                'discount_amount' => $pricing['discount_amount'],
                'discount_type' => $pricing['discount_type'],
                'tax_amount' => $pricing['tax_amount'],
                'total_amount' => $pricing['total_amount'],
                'pricing_breakdown' => $pricing['breakdown'] ?? [], // Store breakdown
                'status' => 'pending',
                'payment_status' => 'pending',
                'special_requests' => $specialRequests,
                'participant_details' => $validated['participant_details'] ?? [],
                'emergency_contact_name' => $validated['emergency_contact_name'],
                'emergency_contact_phone' => $validated['emergency_contact_phone'],
            ]);

            DB::commit();

            // Send confirmation email
            try {
                Mail::to($customer->email)->send(new \App\Mail\BookingConfirmationMail($booking));

                // Notify Admin
                $admins = \App\Models\User::where('is_admin', true)->get();
                \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\NewBookingNotification($booking));
            } catch (\Exception $e) {
                // Log error but don't fail controller
                \Illuminate\Support\Facades\Log::error('Notification failed: ' . $e->getMessage());
            }

            if (auth()->check()) {
                return redirect()->route('my-bookings')
                    ->with('success', 'Booking request received! You can track its status here.');
            }

            return redirect()->route('tours.index')
                ->with('success', 'Booking request received! We will review and send you a confirmation email shortly.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Booking creation failed: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'Booking failed: ' . $e->getMessage()])->withInput();
        }
    }

    public function show(Request $request, $bookingReference)
    {
        $booking = Booking::where('booking_reference', $bookingReference)
            ->with(['tour', 'tour.parkLocations', 'customer', 'payments'])
            ->firstOrFail();

        if ($request->ajax()) {
            return view('bookings.partials.details', compact('booking'));
        }

        return view('bookings.show', compact('booking'));
    }

    public function payment($bookingReference, StripeService $stripeService)
    {
        $booking = Booking::where('booking_reference', $bookingReference)
            ->with(['tour', 'customer'])
            ->firstOrFail();

        // Security: Ensure booking is confirmed and has a price set by admin
        if (!in_array($booking->status, ['confirmed']) || !$booking->actual_price) {
            return redirect()->route('bookings.show', $bookingReference)
                ->with('error', 'This booking is not yet ready for payment. Please wait for admin confirmation.');
        }

        try {
            $session = $stripeService->createCheckoutSession($booking);
            return redirect($session->url);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Stripe Integration Error: ' . $e->getMessage());
            return back()->with('error', 'Error initiating payment: ' . $e->getMessage());
        }
    }

    public function paymentSuccess($bookingReference)
    {
        $booking = Booking::where('booking_reference', $bookingReference)->firstOrFail();
        
        // In a real app, we'd verify with Stripe API or Webhooks
        // For now, we'll mark as paid upon redirect (less secure but works for demo)
        
        DB::beginTransaction();
        try {
            $booking->markAsPaid();
            $booking->customer->updateBookingStats();
            
            // Create payment record
            Payment::create([
                'booking_id' => $booking->id,
                'transaction_id' => 'STRIPE-' . strtoupper(Str::random(12)),
                'payment_method' => 'stripe',
                'amount' => $booking->actual_price,
                'currency' => config('safaris.currency', 'USD'),
                'status' => 'completed',
            ]);

            DB::commit();

            // Notify Customer
            try {
                Mail::to($booking->customer->email)->send(new \App\Mail\BookingConfirmed($booking));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Mail Error: ' . $e->getMessage());
            }

            return redirect()->route('bookings.show', $bookingReference)
                ->with('success', 'Payment successful! Your adventure is confirmed.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('bookings.show', $bookingReference)
                ->with('error', 'Error updating payment status: ' . $e->getMessage());
        }
    }

    public function paymentCancel($bookingReference)
    {
        return redirect()->route('bookings.show', $bookingReference)
            ->with('info', 'Payment was cancelled. You can try again when you are ready.');
    }

    public function cancel(Request $request, $bookingReference)
    {
        $booking = Booking::where('booking_reference', $bookingReference)->firstOrFail();

        if (!$booking->canBeCancelled()) {
            return back()->withErrors(['error' => 'This booking cannot be cancelled.']);
        }

        $booking->cancel($request->cancellation_reason);

        return redirect()->route('bookings.show', $booking->booking_reference)
            ->with('success', 'Booking cancelled successfully.');
    }

    public function myBookings()
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to view your bookings.');
        }

        $customer = Customer::where('user_id', auth()->id())->first();

        if (!$customer) {
            $bookings = collect();
        } else {
            $bookings = Booking::where('customer_id', $customer->id)
                ->with(['tour', 'tour.parkLocations', 'payments'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }

        return view('bookings.my-bookings', compact('bookings'));
    }
}