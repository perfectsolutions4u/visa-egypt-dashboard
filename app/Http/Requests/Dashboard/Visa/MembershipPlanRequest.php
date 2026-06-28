<?php

namespace App\Http\Requests\Dashboard\Visa;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MembershipPlanRequest extends FormRequest
{
    use ParsesVisaFormFields;

    public function authorize(): bool
    {
        return true;
    }

    public function attributes(): array
    {
        return [
            'slug' => 'Slug',
            'name' => 'Name',
            'tagline' => 'Tagline',
            'description' => 'Description',
            'features' => 'Features',
            'special_offer_text' => 'Special Offer Text',
            'special_offer_included' => 'Special Offer Included',
            'theme_color' => 'Theme Color',
            'is_featured' => 'Featured Plan',
            'discount_percent' => 'Discount Percent',
            'price_usd' => 'Price (USD)',
            'daily_points' => 'Daily Reward Points',
            'sort_order' => 'Sort Order',
            'is_active' => 'Active',
            'voucher_ids' => 'Vouchers',
            'coupon_ids' => 'Coupons',
        ];
    }

    public function rules(): array
    {
        $planId = $this->route('membership_plan')?->id;

        return [
            'slug' => [
                'required',
                'string',
                'max:50',
                'alpha_dash',
                Rule::unique('membership_plans', 'slug')->ignore($planId),
            ],
            'name' => ['required', 'string', 'max:125'],
            'tagline' => ['nullable', 'string', 'max:125'],
            'description' => ['nullable', 'string'],
            'features' => ['nullable', 'string'],
            'special_offer_text' => ['nullable', 'string', 'max:255'],
            'special_offer_included' => ['nullable'],
            'theme_color' => ['nullable', 'string', 'max:20'],
            'is_featured' => ['nullable'],
            'discount_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'price_usd' => ['required', 'numeric', 'min:0'],
            'daily_points' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable'],
            'voucher_ids' => ['nullable', 'array'],
            'voucher_ids.*' => ['integer', 'exists:visa_vouchers,id'],
            'coupon_ids' => ['nullable', 'array'],
            'coupon_ids.*' => ['integer', 'exists:coupons,id'],
        ];
    }

    public function getSanitized(): array
    {
        $data = $this->booleansFromCheckboxes($this->validated(), [
            'is_active',
            'special_offer_included',
            'is_featured',
        ]);
        $data['slug'] = strtolower($data['slug']);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['daily_points'] = (int) ($data['daily_points'] ?? 0);
        $data['theme_color'] = $data['theme_color'] ?: '#007BFF';
        $data['features'] = $this->parseFeatures($data['features'] ?? null);

        return $data;
    }
}
