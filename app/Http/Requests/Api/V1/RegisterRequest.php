<?php

namespace App\Http\Requests\Api\V1;

use App\Traits\Response\RequestValidationErrorResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class RegisterRequest extends FormRequest
{
    use RequestValidationErrorResponse;

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'min:2'],
            'email' => ['required', 'email', 'unique:clients,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:100'],
            'whatsapp' => ['nullable', 'string', 'max:100'],
            'language' => ['nullable', 'string', 'max:10'],
            'nationality' => ['nullable', 'string', 'max:125'],
            'birthdate' => ['nullable', 'date', 'before:today', 'date_format:Y-m-d'],
        ];
    }

    public function getSanitized(): array
    {
        return array_merge($this->validated(), [
            'password' => Hash::make($this->get('password')),
            'language' => $this->get('language', 'en'),
        ]);
    }
}
