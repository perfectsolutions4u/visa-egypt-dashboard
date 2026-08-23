<?php

namespace App\Http\Requests\Dashboard\Visa;

use App\Models\Visa\AdditionalService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdditionalServiceRequest extends FormRequest
{
    use ParsesVisaFormFields;

    public function authorize(): bool
    {
        return true;
    }

    public function attributes(): array
    {
        return [
            'title' => 'Title',
            'description' => 'Description',
            'price' => 'Price',
            'currency' => 'Currency',
            'price_from' => 'Show "From" Before Price',
            'icon' => 'Icon',
            'accent_color' => 'Accent Color',
            'features' => 'Features',
            'sort_order' => 'Sort Order',
            'is_active' => 'Active',
        ];
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:125'],
            'description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:10'],
            'price_from' => ['nullable'],
            'icon' => ['required', Rule::in(array_keys(AdditionalService::iconOptions()))],
            'accent_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'features' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable'],
        ];
    }

    public function getSanitized(): array
    {
        $data = $this->booleansFromCheckboxes($this->validated(), [
            'price_from',
            'is_active',
        ]);
        $data['features'] = $this->parseFeatures($data['features'] ?? null);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        return $data;
    }
}
