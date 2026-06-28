<?php

namespace App\Http\Requests\Dashboard\Visa;

use App\Enums\Visa\StaffType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StaffRequest extends FormRequest
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
            'full_name' => 'Full Name',
            'phone' => 'Phone',
            'whatsapp' => 'WhatsApp',
            'languages' => 'Languages',
            'rating' => 'Rating',
            'license_number' => 'License Number',
            'photo' => 'Photo',
            'is_active' => 'Active',
            'login_email' => 'Login Email',
            'login_password' => 'Login Password',
        ];
    }

    public function rules(): array
    {
        $staffId = $this->route('staff')?->user_id;

        return [
            'type' => ['required', Rule::in(StaffType::all())],
            'full_name' => ['required', 'string', 'max:125'],
            'phone' => ['required', 'string', 'max:125'],
            'whatsapp' => ['nullable', 'string', 'max:125'],
            'languages' => ['nullable', 'string'],
            'rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'license_number' => ['nullable', 'string', 'max:125'],
            'photo' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable'],
            'login_email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($staffId),
            ],
            'login_password' => ['nullable', 'string', 'min:8', 'max:255'],
        ];
    }

    public function getSanitized(): array
    {
        $data = $this->booleansFromCheckboxes($this->validated(), ['is_active']);
        $data['languages'] = $this->parseLanguages($data['languages'] ?? null);

        return $data;
    }

    public function loginEmail(): ?string
    {
        $email = $this->input('login_email');

        return $email ? strtolower(trim((string) $email)) : null;
    }

    public function loginPassword(): ?string
    {
        $password = $this->input('login_password');

        return $password ? (string) $password : null;
    }
}
