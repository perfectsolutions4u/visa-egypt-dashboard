<?php

use App\Http\Controllers\Api\V1\AppContentController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\LoyaltyController;
use App\Http\Controllers\Api\V1\MembershipController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\OfferController;
use App\Http\Controllers\Api\V1\PaymentDiscountController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\ProgramController;
use App\Http\Controllers\Api\V1\ServicePackageController;
use App\Http\Controllers\Api\V1\TrackingController;
use App\Http\Controllers\Api\V1\VehicleController;
use App\Http\Controllers\Api\V1\VisaBookingController;
use App\Http\Controllers\Api\V1\VisaSettingController;
use App\Http\Controllers\Api\V1\VoucherController;
use App\Http\Controllers\Api\V1\WalletController;
use Illuminate\Support\Facades\Route;

return function (string $namePrefix = 'v1.'): void {
    Route::prefix('v1')
        ->as($namePrefix)
        ->middleware(['api.localize'])
        ->group(function () {
            // Auth (public)
            Route::post('auth/register', [AuthController::class, 'register'])->name('auth.register');
            Route::post('auth/login', [AuthController::class, 'login'])->name('auth.login');
            Route::post('auth/verify-otp', [AuthController::class, 'verifyOtp'])->name('auth.verify-otp');

            // Public content
            Route::get('programs', [ProgramController::class, 'index'])->name('programs.index');
            Route::get('programs/{program:slug}', [ProgramController::class, 'show'])->name('programs.show');
            Route::get('service-packages', [ServicePackageController::class, 'index'])->name('service-packages.index');
            Route::get('vehicles', [VehicleController::class, 'index'])->name('vehicles.index');
            Route::get('offers', [OfferController::class, 'index'])->name('offers.index');
            Route::get('settings/visa', [VisaSettingController::class, 'index'])->name('settings.visa');
            Route::get('content/visa-on-arrival', [AppContentController::class, 'visaOnArrival'])->name('content.visa-on-arrival');
            Route::get('content/arrival-journey', [AppContentController::class, 'arrivalJourney'])->name('content.arrival-journey');
            Route::get('content/support', [AppContentController::class, 'support'])->name('content.support');
            Route::get('content/policies', [AppContentController::class, 'policies'])->name('content.policies');
            Route::get('content/visa-eligibility', [AppContentController::class, 'visaEligibility'])->name('content.visa-eligibility');
            Route::get('content/additional-services', [AppContentController::class, 'additionalServices'])->name('content.additional-services');
            Route::get('content/membership-plans', [AppContentController::class, 'membershipPlans'])->name('content.membership-plans');

            // Authenticated
            Route::middleware('auth:sanctum')->group(function () {
                Route::post('auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
                Route::get('auth/me', [AuthController::class, 'me'])->name('auth.me');

                Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
                Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
                Route::post('profile/photo', [ProfileController::class, 'uploadPhoto'])->name('profile.photo');
                Route::put('profile/language', [ProfileController::class, 'updateLanguage'])->name('profile.language');

                Route::get('bookings', [VisaBookingController::class, 'index'])->name('bookings.index');
                Route::post('bookings', [VisaBookingController::class, 'store'])->name('bookings.store');
                Route::get('bookings/{visa_booking}', [VisaBookingController::class, 'show'])->name('bookings.show');
                Route::get('bookings/{visa_booking}/tracking', [TrackingController::class, 'show'])->name('bookings.tracking');

                Route::get('membership', [MembershipController::class, 'show'])->name('membership.show');
                Route::post('membership/checkout', [MembershipController::class, 'checkout'])->name('membership.checkout');

                Route::get('wallet', [WalletController::class, 'show'])->name('wallet.show');
                Route::get('wallet/transactions', [WalletController::class, 'transactions'])->name('wallet.transactions');

                Route::get('vouchers', [VoucherController::class, 'index'])->name('vouchers.index');
                Route::post('vouchers/redeem', [VoucherController::class, 'redeem'])->name('vouchers.redeem');

                Route::get('loyalty', [LoyaltyController::class, 'show'])->name('loyalty.show');
                Route::post('loyalty/daily-claim', [LoyaltyController::class, 'claimDaily'])->name('loyalty.daily-claim');
                Route::post('loyalty/preview', [LoyaltyController::class, 'preview'])->name('loyalty.preview');
                Route::get('loyalty/transactions', [LoyaltyController::class, 'transactions'])->name('loyalty.transactions');

                Route::get('payments/discount-options', [PaymentDiscountController::class, 'options'])->name('payments.discount-options');
                Route::post('payments/discount-preview', [PaymentDiscountController::class, 'preview'])->name('payments.discount-preview');

                Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
                Route::get('payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');

                Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
                Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
                Route::patch('notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
                Route::patch('notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
            });
        });
};
