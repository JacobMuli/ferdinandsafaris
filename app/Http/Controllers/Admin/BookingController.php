<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['tour', 'customer']);

        // Filters
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('customer_type') && $request->customer_type !== 'all') {
            $query->where('customer_type', $request->customer_type);
        }

        if ($request->has('payment_status') && $request->payment_status !== 'all') {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('date_from')) {
            $query->where('tour_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('tour_date', '<=', $request->date_to);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('booking_reference', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('email', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        // Sorting
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'tour_date':
                $query->orderBy('tour_date', 'asc');
                break;
            case 'amount_high':
                $query->orderBy('total_amount', 'desc');
                break;
            case 'amount_low':
                $query->orderBy('total_amount', 'asc');
                break;
            default: // latest
                $query->orderBy('created_at', 'desc');
        }

        $bookings = $query->paginate(20);

        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        $booking->load(['tour', 'customer', 'payments']);

        return view('admin.bookings.show', compact('booking'));
    }

    public function confirm(Booking $booking)
    {
        if ($booking->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending bookings can be confirmed.']);
        }

        if (is_null($booking->actual_price)) {
            return back()->withErrors(['error' => 'Please set the Actual Price before confirming the booking.']);
        }

        $booking->update([
            'status' => 'confirmed',
            'payment_status' => 'pending'
        ]);

        // Send confirmation email to customer
        Mail::to($booking->customer->email)->send(new \App\Mail\BookingConfirmed($booking));

        return back()->with('success', 'Booking confirmed successfully!');
    }

    public function cancel(Request $request, Booking $booking)
    {
        $request->validate([
            'cancellation_reason' => 'required|string',
        ]);

        $booking->cancel($request->cancellation_reason);

        // Send cancellation email to customer
        Mail::to($booking->customer->email)->send(new \App\Mail\BookingCancelled($booking));

        return back()->with('success', 'Booking cancelled successfully!');
    }

    public function complete(Booking $booking)
    {
        if (!in_array($booking->status, ['confirmed', 'paid'])) {
            return back()->withErrors(['error' => 'Only confirmed or paid bookings can be marked as completed.']);
        }

        $booking->complete();

        return back()->with('success', 'Booking marked as completed!');
    }

    public function updatePrice(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'actual_price' => 'required|numeric|min:0',
        ]);

        $booking->update([
            'actual_price' => $validated['actual_price']
        ]);

        // Optional: Trigger recalculation or log change
        // notification logic could go here

        return back()->with('success', 'Actual billed price updated successfully.');
    }

    public function calendar(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $startOfMonth = \Carbon\Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        // Fetch bookings that might overlap: Start date is on or before end of month
        // And effectively End Date (Start + Duration) is on or after Start of Month.
        // Simplified: Start Date <= End Of Month. We'll filter strictly in loop.
        $queryBookings = Booking::with(['tour.parkLocations', 'tourGuides'])
            ->where('tour_date', '<=', $endOfMonth)
            ->where('tour_date', '>=', $startOfMonth->copy()->subDays(30)) // Optimization: don't fetch ancient bookings
            ->whereIn('status', ['confirmed', 'paid', 'completed', 'pending'])
            ->get();

        $events = [];

        foreach ($queryBookings as $booking) {
            $duration = $booking->tour->duration_days ?? 1;
            $startDate = $booking->tour_date->copy(); // Ensure Carbon instance
            $endDate = $booking->tour_date->copy()->addDays($duration - 1);

            // Skip if booking ends before month starts
            if ($endDate->lt($startOfMonth)) {
                continue;
            }

            // Iterate through every day of the booking
            $period = \Carbon\CarbonPeriod::create($startDate, $endDate);

            foreach ($period as $date) {
                // Only add if date is within the requested month (optional, but keeps payload smaller)
                // Actually, let's keep all to handle edge overlaps correctly if view asks for prev/next month days
                $dateString = $date->format('Y-m-d');

                if (!isset($events[$dateString])) {
                    $events[$dateString] = collect();
                }

                // Clone booking to attach view-specific metadata without affecting other references
                $eventBooking = clone $booking;
                $eventBooking->is_start = $date->isSameDay($startDate);
                $eventBooking->is_end = $date->isSameDay($endDate);

                $events[$dateString]->push($eventBooking);
            }
        }

        return view('admin.bookings.calendar', [
            'bookings' => $events,
            'month' => $month,
            'year' => $year
        ]);
    }

    public function export(Request $request)
    {
        $directory = 'exports';
        $filename = 'bookings.csv';
        $path = $directory . '/' . $filename;

        if (!file_exists(storage_path('app/' . $directory))) {
            mkdir(storage_path('app/' . $directory), 0755, true);
        }

        $bookings = Booking::with(['customer', 'tour'])->get();

        $handle = fopen(storage_path('app/' . $path), 'w');

        fputcsv($handle, [
            'Booking ID',
            'Customer',
            'Tour',
            'Status',
            'Total Amount',
            'Booking Date'
        ]);

        foreach ($bookings as $booking) {
            fputcsv($handle, [
                $booking->id,
                optional($booking->customer)->full_name ?? 'N/A',
                optional($booking->tour)->name ?? 'N/A',
                $booking->status,
                $booking->total_amount,
                $booking->created_at->format('Y-m-d'),
            ]);
        }

        fclose($handle);

        return response()->download(
            storage_path('app/' . $path)
        )->deleteFileAfterSend(false);
    }

    public function edit(Booking $booking)
    {
        // Allow editing regardless of status, but careful with logic
        $booking->load(['tour', 'customer', 'tourGuides', 'vehicles']);
        $availableGuides = \App\Models\TourGuide::active()->get();
        // Assuming we have a Vehicle model and can list available ones
        $availableVehicles = \App\Models\Vehicle::available()->get();

        return view('admin.bookings.edit', compact('booking', 'availableGuides', 'availableVehicles'));
    }

    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'tour_date' => 'required|date',
            'special_requests' => 'nullable|string',
            'tour_guide_id' => 'nullable|exists:tour_guides,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            // Add other fields as necessary, but AVOID core tour package fields if possible
        ]);

        // Update basic booking details
        $booking->update([
            'tour_date' => $validated['tour_date'],
            'special_requests' => $validated['special_requests'],
        ]);

        // Update Guide Assignment
        if ($request->has('tour_guide_id')) {
            // Detach all current and attach new (or sync)
            // For simplicity, assuming one lead guide for now or replacing current lead
            $booking->tourGuides()->sync([$validated['tour_guide_id'] => ['is_lead_guide' => true]]);
        }

        // Update Vehicle Assignment
        if ($request->has('vehicle_id')) {
            // Similar logic for vehicle
             $booking->vehicles()->sync([$validated['vehicle_id'] => [
                 'start_date' => $booking->tour_date,
                 'end_date' => $booking->tour_date->copy()->addDays($booking->tour->duration_days ?? 1),
                 'status' => 'reserved'
             ]]);

             // Recalculate totals
             $booking->recalculateTotals();
        }

        return redirect()->route('admin.bookings.show', $booking)
            ->with('success', 'Booking updated successfully.');
    }
}