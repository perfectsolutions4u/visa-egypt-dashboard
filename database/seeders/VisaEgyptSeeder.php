<?php

namespace Database\Seeders;

use App\Enums\Visa\StaffType;
use App\Enums\Visa\VisaBookingStatus;
use App\Enums\Visa\VisaServiceType;
use App\Models\Client;
use App\Models\Visa\AdditionalService;
use App\Models\Visa\AppNotification;
use App\Models\Visa\MembershipTier;
use App\Models\Visa\Program;
use App\Models\Visa\ServicePackage;
use App\Models\Visa\Staff;
use App\Models\Visa\TrackingEvent;
use App\Models\Visa\Vehicle;
use App\Models\Visa\VisaBooking;
use App\Models\Visa\VisaBookingAssignment;
use App\Services\Visa\BookingRefGenerator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class VisaEgyptSeeder extends Seeder
{
    public function run(): void
    {
        app(\App\Services\Visa\SupportContentService::class)->seedDefaults();

        $this->seedMembershipPlans();

        Program::query()->update(['is_active' => false]);

        $programs = [
            [
                'name' => 'Cairo Day Tour',
                'duration' => '1 Day',
                'cities' => ['Cairo'],
                'highlights' => ['Pyramids', 'GEM', 'Lunch'],
                'itinerary' => [
                    ['day' => 1, 'title' => 'Pyramids & GEM', 'description' => 'Giza pyramids, Sphinx, and Grand Egyptian Museum.'],
                ],
                'inclusions' => ['Hotel pickup', 'Lunch', 'Professional Tour Guide', 'Entrance fees'],
                'exclusions' => ['Personal expenses', 'Tipping'],
                'starting_price' => 89,
                'best' => false,
            ],
            [
                'name' => 'Cairo Highlights',
                'duration' => '2 Days',
                'cities' => ['Cairo'],
                'highlights' => ['Pyramids', 'GEM', 'Old Cairo', 'Khan El Khalili'],
                'itinerary' => [
                    ['day' => 1, 'title' => 'Pyramids & Museum'],
                    ['day' => 2, 'title' => 'Old Cairo & Bazaar'],
                ],
                'inclusions' => ['Hotel', 'Daily Breakfast', 'Guide', 'Transportation'],
                'exclusions' => ['International Flights', 'Personal Expenses'],
                'starting_price' => 189,
                'best' => false,
            ],
            [
                'name' => 'Cairo & Alexandria',
                'duration' => '3 Days',
                'cities' => ['Cairo', 'Alexandria'],
                'highlights' => ['Cairo', 'Alexandria', 'Citadel'],
                'itinerary' => [
                    ['day' => 1, 'title' => 'Arrival Cairo'],
                    ['day' => 2, 'title' => 'Alexandria Day Tour'],
                    ['day' => 3, 'title' => 'Citadel & Departure'],
                ],
                'inclusions' => ['Hotel', 'Daily Breakfast', 'Guide', 'Transportation'],
                'exclusions' => ['International Flights', 'Tipping'],
                'starting_price' => 289,
                'best' => false,
            ],
            [
                'name' => 'Cairo & Luxor',
                'duration' => '4 Days',
                'cities' => ['Cairo', 'Luxor'],
                'highlights' => ['Cairo', 'Luxor', 'Internal Flight'],
                'itinerary' => [
                    ['day' => 1, 'title' => 'Arrival Cairo'],
                    ['day' => 2, 'title' => 'Pyramids & GEM'],
                    ['day' => 3, 'title' => 'Flight to Luxor'],
                    ['day' => 4, 'title' => 'Temples & Departure'],
                ],
                'inclusions' => ['Hotel Accommodation', 'Daily Breakfast', 'Domestic Flights', 'Professional Tour Guide'],
                'exclusions' => ['International Flights', 'Optional Activities'],
                'starting_price' => 429,
                'best' => false,
            ],
            [
                'name' => 'Egypt Discovery',
                'duration' => '5 Days / 4 Nights',
                'cities' => ['Cairo', 'Luxor', 'Aswan'],
                'highlights' => ['Pyramids of Giza', 'Grand Egyptian Museum', 'Karnak Temple', 'Luxor Temple', 'Felucca Experience', 'Nubian Village'],
                'itinerary' => [
                    ['day' => 1, 'title' => 'Arrival Cairo'],
                    ['day' => 2, 'title' => 'Pyramids & GEM'],
                    ['day' => 3, 'title' => 'Flight to Luxor'],
                    ['day' => 4, 'title' => 'Felucca & Nubian Village'],
                    ['day' => 5, 'title' => 'Departure'],
                ],
                'inclusions' => ['Airport Meet & Assist', 'Hotel Accommodation', 'Daily Breakfast', 'Professional Tour Guide', 'Transportation', 'Domestic Flights'],
                'exclusions' => ['International Flights', 'Personal Expenses', 'Optional Activities', 'Tipping'],
                'starting_price' => 595,
                'best' => true,
            ],
            [
                'name' => 'Nile Cruise Experience',
                'duration' => '5 Days',
                'cities' => ['Luxor', 'Aswan'],
                'highlights' => ['Nile Cruise', 'Luxor', 'Aswan'],
                'itinerary' => [
                    ['day' => 1, 'title' => 'Embark Luxor'],
                    ['day' => 2, 'title' => 'West Bank'],
                    ['day' => 3, 'title' => 'Edfu & Kom Ombo'],
                    ['day' => 4, 'title' => 'Aswan Highlights'],
                    ['day' => 5, 'title' => 'Disembark'],
                ],
                'inclusions' => ['Nile Cruise', 'Full board', 'Guide', 'Temple visits'],
                'exclusions' => ['International Flights', 'Personal Expenses'],
                'starting_price' => 649,
                'best' => false,
            ],
            [
                'name' => 'Egypt Classic',
                'duration' => '6 Days',
                'cities' => ['Cairo', 'Luxor', 'Aswan'],
                'highlights' => ['Cairo', 'Luxor', 'Aswan', 'Internal Flights'],
                'itinerary' => [
                    ['day' => 1, 'title' => 'Arrival Cairo'],
                    ['day' => 2, 'title' => 'Pyramids & Museum'],
                    ['day' => 3, 'title' => 'Flight to Luxor'],
                    ['day' => 4, 'title' => 'Luxor Temples'],
                    ['day' => 5, 'title' => 'Aswan'],
                    ['day' => 6, 'title' => 'Departure'],
                ],
                'inclusions' => ['Hotels', 'Breakfast', 'Domestic Flights', 'Guide'],
                'exclusions' => ['International Flights', 'Tipping'],
                'starting_price' => 749,
                'best' => false,
            ],
            [
                'name' => 'Egypt Premium',
                'duration' => '7 Days',
                'cities' => ['Cairo', 'Luxor', 'Aswan'],
                'highlights' => ['Premium Hotels', 'Private Transfers', 'Dedicated Support'],
                'itinerary' => [
                    ['day' => 1, 'title' => 'VIP Arrival'],
                    ['day' => 2, 'title' => 'Private Cairo Tour'],
                    ['day' => 3, 'title' => 'Museum & Old Cairo'],
                    ['day' => 4, 'title' => 'Luxor Private Guide'],
                    ['day' => 5, 'title' => 'West Bank'],
                    ['day' => 6, 'title' => 'Aswan Leisure'],
                    ['day' => 7, 'title' => 'Departure'],
                ],
                'inclusions' => ['Premium Hotels', 'Private Transfers', 'Dedicated Support', 'Domestic Flights'],
                'exclusions' => ['International Flights', 'Personal Expenses'],
                'starting_price' => 999,
                'best' => false,
            ],
            [
                'name' => 'Complete Egypt',
                'duration' => '7 Days',
                'cities' => ['Cairo', 'Luxor', 'Aswan'],
                'highlights' => ['Cairo', 'Luxor', 'Aswan', 'Nile Cruise', 'Domestic Flights'],
                'itinerary' => [
                    ['day' => 1, 'title' => 'Arrival Cairo'],
                    ['day' => 2, 'title' => 'Pyramids & GEM'],
                    ['day' => 3, 'title' => 'Flight to Luxor'],
                    ['day' => 4, 'title' => 'Nile Cruise'],
                    ['day' => 5, 'title' => 'Temples along the Nile'],
                    ['day' => 6, 'title' => 'Aswan & Felucca'],
                    ['day' => 7, 'title' => 'Departure'],
                ],
                'inclusions' => ['Hotels & Cruise', 'Breakfast', 'Domestic Flights', 'Guide'],
                'exclusions' => ['International Flights', 'Optional Activities'],
                'starting_price' => 1199,
                'best' => false,
            ],
        ];

        foreach ($programs as $i => $program) {
            Program::updateOrCreate(
                ['slug' => Str::slug($program['name'])],
                [
                    'name' => $program['name'],
                    'duration' => $program['duration'],
                    'cities' => $program['cities'],
                    'highlights' => $program['highlights'],
                    'itinerary' => $program['itinerary'],
                    'inclusions' => $program['inclusions'],
                    'exclusions' => $program['exclusions'],
                    'starting_price' => $program['starting_price'],
                    'is_active' => true,
                    'is_best_seller' => $program['best'],
                    'sort_order' => $i + 1,
                ]
            );
        }

        $meetAssist = [
            ['tier' => 'basic', 'name' => 'Basic', 'price' => 15],
            ['tier' => 'comfort', 'name' => 'Comfort', 'price' => 50],
            ['tier' => 'premium', 'name' => 'Premium', 'price' => 90, 'popular' => true],
            ['tier' => 'vip', 'name' => 'VIP', 'price' => 125],
        ];

        foreach ($meetAssist as $pkg) {
            ServicePackage::updateOrCreate(
                ['service_type' => VisaServiceType::MEET_ASSIST->value, 'tier' => $pkg['tier']],
                [
                    'name' => $pkg['name'],
                    'price' => $pkg['price'],
                    'features' => ['Fast track', 'Meet & greet'],
                    'includes_visa' => false,
                    'is_popular' => $pkg['popular'] ?? false,
                    'is_active' => true,
                ]
            );
        }

        $transitTours = [
            [
                'hours' => 3,
                'price' => 120,
                'name' => '3 Hours Transit Tour',
                'popular' => false,
                'features' => ['Pyramids', 'Panoramic View', 'Short Stop'],
            ],
            [
                'hours' => 6,
                'price' => 180,
                'name' => '6 Hours Transit Tour',
                'popular' => true,
                'features' => ['Pyramids', 'Grand Egyptian Museum', 'Lunch', 'Shopping Stop'],
            ],
            [
                'hours' => 8,
                'price' => 240,
                'name' => '8 Hours Transit Tour',
                'popular' => false,
                'features' => ['Pyramids', 'Grand Egyptian Museum', 'Lunch', 'Shopping', 'Old Cairo Visit'],
            ],
        ];

        foreach ($transitTours as $tour) {
            ServicePackage::updateOrCreate(
                ['service_type' => VisaServiceType::TRANSIT_TOUR->value, 'tier' => "{$tour['hours']}h"],
                [
                    'name' => $tour['name'],
                    'price' => $tour['price'],
                    'duration_hours' => $tour['hours'],
                    'features' => $tour['features'],
                    'is_popular' => $tour['popular'],
                    'is_active' => true,
                ]
            );
        }

        $vehicles = [
            [
                'type' => 'sedan',
                'name' => 'Limousine Sedan',
                'max_passengers' => 3,
                'max_bags' => 2,
                'base_price' => 28,
                'tags' => 'Couples, Business Travel',
            ],
            [
                'type' => 'van',
                'name' => 'Hyundai H1 Tourist Van',
                'max_passengers' => 5,
                'max_bags' => 5,
                'base_price' => 45,
                'tags' => 'Family Transfer, Small Groups',
            ],
            [
                'type' => 'minibus',
                'name' => 'Toyota Hiace Tourist Minibus',
                'max_passengers' => 8,
                'max_bags' => 8,
                'base_price' => 75,
                'tags' => 'Groups, Large Families',
            ],
        ];

        foreach ($vehicles as $v) {
            Vehicle::updateOrCreate(['type' => $v['type']], array_merge($v, ['is_active' => true]));
        }

        $this->seedAdditionalServices();

        Staff::updateOrCreate(
            ['full_name' => 'Ahmed Representative', 'type' => StaffType::REPRESENTATIVE->value],
            ['phone' => '+201000000001', 'whatsapp' => '+201000000001', 'languages' => ['ar', 'en'], 'rating' => 4.8, 'is_active' => true]
        );
        Staff::updateOrCreate(
            ['full_name' => 'Mohamed Driver', 'type' => StaffType::DRIVER->value],
            ['phone' => '+201000000002', 'license_number' => 'DRV-001', 'languages' => ['ar'], 'rating' => 4.5, 'is_active' => true]
        );
        Staff::updateOrCreate(
            ['type' => StaffType::GUIDE->value],
            [
                'full_name' => 'Asma',
                'phone' => '+201148165143',
                'whatsapp' => '+201148165143',
                'license_number' => '17635',
                'languages' => ['en', 'ar', 'fr'],
                'rating' => 4.9,
                'is_active' => true,
            ]
        );

        $this->seedDemoBookings();
        $this->seedDemoNotifications();
    }

    private function seedDemoBookings(): void
    {
        if (VisaBooking::exists()) {
            return;
        }

        $client = Client::first() ?? Client::create([
            'name' => 'Demo Visa Client',
            'email' => 'visa.demo@example.com',
            'password' => Hash::make('password'),
            'phone' => '+201111111111',
            'whatsapp' => '+201111111111',
            'language' => 'en',
            'nationality' => 'United Kingdom',
        ]);

        $program = Program::first();
        $meetPackage = ServicePackage::where('service_type', VisaServiceType::MEET_ASSIST->value)->first();
        $transitPackage = ServicePackage::where('service_type', VisaServiceType::TRANSIT_TOUR->value)->first();
        $vehicle = Vehicle::first();
        $representative = Staff::where('type', StaffType::REPRESENTATIVE->value)->first();
        $driver = Staff::where('type', StaffType::DRIVER->value)->first();
        $refs = app(BookingRefGenerator::class);

        $samples = [
            [
                'service_type' => VisaServiceType::MEET_ASSIST,
                'status' => VisaBookingStatus::PENDING,
                'service_package_id' => $meetPackage?->id,
                'flight_number' => 'MS777',
                'total_amount' => 50,
            ],
            [
                'service_type' => VisaServiceType::MEET_ASSIST,
                'status' => VisaBookingStatus::PENDING,
                'service_package_id' => $meetPackage?->id,
                'flight_number' => 'BA154',
                'total_amount' => 90,
            ],
            [
                'service_type' => VisaServiceType::AIRPORT_TRANSFER,
                'status' => VisaBookingStatus::CONFIRMED,
                'vehicle_id' => $vehicle?->id,
                'destination' => 'Cairo Downtown',
                'total_amount' => 35,
            ],
            [
                'service_type' => VisaServiceType::TRANSIT_TOUR,
                'status' => VisaBookingStatus::ASSIGNED,
                'service_package_id' => $transitPackage?->id,
                'flight_number' => 'EK924',
                'total_amount' => 180,
                'assign_staff' => $driver,
            ],
            [
                'service_type' => VisaServiceType::MEET_ASSIST,
                'status' => VisaBookingStatus::IN_PROGRESS,
                'service_package_id' => $meetPackage?->id,
                'flight_number' => 'LH582',
                'total_amount' => 125,
                'assign_staff' => $representative,
                'tracking_key' => 'waiting_at_airport',
                'tracking_label' => 'Waiting at Airport',
            ],
            [
                'service_type' => VisaServiceType::EXPLORE_EGYPT,
                'status' => VisaBookingStatus::PENDING,
                'program_id' => $program?->id,
                'travelers_count' => 2,
                'total_amount' => $program?->starting_price ?? 595,
            ],
            [
                'service_type' => VisaServiceType::VISA_ON_ARRIVAL,
                'status' => VisaBookingStatus::COMPLETED,
                'nationality' => 'Germany',
                'total_amount' => 25,
            ],
            [
                'service_type' => VisaServiceType::AIRPORT_TRANSFER,
                'status' => VisaBookingStatus::CANCELLED,
                'total_amount' => 40,
            ],
        ];

        foreach ($samples as $sample) {
            $assignStaff = $sample['assign_staff'] ?? null;
            $withTracking = ($sample['status'] === VisaBookingStatus::IN_PROGRESS)
                || isset($sample['tracking_key']);
            unset($sample['assign_staff'], $sample['tracking_key'], $sample['tracking_label']);

            $booking = VisaBooking::create(array_merge([
                'client_id' => $client->id,
                'booking_ref' => $refs->generate(),
                'travel_date' => now()->addDays(random_int(1, 14)),
                'travelers_count' => $sample['travelers_count'] ?? 1,
                'nationality' => $sample['nationality'] ?? 'United Kingdom',
                'contact_email' => $client->email,
                'contact_whatsapp' => $client->whatsapp,
                'meeting_point' => 'Cairo Airport Terminal 3',
            ], $sample));

            if ($assignStaff) {
                VisaBookingAssignment::create([
                    'visa_booking_id' => $booking->id,
                    'staff_id' => $assignStaff->id,
                    'vehicle_id' => $vehicle?->id,
                    'assigned_at' => now(),
                ]);
            }

            if ($withTracking) {
                TrackingEvent::create([
                    'visa_booking_id' => $booking->id,
                    'status_key' => 'staff_assigned',
                    'status_label' => 'Staff Assigned',
                    'event_at' => now()->subHour(),
                    'staff_id' => $assignStaff?->id,
                    'is_current' => false,
                ]);
                TrackingEvent::create([
                    'visa_booking_id' => $booking->id,
                    'status_key' => 'waiting_at_airport',
                    'status_label' => 'Waiting at Airport',
                    'event_at' => now(),
                    'staff_id' => $assignStaff?->id,
                    'is_current' => true,
                ]);
            }
        }
    }

    private function seedDemoNotifications(): void
    {
        $client = Client::first();
        if (! $client) {
            return;
        }

        $trackingBooking = VisaBooking::where('client_id', $client->id)
            ->where('status', VisaBookingStatus::IN_PROGRESS->value)
            ->first();

        $confirmedBooking = VisaBooking::where('client_id', $client->id)
            ->where('status', VisaBookingStatus::CONFIRMED->value)
            ->first();

        $samples = [
            [
                'title' => 'Representative at the airport',
                'body' => 'Your meet & assist representative is waiting at Terminal 3.',
                'type' => 'tracking',
                'target_screen' => 'live_tracking',
                'target_id' => $trackingBooking?->booking_ref,
                'read_at' => null,
            ],
            [
                'title' => 'Booking confirmed',
                'body' => 'Your airport transfer has been confirmed. View details in My Bookings.',
                'type' => 'booking',
                'target_screen' => 'booking',
                'target_id' => $confirmedBooking?->booking_ref,
                'read_at' => null,
            ],
            [
                'title' => 'Exclusive offer',
                'body' => 'Save 20% on your next transit tour. Limited time only!',
                'type' => 'offer',
                'target_screen' => 'offers',
                'target_id' => null,
                'read_at' => now()->subDay(),
            ],
            [
                'title' => 'Welcome to Visa Egypt',
                'body' => 'Explore our services and book your seamless airport experience.',
                'type' => 'general',
                'target_screen' => null,
                'target_id' => null,
                'read_at' => now()->subDays(3),
            ],
        ];

        foreach ($samples as $sample) {
            AppNotification::updateOrCreate(
                [
                    'client_id' => $client->id,
                    'title' => $sample['title'],
                ],
                $sample + ['client_id' => $client->id],
            );
        }
    }

    private function seedAdditionalServices(): void
    {
        $services = [
            [
                'title' => 'Egypt Tourist SIM Card',
                'description' => 'Stay connected during your trip with our tourist SIM card.',
                'price' => 15,
                'price_from' => false,
                'icon' => 'sim_card',
                'accent_color' => '#F26522',
                'features' => [
                    '40 GB Data',
                    'Instant Activation',
                    'Ready at Airport',
                    'No International Calls',
                ],
                'sort_order' => 1,
            ],
            [
                'title' => 'Airport Transfer',
                'description' => 'Private transfer from Cairo Airport to any destination within Cairo.',
                'price' => 25,
                'price_from' => true,
                'icon' => 'local_taxi',
                'accent_color' => '#0E7C7B',
                'features' => [
                    'Private Car',
                    'Professional Driver',
                    'Meet & Greet',
                ],
                'sort_order' => 2,
            ],
            [
                'title' => 'Hotel Booking',
                'description' => 'Book your hotel starting from 3-star hotels with the best available rates.',
                'price' => 35,
                'price_from' => true,
                'icon' => 'apartment',
                'accent_color' => '#D4A017',
                'features' => [
                    '3 Stars',
                    '4 Stars',
                    '5 Stars',
                    'Best Available Rates',
                    'Flexible Options',
                ],
                'sort_order' => 3,
            ],
            [
                'title' => 'Pyramids Half Day Tour',
                'description' => 'Explore the Pyramids of Giza, Sphinx & more.',
                'price' => 42.50,
                'price_from' => false,
                'icon' => 'tour',
                'accent_color' => '#C9A227',
                'features' => [
                    'Giza Pyramids',
                    'Sphinx',
                    'Hotel Pickup',
                ],
                'sort_order' => 5,
            ],
            [
                'title' => 'Nile Felucca Ride',
                'description' => 'Relax and enjoy a traditional sail on the Nile.',
                'price' => 28,
                'price_from' => false,
                'icon' => 'directions_boat',
                'accent_color' => '#1B7A3A',
                'features' => [
                    'Traditional Felucca',
                    '1 Hour Sail',
                    'Sunset Option',
                ],
                'sort_order' => 6,
            ],
            [
                'title' => 'Nile Dinner Cruise',
                'description' => 'Enjoy dinner with live show on the Nile.',
                'price' => 52.50,
                'price_from' => false,
                'icon' => 'directions_boat',
                'accent_color' => '#D32027',
                'features' => [
                    'Hotel Pickup',
                    'Hotel Drop-off',
                    'Open Buffet Dinner',
                    'Live Show',
                    'Belly Dance & Tanoura Show',
                    '2 Hours Cruise',
                    'Available Daily',
                    'Visa Club Exclusive',
                ],
                'sort_order' => 4,
            ],
        ];

        foreach ($services as $service) {
            AdditionalService::updateOrCreate(
                ['title' => $service['title']],
                array_merge($service, ['currency' => 'USD', 'is_active' => true])
            );
        }
    }

    private function seedMembershipPlans(): void
    {
        MembershipTier::query()
            ->whereIn('slug', ['basic', 'comfort', 'premium', 'vip'])
            ->update(['is_active' => false]);

        $plans = [
            [
                'slug' => 'silver',
                'name' => 'SILVER MEMBER',
                'tagline' => '15% OFF',
                'description' => 'Free membership with exclusive deals and booking priority.',
                'features' => [
                    'Travel Program Discounts',
                    'Members Offers',
                    'Points Collection',
                    'Priority Booking',
                ],
                'special_offer_text' => 'Free Membership',
                'special_offer_included' => true,
                'theme_color' => '#9EA7B3',
                'is_featured' => false,
                'discount_percent' => 15,
                'price_usd' => 0,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'slug' => 'gold',
                'name' => 'GOLD MEMBER',
                'tagline' => '20% OFF',
                'description' => 'Most popular club level with faster support and bonus points.',
                'features' => [
                    'All Silver Benefits',
                    'Exclusive Promotions',
                    'Faster Support',
                    'Bonus Points',
                ],
                'special_offer_text' => 'MOST POPULAR',
                'special_offer_included' => true,
                'theme_color' => '#C5A059',
                'is_featured' => true,
                'discount_percent' => 20,
                'price_usd' => 49,
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'slug' => 'platinum',
                'name' => 'PLATINUM MEMBER',
                'tagline' => '25% OFF',
                'description' => 'Best value VIP membership with airport benefits.',
                'features' => [
                    'All Gold Benefits',
                    'VIP Priority Support',
                    'Premium Offers',
                    'Airport Benefits',
                    'Extra Reward Points',
                ],
                'special_offer_text' => 'BEST VALUE',
                'special_offer_included' => true,
                'theme_color' => '#3B6CB5',
                'is_featured' => false,
                'discount_percent' => 25,
                'price_usd' => 99,
                'sort_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            MembershipTier::updateOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
