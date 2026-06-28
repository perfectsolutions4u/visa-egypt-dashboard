<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PreviewPaymentDiscountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subtotal' => ['required', 'numeric', 'min:0.01'],
            'service_type' => ['nullable', 'string', 'max:50'],
            'discount_type' => ['nullable', Rule::in(['coupon', 'voucher', 'points', 'wallet'])],
            'coupon_id' => ['nullable', 'integer', 'exists:coupons,id'],
            'coupon_code' => ['nullable', 'string', 'max:50'],
            'voucher_id' => ['nullable', 'integer', 'exists:visa_vouchers,id'],
            'points_to_use' => ['nullable', 'integer', 'min:0'],
            'wallet_amount_to_use' => ['nullable', 'numeric', 'min:0.01'],
        ];
    }
}
