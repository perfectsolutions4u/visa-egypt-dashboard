<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\Visa\VisaPaymentMethod;
use App\Traits\Response\RequestValidationErrorResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreatePaymentRequest extends FormRequest
{
    use RequestValidationErrorResponse;

    public function rules(): array
    {
        return [
            'visa_booking_id' => ['nullable', 'exists:visa_bookings,id'],
            'membership_id' => ['nullable', 'exists:memberships,id'],
            'subtotal' => ['nullable', 'numeric', 'min:0.01'],
            'amount' => ['required', 'numeric', 'min:0'],
            'service_type' => ['nullable', 'string', 'max:50'],
            'discount_type' => ['nullable', \Illuminate\Validation\Rule::in(['coupon', 'voucher', 'points', 'wallet'])],
            'coupon_id' => ['nullable', 'integer', 'exists:coupons,id'],
            'coupon_code' => ['nullable', 'string', 'max:50'],
            'voucher_id' => ['nullable', 'integer', 'exists:visa_vouchers,id'],
            'points_to_use' => ['nullable', 'integer', 'min:0'],
            'wallet_amount_to_use' => ['nullable', 'numeric', 'min:0.01'],
            'method' => ['required', Rule::in(VisaPaymentMethod::all())],
            'currency' => ['nullable', 'string', 'max:10'],
        ];
    }
}
