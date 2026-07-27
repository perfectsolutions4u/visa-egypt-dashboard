<?php

namespace App\Services\Visa;

use App\Enums\SettingKey;
use App\Models\Setting;
use App\Services\Cache\AppCache;

class AppUpdateSettingsService
{
    public function defaults(): array
    {
        return [
            'is_active' => false,
            'force_update' => false,
            'latest_version' => '1.0.0',
            'min_version' => '1.0.0',
            'android_version' => '1.0.0',
            'android_build' => 1,
            'android_download_url' => '',
            'ios_version' => '1.0.0',
            'ios_build' => 1,
            'ios_download_url' => '',
            'message_ar' => 'يتوفر تحديث جديد للتطبيق',
            'message_en' => 'A new app update is available',
        ];
    }

    public function get(): array
    {
        $defaults = $this->defaults();
        $setting = Setting::where('option_key', SettingKey::APP_UPDATE->value)->first();
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
        $payload['is_active'] = filter_var($payload['is_active'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $payload['force_update'] = filter_var($payload['force_update'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $payload['android_build'] = (int) $payload['android_build'];
        $payload['ios_build'] = (int) $payload['ios_build'];
        $payload['android_version'] = $payload['android_version'] ?: $payload['latest_version'];
        $payload['ios_version'] = $payload['ios_version'] ?: $payload['latest_version'];

        Setting::updateOrCreate(
            ['option_key' => SettingKey::APP_UPDATE->value],
            ['option_value' => $payload]
        );

        AppCache::forgetBulk('settings_');
    }

    public function apiPayload(?string $platform = null, ?string $version = null, ?int $build = null): array
    {
        $settings = $this->get();

        $payload = [
            'latest_version' => $settings['latest_version'],
            'min_version' => $settings['min_version'],
            'force_update' => (bool) $settings['force_update'],
            'android' => [
                'version' => $settings['android_version'],
                'build' => (int) $settings['android_build'],
                'download_url' => $settings['android_download_url'],
            ],
            'ios' => [
                'version' => $settings['ios_version'],
                'build' => (int) $settings['ios_build'],
                'download_url' => $settings['ios_download_url'],
            ],
            'message' => [
                'ar' => $settings['message_ar'],
                'en' => $settings['message_en'],
            ],
            'is_active' => (bool) $settings['is_active'],
        ];

        if ($platform !== null || $version !== null || $build !== null) {
            $decision = $this->evaluateUpdate($settings, $platform, $version, $build);
            $payload = array_merge($payload, $decision);
        }

        return $payload;
    }

    public function evaluateUpdate(array $settings, ?string $platform, ?string $version, ?int $build): array
    {
        $platform = strtolower((string) $platform);
        $platformData = $platform === 'ios' ? [
            'version' => $settings['ios_version'],
            'build' => (int) $settings['ios_build'],
            'download_url' => $settings['ios_download_url'],
        ] : [
            'version' => $settings['android_version'],
            'build' => (int) $settings['android_build'],
            'download_url' => $settings['android_download_url'],
        ];

        if (! $settings['is_active']) {
            return [
                'update_required' => false,
                'force_update' => false,
                'download_url' => $platformData['download_url'],
                'platform_version' => $platformData['version'],
                'platform_build' => $platformData['build'],
            ];
        }

        $latestVersion = $platformData['version'] ?: $settings['latest_version'];
        $minVersion = $settings['min_version'] ?: $latestVersion;
        $currentVersion = $version ?: '0.0.0';

        $belowLatest = version_compare($currentVersion, $latestVersion, '<');
        $belowMin = version_compare($currentVersion, $minVersion, '<');
        $belowBuild = $build !== null && $build < $platformData['build'];

        $updateRequired = $belowLatest || $belowBuild;
        $forceUpdate = (bool) $settings['force_update'] || $belowMin;

        if (! $updateRequired) {
            $forceUpdate = false;
        }

        return [
            'update_required' => $updateRequired,
            'force_update' => $forceUpdate,
            'download_url' => $platformData['download_url'],
            'platform_version' => $platformData['version'],
            'platform_build' => $platformData['build'],
        ];
    }
}
