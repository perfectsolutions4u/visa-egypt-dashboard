<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\Visa\VisaBookingStatus;
use App\Enums\Visa\VisaPaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CreatePaymentRequest;
use App\Http\Resources\Api\V1\VisaPaymentResource;
use App\Models\Visa\VisaBooking;
use App\Models\Visa\VisaPayment;
use App\Models\Visa\Voucher;
use App\Services\Visa\LoyaltyService;
use App\Services\Visa\MembershipCheckoutService;
use App\Services\Visa\PaymentDiscountService;
use App\Services\Visa\VisaPaymentGatewayService;
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
        WalletService $wallets,
        VisaPaymentGatewayService $gateway,
        MembershipCheckoutService $membershipCheckout
    ) {
        $client = $request->user();
        $data = $request->validated();
        $purpose = $data['purpose'] ?? (
            ! empty($data['membership_id']) ? 'membership'
                : (! empty($data['visa_booking_id']) ? 'booking' : 'booking')
        );

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
            $subtotal,
            $purpose,
            $gateway,
            $membershipCheckout
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
                'purpose' => $purpose,
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

            if ($status !== VisaPaymentStatus::COMPLETED) {
                $initiation = $gateway->initiate($payment);
                if ($initiation['auto_completed']) {
                    $payment = $gateway->markCompleted($payment);
                } else {
                    $payment->update(['status' => $initiation['status']]);
                    $payment->setAttribute('payment_url', $initiation['payment_url']);
                }
            }

            if ($payment->status === VisaPaymentStatus::COMPLETED) {
                $loyalty->earnForPayment($client, $payment, (int) $preview['points_to_earn']);
                $membershipCheckout->activateAfterPayment($payment);
                $this->confirmBookingIfPaid($payment);
                if ($purpose === 'wallet_topup') {
                    $wallets->creditTopUp($client, (float) $payment->amount, $payment);
                }
            }

            return $payment;
        });

        $resource = (new VisaPaymentResource($payment))->toArray($request);
        if ($payment->getAttribute('payment_url')) {
            $resource['payment_url'] = $payment->getAttribute('payment_url');
        } elseif ($payment->status !== VisaPaymentStatus::COMPLETED) {
            $initiation = $gateway->initiate($payment);
            $resource['payment_url'] = $initiation['payment_url'];
        }

        return $this->send($resource, 'Payment initiated.', 201);
    }

    public function show(Request $request, VisaPayment $payment)
    {
        abort_if($payment->client_id !== $request->user()->id, 403);

        return $this->send(new VisaPaymentResource($payment));
    }

    public function confirm(
        Request $request,
        VisaPayment $payment,
        VisaPaymentGatewayService $gateway,
        LoyaltyService $loyalty,
        MembershipCheckoutService $membershipCheckout,
        WalletService $wallets
    ) {
        abort_if($payment->client_id !== $request->user()->id, 403);

        if ($payment->status === VisaPaymentStatus::COMPLETED) {
            return $this->send(new VisaPaymentResource($payment), 'Payment already completed.');
        }

        $payment = DB::transaction(function () use ($payment, $gateway, $loyalty, $membershipCheckout, $wallets) {
            $payment = $gateway->markCompleted($payment);
            $loyalty->earnForPayment($payment->client, $payment, (int) ($payment->points_earned ?? 0));
            $membershipCheckout->activateAfterPayment($payment);
            $this->confirmBookingIfPaid($payment);
            if ($payment->purpose === 'wallet_topup') {
                $wallets->creditTopUp($payment->client, (float) $payment->amount, $payment);
            }

            return $payment;
        });

        return $this->send(new VisaPaymentResource($payment), 'Payment confirmed.');
    }

    private function confirmBookingIfPaid(VisaPayment $payment): void
    {
        if (! $payment->visa_booking_id) {
            return;
        }

        $booking = VisaBooking::query()->find($payment->visa_booking_id);
        if (! $booking) {
            return;
        }

        $status = $booking->status instanceof VisaBookingStatus
            ? $booking->status
            : VisaBookingStatus::tryFrom((string) $booking->status);

        if ($status === VisaBookingStatus::PENDING) {
            $booking->update(['status' => VisaBookingStatus::CONFIRMED]);
        }
    }
}
