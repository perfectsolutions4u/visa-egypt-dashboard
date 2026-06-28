<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\TourController;
use App\Http\Controllers\Api\TripController;
use App\Http\Controllers\Api\RecaptchaController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\PasswordController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\CarRentalController;
use App\Http\Controllers\Api\CustomTripController;
use App\Http\Controllers\Api\TourReviewController;
use App\Http\Controllers\Api\DestinationController;
use App\Http\Controllers\Api\BlogCategoryController;
use App\Http\Controllers\Api\ContactRequestController;
use App\Http\Controllers\Api\Payment\PaypalController;
use App\Http\Controllers\Api\Payment\FawaterkController;
use App\Http\Controllers\Api\CustomizedTripCategoryController;
use App\Http\Controllers\Api\AmenityController;
use App\Http\Controllers\Api\HotelController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\HotelRoomBookingController;
use App\Http\Controllers\Api\CityController;

// API Routes with Localization Middleware
Route::group(['as' => 'api.', 'middleware' => ['api.localize']], function () {
    
    // Public Routes (No Authentication Required)
    
    // ReCAPTCHA Verification
    Route::post('/verify-recaptcha', [RecaptchaController::class, 'verify']);
    
    // Hotel Availability Search (Public)
    Route::post('hotels/search-availability', [HotelController::class, 'searchAvailability'])
        ->name('hotels.search.availability');
    
    // Trip Search & Booking (Public)
    Route::group(['prefix' => 'trips', 'as' => 'trips.'], function () {
        Route::get('/', [TripController::class, 'index'])->name('index');
        Route::post('search', [TripController::class, 'search'])->name('search');
        Route::get('{trip}', [TripController::class, 'show'])->name('show');
        Route::post('book', [TripController::class, 'book'])->name('book');
    });
    
    // Cities (Public)
    Route::get('cities', [CityController::class, 'index'])->name('cities.index');
    
    // Payment Routes
    Route::group(['prefix' => 'payments', 'as' => 'payments.'], function () {
        Route::group(['prefix' => 'fawaterk', 'as' => 'fawaterk.'], function () {
            Route::get('methods', [FawaterkController::class, 'methods'])->name('methods');
            Route::get('update/invoice', [FawaterkController::class, 'updateInvoice'])->name('update.invoice');
        });
        Route::group(['prefix' => 'paypal', 'as' => 'paypal.'], function () {
            Route::get('capture', [PaypalController::class, 'capture'])->name('capture');
            Route::get('cancel', [PaypalController::class, 'cancel'])->name('cancel');
        });
    });
    
    // Cart Management
    Route::group(['prefix' => 'cart', 'as' => 'cart.'], function () {
        Route::get('list', [CartController::class, 'list'])->name('list');
        Route::post('tours/append', [CartController::class, 'appendTour'])->name('append.tour');
        Route::post('rentals/append', [CartController::class, 'appendRental'])->name('append.rental');
        Route::post('hotel-bookings/append', [CartController::class, 'appendHotelRoomBooking'])->name('append.hotel_booking');
        Route::delete('remove/{item}', [CartController::class, 'remove'])->name('remove');
        Route::delete('clear', [CartController::class, 'clear'])->name('clear');
    });
    
    // Booking Creation
    Route::post('/bookings', [BookingController::class, 'create'])->name('create');
    
    // Authentication Routes
    Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
        Route::post('/login', [AuthController::class, 'login'])->name('login');
        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::post('password/forget', [PasswordController::class, 'forget'])->name('client.forget.password');
        Route::post('password/reset', [PasswordController::class, 'reset'])->name('client.reset.password');
        Route::post('password/otp/verify', [PasswordController::class, 'otpVerify'])->name('client.password.otp.verify');
    });
    
    // Custom Trips
    Route::post('custom/trips', CustomTripController::class)->name('custom.trips');
    
    // Content Routes (Public)
    Route::group(['prefix' => 'destinations', 'as' => 'destinations.'], function () {
        Route::get('/', [DestinationController::class, 'index'])->name('index');
        Route::get('/{slug}', [DestinationController::class, 'show'])->name('show');
    });
    
    Route::group(['prefix' => 'categories', 'as' => 'categories.'], function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/{slug}', [CategoryController::class, 'show'])->name('show');
    });
    
    Route::group(['prefix' => 'tours', 'as' => 'tours.'], function () {
        Route::get('/', [TourController::class, 'index'])->name('index');
        Route::get('/stats', [TourController::class, 'stats'])->name('stats');
        Route::get('/{slug}', [TourController::class, 'show'])->name('show');
    });
    
    Route::group(['prefix' => 'tour-reviews', 'as' => 'tour-reviews.'], function () {
        Route::get('/', [TourReviewController::class, 'index'])->name('index');
        Route::get('/{tourReview}', [TourReviewController::class, 'show'])->name('show');
    });
    
    Route::group(['prefix' => 'blogs', 'as' => 'blogs.'], function () {
        Route::get('/', [BlogController::class, 'index'])->name('index');
        Route::get('/{slug}', [BlogController::class, 'show'])->name('show');
    });
    
    Route::group(['prefix' => 'blog-categories', 'as' => 'blog-categories.'], function () {
        Route::get('/', [BlogCategoryController::class, 'index'])->name('index');
        Route::get('/{slug}', [BlogCategoryController::class, 'show'])->name('show');
    });
    
    Route::group(['prefix' => 'locations', 'as' => 'locations.'], function () {
        Route::get('/', [LocationController::class, 'index'])->name('index');
        Route::get('/{slug}', [LocationController::class, 'show'])->name('show');
    });
    
    Route::group(['prefix' => 'faqs', 'as' => 'faqs.'], function () {
        Route::get('/', [FaqController::class, 'index'])->name('index');
    });
    
    Route::group(['prefix' => 'pages', 'as' => 'pages.'], function () {
        Route::get('/{slug}', [PageController::class, 'show'])->name('show');
    });
    
    Route::group(['prefix' => 'countries', 'as' => 'countries.'], function () {
        Route::get('/', [CountryController::class, 'index'])->name('index');
    });
    
    Route::group(['prefix' => 'currencies', 'as' => 'currencies.'], function () {
        Route::get('/', [CurrencyController::class, 'index'])->name('index');
    });
    
    Route::group(['prefix' => 'amenities', 'as' => 'amenities.'], function () {
        Route::get('/', [AmenityController::class, 'index'])->name('index');
    });
    
    Route::group(['prefix' => 'hotels', 'as' => 'hotels.'], function () {
        Route::get('/', [HotelController::class, 'index'])->name('index');
        Route::get('/{slug}', [HotelController::class, 'show'])->name('show');
    });
    
    Route::group(['prefix' => 'rooms', 'as' => 'rooms.'], function () {
        Route::get('/', [RoomController::class, 'index'])->name('index');
        Route::get('/{slug}', [RoomController::class, 'show'])->name('show');
    });
    
    Route::group(['prefix' => 'car-rentals', 'as' => 'car-rentals.'], function () {
        Route::get('/', [CarRentalController::class, 'index'])->name('index');
        Route::get('/{slug}', [CarRentalController::class, 'show'])->name('show');
    });
    
    Route::group(['prefix' => 'customized-trip-categories', 'as' => 'customized-trip-categories.'], function () {
        Route::get('/', [CustomizedTripCategoryController::class, 'index'])->name('index');
    });
    
    Route::group(['prefix' => 'settings', 'as' => 'settings.'], function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
    });
    
    Route::group(['prefix' => 'coupons', 'as' => 'coupons.'], function () {
        Route::post('validate', [CouponController::class, 'validate'])->name('validate');
    });
    
    Route::post('contact-requests', [ContactRequestController::class, 'store'])->name('contact-requests.store');
    
    // Protected Routes (Authentication Required)
    Route::group(['middleware' => ['auth:sanctum']], function () {
        
        Route::group(['prefix' => 'profile', 'as' => 'profile.'], function () {
            Route::get('/', [ProfileController::class, 'show'])->name('show');
            Route::put('/', [ProfileController::class, 'update'])->name('update');
            Route::put('password', [ProfileController::class, 'updatePassword'])->name('update.password');
        });
        
        Route::group(['prefix' => 'wishlist', 'as' => 'wishlist.'], function () {
            Route::get('/', [WishlistController::class, 'index'])->name('index');
            Route::post('tours/{tour}', [WishlistController::class, 'toggleTour'])->name('toggle.tour');
            Route::delete('tours/{tour}', [WishlistController::class, 'removeTour'])->name('remove.tour');
        });
        
        Route::group(['prefix' => 'tour-reviews', 'as' => 'tour-reviews.'], function () {
            Route::post('{tour}', [TourReviewController::class, 'store'])->name('store');
        });
        
        Route::group(['prefix' => 'hotel-room-bookings', 'as' => 'hotel-room-bookings.'], function () {
            Route::get('/', [HotelRoomBookingController::class, 'index'])->name('index');
            Route::post('/', [HotelRoomBookingController::class, 'store'])->name('store');
            Route::get('{booking}', [HotelRoomBookingController::class, 'show'])->name('show');
        });
    });
    //RoutePlace
});
