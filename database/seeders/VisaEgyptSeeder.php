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

        $programs = [
            'Egypt Discovery', 'Nile Adventure', 'Cairo & Luxor Classic', 'Red Sea Escape',
            'Desert & Oasis', 'Alexandria Coastal', 'Pharaonic Wonders', 'Family Egypt Fun', 'Luxury Egypt Experience',
        ];

        foreach ($programs as $i => $name) {
            Program::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'duration' => '5 Days / 4 Nights',
                    'cities' => ['Cairo', 'Luxor'],
                    'highlights' => ['Pyramids', 'Nile Cruise'],
                    'itinerary' => [['day' => 1, 'title' => 'Arrival', 'description' => 'Airport pickup']],
                    'inclusions' => ['Hotel', 'Breakfast'],
                    'exclusions' => ['Flights'],
                    'starting_price' => 595 - ($i * 20),
                    'is_active' => true,
                    'is_best_seller' => $i === 0,
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

        foreach ([3 => 120, 6 => 180, 8 => 240] as $hours => $price) {
            ServicePackage::updateOrCreate(
                ['service_type' => VisaServiceType::TRANSIT_TOUR->value, 'tier' => "{$hours}h"],
                [
                    'name' => "Transit {$hours} Hours",
                    'price' => $price,
                    'duration_hours' => $hours,
                    'features' => ['Guide', 'Transport'],
                    'is_active' => true,
                ]
            );
        }

        $vehicles = [
            ['type' => 'sedan', 'name' => 'Limousine Sedan', 'max_passengers' => 3, 'max_bags' => 2, 'base_price' => 28],
            ['type' => 'van', 'name' => 'Hyundai H1 Van', 'max_passengers' => 5, 'max_bags' => 5, 'base_price' => 45],
            ['type' => 'minibus', 'name' => 'Toyota Hiace Minibus', 'max_passengers' => 8, 'max_bags' => 8, 'base_price' => 75],
        ];

        foreach ($vehicles as $v) {
            Vehicle::updateOrCreate(['name' => $v['name']], array_merge($v, ['is_active' => true]));
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
            ['full_name' => 'Sara Guide', 'type' => StaffType::GUIDE->value],
            ['phone' => '+201000000003', 'languages' => ['en', 'fr'], 'rating' => 4.9, 'is_active' => true]
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
                'title' => 'Nile Dinner Cruise',
                'description' => 'Enjoy a magical evening on the Nile. Includes hotel pickup and drop-off.',
                'price' => 75,
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
            ->whereIn('slug', ['silver', 'gold', 'platinum'])
            ->update(['is_active' => false]);

        $plans = [
            [
                'slug' => 'basic',
                'name' => 'BASIC',
                'tagline' => 'Essential Support',
                'description' => 'Airport assistance essentials for a smooth arrival.',
                'features' => [
                    'Meet & Greet at Airport',
                    'Airport Assistance',
                    'Immigration Guidance',
                    'Baggage Assistance',
                ],
                'special_offer_text' => 'Visa Fee Not Included',
                'special_offer_included' => false,
                'theme_color' => '#007BFF',
                'is_featured' => false,
                'discount_percent' => 0,
                'price_usd' => 15,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'slug' => 'comfort',
                'name' => 'COMFORT',
                'tagline' => 'Enhanced Airport Care',
                'description' => 'Faster processing and priority support at the airport.',
                'features' => [
                    'All Basic Services',
                    'Fast Track Immigration',
                    'Priority Visa Processing',
                    'Baggage Assistance',
                ],
                'special_offer_text' => 'Visa On Arrival Included (30 USD)',
                'special_offer_included' => true,
                'theme_color' => '#00A884',
                'is_featured' => false,
                'discount_percent' => 5,
                'price_usd' => 45,
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'slug' => 'premium',
                'name' => 'PREMIUM',
                'tagline' => 'Best Value Package',
                'description' => 'Dedicated representative and lounge access for premium travelers.',
                'features' => [
                    'All Comfort Services',
                    'Hotel Transfer (One Way)',
                    'Dedicated Representative',
                    'Airport Lounge Access (3 Hours)',
                    'Baggage Assistance',
                ],
                'special_offer_text' => 'Visa On Arrival Included (30 USD)',
                'special_offer_included' => true,
                'theme_color' => '#6F42C1',
                'is_featured' => true,
                'discount_percent' => 10,
                'price_usd' => 75,
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'slug' => 'vip',
                'name' => 'VIP',
                'tagline' => 'Ultimate Experience',
                'description' => 'Private escort, concierge service, and unlimited lounge access.',
                'features' => [
                    'All Premium Services',
                    'VIP Fast Track',
                    'Private Escort',
                    'Concierge Service',
                    'Airport Lounge Access (Unlimited)',
                    'Flight Monitoring & Support',
                ],
                'special_offer_text' => 'Visa On Arrival Included (30 USD)',
                'special_offer_included' => true,
                'theme_color' => '#D4AF37',
                'is_featured' => false,
                'discount_percent' => 15,
                'price_usd' => 125,
                'sort_order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            MembershipTier::updateOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
