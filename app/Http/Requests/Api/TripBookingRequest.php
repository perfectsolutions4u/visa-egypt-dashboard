<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Trip;

class TripBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'trip_id' => 'required|exists:trips,id',
            'client_id' => 'nullable|exists:clients,id',
            'adults' => 'required|integer|min:1|max:20',
            'children' => 'nullable|integer|min:0|max:10',
            'contact_name' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:20',
            'contact_email' => 'required|email|max:255',
            'special_requests' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'trip_id.required' => 'Trip ID is required.',
            'trip_id.exists' => 'Selected trip does not exist.',
            'client_id.exists' => 'Selected client does not exist.',
            'adults.required' => 'Number of adults is required.',
            'adults.integer' => 'Number of adults must be a whole number.',
            'adults.min' => 'Number of adults must be at least 1.',
            'adults.max' => 'Number of adults cannot exceed 20.',
            'children.integer' => 'Number of children must be a whole number.',
            'children.min' => 'Number of children cannot be negative.',
            'children.max' => 'Number of children cannot exceed 10.',
            'contact_name.required' => 'Contact name is required.',
            'contact_name.string' => 'Contact name must be a string.',
            'contact_name.max' => 'Contact name cannot exceed 255 characters.',
            'contact_phone.required' => 'Contact phone is required.',
            'contact_phone.string' => 'Contact phone must be a string.',
            'contact_phone.max' => 'Contact phone cannot exceed 20 characters.',
            'contact_email.required' => 'Contact email is required.',
            'contact_email.email' => 'Please enter a valid email address.',
            'contact_email.max' => 'Contact email cannot exceed 255 characters.',
            'special_requests.max' => 'Special requests cannot exceed 1000 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'trip_id' => 'trip',
            'client_id' => 'client',
            'adults' => 'number of adults',
            'children' => 'number of children',
            'contact_name' => 'contact name',
            'contact_phone' => 'contact phone',
            'contact_email' => 'contact email',
            'special_requests' => 'special requests',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Check if trip has enough available seats
            $trip = Trip::find($this->trip_id);
            if ($trip) {
                $totalPassengers = $this->adults + ($this->children ?? 0);
                if (!$trip->hasAvailableSeats($totalPassengers)) {
                    $validator->errors()->add('trip_id', 'Trip does not have enough available seats for the requested number of passengers.');
                }
            }
        });
    }
}
