<?php

namespace App\Services;

use App\Models\Tour;
use App\Models\ParkLocation;
use App\Models\VehicleType;

class PricingService
{

    /**
     * Calculate the full price breakdown for a tour booking.
     *
     * @param Tour $tour
     * @param int $adults
     * @param int $children
     * @param string $customerType
     * @param int|null $vehicleTypeId
     * @param string $accommodationType (budget, standard, deluxe, suite)
     * @return array
     */
    public function calculateTourPrice(Tour $tour, int $adults, int $children, string $customerType, ?int $vehicleTypeId = null, string $accommodationType = 'standard', $accommodationId = null): array
    {
        // Use the Tour model's central logic
        $options = [
            'vehicle_type_id' => $vehicleTypeId,
            'accommodation_id' => $accommodationId,
            // 'resident_status' => ... // Could be added if passed in
        ];

        // Tour::calculatePrice returns ['base_price', 'discount', 'final_price', 'breakdown']
        $calculated = $tour->calculatePrice($customerType, $adults, $children, $options);

        $basePrice = $calculated['base_price']; // This includes park/accom/vehicle now in Tour model logic
        $discountAmount = $calculated['discount'];
        $finalPrice = $calculated['final_price'];
        $breakdown = $calculated['breakdown'];

        // Tax Calculation (if not already in Tour model? Tour model didn't seem to have tax logic in calculatePrice, checking Step 818)
        // Step 818 Tour::calculatePrice: Returns base - discount. No tax mentioned.
        // BookingController::recalculateTotals had tax logic (16% VAT).
        // Let's apply tax here.

        $taxableAmount = $finalPrice;
        $taxRate = \App\Models\SiteSetting::get('tax_rate', 0.16);
        $taxAmount = $taxableAmount * $taxRate;
        $totalAmount = $taxableAmount + $taxAmount;

        return [
            'base_price' => round($basePrice, 2), // Total before discount
            'discount_amount' => round($discountAmount, 2),
            'discount_type' => ($discountAmount > 0) ? $customerType : null,
            'tax_amount' => round($taxAmount, 2),
            'total_amount' => round($totalAmount, 2),
            'currency' => config('safaris.currency', 'USD'),
            'breakdown' => $breakdown, // Pass through the detailed breakdown

            // Legacy keys for compatibility if needed (though we should update controller to use breakdown)
            'park_fees' => round($breakdown['park_fees'] ?? 0, 2),
            'vehicle_upgrade_cost' => round($breakdown['vehicle_cost'] ?? 0, 2),
            'accommodation_adjustment' => round($breakdown['accommodation_cost'] ?? 0, 2),
        ];
    }
}
