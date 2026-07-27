<?php

namespace App\Http\Requests\Dashboard\Visa;

use Illuminate\Foundation\Http\FormRequest;

class VisaEligibleNationalityRequest extends FormRequest
{
    use ParsesVisaFormFields;

    public function authorize(): bool
    {
        return true;
    }

    public function attributes(): array
    {
        return [
            'name' => 'Name',
            'code' => 'Code',
            'aliases' => 'Aliases',
            'is_active' => 'Active',
            'sort_order' => 'Sort Order',
        ];
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:125'],
            'code' => ['nullable', 'string', 'max:20'],
            'aliases' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function getSanitized(): array
    {
        $data = $this->validated();
        $data['is_active'] = $this->boolean('is_active');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['code'] = isset($data['code']) ? strtoupper(trim((string) $data['code'])) : null;
        $data['aliases'] = isset($data['aliases']) ? trim((string) $data['aliases']) : null;

        return $data;
    }
}
