<?php

namespace App\Http\Requests\Dashboard\Visa;

use Illuminate\Foundation\Http\FormRequest;

class VehicleRequest extends FormRequest
{
    use ParsesVisaFormFields;

    public function authorize(): bool
    {
        return true;
    }

    public function attributes(): array
    {
        return [
            'type' => 'Type',
            'name' => 'Name',
            'max_passengers' => 'Max Passengers',
            'max_bags' => 'Max Bags',
            'image' => 'Image',
            'is_active' => 'Active',
        ];
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:125'],
            'max_passengers' => ['required', 'integer', 'min:1', 'max:255'],
            'max_bags' => ['required', 'integer', 'min:0', 'max:255'],
            'image' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable'],
        ];
    }

    public function getSanitized(): array
    {
        return $this->booleansFromCheckboxes($this->validated(), ['is_active']);
    }
}
