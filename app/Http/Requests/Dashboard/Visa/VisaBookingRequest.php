<?php

namespace App\Http\Requests\Dashboard\Visa;

use App\Enums\Visa\VisaBookingStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VisaBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'notes' => ['nullable', 'string'],
            'status' => ['nullable', Rule::in(VisaBookingStatus::all())],
        ];
    }

    public function getSanitized(): array
    {
        return array_filter($this->validated(), fn ($v) => $v !== null);
    }
}
