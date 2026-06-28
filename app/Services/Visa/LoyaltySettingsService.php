<?php

namespace App\Services\Visa;

use App\Enums\SettingKey;
use App\Models\Setting;
use App\Services\Cache\AppCache;

class LoyaltySettingsService
{
    public function defaults(): array
    {
        return [
            'enabled' => true,
            'earn_points_per_usd' => 10,
            'redeem_points_per_usd' => 100,
            'min_points_redeem' => 100,
            'max_redeem_percent' => 50,
        ];
    }

    public function get(): array
    {
        $defaults = $this->defaults();
        $setting = Setting::where('option_key', SettingKey::VISA_LOYALTY->value)->first();
        $raw = $setting?->option_value;

        if (is_string($raw)) {
            $raw = json_decode($raw, true);
        }

        if (! is_array($raw)) {
            return $defaults;
        }

        return array_merge($defaults, array_intersect_key($raw, $defaults));
    }

    public function save(array $data): void
    {
        $payload = array_merge($this->defaults(), array_intersect_key($data, $this->defaults()));
        $payload['enabled'] = filter_var($payload['enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $payload['earn_points_per_usd'] = (int) $payload['earn_points_per_usd'];
        $payload['redeem_points_per_usd'] = (int) $payload['redeem_points_per_usd'];
        $payload['min_points_redeem'] = (int) $payload['min_points_redeem'];
        $payload['max_redeem_percent'] = (float) $payload['max_redeem_percent'];

        Setting::updateOrCreate(
            ['option_key' => SettingKey::VISA_LOYALTY->value],
            ['option_value' => $payload]
        );

        AppCache::forgetBulk('settings_');
    }
}
