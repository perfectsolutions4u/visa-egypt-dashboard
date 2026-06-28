<?php

namespace App\Http\Requests\Dashboard\Visa;

use App\Enums\Visa\VisaServiceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServicePackageRequest extends FormRequest
{
    use ParsesVisaFormFields;

    public function authorize(): bool
    {
        return true;
    }

    public function attributes(): array
    {
        return [
            'service_type' => 'Service Type',
            'tier' => 'Tier',
            'name' => 'Name',
            'price' => 'Price',
            'features' => 'Features',
            'includes_visa' => 'Includes Visa',
            'is_popular' => 'Popular',
            'duration_hours' => 'Duration Hours',
            'is_active' => 'Active',
        ];
    }

    public function rules(): array
    {
        return [
            'service_type' => ['required', Rule::in(VisaServiceType::all())],
            'tier' => ['required', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:125'],
            'price' => ['required', 'numeric', 'min:0'],
            'features' => ['nullable', 'string'],
            'includes_visa' => ['nullable'],
            'is_popular' => ['nullable'],
            'duration_hours' => ['nullable', 'integer', 'min:0', 'max:255'],
            'is_active' => ['nullable'],
        ];
    }

    public function getSanitized(): array
    {
        $data = $this->booleansFromCheckboxes($this->validated(), [
            'includes_visa',
            'is_popular',
            'is_active',
        ]);
        $data['features'] = $this->parseFeatures($data['features'] ?? null);

        return $data;
    }
}
