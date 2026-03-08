<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Customer;

class EnsureProfileComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Skip for admins or unauthenticated users (auth middleware handles unauth)
        if (!$user || $user->is_admin) {
            return $next($request);
        }

        // Fetch or create customer record
        $customer = Customer::firstOrCreate(
            ['user_id' => $user->id],
            [
                'email' => $user->email,
                'first_name' => explode(' ', $user->name)[0],
                'last_name' => (explode(' ', $user->name)[1] ?? '') ?: explode(' ', $user->name)[0],
                'phone' => '',
                'country' => '',
                'customer_type' => 'individual',
            ]
        );

        // Check required fields
        if (empty($customer->first_name) || empty($customer->last_name) || empty($customer->phone) || empty($customer->country)) {

            // Allow access to profile routes and essential pages to prevent endless redirect loop
            if ($request->routeIs('profile.*') ||
                $request->routeIs('logout') ||
                $request->routeIs('my-bookings') ||
                $request->routeIs('chat.*')) {
                return $next($request);
            }

            return redirect()->route('profile.edit')
                ->with('warning', 'Please complete your profile information (Phone, Country) before proceeding.');
        }

        return $next($request);
    }
}
