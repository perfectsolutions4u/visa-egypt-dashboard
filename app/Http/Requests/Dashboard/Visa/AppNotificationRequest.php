<?php

namespace App\Http\Requests\Dashboard\Visa;

use Illuminate\Foundation\Http\FormRequest;

class AppNotificationRequest extends FormRequest
{
    use ParsesVisaFormFields;

    public function authorize(): bool
    {
        return true;
    }

    public function attributes(): array
    {
        return [
            'client_id' => 'Client',
            'broadcast_all' => 'Broadcast to All',
            'title' => 'Title',
            'body' => 'Body',
            'type' => 'Type',
            'target_screen' => 'Target Screen',
            'target_id' => 'Target ID',
        ];
    }

    public function rules(): array
    {
        return [
            'broadcast_all' => ['nullable'],
            'client_id' => ['nullable', 'required_without:broadcast_all', 'integer', 'exists:clients,id'],
            'title' => ['required', 'string', 'max:125'],
            'body' => ['nullable', 'string'],
            'type' => ['nullable', 'string', 'max:50'],
            'target_screen' => ['nullable', 'string', 'max:125'],
            'target_id' => ['nullable', 'string', 'max:125'],
        ];
    }

    public function getSanitized(): array
    {
        $data = $this->validated();
        unset($data['broadcast_all']);

        if ($this->filled('broadcast_all')) {
            $data['client_id'] = null;
        }

        return $data;
    }
}
