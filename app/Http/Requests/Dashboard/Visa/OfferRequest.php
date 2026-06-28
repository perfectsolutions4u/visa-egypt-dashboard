<?php

namespace App\Http\Requests\Dashboard\Visa;

use App\Enums\Visa\MembershipPlan;
use App\Enums\Visa\OfferServiceTarget;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OfferRequest extends FormRequest
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
            'service_target' => 'Service Target',
            'discount_percent' => 'Discount Percent',
            'membership_level' => 'Membership Level',
            'active_from' => 'Active From',
            'active_to' => 'Active To',
            'is_active' => 'Active',
        ];
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:125'],
            'description' => ['nullable', 'string'],
            'service_target' => ['required', Rule::in(OfferServiceTarget::all())],
            'discount_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'membership_level' => ['nullable', Rule::in(MembershipPlan::allowedSlugs())],
            'active_from' => ['nullable', 'date'],
            'active_to' => ['nullable', 'date', 'after_or_equal:active_from'],
            'is_active' => ['nullable'],
        ];
    }

    public function getSanitized(): array
    {
        return $this->booleansFromCheckboxes($this->validated(), ['is_active']);
    }
}
