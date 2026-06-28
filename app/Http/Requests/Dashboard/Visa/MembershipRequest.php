<?php

namespace App\Http\Requests\Dashboard\Visa;

use App\Enums\Visa\MembershipPlan;
use App\Models\Visa\MembershipTier;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MembershipRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function attributes(): array
    {
        return [
            'client_id' => 'Client',
            'plan_type' => 'Plan Type',
            'discount_percent' => 'Discount Percent',
            'points_balance' => 'Points Balance',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'status' => 'Status',
        ];
    }

    public function rules(): array
    {
        return [
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'plan_type' => ['required', Rule::in(MembershipPlan::allowedSlugs())],
            'discount_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'points_balance' => ['required', 'integer', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'string', Rule::in(['pending', 'active', 'expired', 'cancelled'])],
        ];
    }

    public function getSanitized(): array
    {
        $data = $this->validated();

        if (empty($data['discount_percent']) && ! empty($data['plan_type'])) {
            $tier = MembershipTier::query()->where('slug', $data['plan_type'])->first();
            $data['discount_percent'] = $tier?->discount_percent
                ?? MembershipPlan::tryFrom($data['plan_type'])?->discountPercent()
                ?? 0;
        }

        return $data;
    }
}
