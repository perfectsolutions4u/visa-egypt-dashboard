<?php

namespace App\Http\Requests\Dashboard\Visa;

use Illuminate\Foundation\Http\FormRequest;

class LoyaltySettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'enabled' => ['nullable', 'boolean'],
            'earn_points_per_usd' => ['required', 'integer', 'min:0', 'max:10000'],
            'redeem_points_per_usd' => ['required', 'integer', 'min:1', 'max:100000'],
            'min_points_redeem' => ['required', 'integer', 'min:0', 'max:100000'],
            'max_redeem_percent' => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }
}
