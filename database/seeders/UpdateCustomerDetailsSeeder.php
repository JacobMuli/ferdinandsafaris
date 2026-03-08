<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Customer;
use Faker\Factory as Faker;

class UpdateCustomerDetailsSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $users = User::all();
        $count = 0;

        foreach ($users as $user) {
            // Check if user has a customer profile, if not create/update it
            $customer = Customer::firstOrNew(['user_id' => $user->id]);
            
            // Name logic
            if (!$customer->exists) {
                $nameParts = explode(' ', $user->name, 2);
                $customer->first_name = $nameParts[0];
                $customer->last_name = $nameParts[1] ?? 'User';
                $customer->email = $user->email;
            }

            // Populate missing fields
            if (empty($customer->phone)) {
                $customer->phone = $faker->phoneNumber;
            }
            if (empty($customer->country)) {
                $customer->country = $faker->country;
            }
            if (empty($customer->emergency_contact_name)) {
                $customer->emergency_contact_name = $faker->name;
            }
            if (empty($customer->emergency_contact_phone)) {
                $customer->emergency_contact_phone = $faker->phoneNumber;
            }
            
            // Set default customer type if missing
            if (empty($customer->customer_type)) {
                $customer->customer_type = 'individual';
            }

            $customer->save();
            $count++;
        }

        $this->command->info("Customer details (phone, country, emergency contacts) updated for {$count} users.");
    }
}
