<?php

use App\Http\Controllers\Dashboard\AutoTranslationController;
use App\Http\Controllers\Dashboard\BlogController;
use App\Http\Controllers\Dashboard\BookingController;
use App\Http\Controllers\Dashboard\CarRentalController;
use App\Http\Controllers\Dashboard\CarRouteController;
use App\Http\Controllers\Dashboard\CategoryController;
use App\Http\Controllers\Dashboard\ClientController;
use App\Http\Controllers\Dashboard\ContactRequestController;
use App\Http\Controllers\Dashboard\CouponController;
use App\Http\Controllers\Dashboard\CurrencyController;
use App\Http\Controllers\Dashboard\CustomTripController;
use App\Http\Controllers\Dashboard\DestinationController;
use App\Http\Controllers\Dashboard\LocationController;
use App\Http\Controllers\Dashboard\MainController;
use App\Http\Controllers\Dashboard\PageController;
use App\Http\Controllers\Dashboard\RoleController;
use App\Http\Controllers\Dashboard\DatabaseBackupController;
use App\Http\Controllers\Dashboard\SettingController;
use App\Http\Controllers\Dashboard\SitemapController;
use App\Http\Controllers\Dashboard\TourController;
use App\Http\Controllers\Dashboard\TourOptionController;
use App\Http\Controllers\Dashboard\TourReviewController;
use App\Http\Controllers\Dashboard\TripController;
use App\Http\Controllers\Dashboard\TripBookingController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Dashboard\FaqController;
use App\Http\Controllers\Dashboard\BlogCategoryController;
use App\Http\Controllers\Dashboard\CustomizedTripCategoryController;
use App\Http\Controllers\Dashboard\RedirectRuleController;
use App\Http\Controllers\Dashboard\AmenityController;
use App\Http\Controllers\Dashboard\HotelController;
use App\Http\Controllers\Dashboard\RoomController;
use App\Http\Controllers\Dashboard\HotelRoomBookingController;
use App\Http\Controllers\Dashboard\Visa\ProgramController;
use App\Http\Controllers\Dashboard\Visa\ServicePackageController;
use App\Http\Controllers\Dashboard\Visa\VehicleController;
use App\Http\Controllers\Dashboard\Visa\StaffController;
use App\Http\Controllers\Dashboard\Visa\OfferController;
use App\Http\Controllers\Dashboard\Visa\VoucherController;
use App\Http\Controllers\Dashboard\Visa\MembershipPlanController;
use App\Http\Controllers\Dashboard\Visa\LoyaltySettingsController;
use App\Http\Controllers\Dashboard\Visa\AppUpdateSettingsController;
use App\Http\Controllers\Dashboard\Visa\PoliciesContentController;
use App\Http\Controllers\Dashboard\Visa\SupportContentController;
use App\Http\Controllers\Dashboard\Visa\VisaOnArrivalContentController;
use App\Http\Controllers\Dashboard\Visa\VisaEligibleNationalityController;
use App\Http\Controllers\Dashboard\Visa\MembershipController;
use App\Http\Controllers\Dashboard\Visa\VisaPaymentController;
use App\Http\Controllers\Dashboard\Visa\AppNotificationController;
use App\Http\Controllers\Dashboard\Visa\VisaBookingController;
use App\Http\Controllers\Dashboard\Visa\TrackingController;
use Illuminate\Support\Facades\Route;
//controllers
Route::group([
    'prefix' => 'dashboard',
    'middleware' => ['auth:web', 'permitted'],
    'as' => 'dashboard.'
], function () {
    
    // Profile & Theme Routes
    Route::get('toggle-theme', [ProfileController::class, 'toggleTheme'])->name('toggle-theme');
    
    // System Routes
    Route::post('cache/clear', [MainController::class, 'clearCache'])->name('cache.clear');
    Route::post('translate', [AutoTranslationController::class, 'translate'])->name('model.auto.translate');
    Route::get('sitemap/generate', SitemapController::class)->name('sitemap.generate');
    
    // User Management
    Route::resource('users', UserController::class)->except('show');
    Route::resource('roles', RoleController::class)->except('show');
    Route::resource('clients', ClientController::class);
    Route::post('clients/{client}/wallet/adjust', [ClientController::class, 'adjustWallet'])->name('clients.wallet.adjust');
    
    // Content Management
    Route::resource('destinations', DestinationController::class)->except('show');
    Route::resource('categories', CategoryController::class)->except('show');
    Route::resource('tours', TourController::class)->except('show');
    Route::resource('tour-options', TourOptionController::class)->except('show');
    Route::resource('pages', PageController::class)->except('show');
    Route::resource('blogs', BlogController::class)->except('show');
    Route::resource('blog-categories', BlogCategoryController::class)->except('show');
    Route::resource('locations', LocationController::class)->except('show');
    Route::resource('faqs', FaqController::class)->except('show');
    
    // Hotel Management
    Route::resource('hotels', HotelController::class)->except('show');
    Route::resource('rooms', RoomController::class)->except('show');
    Route::resource('amenities', AmenityController::class)->except('show');
    Route::resource('hotel_room_bookings', HotelRoomBookingController::class);
    
    // Car Rental Management
    Route::resource('car-routes', CarRouteController::class)->except('show');
    Route::post('car-routes/import', [CarRouteController::class, 'import'])->name('car-routes.import');
    Route::get('car-routes/template', [CarRouteController::class, 'template'])->name('car-routes.template');
    
    Route::group(['prefix' => 'car-rentals', 'as' => 'car-rentals.'], function () {
        Route::get('/', [CarRentalController::class, 'index'])->name('index');
        Route::get('/{carRental}', [CarRentalController::class, 'show'])->name('show');
    });
    
    // Custom Trips Management
    Route::group(['prefix' => 'custom-trips', 'as' => 'custom-trips.'], function () {
        Route::get('/', [CustomTripController::class, 'index'])->name('index');
        Route::get('/{customTrip}', [CustomTripController::class, 'show'])->name('show');
        Route::put('/{customTrip}', [CustomTripController::class, 'assign'])->name('assign');
    });
    
    Route::resource('customized-trip-categories', CustomizedTripCategoryController::class)->except('show');
    
    // Trips & Bookings Management
    Route::resource('trips', TripController::class);
    Route::get('trips/{trip}/bookings', [TripController::class, 'tripBookings'])->name('trips.trip-bookings');
    Route::post('trips/{trip}/toggle-status', [TripController::class, 'toggleStatus'])->name('trips.toggle-status');
    Route::get('trips/{trip}/details', [TripController::class, 'getTripDetails'])->name('trips.details');
    
    Route::resource('trip-bookings', TripBookingController::class);
    Route::post('trip-bookings/{tripBooking}/toggle-status', [TripBookingController::class, 'toggleStatus'])->name('trip-bookings.toggle-status');
    Route::post('trip-bookings/{tripBooking}/cancel', [TripBookingController::class, 'cancel'])->name('trip-bookings.cancel');
    Route::get('trip-bookings/export', [TripBookingController::class, 'export'])->name('trip-bookings.export');
    
    // Financial Management
    Route::resource('coupons', CouponController::class)->except('show');
    Route::resource('currencies', CurrencyController::class)->except('show');
    Route::get('currencies/rates/update', [CurrencyController::class, 'updateRates'])->name('currencies.rates.update');
    
    // Booking Management
    Route::resource('bookings', BookingController::class)->except(['create', 'store', 'edit', 'destroy']);
    Route::resource('tour-reviews', TourReviewController::class)->only('index');
    
    // Contact Management
    Route::resource('contact-requests', ContactRequestController::class)->except('show');
    Route::post('contact-requests/mark-as-spam', [ContactRequestController::class, 'markAsSpam'])
        ->name('contact-requests.mark-as-spam');
    
    // SEO & Redirects
    Route::resource('redirect-rules', RedirectRuleController::class)->except('show');
    Route::get('redirect-rules/export', [RedirectRuleController::class, 'export'])->name('redirect-rules.export');
    Route::post('redirect-rules/import', [RedirectRuleController::class, 'import'])->name('redirect-rules.import');
    
    // Settings
    Route::group(['prefix' => 'settings', 'as' => 'settings.'], function () {
        Route::get('show', [SettingController::class, 'show'])->name('show');
        Route::put('update', [SettingController::class, 'update'])->name('update');
    });

    Route::get('database-backups', [DatabaseBackupController::class, 'index'])->name('database-backups.index');
    Route::post('database-backups', [DatabaseBackupController::class, 'store'])->name('database-backups.store');
    Route::get('database-backups/{filename}/download', [DatabaseBackupController::class, 'download'])
        ->where('filename', '[A-Za-z0-9._-]+')
        ->name('database-backups.download');
    Route::post('database-backups/restore', [DatabaseBackupController::class, 'restore'])->name('database-backups.restore');
    Route::delete('database-backups/{filename}', [DatabaseBackupController::class, 'destroy'])
        ->where('filename', '[A-Za-z0-9._-]+')
        ->name('database-backups.destroy');

    // Visa Egypt Management
    Route::resource('programs', ProgramController::class)->except('show');
    Route::resource('service-packages', ServicePackageController::class)->except('show');
    Route::resource('vehicles', VehicleController::class)->except('show');
    Route::resource('staff', StaffController::class)->except('show');
    Route::resource('offers', OfferController::class)->except('show');
    Route::resource('vouchers', VoucherController::class)->except('show');
    Route::get('membership-plans/manage', [MembershipPlanController::class, 'manage'])->name('membership-plans.manage');
    Route::post('membership-plans/{membershipPlan}/toggle-active', [MembershipPlanController::class, 'toggleActive'])->name('membership-plans.toggle-active');
    Route::post('membership-plans/{membershipPlan}/toggle-featured', [MembershipPlanController::class, 'toggleFeatured'])->name('membership-plans.toggle-featured');
    Route::resource('membership-plans', MembershipPlanController::class)->except('show');
    Route::get('policies/edit', [PoliciesContentController::class, 'edit'])->name('policies.edit');
    Route::put('policies', [PoliciesContentController::class, 'update'])->name('policies.update');
    Route::get('visa-on-arrival/edit', [VisaOnArrivalContentController::class, 'edit'])->name('visa-on-arrival.edit');
    Route::put('visa-on-arrival', [VisaOnArrivalContentController::class, 'update'])->name('visa-on-arrival.update');
    Route::resource('visa-nationalities', VisaEligibleNationalityController::class)->except('show');
    Route::get('support-content/edit', [SupportContentController::class, 'edit'])->name('visa-settings.edit');
    Route::put('support-content', [SupportContentController::class, 'update'])->name('visa-settings.update');
    Route::get('loyalty-settings/edit', [LoyaltySettingsController::class, 'edit'])->name('loyalty-settings.edit');
    Route::put('loyalty-settings', [LoyaltySettingsController::class, 'update'])->name('loyalty-settings.update');
    Route::get('app-update-settings/edit', [AppUpdateSettingsController::class, 'edit'])->name('app-update-settings.edit');
    Route::put('app-update-settings', [AppUpdateSettingsController::class, 'update'])->name('app-update-settings.update');
    Route::resource('memberships', MembershipController::class);
    Route::resource('visa-payments', VisaPaymentController::class)->only(['index', 'show']);
    Route::resource('app-notifications', AppNotificationController::class)->only(['index', 'create', 'store', 'show']);

    Route::resource('visa-bookings', VisaBookingController::class)->only(['index', 'show', 'update']);
    Route::post('visa-bookings/{visa_booking}/confirm', [VisaBookingController::class, 'confirm'])->name('visa-bookings.confirm');
    Route::post('visa-bookings/{visa_booking}/cancel', [VisaBookingController::class, 'cancel'])->name('visa-bookings.cancel');
    Route::post('visa-bookings/{visa_booking}/accept', [VisaBookingController::class, 'accept'])->name('visa-bookings.accept');
    Route::post('visa-bookings/{visa_booking}/reject', [VisaBookingController::class, 'reject'])->name('visa-bookings.reject');
    Route::post('visa-bookings/{visa_booking}/assign', [VisaBookingController::class, 'assign'])->name('visa-bookings.assign');

    Route::get('tracking', [TrackingController::class, 'index'])->name('tracking.index');
    Route::get('tracking/{visa_booking}', [TrackingController::class, 'show'])->name('tracking.show');
    Route::post('tracking/{visa_booking}/advance', [TrackingController::class, 'advance'])->name('tracking.advance');
    Route::post('tracking/{visa_booking}/complete', [TrackingController::class, 'complete'])->name('tracking.complete');
    //RoutePlace
});
