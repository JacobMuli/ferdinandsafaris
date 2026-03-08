<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

use App\Models\Customer;
use App\Models\Payment;

class ProfileController extends Controller
{
    private $systemDataService;

    public function __construct(\App\Services\SystemDataService $systemDataService)
    {
        $this->systemDataService = $systemDataService;
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        // Try to find customer by user_id first
        $customer = Customer::where('user_id', $user->id)->first();

        // If not found, try to find by email and link it
        if (!$customer) {
            $customer = Customer::where('email', $user->email)->first();

            if ($customer) {
                // Link existing customer to this user
                $customer->update(['user_id' => $user->id]);
            } else {
                // Create new customer if doesn't exist at all
                $customer = Customer::create([
                    'user_id' => $user->id,
                    'first_name' => explode(' ', $user->name)[0],
                    'last_name' => (explode(' ', $user->name)[1] ?? '') ?: explode(' ', $user->name)[0],
                    'email' => $user->email,
                    'phone' => '',
                    'country' => '',
                    'customer_type' => 'individual',
                ]);
            }
        }

        $completionPercentage = $customer ? $this->calculateCompletionPercentage($customer) : 0;
        $countries = $this->systemDataService->getGenericCountries();

        return view('profile.edit', [
            'user' => $user,
            'customer' => $customer,
            'payments' => $customer->bookings()->with('payments')->get()->pluck('payments')->flatten()->sortByDesc('created_at')->take(5),
            'completionPercentage' => $completionPercentage,
            'countries' => $countries
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the customer's detailed information.
     */
    public function updateCustomer(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'], // Phone should be required
            'country' => ['required', 'string', 'max:100'], // Country must be required to match NOT NULL DB constraint
            'company_name' => ['nullable', 'string', 'max:255'],
            'tax_id' => ['nullable', 'string', 'max:100'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
            'avatar' => ['nullable', 'image', 'max:1024'], // 1MB Max
        ]);

        $data = $request->except('avatar');

        // Handle Avatar Upload
        if ($request->hasFile('avatar')) {
             $path = $request->file('avatar')->store('avatars', 'public');
             $data['avatar'] = $path;
        }

        $customer = Customer::updateOrCreate(
            ['user_id' => $request->user()->id],
            $data
        );

        return Redirect::route('profile.edit')->with('status', 'customer-updated');
    }

    private function calculateCompletionPercentage(Customer $customer): int
    {
        $fields = [
            'first_name', 'last_name', 'email', 'phone', 'country',
            'emergency_contact_name', 'emergency_contact_phone', 'avatar'
        ];

        $filled = 0;
        foreach ($fields as $field) {
            if (!empty($customer->$field)) {
                $filled++;
            }
        }

        return (int) round(($filled / count($fields)) * 100);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
