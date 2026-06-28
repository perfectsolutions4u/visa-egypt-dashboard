<?php

namespace App\Http\Requests\Dashboard\Visa;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProgramRequest extends FormRequest
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
            'slug' => 'Slug',
            'duration' => 'Duration',
            'starting_price' => 'Starting Price',
            'hero_image' => 'Hero Image',
            'is_active' => 'Active',
            'is_best_seller' => 'Best Seller',
            'sort_order' => 'Sort Order',
            'cities' => 'Cities',
            'highlights' => 'Highlights',
            'itinerary' => 'Itinerary',
            'inclusions' => 'Inclusions',
            'exclusions' => 'Exclusions',
        ];
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:125'],
            'slug' => [
                'required',
                'string',
                'max:125',
                Rule::unique('programs')->ignore($this->route('program')),
            ],
            'duration' => ['nullable', 'string', 'max:125'],
            'starting_price' => ['required', 'numeric', 'min:0'],
            'hero_image' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable'],
            'is_best_seller' => ['nullable'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'cities' => ['nullable', 'json'],
            'highlights' => ['nullable', 'json'],
            'itinerary' => ['nullable', 'json'],
            'inclusions' => ['nullable', 'json'],
            'exclusions' => ['nullable', 'json'],
        ];
    }

    public function getSanitized(): array
    {
        $data = $this->booleansFromCheckboxes($this->validated(), ['is_active', 'is_best_seller']);

        return $this->decodeJsonFields($data, [
            'cities',
            'highlights',
            'itinerary',
            'inclusions',
            'exclusions',
        ]);
    }
}
