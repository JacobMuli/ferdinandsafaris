<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TourController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\TourController as AdminTourController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\ContactController;
// use App\Http\Controllers\PaymentController; // Removed

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home
Route::get('/', function () {
    $cmsPage = \App\Models\CmsPage::where('slug', 'home')->where('is_active', true)->with('sections')->first();
    
    // Fallback data if CMS not seeded or active
    $hero = $cmsPage?->sections->where('section_key', 'hero')->first()?->content ?? [
        'heading' => "Discover Africa's Wonders",
        'subheading' => "Experience unforgettable safari adventures with expert guides and luxury accommodations",
        'bg_image' => "https://images.unsplash.com/photo-1516426122078-c23e76319801"
    ];

    $stats = $cmsPage?->sections->where('section_key', 'stats')->first()?->content['items'] ?? [
        ['value' => '500+', 'label' => 'Happy Travelers'],
        ['value' => '50+', 'label' => 'Unique Tours'],
        ['value' => '15+', 'label' => 'Years Experience'],
        ['value' => '4.9', 'label' => 'Average Rating'],
    ];

    $featuredTours = \App\Models\Tour::where('is_featured', true)->latest()->take(6)->get();
    $testimonials = \App\Models\Testimonial::where('is_featured', true)
        ->where('is_approved', true)
        ->latest()
        ->take(10)
        ->get();

    // Top Destinations for Grid
    $topDestinations = \App\Models\ParkLocation::where('is_active', true)
        ->orderBy('is_featured', 'desc')
        ->orderBy('popularity_rank', 'desc')
        ->take(12)
        ->get();
        
    return view('welcome', compact('featuredTours', 'testimonials', 'cmsPage', 'hero', 'stats', 'topDestinations'));
})->name('home');

Route::get('/about', function () {
    $cmsPage = \App\Models\CmsPage::where('slug', 'about')->where('is_active', true)->with('sections')->first();
    // Fallback? The views should handle nulls or sections finding.
    return view('pages.about', compact('cmsPage'));
})->name('about');

Route::get('/contact', function () {
    $cmsPage = \App\Models\CmsPage::where('slug', 'contact')->where('is_active', true)->with('sections')->first();
    return view('pages.contact', compact('cmsPage'));
})->name('contact');

Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit')->middleware('throttle:contact');

Route::get('/community', function () {
    $cmsPage = \App\Models\CmsPage::where('slug', 'community')->where('is_active', true)->with('sections')->first();
    return view('pages.community', compact('cmsPage'));
})->name('community');


// =====================
// Public Tour Routes
// =====================
Route::get('/tours', [TourController::class, 'index'])->name('tours.index');
Route::get('/tours/featured', [TourController::class, 'featured'])->name('tours.featured');
Route::get('/tours/category/{category}', [TourController::class, 'category'])->name('tours.category');
Route::get('/tours/{tour}', [TourController::class, 'show'])->name('tours.show');

Route::post('/tours/{tour}/check-availability', [TourController::class, 'checkAvailability'])
    ->name('tours.check-availability');

Route::post('/tours/{tour}/calculate-price', [TourController::class, 'calculatePrice'])
    ->name('tours.calculate-price');


// =====================
// Booking Routes (Guest + Auth)
// =====================
Route::get('/tours/{tour}/book', [BookingController::class, 'create'])
    ->name('bookings.create');

Route::post('/bookings', [BookingController::class, 'store'])
    ->name('bookings.store')->middleware(['throttle:bookings']);

Route::get('/bookings/{bookingReference}', [BookingController::class, 'show'])
    ->name('bookings.show');

Route::get('/bookings/{bookingReference}/payment', [BookingController::class, 'payment'])
    ->name('bookings.payment');
Route::get('/bookings/{bookingReference}/payment/success', [BookingController::class, 'paymentSuccess'])
    ->name('bookings.payment.success');
Route::get('/bookings/{bookingReference}/payment/cancel', [BookingController::class, 'paymentCancel'])
    ->name('bookings.payment.cancel');

Route::post('/bookings/{bookingReference}/cancel', [BookingController::class, 'cancel'])
    ->name('bookings.cancel');


