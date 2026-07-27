<?php

namespace App\Services\Visa;

use App\Enums\SettingKey;
use App\Models\Setting;
use App\Models\Visa\VisaEligibleNationality;
use App\Services\Cache\AppCache;

class VisaOnArrivalContentService
{
    public function defaults(): array
    {
        return [
            'title' => 'Visa On Arrival',
            'subtitle' => 'Get your visa easily when you arrive in Egypt.',
            'visa_fee_usd' => 30,
            'stay_days' => 30,
            'entry_type' => 'Single Entry Visa',
            'eligible_message' => 'Great news! You can get Visa On Arrival when you arrive in Egypt.',
            'ineligible_message' => 'Please contact support to confirm eligibility for your nationality.',
            'features' => [
                ['title' => 'No Pre-Approval Needed', 'description' => 'No documents submitted in advance.'],
                ['title' => 'Pay at the Airport', 'description' => 'Pay in cash (USD) at the visa counter.'],
                ['title' => 'Official & Secure', 'description' => 'Government authorized service.'],
                ['title' => 'Quick & Easy Process', 'description' => 'Fast, simple and hassle-free process.'],
            ],
            'required_documents' => [
                ['title' => 'Valid Passport', 'description' => 'Must be valid for at least 6 months.'],
                ['title' => 'Return Ticket', 'description' => 'Confirmed return or onward ticket.'],
                ['title' => 'Visa Fee', 'description' => 'Pay 30 USD at the airport.'],
            ],
            'steps' => [
                ['title' => 'Arrive in Egypt', 'description' => 'Land at any Egyptian international airport.'],
                ['title' => 'Meet Our Representative', 'description' => 'Look for Visa Egypt representative.'],
                ['title' => 'Visa Processing', 'description' => 'We assist you with the process.'],
                ['title' => 'Receive Your Visa', 'description' => 'Get your visa and enjoy your stay!'],
            ],
        ];
    }

    public function get(): array
    {
        $defaults = $this->defaults();
        $stored = $this->stored();

        return [
            'title' => (string) ($stored['title'] ?? $defaults['title']),
            'subtitle' => (string) ($stored['subtitle'] ?? $defaults['subtitle']),
            'visa_fee_usd' => (float) ($stored['visa_fee_usd'] ?? $defaults['visa_fee_usd']),
            'stay_days' => (int) ($stored['stay_days'] ?? $defaults['stay_days']),
            'entry_type' => (string) ($stored['entry_type'] ?? $defaults['entry_type']),
            'eligible_message' => (string) ($stored['eligible_message'] ?? $defaults['eligible_message']),
            'ineligible_message' => (string) ($stored['ineligible_message'] ?? $defaults['ineligible_message']),
            'features' => $this->normalizeItems($stored['features'] ?? $defaults['features']),
            'required_documents' => $this->normalizeItems($stored['required_documents'] ?? $defaults['required_documents']),
            'steps' => $this->normalizeItems($stored['steps'] ?? $defaults['steps']),
        ];
    }

    public function getForApi(): array
    {
        $content = $this->get();
        $nationalities = VisaEligibleNationality::query()
            ->active()
            ->ordered()
            ->get();

        $content['nationalities'] = $nationalities
            ->map(fn (VisaEligibleNationality $item) => $item->code ?: $item->name)
            ->values()
            ->all();

        $content['eligible_nationalities'] = $nationalities
            ->map(fn (VisaEligibleNationality $item) => $item->toApiArray())
            ->values()
            ->all();

        return $content;
    }

    public function checkEligibility(?string $nationality): array
    {
        $content = $this->get();
        $needle = strtolower(trim((string) $nationality));
        $rows = VisaEligibleNationality::query()->active()->ordered()->get();

        $eligible = $needle !== '' && $rows->contains(function (VisaEligibleNationality $row) use ($needle) {
            foreach ($row->matchTokens() as $token) {
                if ($token !== '' && (str_contains($needle, $token) || str_contains($token, $needle))) {
                    return true;
                }
            }

            return false;
        });

        if ($needle === '' && $rows->isEmpty()) {
            $eligible = false;
        }

        return [
            'eligible' => $eligible,
            'nationality' => $nationality,
            'visa_fee_usd' => $content['visa_fee_usd'],
            'stay_days' => $content['stay_days'],
            'entry_type' => $content['entry_type'],
            'message' => $eligible
                ? $content['eligible_message']
                : $content['ineligible_message'],
            'eligible_nationalities' => $rows
                ->map(fn (VisaEligibleNationality $item) => $item->toApiArray())
                ->values()
                ->all(),
        ];
    }

