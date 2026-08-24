<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\Visa\VisaPaymentMethod;
use App\Traits\Response\RequestValidationErrorResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WalletTopUpRequest extends FormRequest
{
    use RequestValidationErrorResponse;

    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:1', 'max:100000'],
            'method' => ['required', Rule::in([
                VisaPaymentMethod::CARD->value,
                VisaPaymentMethod::PAYPAL->value,
            ])],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
