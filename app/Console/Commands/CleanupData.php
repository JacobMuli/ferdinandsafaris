<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanupData extends Command
{
    protected $signature = 'data:cleanup';
    protected $description = 'Clean up seeded data for manual testing';

    public function handle()
    {
        if (!$this->confirm('This will wipe all Bookings, Payments, Reviews, Customers, and Non-Admin Users. Are you sure?', true)) {
            return;
        }

        $this->info('Starting clean up...');

        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();

        $this->info('Truncating Payments...');
        \App\Models\Payment::truncate();

        $this->info('Truncating Bookings...');
        \App\Models\Booking::truncate();

        $this->info('Truncating Reviews...');
        \App\Models\Review::truncate();

        $this->info('Truncating Customers...');
        \App\Models\Customer::truncate();

        $this->info('Deleting Non-Admin Users...');
        \App\Models\User::where('is_admin', false)->delete();

        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        $this->info('Cleanup complete!');
    }
}
