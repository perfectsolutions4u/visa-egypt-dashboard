<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Payment driver
    |--------------------------------------------------------------------------
    | sandbox  — completes card/paypal payments immediately (dev / staging)
    | manual   — keeps payments pending for admin confirmation
    | gateway  — uses external redirect URL when VISA_PAYMENT_GATEWAY_URL is set
    */
    'driver' => env('VISA_PAYMENT_DRIVER', 'sandbox'),

    'gateway_url' => env('VISA_PAYMENT_GATEWAY_URL'),

    'currency' => env('VISA_PAYMENT_CURRENCY', 'USD'),

    'webhook_secret' => env('VISA_PAYMENT_WEBHOOK_SECRET'),
];
