<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\SettingKey;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Traits\Response\HasApiResponse;

class VisaSettingController extends Controller
{
    use HasApiResponse;

    public function index()
    {
        $keys = [
            SettingKey::VISA_FAQ,
            SettingKey::VISA_TERMS,
            SettingKey::VISA_PRIVACY,
            SettingKey::VISA_ABOUT,
            SettingKey::VISA_SUPPORT_EMAIL,
            SettingKey::VISA_SUPPORT_PHONE,
            SettingKey::VISA_SUPPORT_WHATSAPP,
        ];

        $settings = Setting::whereIn('option_key', array_map(fn ($k) => $k->value, $keys))->get()
            ->mapWithKeys(fn ($s) => [$s->option_key => $s->option_value[0] ?? $s->option_value]);

        return $this->send($settings);
    }
}
