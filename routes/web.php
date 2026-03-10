<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TourController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\SupportChatController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\TourController as AdminTourController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\TourGuideController as AdminTourGuideController;
use App\Http\Controllers\Admin\ParkLocationController as AdminParkLocationController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\TestimonialController as AdminTestimonialController;
use App\Http\Controllers\Admin\AccommodationController as AdminAccommodationController;
use App\Http\Controllers\Admin\VehicleTypeController as AdminVehicleTypeController;
use App\Http\Controllers\Admin\VehicleController as AdminVehicleController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\CmsPageController;
use App\Http\Controllers\Admin\CmsSectionController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\BillingController;
use App\Http\Controllers\Admin\SecurityController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\TourGuide\AssignmentController as GuideAssignmentController;

/*
|--------------------------------------------------------------------------
| Web Routes - Ferdinand Safaris
|--------------------------------------------------------------------------
| This file is organized by domain: Public Pages, Tours, Bookings, 
| Payments, Customer Account, Admin Panel, and Guide Portal.
*/

// =========================================================================
// 1. PUBLIC PAGES & STATIC CONTENT
// =========================================================================
Route::get('/', function () {
    $cmsPage = \App\Models\CmsPage::where('slug', 'home')->where('is_active', true)->with('sections')->first();
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
    $featuredTours = \App\Models\Tour::where('is_featured', true)
        ->withCount('likes')
        ->orderBy('likes_count', 'desc')
        ->orderBy('views', 'desc')
        ->latest()
        ->take(6)
        ->get();
    $testimonials = \App\Models\Testimonial::where('is_featured', true)->where('is_approved', true)->latest()->take(10)->get();
    $topDestinations = \App\Models\ParkLocation::where('is_active', true)->orderBy('is_featured', 'desc')->orderBy('popularity_rank', 'desc')->take(12)->get();
    return view('welcome', compact('featuredTours', 'testimonials', 'cmsPage', 'hero', 'stats', 'topDestinations'));
})->name('home');

