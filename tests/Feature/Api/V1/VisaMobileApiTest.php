<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class VisaMobileApiTest extends TestCase
{
    public function test_v1_routes_are_registered_under_api_and_root_prefixes(): void
    {
        $routeNames = [
            'auth.register',
            'auth.login',
            'auth.verify-otp',
            'auth.logout',
            'auth.me',
            'programs.index',
            'programs.show',
            'service-packages.index',
            'vehicles.index',
            'offers.index',
            'settings.visa',
            'profile.show',
            'bookings.index',
            'membership.show',
            'wallet.show',
            'notifications.index',
        ];

        foreach (['v1.', 'v1.root.'] as $prefix) {
            foreach ($routeNames as $name) {
                $this->assertTrue(
                    Route::has($prefix.$name),
                    "Missing route: {$prefix}{$name}"
                );
            }
        }
    }

    public function test_register_endpoint_is_not_missing_route(): void
    {
        foreach (['/v1/auth/register', '/api/v1/auth/register'] as $uri) {
            $this->postJson($uri, [], ['X-Localize' => 'en'])
                ->assertStatus(422)
                ->assertJsonPath('status', false);
        }
    }
}
