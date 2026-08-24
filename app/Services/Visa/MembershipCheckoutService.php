<?php

namespace App\Services\Visa;

use App\Enums\Visa\VisaPaymentMethod;
use App\Enums\Visa\VisaPaymentStatus;
use App\Models\Client;
use App\Models\Visa\Membership;
use App\Models\Visa\MembershipTier;
use App\Models\Visa\VisaPayment;
use Illuminate\Support\Str;

class MembershipCheckoutService
{
    public function __construct(
        private readonly VisaPaymentGatewayService $gateway
    ) {
    }

    public function checkout(Client $client, string $planSlug, VisaPaymentMethod $method): array
    {
        $tier = MembershipTier::findActiveBySlug($planSlug);

        if (! $tier) {
            throw new \InvalidArgumentException('Selected membership plan is not available.');
        }

        $amount = (float) $tier->price_usd;

        Membership::where('client_id', $client->id)->where('status', 'active')->update(['status' => 'expired']);

        $membership = Membership::create([
            'client_id' => $client->id,
            'plan_type' => $tier->slug,
            'discount_percent' => $tier->discount_percent,
            'points_balance' => 0,
            'start_date' => now(),
            'end_date' => now()->addYear(),
            'status' => 'pending',
        ]);

        $payment = VisaPayment::create([
            'client_id' => $client->id,
            'membership_id' => $membership->id,
            'purpose' => 'membership',
            'subtotal' => $amount,
            'amount' => $amount,
            'currency' => 'USD',
            'method' => $method,
            'status' => VisaPaymentStatus::PENDING,
            'gateway_reference' => 'MBR-'.Str::upper(Str::random(10)),
        ]);

        $initiation = $this->gateway->initiate($payment);
        if ($initiation['auto_completed']) {
            $payment = $this->gateway->markCompleted($payment);
            $this->activateAfterPayment($payment);
            $membership = $membership->fresh();
        } else {
            $payment->update(['status' => $initiation['status']]);
            $payment->setAttribute('payment_url', $initiation['payment_url']);
        }

        return compact('membership', 'payment', 'amount');
    }

    public function activateAfterPayment(VisaPayment $payment): void
    {
        if ($payment->membership_id) {
            Membership::where('id', $payment->membership_id)->update(['status' => 'active']);
        }
    }
}
