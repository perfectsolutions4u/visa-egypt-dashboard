<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\Visa\VisaServiceType;
use App\Traits\Response\RequestValidationErrorResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateVisaBookingRequest extends FormRequest
{
    use RequestValidationErrorResponse;

    protected function prepareForValidation(): void
    {
        $specialRequests = $this->input('special_requests');

        if (is_string($specialRequests) && $specialRequests !== '') {
            $this->merge([
                'special_requests' => [['notes' => $specialRequests]],
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'service_type' => ['required', Rule::in(VisaServiceType::all())],
            'travel_date' => ['nullable', 'date', 'after_or_equal:today'],
            'travelers_count' => ['nullable', 'integer', 'min:1', 'max:20'],
            'nationality' => ['nullable', 'string', 'max:125'],
            'contact_email' => ['nullable', 'email'],
            'contact_whatsapp' => ['nullable', 'string', 'max:125'],
            'flight_number' => ['nullable', 'string', 'max:125'],
            'arrival_time' => ['nullable', 'date_format:H:i'],
            'meeting_point' => ['nullable', 'string', 'max:125'],
            'destination' => ['nullable', 'string', 'max:125'],
            'program_id' => ['nullable', 'exists:programs,id'],
            'service_package_id' => ['nullable', 'exists:service_packages,id'],
            'vehicle_id' => ['nullable', 'exists:vehicles,id'],
            'special_requests' => ['nullable', 'array'],
            'metadata' => ['nullable', 'array'],
            'total_amount' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
