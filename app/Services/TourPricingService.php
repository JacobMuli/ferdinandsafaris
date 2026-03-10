<?php

namespace App\Services;

use App\Models\Tour;
use App\Models\Accommodation;
use App\Models\VehicleType;

class TourPricingService
{
    public function calculate(Tour $tour, $customerType, $adultsCount, $childrenCount = 0, $options = [])
    {
        // 1. Base Tour Cost (Service/Guide/margin)
        $basePrice = $adultsCount * $tour->price_per_person;
        if ($childrenCount > 0 && $tour->child_price) {
            $basePrice += $childrenCount * $tour->child_price;
        }

        $breakdown = [
            'base_tour_cost' => $basePrice,
            'accommodation_cost' => 0,
            'vehicle_cost' => 0,
            'park_fees' => 0,
            'discount' => 0,
        ];

        $durationDays = $tour->duration_days;

        // 2. Accommodation Cost
        if (!empty($options['accommodation_id'])) {
            $accommodation = $tour->accommodations()->find($options['accommodation_id']);
            if (!$accommodation) {
                 $accommodation = Accommodation::find($options['accommodation_id']);
            }

            if ($accommodation) {
                $pppn = $accommodation->pivot->price_per_night ?? $accommodation->price_per_night_standard;
                $nights = $accommodation->pivot->nights ?? ($durationDays - 1);
                if ($nights < 1) $nights = 1;

                $accomCost = ($pppn * $nights * ($adultsCount + $childrenCount));
                $breakdown['accommodation_cost'] = $accomCost;
                $basePrice += $accomCost;
            }
        }

        // 3. Vehicle Cost
        if (!empty($options['vehicle_type_id'])) {
            $vehicleType = VehicleType::find($options['vehicle_type_id']);
            if ($vehicleType) {
                $vehicleCost = $vehicleType->base_daily_rate * $durationDays;
                $breakdown['vehicle_cost'] = $vehicleCost;
                $basePrice += $vehicleCost;
            }
        }

        // 4. Park Fees
        $parkFees = 0;
        foreach ($tour->parkLocations as $park) {
            $daysInPark = $park->pivot->days ?? 1;
            $adultFee = $park->entry_fee_adult * $daysInPark * $adultsCount;
            $childFee = $park->entry_fee_child * $daysInPark * $childrenCount;
            $parkFees += ($adultFee + $childFee);
        }
        $breakdown['park_fees'] = $parkFees;
        $basePrice += $parkFees;

        // 5. Discounts
        $totalParticipants = $adultsCount + $childrenCount;
        $discount = 0;

        if ($customerType === 'group' && $totalParticipants >= $tour->min_group_size) {
            $discount = $basePrice * ($tour->group_discount_percentage / 100);
        } elseif ($customerType === 'corporate') {
            $discount = $basePrice * ($tour->corporate_discount_percentage / 100);
        }

        $breakdown['discount'] = $discount;

        return [
            'base_price' => $basePrice,
            'discount' => $discount,
            'final_price' => $basePrice - $discount,
            'breakdown' => $breakdown
        ];
    }
}
