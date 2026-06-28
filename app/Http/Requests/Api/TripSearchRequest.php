<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class TripSearchRequest extends FormRequest
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
        $rules = [
            'trip_type' => 'required|in:one_way,round_trip,special_discount',
            'departure_city_id' => 'required|exists:cities,id',
            'arrival_city_id' => 'required|exists:cities,id|different:departure_city_id',
            'travel_date' => 'required|date|after_or_equal:today',
            'passengers' => 'required|integer|min:1|max:50',
        ];

        // Add conditional rules for round trips
        if ($this->input('trip_type') === 'round_trip') {
            $rules['return_date'] = 'required|date|after:travel_date';
        } else {
            $rules['return_date'] = 'nullable|date|after:travel_date';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'trip_type.required' => 'Trip type is required.',
            'trip_type.in' => 'Invalid trip type. Must be one_way, round_trip, or special_discount.',
            'departure_city_id.required' => 'Departure city is required.',
            'departure_city_id.exists' => 'Selected departure city does not exist.',
            'arrival_city_id.required' => 'Arrival city is required.',
            'arrival_city_id.exists' => 'Selected arrival city does not exist.',
            'arrival_city_id.different' => 'Departure and arrival cities must be different.',
            'travel_date.required' => 'Travel date is required.',
            'travel_date.date' => 'Invalid travel date format.',
            'travel_date.after_or_equal' => 'Travel date must be today or a future date.',
            'return_date.required' => 'Return date is required for round trips.',
            'return_date.date' => 'Invalid return date format.',
            'return_date.after' => 'Return date must be after travel date.',
            'passengers.required' => 'Number of passengers is required.',
            'passengers.integer' => 'Number of passengers must be a whole number.',
            'passengers.min' => 'Number of passengers must be at least 1.',
            'passengers.max' => 'Number of passengers cannot exceed 50.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'trip_type' => 'trip type',
            'departure_city_id' => 'departure city',
            'arrival_city_id' => 'arrival city',
            'travel_date' => 'travel date',
            'return_date' => 'return date',
            'passengers' => 'number of passengers',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'return_date' => $this->return_date ?: null,
        ]);
    }
}
