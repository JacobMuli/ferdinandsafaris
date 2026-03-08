<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Use middleware for auth checks
    }

    public function rules(): array
    {
        return [
            'tour_id' => ['required', 'exists:tours,id'],
            'tour_date' => ['required', 'date', 'after:today'], // Basic check, advanced avail check is in Controller/Service
            'customer_type' => ['required', 'in:individual,family,group,corporate'],
            'adults_count' => ['required', 'integer', 'min:1'],
            'children_count' => ['nullable', 'integer', 'min:0'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:255'],
            // Corporate optional fields
            'company_name' => ['nullable', 'string', 'max:255', 'required_if:customer_type,corporate'],
            'tax_id' => ['nullable', 'string', 'max:50'],
            'special_requests' => ['nullable', 'string', 'max:1000'],
            'emergency_contact_name' => ['required', 'string', 'max:255'],
            'emergency_contact_phone' => ['required', 'string', 'max:20'],
            'participant_details' => ['nullable', 'array'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // "Individual" strict logic check
            if ($this->customer_type === 'individual') {
                if ($this->adults_count > 1) {
                    $validator->errors()->add('adults_count', 'Individual bookings are limited to 1 adult.');
                }
                if ($this->children_count > 0) {
                     $validator->errors()->add('children_count', 'Individual bookings cannot include children.');
                }
            }
        });
    }
}