Route::get('/about', function () {
    $cmsPage = \App\Models\CmsPage::where('slug', 'about')->where('is_active', true)->with('sections')->first();
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

// Newsletter
Route::post('/newsletter/subscribe', [\App\Http\Controllers\NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

// Community Stories
Route::post('/community/stories', [\App\Http\Controllers\CommunityStoryController::class, 'store'])->middleware('auth')->name('community.stories.store');

// Social Auth Callback
Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirectToProvider'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback'])->name('social.callback');


// =========================================================================
// 2. TOURS & EXPLORATION
// =========================================================================
Route::prefix('tours')->name('tours.')->group(function () {
    Route::get('/', [TourController::class, 'index'])->name('index');
    Route::get('/featured', [TourController::class, 'featured'])->name('featured');
    Route::get('/category/{category}', [TourController::class, 'category'])->name('category');
    Route::get('/{tour}', [TourController::class, 'show'])->name('show');
    
    // Ajax Interactivity
    Route::post('/{tour}/check-availability', [TourController::class, 'checkAvailability'])->name('check-availability');
    Route::post('/{tour}/calculate-price', [TourController::class, 'calculatePrice'])->name('calculate-price');
    Route::post('/{tour}/like', [TourController::class, 'toggleLike'])->name('like');
});


// =========================================================================
// 3. BOOKING LIFECYCLE
// =========================================================================
Route::get('/tours/{tour}/book', [BookingController::class, 'create'])->name('bookings.create');
Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store')->middleware(['throttle:bookings']);
Route::get('/bookings/{bookingReference}', [BookingController::class, 'show'])->name('bookings.show');
Route::post('/bookings/{bookingReference}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');

// Payment Processing (Stripe)
Route::prefix('bookings/{bookingReference}/payment')->name('bookings.payment.')->group(function () {
    Route::get('/', [PaymentController::class, 'payment'])->name('init');
    Route::get('/success', [PaymentController::class, 'paymentSuccess'])->name('success');
    Route::get('/cancel', [PaymentController::class, 'paymentCancel'])->name('cancel');
});


// =========================================================================
// 4. CUSTOMER ACCOUNT AREA
// =========================================================================
Route::middleware(['auth', 'verified', 'profile.complete'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
    Route::get('/reviews', [DashboardController::class, 'reviews'])->name('reviews.index');
    Route::get('/my-bookings', [BookingController::class, 'myBookings'])->name('my-bookings');

    // Profile Management
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::patch('/customer', [ProfileController::class, 'updateCustomer'])->name('customer.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // Experience & Support
    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/', [SupportChatController::class, 'index'])->name('index');
        Route::get('/fetch', [SupportChatController::class, 'fetch'])->name('fetch');
        Route::post('/send', [SupportChatController::class, 'store'])->name('store');
    });
});


// =========================================================================
// 5. TOUR GUIDE PORTAL
// =========================================================================
Route::prefix('guide')
    ->name('guide.')
    ->middleware(['auth', 'verified', 'guide'])
    ->group(function () {
        Route::get('/dashboard', [GuideAssignmentController::class, 'dashboard'])->name('dashboard');
        Route::get('/assignments', [GuideAssignmentController::class, 'index'])->name('assignments.index');
        Route::get('/assignments/{assignment}', [GuideAssignmentController::class, 'show'])->name('assignments.show');
        Route::post('/assignments/{assignment}/accept', [GuideAssignmentController::class, 'accept'])->name('assignments.accept');
        Route::get('/assignments/{assignment}/decline', [GuideAssignmentController::class, 'showDeclineForm'])->name('assignments.decline');
        Route::post('/assignments/{assignment}/decline', [GuideAssignmentController::class, 'decline'])->name('assignments.decline.submit');
});

// Signed Url Routes for Quick Action (No Auth Required)
Route::get('/guide/assignment/{assignment}', [GuideAssignmentController::class, 'show'])->name('guide.assignment.public.show');
Route::get('/guide/assignment/{assignment}/accept', [GuideAssignmentController::class, 'accept'])->name('guide.assignment.public.accept');
Route::get('/guide/assignment/{assignment}/decline', [GuideAssignmentController::class, 'showDeclineForm'])->name('guide.assignment.public.decline');


// =========================================================================
// 6. ADMINISTRATIVE CONTROL PANEL
// =========================================================================
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {

        // Core Admin Dashboard
        Route::redirect('/', '/admin/dashboard');
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/analytics', [AdminDashboardController::class, 'analytics'])->name('analytics');

        // Inventory: Tours & Locations
        Route::get('/tours/export', [AdminTourController::class, 'export'])->name('tours.export');
        Route::post('/tours/import', [AdminTourController::class, 'import'])->name('tours.import');
        Route::resource('tours', AdminTourController::class);
        Route::post('/tours/{tour}/toggle-status', [AdminTourController::class, 'toggleStatus'])->name('tours.toggle-status');
        Route::post('/tours/{tour}/toggle-featured', [AdminTourController::class, 'toggleFeatured'])->name('tours.toggle-featured');
        Route::delete('/tours/{tour}/gallery/{index}', [AdminTourController::class, 'deleteGalleryImage'])->name('tours.delete-gallery-image');

        Route::get('/locations/export', [AdminParkLocationController::class, 'export'])->name('locations.export');
        Route::post('/locations/import', [AdminParkLocationController::class, 'import'])->name('locations.import');
        Route::resource('locations', AdminParkLocationController::class);

        // Operations: Bookings & Guides
        Route::get('/bookings/calendar', [AdminBookingController::class, 'calendar'])->name('bookings.calendar');
        Route::get('/bookings/export', [AdminBookingController::class, 'export'])->name('bookings.export');
        Route::resource('bookings', AdminBookingController::class)->only(['index', 'show']);
        Route::post('/bookings/{booking}/confirm', [AdminBookingController::class, 'confirm'])->name('bookings.confirm');
        Route::post('/bookings/{booking}/cancel', [AdminBookingController::class, 'cancel'])->name('bookings.cancel');
        Route::post('/bookings/{booking}/complete', [AdminBookingController::class, 'complete'])->name('bookings.complete');
        Route::post('/bookings/{booking}/update-price', [AdminBookingController::class, 'updatePrice'])->name('bookings.update-price');

        Route::get('/guides/export', [AdminTourGuideController::class, 'export'])->name('guides.export');
        Route::post('/guides/import', [AdminTourGuideController::class, 'import'])->name('guides.import');
        Route::resource('guides', AdminTourGuideController::class);

        // Feedback: Reviews & Testimonials
        Route::resource('reviews', AdminReviewController::class)->only(['index', 'destroy']);
        Route::post('/reviews/{review}/toggle-status', [AdminReviewController::class, 'toggleStatus'])->name('reviews.toggle-status');
        Route::resource('testimonials', AdminTestimonialController::class)->only(['index', 'destroy']);
        Route::post('/testimonials/{testimonial}/toggle-status', [AdminTestimonialController::class, 'toggleStatus'])->name('testimonials.toggle-status');

        // Assets: Accommodations & Vehicles
        Route::resource('accommodations', AdminAccommodationController::class);
        Route::resource('vehicle-types', AdminVehicleTypeController::class);
        Route::resource('vehicles', AdminVehicleController::class);

        // User Management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/admins', [AdminUserController::class, 'admins'])->name('admins');
            Route::get('/customers', [AdminUserController::class, 'customers'])->name('customers');
            Route::post('/{user}/promote', [AdminUserController::class, 'promote'])->name('promote');
            Route::post('/{user}/demote', [AdminUserController::class, 'demote'])->name('demote');
        });

        // Content & Communications
        Route::resource('cms-pages', CmsPageController::class);
        Route::resource('cms-sections', CmsSectionController::class);
        Route::prefix('messages')->name('messages.')->group(function () {
            Route::get('/', [MessageController::class, 'index'])->name('index');
            Route::get('/{user}', [MessageController::class, 'show'])->name('show');
            Route::post('/{user}', [MessageController::class, 'store'])->name('store');
        });

        // System Settings, Security & Audit
        Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
        Route::get('/security', [SecurityController::class, 'index'])->name('security.index');
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');

        // Newsletter Management
        Route::get('/newsletter', [\App\Http\Controllers\Admin\NewsletterController::class, 'index'])->name('newsletter.index');
        Route::delete('/newsletter/{subscriber}', [\App\Http\Controllers\Admin\NewsletterController::class, 'destroy'])->name('newsletter.destroy');
        Route::patch('/newsletter/{subscriber}/toggle', [\App\Http\Controllers\Admin\NewsletterController::class, 'toggle'])->name('newsletter.toggle');

        // Community Story Moderation
        Route::get('/community-stories', [\App\Http\Controllers\Admin\CommunityStoryController::class, 'index'])->name('community-stories.index');
        Route::patch('/community-stories/{story}/approve', [\App\Http\Controllers\Admin\CommunityStoryController::class, 'approve'])->name('community-stories.approve');
        Route::patch('/community-stories/{story}/reject', [\App\Http\Controllers\Admin\CommunityStoryController::class, 'reject'])->name('community-stories.reject');
        Route::patch('/community-stories/{story}/toggle-featured', [\App\Http\Controllers\Admin\CommunityStoryController::class, 'toggleFeatured'])->name('community-stories.toggle-featured');
        Route::delete('/community-stories/{story}', [\App\Http\Controllers\Admin\CommunityStoryController::class, 'destroy'])->name('community-stories.destroy');
    });
