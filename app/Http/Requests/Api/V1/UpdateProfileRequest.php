<?php

namespace App\Http\Requests\Api\V1;

use App\Traits\Response\RequestValidationErrorResponse;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    use RequestValidationErrorResponse;

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255', 'min:2'],
            'phone' => ['nullable', 'string', 'max:100'],
            'whatsapp' => ['nullable', 'string', 'max:100'],
            'nationality' => ['nullable', 'string', 'max:125'],
            'birthdate' => ['nullable', 'date', 'before:today', 'date_format:Y-m-d'],
            'image' => ['nullable', 'string'],
        ];
    }
}