// =====================
// Authenticated User Routes
// =====================
Route::middleware(['auth', 'verified', 'profile.complete'])->group(function () {

    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/my-bookings', [BookingController::class, 'myBookings'])
        ->name('my-bookings');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::patch('/profile/customer', [ProfileController::class, 'updateCustomer'])
        ->name('profile.customer.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    // Support Chat
    Route::get('/chat', [App\Http\Controllers\SupportChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/fetch', [App\Http\Controllers\SupportChatController::class, 'fetch'])->name('chat.fetch');
    Route::post('/chat/send', [App\Http\Controllers\SupportChatController::class, 'store'])->name('chat.store');
});


// =====================
// Admin Routes
// =====================
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {

        // Dashboard
        Route::redirect('/', '/admin/dashboard');
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/analytics', [AdminDashboardController::class, 'analytics'])
            ->name('analytics');

        // Tours Management
        Route::get('/tours/export', [AdminTourController::class, 'export'])->name('tours.export');
        Route::post('/tours/import', [AdminTourController::class, 'import'])->name('tours.import');
        Route::resource('tours', AdminTourController::class);

        Route::post('/tours/{tour}/toggle-status', [AdminTourController::class, 'toggleStatus'])
            ->name('tours.toggle-status');

        Route::post('/tours/{tour}/toggle-featured', [AdminTourController::class, 'toggleFeatured'])
            ->name('tours.toggle-featured');

        Route::delete('/tours/{tour}/gallery/{index}', [AdminTourController::class, 'deleteGalleryImage'])
            ->name('tours.delete-gallery-image');

        // Bookings Management
        Route::get('/bookings', [AdminBookingController::class, 'index'])
            ->name('bookings.index');

        Route::get('/bookings/calendar', [AdminBookingController::class, 'calendar'])
            ->name('bookings.calendar');

        Route::get('/bookings/export', [AdminBookingController::class, 'export'])
            ->name('bookings.export');

        Route::get('/bookings/{booking}', [AdminBookingController::class, 'show'])
            ->name('bookings.show');

        Route::post('/bookings/{booking}/confirm', [AdminBookingController::class, 'confirm'])
            ->name('bookings.confirm');

        Route::post('/bookings/{booking}/cancel', [AdminBookingController::class, 'cancel'])
            ->name('bookings.cancel');

        Route::post('/bookings/{booking}/complete', [AdminBookingController::class, 'complete'])
            ->name('bookings.complete');

        Route::post('/bookings/{booking}/update-price', [AdminBookingController::class, 'updatePrice'])
            ->name('bookings.update-price');

        // Tour Guides Management
        Route::get('/guides/export', [\App\Http\Controllers\Admin\TourGuideController::class, 'export'])->name('guides.export');
        Route::post('/guides/import', [\App\Http\Controllers\Admin\TourGuideController::class, 'import'])->name('guides.import');
        Route::resource('guides', \App\Http\Controllers\Admin\TourGuideController::class);

        // Park Locations Management
        Route::get('/locations/export', [\App\Http\Controllers\Admin\ParkLocationController::class, 'export'])->name('locations.export');
        Route::post('/locations/import', [\App\Http\Controllers\Admin\ParkLocationController::class, 'import'])->name('locations.import');
        Route::resource('locations', \App\Http\Controllers\Admin\ParkLocationController::class);

        // Reviews & Testimonials
        Route::resource('reviews', \App\Http\Controllers\Admin\ReviewController::class)->only(['index', 'destroy']);
        Route::post('/reviews/{review}/toggle-status', [\App\Http\Controllers\Admin\ReviewController::class, 'toggleStatus'])->name('reviews.toggle-status');

        Route::resource('testimonials', \App\Http\Controllers\Admin\TestimonialController::class)->only(['index', 'destroy']);
        Route::post('/testimonials/{testimonial}/toggle-status', [\App\Http\Controllers\Admin\TestimonialController::class, 'toggleStatus'])->name('testimonials.toggle-status');

        // Accommodations & Vehicles
        Route::get('/accommodations/export', [\App\Http\Controllers\Admin\AccommodationController::class, 'export'])->name('accommodations.export');
        Route::post('/accommodations/import', [\App\Http\Controllers\Admin\AccommodationController::class, 'import'])->name('accommodations.import');
        Route::resource('accommodations', \App\Http\Controllers\Admin\AccommodationController::class);
        
        Route::get('/vehicle-types/export', [\App\Http\Controllers\Admin\VehicleTypeController::class, 'export'])->name('vehicle-types.export');
        Route::post('/vehicle-types/import', [\App\Http\Controllers\Admin\VehicleTypeController::class, 'import'])->name('vehicle-types.import');
        Route::resource('vehicle-types', \App\Http\Controllers\Admin\VehicleTypeController::class);
        
        Route::get('/vehicles/export', [\App\Http\Controllers\Admin\VehicleController::class, 'export'])->name('vehicles.export');
        Route::post('/vehicles/import', [\App\Http\Controllers\Admin\VehicleController::class, 'import'])->name('vehicles.import');
        Route::resource('vehicles', \App\Http\Controllers\Admin\VehicleController::class);

        // User Management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/admins', [\App\Http\Controllers\Admin\UserController::class, 'admins'])->name('admins');
            Route::get('/customers', [\App\Http\Controllers\Admin\UserController::class, 'customers'])->name('customers');
            Route::post('/{user}/promote', [\App\Http\Controllers\Admin\UserController::class, 'promote'])->name('promote');
            Route::post('/{user}/demote', [\App\Http\Controllers\Admin\UserController::class, 'demote'])->name('demote');
        });
        // Content Management (CMS)
        Route::resource('cms-pages', App\Http\Controllers\Admin\CmsPageController::class);
        Route::resource('cms-sections', App\Http\Controllers\Admin\CmsSectionController::class);

        // Messages
        Route::get('/messages', [App\Http\Controllers\Admin\MessageController::class, 'index'])->name('messages.index');
        Route::get('/messages/{user}', [App\Http\Controllers\Admin\MessageController::class, 'show'])->name('messages.show');
        Route::post('/messages/{user}', [App\Http\Controllers\Admin\MessageController::class, 'store'])->name('messages.store');

        // Billing & Security
        Route::get('/billing', [App\Http\Controllers\Admin\BillingController::class, 'index'])->name('billing.index');
        Route::get('/security', [App\Http\Controllers\Admin\SecurityController::class, 'index'])->name('security.index');

    });


// =====================
// Auth Routes (Breeze / Jetstream)
// =====================
require __DIR__.'/auth.php';

// Social Auth
Route::get('/auth/{provider}/redirect', [App\Http\Controllers\Auth\SocialAuthController::class, 'redirectToProvider'])
    ->name('social.redirect');
Route::get('/auth/{provider}/callback', [App\Http\Controllers\Auth\SocialAuthController::class, 'handleProviderCallback'])
    ->name('social.callback');
