<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\OfferResource;
use App\Models\Visa\MembershipTier;
use App\Models\Visa\Offer;
use App\Services\Visa\PoliciesContentService;
use App\Services\Visa\SupportContentService;
use App\Services\Visa\VisaOnArrivalContentService;
use App\Traits\Response\HasApiResponse;
use Illuminate\Http\Request;

class AppContentController extends Controller
{
    use HasApiResponse;

    public function visaOnArrival(VisaOnArrivalContentService $content)
    {
        return $this->send($content->getForApi());
    }

    public function arrivalJourney()
    {
        return $this->send([
            'title' => "We've got you covered",
            'subtitle' => "Here's what will happen when you arrive.",
            'steps' => [
                ['key' => 'arrival', 'title' => 'Arrival at Cairo International Airport', 'description' => 'After you land, proceed to the Arrivals Hall and passport control.'],
                ['key' => 'meet_rep', 'title' => 'Meet Your Representative', 'description' => 'Our representative will be waiting for you holding a Visa Egypt sign.'],
                ['key' => 'visa_assist', 'title' => 'Fast Track & Visa Assistance', 'description' => 'We will assist you with visa on arrival and fast track through procedures.'],
                ['key' => 'baggage', 'title' => 'Baggage Claim Assistance', 'description' => 'We will help you with your luggage and guide you to the exit.'],
                ['key' => 'transfer', 'title' => 'Transfer to Your Destination', 'description' => 'Enjoy a comfortable ride to your hotel or chosen destination.'],
            ],
            'important_notes' => [
                'Your representative will be waiting after passport control.',
                'Look for the Visa Egypt sign with your name.',
                'For any issues, contact our 24/7 support.',
            ],
        ]);
    }

    public function support(SupportContentService $supportContent)
    {
        return $this->send($supportContent->get());
    }

    public function policies(PoliciesContentService $policies)
    {
        return $this->send($policies->get());
    }

    public function visaEligibility(Request $request, VisaOnArrivalContentService $content)
    {
        return $this->send($content->checkEligibility($request->query('nationality')));
    }

    public function additionalServices()
    {
        $offers = Offer::query()
            ->where('is_active', true)
            ->latest()
            ->get();

        return $this->send(OfferResource::collection($offers));
    }

    public function membershipPlans()
    {
        $plans = MembershipTier::activeOrdered()
            ->map(fn (MembershipTier $tier) => $tier->toApiArray())
            ->values()
            ->all();

        if ($plans === []) {
            $plans = [
                ['tier' => 'silver', 'name' => 'Silver Member', 'discount_percent' => 10, 'price_usd' => 49],
                ['tier' => 'gold', 'name' => 'Gold Member', 'discount_percent' => 15, 'price_usd' => 99],
                ['tier' => 'platinum', 'name' => 'Platinum Member', 'discount_percent' => 25, 'price_usd' => 199],
            ];
        }

        return $this->send(['plans' => $plans]);
    }
}
