<?php

$base = $argv[1] ?? 'http://127.0.0.1:8000';
$prefixes = ['/v1', '/api/v1'];

$publicGets = [
    'programs',
    'service-packages',
    'vehicles',
    'offers',
    'settings/visa',
];

foreach ($prefixes as $prefix) {
    echo "\n=== Testing {$base}{$prefix} ===\n";
    foreach ($publicGets as $path) {
        $url = "{$base}{$prefix}/{$path}";
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Accept: application/json', 'X-Localize: en'],
        ]);
        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $status = json_decode($body, true)['status'] ?? null;
        echo sprintf("%-20s HTTP %d status=%s\n", $path, $code, $status === true ? 'ok' : 'fail');
    }

    $registerUrl = "{$base}{$prefix}/auth/register";
    $payload = json_encode([
        'name' => 'API Tester',
        'email' => 'apitest_' . str_replace('/', '_', trim($prefix, '/')) . '@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'language' => 'en',
    ]);
    $ch = curl_init($registerUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => [
            'Accept: application/json',
            'Content-Type: application/json',
            'X-Localize: en',
        ],
    ]);
    $body = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $decoded = json_decode($body, true);
    echo sprintf("%-20s HTTP %d status=%s\n", 'auth/register', $code, ($decoded['status'] ?? false) ? 'ok' : 'fail');

    if ($code === 201 && isset($decoded['data']['otp'])) {
        $otp = $decoded['data']['otp'];
        $email = 'apitest_' . str_replace('/', '_', trim($prefix, '/')) . '@example.com';
        $verifyUrl = "{$base}{$prefix}/auth/verify-otp";
        $ch = curl_init($verifyUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode(['email' => $email, 'otp' => $otp]),
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/json',
                'X-Localize: en',
            ],
        ]);
        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $decoded = json_decode($body, true);
        $token = $decoded['data']['token'] ?? null;
        echo sprintf("%-20s HTTP %d status=%s\n", 'auth/verify-otp', $code, ($decoded['status'] ?? false) ? 'ok' : 'fail');

        if ($token) {
            $authPaths = ['auth/me', 'profile', 'bookings', 'membership', 'wallet', 'notifications'];
            foreach ($authPaths as $path) {
                $url = "{$base}{$prefix}/{$path}";
                $ch = curl_init($url);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER => [
                        'Accept: application/json',
                        'X-Localize: en',
                        "Authorization: Bearer {$token}",
                    ],
                ]);
                $body = curl_exec($ch);
                $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                $decoded = json_decode($body, true);
                echo sprintf("%-20s HTTP %d status=%s\n", $path, $code, ($decoded['status'] ?? ($code < 400)) ? 'ok' : 'fail');
            }
        }
    }
}

echo "\nDone.\n";