    public function save(array $data): void
    {
        $payload = [
            'title' => trim((string) ($data['title'] ?? '')),
            'subtitle' => trim((string) ($data['subtitle'] ?? '')),
            'visa_fee_usd' => (float) ($data['visa_fee_usd'] ?? 0),
            'stay_days' => (int) ($data['stay_days'] ?? 0),
            'entry_type' => trim((string) ($data['entry_type'] ?? '')),
            'eligible_message' => trim((string) ($data['eligible_message'] ?? '')),
            'ineligible_message' => trim((string) ($data['ineligible_message'] ?? '')),
            'features' => $this->normalizeItems($data['features'] ?? []),
            'required_documents' => $this->normalizeItems($data['required_documents'] ?? []),
            'steps' => $this->normalizeItems($data['steps'] ?? []),
        ];

        Setting::updateOrCreate(
            ['option_key' => SettingKey::VISA_ON_ARRIVAL->value],
            ['option_value' => [$payload]]
        );

        AppCache::forgetBulk('settings_');
    }

    public function seedDefaultNationalitiesIfEmpty(): void
    {
        if (VisaEligibleNationality::query()->exists()) {
            return;
        }

        $defaults = [
            ['name' => 'United States', 'code' => 'US', 'aliases' => 'usa,us,american', 'sort_order' => 1],
            ['name' => 'United Kingdom', 'code' => 'UK', 'aliases' => 'britain,british,england,gb', 'sort_order' => 2],
            ['name' => 'Canada', 'code' => 'CA', 'aliases' => 'canadian', 'sort_order' => 3],
            ['name' => 'Australia', 'code' => 'AU', 'aliases' => 'australian', 'sort_order' => 4],
            ['name' => 'Germany', 'code' => 'DE', 'aliases' => 'german', 'sort_order' => 5],
            ['name' => 'France', 'code' => 'FR', 'aliases' => 'french', 'sort_order' => 6],
            ['name' => 'Italy', 'code' => 'IT', 'aliases' => 'italian', 'sort_order' => 7],
            ['name' => 'Spain', 'code' => 'ES', 'aliases' => 'spanish', 'sort_order' => 8],
            ['name' => 'Netherlands', 'code' => 'NL', 'aliases' => 'dutch', 'sort_order' => 9],
            ['name' => 'Belgium', 'code' => 'BE', 'aliases' => 'belgian', 'sort_order' => 10],
            ['name' => 'Japan', 'code' => 'JP', 'aliases' => 'japanese', 'sort_order' => 11],
            ['name' => 'South Korea', 'code' => 'KR', 'aliases' => 'korea,korean', 'sort_order' => 12],
            ['name' => 'New Zealand', 'code' => 'NZ', 'aliases' => 'kiwi', 'sort_order' => 13],
            ['name' => 'Switzerland', 'code' => 'CH', 'aliases' => 'swiss', 'sort_order' => 14],
            ['name' => 'Sweden', 'code' => 'SE', 'aliases' => 'swedish', 'sort_order' => 15],
            ['name' => 'Norway', 'code' => 'NO', 'aliases' => 'norwegian', 'sort_order' => 16],
            ['name' => 'Denmark', 'code' => 'DK', 'aliases' => 'danish', 'sort_order' => 17],
            ['name' => 'Egypt', 'code' => 'EG', 'aliases' => 'egyptian', 'sort_order' => 18],
            ['name' => 'European Union', 'code' => 'EU', 'aliases' => 'europe,eu', 'sort_order' => 19],
        ];

        foreach ($defaults as $row) {
            VisaEligibleNationality::create(array_merge($row, ['is_active' => true]));
        }
    }

    private function stored(): array
    {
        $setting = Setting::where('option_key', SettingKey::VISA_ON_ARRIVAL->value)->first();
        if (! $setting) {
            return [];
        }

        $value = $setting->option_value;
        if (is_array($value) && isset($value[0]) && is_array($value[0])) {
            return $value[0];
        }

        if (is_array($value)) {
            return $value;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);

            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    private function normalizeItems(mixed $items): array
    {
        if (is_string($items)) {
            $decoded = json_decode($items, true);
            $items = is_array($decoded) ? $decoded : [];
        }

        if (! is_array($items)) {
            return [];
        }

        return collect($items)
            ->map(function ($item) {
                if (is_string($item)) {
                    return ['title' => trim($item), 'description' => ''];
                }

                if (! is_array($item)) {
                    return null;
                }

                $title = trim((string) ($item['title'] ?? ''));
                $description = trim((string) ($item['description'] ?? ''));

                if ($title === '' && $description === '') {
                    return null;
                }

                return [
                    'title' => $title,
                    'description' => $description,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }
}
