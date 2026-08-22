<?php

namespace App\Providers;

use App\Enums\SettingKey;
use App\Enums\PaymentMethod;
use App\Payments\PaymentFactory;
use App\Payments\PaymentGateway;
use App\Channels\WhatsappChannel;
use App\Services\Recaptcha\RecaptchaService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Illuminate\Notifications\ChannelManager;
use App\Services\Translation\Google\FreeTranslator;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     * @throws BindingResolutionException
     */
    public function register(): void
    {
        $app = $this->app;
        $this->app->make(ChannelManager::class)->extend('whatsapp', function () use ($app) {
            return $app->make(WhatsappChannel::class);
        });

        $this->app->bind(PaymentGateway::class,
            fn() => PaymentFactory::instance(request('payment_method', PaymentMethod::COD->value)));

            $this->app->bind(RecaptchaService::class, function ($app) {
                return new RecaptchaService();
            });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(125);
        File::macro('isEmptyDir', function ($path) {
            return count(glob("$path/*")) === 0;
        });

        // Multipart uploads (Flutter web) sometimes omit the Authorization header.
        Sanctum::getAccessTokenFromRequestUsing(function ($request) {
            $bearer = $request->bearerToken();
            if (filled($bearer)) {
                return $bearer;
            }

            $header = $request->header('X-Authorization');
            if (is_string($header) && str_starts_with($header, 'Bearer ')) {
                return substr($header, 7);
            }

            $fromInput = $request->input('access_token');
            return filled($fromInput) ? (string) $fromInput : null;
        });
    }
}
