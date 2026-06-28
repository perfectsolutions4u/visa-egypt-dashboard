<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\OfferResource;
use App\Models\Visa\MembershipTier;
use App\Models\Visa\Offer;
use App\Services\Visa\PoliciesContentService;
use App\Services\Visa\SupportContentService;
use App\Traits\Response\HasApiResponse;
use Illuminate\Http\Request;

class AppContentController extends Controller
{
    use HasApiResponse;

    private const ELIGIBLE_NATIONALITIES = [
        'united states', 'usa', 'us', 'united kingdom', 'uk', 'canada', 'australia',
        'germany', 'france', 'italy', 'spain', 'netherlands', 'belgium', 'japan',
        'south korea', 'new zealand', 'switzerland', 'sweden', 'norway', 'denmark',
        'egypt', 'egyptian', 'eu',
    ];

    public function visaOnArrival()
    {
        return $this->send([
            'title' => 'Visa On Arrival',
            'subtitle' => 'Get your visa easily when you arrive in Egypt.',
            'visa_fee_usd' => 30,
            'stay_days' => 30,
            'entry_type' => 'Single Entry Visa',
            'features' => [
                ['title' => 'No Pre-Approval Needed', 'description' => 'No documents submitted in advance.'],
                ['title' => 'Pay at the Airport', 'description' => 'Pay in cash (USD) at the visa counter.'],
                ['title' => 'Official & Secure', 'description' => 'Government authorized service.'],
                ['title' => 'Quick & Easy Process', 'description' => 'Fast, simple and hassle-free process.'],
            ],
            'nationalities' => ['US', 'UK', 'CA', 'AU', 'EU', 'More'],
            'required_documents' => [
                ['title' => 'Valid Passport', 'description' => 'Must be valid for at least 6 months.'],
                ['title' => 'Return Ticket', 'description' => 'Confirmed return or onward ticket.'],
                ['title' => 'Visa Fee', 'description' => 'Pay 30 USD at the airport.'],
            ],
            'steps' => [
                ['title' => 'Arrive in Egypt', 'description' => 'Land at any Egyptian international airport.'],
                ['title' => 'Meet Our Representative', 'description' => 'Look for Visa Egypt representative.'],
                ['title' => 'Visa Processing', 'description' => 'We assist you with the process.'],
                ['title' => 'Receive Your Visa', 'description' => 'Get your visa and enjoy your stay!'],
            ],
        ]);
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

    public function visaEligibility(Request $request)
    {
        $nationality = strtolower(trim((string) $request->query('nationality', '')));
        $eligible = $nationality === '' || collect(self::ELIGIBLE_NATIONALITIES)
            ->contains(fn ($n) => str_contains($nationality, $n) || str_contains($n, $nationality));

        return $this->send([
            'eligible' => $eligible,
            'nationality' => $request->query('nationality'),
            'visa_fee_usd' => 30,
            'stay_days' => 30,
            'entry_type' => 'Single Entry Visa',
            'message' => $eligible
                ? 'Great news! You can get Visa On Arrival when you arrive in Egypt.'
                : 'Please contact support to confirm eligibility for your nationality.',
        ]);
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
