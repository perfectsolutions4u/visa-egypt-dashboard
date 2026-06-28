<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        Route::bind('visa_booking', function (string $value) {
            return \App\Models\Visa\VisaBooking::query()
                ->where('booking_ref', $value)
                ->orWhere('id', $value)
                ->firstOrFail();
        });

        Route::bind('payment', function (string $value) {
            return \App\Models\Visa\VisaPayment::findOrFail($value);
        });

        Route::bind('notification', function (string $value) {
            return \App\Models\Visa\AppNotification::findOrFail($value);
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            $registerV1Routes = require base_path('routes/api_v1.php');

            Route::middleware('api')
                ->prefix('api')
                ->group(fn () => $registerV1Routes('v1.'));

            // Also expose /v1/* for clients that omit the /api prefix (e.g. Swagger try-it-out).
            Route::middleware('api')
                ->group(fn () => $registerV1Routes('v1.root.'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(10000)->by($request->user()?->id ?: $request->ip());
        });
    }
}
