<?php

namespace App\Http\Requests\Api\V1;

use App\Traits\Response\RequestValidationErrorResponse;
use Illuminate\Foundation\Http\FormRequest;

class ResendOtpRequest extends FormRequest
{
    use RequestValidationErrorResponse;

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'exists:clients,email,deleted_at,NULL'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
