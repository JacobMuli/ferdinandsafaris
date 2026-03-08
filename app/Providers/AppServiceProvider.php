<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Gate::define('add-admin', function (\App\Models\User $user) {
            return $user->email === 'jacobmwalughs@gmail.com';
        });

        // Define rate limiters
        \Illuminate\Support\Facades\RateLimiter::for('bookings', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(3)->by($request->ip());
        });

        \Illuminate\Support\Facades\RateLimiter::for('contact', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by($request->ip());
        });

        \App\Models\Booking::observe(\App\Observers\BookingObserver::class);

        // Share unread messages count with admin sidebar
        \Illuminate\Support\Facades\View::composer('layouts.admin-sidebar', function ($view) {
            $count = \App\Models\Message::where('is_admin_message', false)
                ->where('is_read', false)
                ->count();
            $view->with('unreadMessagesCount', $count);
        });

        // Register new migration paths (Laravel doesn't scan subdirs by default for migrate command context usually,
        // though `loadMigrationsFrom` is more for packages.
        // Better approach for main app is usually in boot, or merging into migrator paths.
        // Actually, simple `loadMigrationsFrom` works in AppServiceProvider.)
        $this->loadMigrationsFrom([
            database_path('migrations/auth'),
            database_path('migrations/tours'),
            database_path('migrations/bookings'),
            database_path('migrations/content'),
        ]);
    }
}
