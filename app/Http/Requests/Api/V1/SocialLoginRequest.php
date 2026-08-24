<?php

namespace App\Http\Requests\Api\V1;

use App\Traits\Response\RequestValidationErrorResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SocialLoginRequest extends FormRequest
{
    use RequestValidationErrorResponse;

    public function rules(): array
    {
        return [
            'provider' => ['required', Rule::in(['google', 'apple'])],
            'email' => ['required', 'email', 'max:255'],
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'provider_id' => ['required', 'string', 'max:255'],
            'language' => ['nullable', 'string', 'max:10'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
