<?php

namespace App\Http\Requests\Dashboard\Visa;

use App\Enums\CouponType;
use App\Enums\Visa\OfferServiceTarget;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VoucherRequest extends FormRequest
{
    use ParsesVisaFormFields;

    public function authorize(): bool
    {
        return true;
    }

    public function attributes(): array
    {
        return [
            'code' => 'Voucher Code',
            'title' => 'Title',
            'description' => 'Description',
            'discount_type' => 'Discount Type',
            'discount_value' => 'Discount Value',
            'min_amount' => 'Minimum Order Amount',
            'service_target' => 'Service Target',
            'client_id' => 'Client',
            'max_uses' => 'Maximum Uses',
            'valid_from' => 'Valid From',
            'valid_to' => 'Valid To',
            'is_active' => 'Active',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('code')) {
            $this->merge([
                'code' => strtoupper(trim((string) $this->input('code'))),
            ]);
        }
    }

    public function rules(): array
    {
        $voucherId = $this->route('voucher')?->id;

        return [
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('visa_vouchers', 'code')->ignore($voucherId),
            ],
            'title' => ['required', 'string', 'max:125'],
            'description' => ['nullable', 'string'],
            'discount_type' => ['required', Rule::in(CouponType::all())],
            'discount_value' => ['required', 'numeric', 'min:0'],
            'min_amount' => ['nullable', 'numeric', 'min:0'],
            'service_target' => ['nullable', Rule::in(OfferServiceTarget::all())],
            'client_id' => ['nullable', 'exists:clients,id'],
            'max_uses' => ['nullable', 'integer', 'min:1'],
            'valid_from' => ['nullable', 'date'],
            'valid_to' => ['nullable', 'date', 'after_or_equal:valid_from'],
            'is_active' => ['nullable'],
        ];
    }

    public function getSanitized(): array
    {
        $data = $this->booleansFromCheckboxes($this->validated(), ['is_active']);

        if (empty($data['client_id'])) {
            $data['client_id'] = null;
        }

        if (empty($data['service_target'])) {
            $data['service_target'] = null;
        }

        if (empty($data['max_uses'])) {
            $data['max_uses'] = null;
        }

        if (empty($data['min_amount'])) {
            $data['min_amount'] = null;
        }

        return $data;
    }
}
