<?php

namespace App\Http\Requests\Dashboard\Visa;

use Illuminate\Foundation\Http\FormRequest;

class PoliciesContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'terms' => ['nullable', 'string'],
            'privacy' => ['nullable', 'string'],
            'about' => ['nullable', 'string'],
        ];
    }
}
