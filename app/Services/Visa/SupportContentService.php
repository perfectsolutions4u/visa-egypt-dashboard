<?php

namespace App\Services\Visa;

use App\Enums\SettingKey;
use App\Models\Setting;
use App\Services\Cache\AppCache;

class SupportContentService
{
    public function defaults(): array
    {
        return [
            'title' => 'Support',
            'subtitle' => 'We are here to help you 24/7',
            'email' => 'support@visaegypt.com',
            'phone' => '+20 100 000 0000',
            'whatsapp' => '+20 100 000 0000',
            'faqs' => [
                [
                    'question' => 'How do I book a Meet & Assist service?',
                    'answer' => 'Go to Top Services > Meet & Assist, select your package, and complete the booking flow.',
                ],
                [
                    'question' => 'Can I pay on arrival?',
                    'answer' => 'Yes, cash on arrival is available for most services at Cairo Airport.',
                ],
                [
                    'question' => 'What is Visa Egypt Club?',
                    'answer' => 'Our membership program offering exclusive discounts and priority support on all services.',
                ],
                [
                    'question' => 'How do I track my booking?',
                    'answer' => 'Open My Bookings and tap your active booking to view live tracking updates.',
                ],
            ],
        ];
    }

    public function get(): array
    {
        $defaults = $this->defaults();

        return [
            'title' => $this->scalar(SettingKey::VISA_SUPPORT_TITLE) ?: $defaults['title'],
            'subtitle' => $this->scalar(SettingKey::VISA_SUPPORT_SUBTITLE) ?: $defaults['subtitle'],
            'email' => $this->scalar(SettingKey::VISA_SUPPORT_EMAIL) ?: $defaults['email'],
            'phone' => $this->scalar(SettingKey::VISA_SUPPORT_PHONE) ?: $defaults['phone'],
            'whatsapp' => $this->scalar(SettingKey::VISA_SUPPORT_WHATSAPP) ?: $defaults['whatsapp'],
            'faqs' => $this->faqs() ?: $defaults['faqs'],
        ];
    }

    public function save(array $data): void
    {
        $this->storeScalar(SettingKey::VISA_SUPPORT_TITLE, $data['title'] ?? '');
        $this->storeScalar(SettingKey::VISA_SUPPORT_SUBTITLE, $data['subtitle'] ?? '');
        $this->storeScalar(SettingKey::VISA_SUPPORT_EMAIL, $data['email'] ?? '');
        $this->storeScalar(SettingKey::VISA_SUPPORT_PHONE, $data['phone'] ?? '');
        $this->storeScalar(SettingKey::VISA_SUPPORT_WHATSAPP, $data['whatsapp'] ?? '');

        Setting::updateOrCreate(
            ['option_key' => SettingKey::VISA_FAQ->value],
            ['option_value' => array_values($data['faqs'] ?? [])]
        );

        AppCache::forgetBulk('settings_');
    }

    public function faqs(): array
    {
        $setting = Setting::where('option_key', SettingKey::VISA_FAQ->value)->first();
        if (! $setting) {
            return [];
        }

        $raw = $setting->option_value;
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            $raw = is_array($decoded) ? $decoded : [];
        }

        if (is_array($raw) && isset($raw[0]) && is_string($raw[0])) {
            $decoded = json_decode($raw[0], true);
            $raw = is_array($decoded) ? $decoded : [];
        }

        if (! is_array($raw)) {
            return [];
        }

        return collect($raw)
            ->map(function ($item) {
                if (! is_array($item)) {
                    return null;
                }

                $question = trim((string) ($item['question'] ?? ''));
                $answer = trim((string) ($item['answer'] ?? ''));

                if ($question === '' || $answer === '') {
                    return null;
                }

                return ['question' => $question, 'answer' => $answer];
            })
            ->filter()
            ->values()
            ->all();
    }

    private function scalar(SettingKey $key): ?string
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

    private function storeScalar(SettingKey $key, string $value): void
    {
        Setting::updateOrCreate(
            ['option_key' => $key->value],
            ['option_value' => [$value]]
        );
    }

    public function seedDefaults(): void
    {
        $defaults = $this->defaults();

        foreach ([
            SettingKey::VISA_SUPPORT_TITLE->value => $defaults['title'],
            SettingKey::VISA_SUPPORT_SUBTITLE->value => $defaults['subtitle'],
            SettingKey::VISA_SUPPORT_EMAIL->value => $defaults['email'],
            SettingKey::VISA_SUPPORT_PHONE->value => $defaults['phone'],
            SettingKey::VISA_SUPPORT_WHATSAPP->value => $defaults['whatsapp'],
        ] as $key => $value) {
            Setting::firstOrCreate(
                ['option_key' => $key],
                ['option_value' => [$value]]
            );
        }

        Setting::firstOrCreate(
            ['option_key' => SettingKey::VISA_FAQ->value],
            ['option_value' => $defaults['faqs']]
        );
    }
}
