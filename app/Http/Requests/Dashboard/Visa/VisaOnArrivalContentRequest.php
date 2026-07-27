<?php

namespace App\Http\Requests\Dashboard\Visa;

use Illuminate\Foundation\Http\FormRequest;

class VisaOnArrivalContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        foreach (['features', 'required_documents', 'steps'] as $field) {
            $value = $this->input($field);

            if (is_string($value) && $value !== '') {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $this->merge([$field => $decoded]);
                }
                continue;
            }

            if (is_array($value)) {
                $this->merge([
                    $field => array_values(array_filter($value, function ($item) {
                        if (! is_array($item)) {
                            return false;
                        }

                        return trim((string) ($item['title'] ?? '')) !== ''
                            || trim((string) ($item['description'] ?? '')) !== '';
                    })),
                ]);
            }
        }
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'visa_fee_usd' => ['required', 'numeric', 'min:0'],
            'stay_days' => ['required', 'integer', 'min:1', 'max:365'],
            'entry_type' => ['required', 'string', 'max:125'],
            'eligible_message' => ['nullable', 'string', 'max:1000'],
            'ineligible_message' => ['nullable', 'string', 'max:1000'],
            'features' => ['nullable', 'array'],
            'features.*.title' => ['nullable', 'string', 'max:255'],
            'features.*.description' => ['nullable', 'string', 'max:1000'],
            'required_documents' => ['nullable', 'array'],
            'required_documents.*.title' => ['nullable', 'string', 'max:255'],
            'required_documents.*.description' => ['nullable', 'string', 'max:1000'],
            'steps' => ['nullable', 'array'],
            'steps.*.title' => ['nullable', 'string', 'max:255'],
            'steps.*.description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
