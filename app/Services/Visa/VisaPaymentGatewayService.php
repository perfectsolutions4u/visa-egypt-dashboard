<?php

namespace App\Services\Visa;

use App\Enums\Visa\VisaPaymentMethod;
use App\Enums\Visa\VisaPaymentStatus;
use App\Models\Visa\VisaPayment;
use Illuminate\Support\Str;

class VisaPaymentGatewayService
{
    public function driver(): string
    {
        return (string) config('visa_payment.driver', 'sandbox');
    }

    /**
     * @return array{status: VisaPaymentStatus, payment_url: ?string, auto_completed: bool}
     */
    public function initiate(VisaPayment $payment): array
    {
        $driver = $this->driver();
        $amount = (float) $payment->amount;
        $method = $payment->method instanceof VisaPaymentMethod
            ? $payment->method->value
            : (string) $payment->method;

        if ($amount <= 0 || $method === VisaPaymentMethod::WALLET->value) {
            return [
                'status' => VisaPaymentStatus::COMPLETED,
                'payment_url' => null,
                'auto_completed' => true,
            ];
        }

        if ($method === VisaPaymentMethod::CASH->value) {
            return [
                'status' => VisaPaymentStatus::PENDING,
                'payment_url' => null,
                'auto_completed' => false,
            ];
        }

        if ($driver === 'sandbox') {
            return [
                'status' => VisaPaymentStatus::COMPLETED,
                'payment_url' => null,
                'auto_completed' => true,
            ];
        }

        $gatewayUrl = config('visa_payment.gateway_url');
        if ($driver === 'gateway' && is_string($gatewayUrl) && $gatewayUrl !== '') {
            $url = rtrim($gatewayUrl, '/').'?ref='.urlencode((string) $payment->gateway_reference)
                .'&amount='.urlencode((string) $payment->amount)
                .'&currency='.urlencode((string) $payment->currency);

            return [
                'status' => VisaPaymentStatus::PENDING,
                'payment_url' => $url,
                'auto_completed' => false,
            ];
        }

        return [
            'status' => VisaPaymentStatus::PENDING,
            'payment_url' => null,
            'auto_completed' => false,
        ];
    }

    public function markCompleted(VisaPayment $payment, ?string $gatewayReference = null): VisaPayment
    {
        $payment->update([
            'status' => VisaPaymentStatus::COMPLETED,
            'gateway_reference' => $gatewayReference
                ?: ($payment->gateway_reference ?: 'PAY-'.Str::upper(Str::random(12))),
        ]);

        return $payment->fresh();
    }
}
