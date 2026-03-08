<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

// Schedule Google Reviews Sync
use Illuminate\Support\Facades\Schedule;

Schedule::command('google-reviews:sync')->daily();

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
