<?php

namespace App\Http\Requests\Dashboard\Visa;

use Illuminate\Foundation\Http\FormRequest;

class AppUpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $versionRule = ['string', 'max:20', 'regex:/^\d+(\.\d+){1,3}$/'];

        return [
            'is_active' => ['nullable', 'boolean'],
            'force_update' => ['nullable', 'boolean'],
            'latest_version' => array_merge(['required'], $versionRule),
            'min_version' => array_merge(['nullable'], $versionRule),
            'android_version' => array_merge(['nullable'], $versionRule),
            'android_build' => ['required', 'integer', 'min:1', 'max:999999'],
            'android_download_url' => ['nullable', 'url', 'max:500', 'regex:/^https:\/\//i'],
            'ios_version' => array_merge(['nullable'], $versionRule),
            'ios_build' => ['required', 'integer', 'min:1', 'max:999999'],
            'ios_download_url' => ['nullable', 'url', 'max:500', 'regex:/^https:\/\//i'],
            'message_ar' => ['nullable', 'string', 'max:1000'],
            'message_en' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'latest_version.regex' => 'Version must look like 1.0.2',
            'min_version.regex' => 'Version must look like 1.0.2',
            'android_version.regex' => 'Version must look like 1.0.2',
            'ios_version.regex' => 'Version must look like 1.0.2',
            'android_download_url.regex' => 'Android download URL must start with https://',
            'ios_download_url.regex' => 'iOS download URL must start with https://',
        ];
    }
}
