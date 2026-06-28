<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class PreviewLoyaltyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subtotal' => ['required', 'numeric', 'min:0.01'],
            'points_to_use' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
