<?php

namespace App\Services\Visa;

use App\Enums\SettingKey;
use App\Models\Setting;
use App\Services\Cache\AppCache;

class PoliciesContentService
{
    public function defaults(): array
    {
        return [
            'title' => 'Policies',
            'terms' => "Terms of Service\n\nBy using Visa Egypt services you agree to our booking and payment terms.",
            'privacy' => "Privacy Policy\n\nWe respect your privacy and protect your personal data.",
            'about' => "About Visa Egypt\n\nWe provide airport meet & assist, transfers, and travel services in Egypt.",
        ];
    }

    public function get(): array
    {
        $defaults = $this->defaults();

        return [
            'title' => $defaults['title'],
            'terms' => $this->text(SettingKey::VISA_TERMS) ?: $defaults['terms'],
            'privacy' => $this->text(SettingKey::VISA_PRIVACY) ?: $defaults['privacy'],
            'about' => $this->text(SettingKey::VISA_ABOUT) ?: $defaults['about'],
        ];
    }

    public function save(array $data): void
    {
        $this->storeText(SettingKey::VISA_TERMS, $data['terms'] ?? '');
        $this->storeText(SettingKey::VISA_PRIVACY, $data['privacy'] ?? '');
        $this->storeText(SettingKey::VISA_ABOUT, $data['about'] ?? '');

        AppCache::forgetBulk('settings_');
    }

    private function text(SettingKey $key): ?string
    {
        $setting = Setting::where('option_key', $key->value)->first();
        if (! $setting) {
            return null;
        }

        $value = $setting->option_value;
        if (is_array($value)) {
            return isset($value[0]) ? trim((string) $value[0]) : null;
        }

        return trim((string) $value);
    }

    private function storeText(SettingKey $key, string $value): void
    {
        Setting::updateOrCreate(
            ['option_key' => $key->value],
            ['option_value' => [$value]]
        );
    }
}
