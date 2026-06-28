<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\PreviewPaymentDiscountRequest;
use App\Services\Visa\PaymentDiscountService;
use App\Traits\Response\HasApiResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PaymentDiscountController extends Controller
{
    use HasApiResponse;

    public function options(Request $request, PaymentDiscountService $discounts)
    {
        $subtotal = (float) $request->query('subtotal', 0);
        $serviceType = $request->query('service_type');

        if ($subtotal <= 0) {
            return $this->send(null, 'Subtotal is required.', 422);
        }

        return $this->send(
            $discounts->options($request->user(), $subtotal, $serviceType)
        );
    }

    public function preview(PreviewPaymentDiscountRequest $request, PaymentDiscountService $discounts)
    {
        $data = $request->validated();

        try {
            $preview = $discounts->preview(
                $request->user(),
                (float) $data['subtotal'],
                $data,
                $data['service_type'] ?? null
            );
        } catch (ValidationException $exception) {
            $message = collect($exception->errors())->flatten()->first() ?? 'Invalid discount selection.';

            return $this->send(null, $message, 422);
        }

        return $this->send($preview);
    }
}
