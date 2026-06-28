<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\Visa\MembershipPlan;
use App\Traits\Response\RequestValidationErrorResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MembershipCheckoutRequest extends FormRequest
{
    use RequestValidationErrorResponse;

    public function rules(): array
    {
        return [
            'plan_type' => ['required', Rule::in(MembershipPlan::allowedSlugs())],
            'payment_method' => ['required', 'string', 'in:card,paypal,wallet'],
        ];
    }
}
