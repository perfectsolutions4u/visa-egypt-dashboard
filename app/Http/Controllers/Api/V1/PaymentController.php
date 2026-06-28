<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\Visa\VisaPaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CreatePaymentRequest;
use App\Http\Resources\Api\V1\VisaPaymentResource;
use App\Models\Visa\VisaPayment;
use App\Models\Visa\Voucher;
use App\Services\Visa\LoyaltyService;
use App\Services\Visa\PaymentDiscountService;
use App\Services\Visa\VisaVoucherPaymentService;
use App\Services\Visa\WalletService;
use App\Traits\Response\HasApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    use HasApiResponse;

    public function store(
        CreatePaymentRequest $request,
        PaymentDiscountService $discounts,
        LoyaltyService $loyalty,
        VisaVoucherPaymentService $voucherPayments,
        WalletService $wallets
    ) {
        $client = $request->user();
        $data = $request->validated();

        if (! empty($data['visa_booking_id'])) {
            $booking = $client->visaBookings()->findOrFail($data['visa_booking_id']);
            abort_if($booking->client_id !== $client->id, 403);
        }

        if (! empty($data['membership_id'])) {
            $client->memberships()->findOrFail($data['membership_id']);
        }

        $subtotal = (float) ($data['subtotal'] ?? $data['amount']);
        $serviceType = $data['service_type'] ?? null;

        $discountInput = [
            'discount_type' => $data['discount_type'] ?? null,
            'coupon_id' => $data['coupon_id'] ?? null,
            'coupon_code' => $data['coupon_code'] ?? null,
            'voucher_id' => $data['voucher_id'] ?? null,
            'points_to_use' => (int) ($data['points_to_use'] ?? 0),
            'wallet_amount_to_use' => isset($data['wallet_amount_to_use'])
                ? (float) $data['wallet_amount_to_use']
                : null,
        ];

        try {
            $preview = $discounts->applyForPayment($client, $subtotal, $discountInput, $serviceType);
        } catch (ValidationException $exception) {
            $message = collect($exception->errors())->flatten()->first() ?? 'Invalid discount selection.';

            return $this->send(null, $message, 422);
        }

        $payment = DB::transaction(function () use (
            $client,
            $data,
            $loyalty,
            $voucherPayments,
            $wallets,
            $preview,
            $subtotal
        ) {
            $method = $data['method'];
            $status = VisaPaymentStatus::PENDING;

            if ($preview['discount_type'] === 'wallet') {
                $method = $preview['total'] <= 0 ? 'wallet' : $method;
                if ($preview['total'] <= 0) {
                    $status = VisaPaymentStatus::COMPLETED;
                }
            }

            $payment = VisaPayment::create([
                'client_id' => $client->id,
                'visa_booking_id' => $data['visa_booking_id'] ?? null,
                'membership_id' => $data['membership_id'] ?? null,
                'subtotal' => $subtotal,
                'discount_type' => $preview['discount_type'],
                'coupon_id' => $preview['coupon_id'],
                'voucher_id' => $preview['voucher_id'],
                'loyalty_discount' => $preview['discount_amount'],
                'points_used' => $preview['points_to_use'],
                'points_earned' => $preview['points_to_earn'],
                'amount' => $preview['total'],
                'currency' => $data['currency'] ?? 'USD',
                'method' => $method,
                'status' => $status,
                'gateway_reference' => 'PAY-'.Str::upper(Str::random(12)),
            ]);

            if ($preview['discount_type'] === 'voucher' && $preview['voucher_id']) {
                $voucher = Voucher::query()->find($preview['voucher_id']);
                if ($voucher) {
                    $voucherPayments->markUsed($client, $voucher);
                }
            }

            if ($preview['discount_type'] === 'points' && $preview['points_to_use'] > 0) {
                $loyalty->redeemForPayment(
                    $client,
                    $payment,
                    (int) $preview['points_to_use'],
                    (float) $preview['discount_amount']
                );
            }

            if ($preview['discount_type'] === 'wallet' && $preview['wallet_amount_to_use'] > 0) {
                $wallets->debitForPayment(
                    $client,
                    $payment,
                    (float) $preview['wallet_amount_to_use']
                );
            }

            $loyalty->earnForPayment($client, $payment, (int) $preview['points_to_earn']);

            return $payment;
        });

        return $this->send(new VisaPaymentResource($payment), 'Payment initiated.', 201);
    }

    public function show(Request $request, VisaPayment $payment)
    {
        abort_if($payment->client_id !== $request->user()->id, 403);

        return $this->send(new VisaPaymentResource($payment));
    }
}
