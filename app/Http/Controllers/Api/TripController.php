<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Models\City;
use App\Http\Requests\Api\TripSearchRequest;
use App\Http\Requests\Api\TripBookingRequest;
use Illuminate\Http\Request;

class TripController extends Controller
{
    public function index(Request $request)
    {
        $query = Trip::available()
            ->with(['departureCity', 'arrivalCity'])
            ->orderBy('travel_date')
            ->orderBy('departure_time');

        // Filter by trip type
        if ($request->has('trip_type')) {
            $query->where('trip_type', $request->trip_type);
        }

        // Filter by departure city
        if ($request->has('departure_city_id')) {
            $query->where('departure_city_id', $request->departure_city_id);
        }

        // Filter by arrival city
        if ($request->has('arrival_city_id')) {
            $query->where('arrival_city_id', $request->arrival_city_id);
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->whereDate('travel_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('travel_date', '<=', $request->date_to);
        }

        // Filter by price range
        if ($request->has('price_min')) {
            $query->where('seat_price', '>=', $request->price_min);
        }

        if ($request->has('price_max')) {
            $query->where('seat_price', '<=', $request->price_max);
        }

        // Paginate results
        $perPage = $request->get('per_page', 15);
        $trips = $query->paginate($perPage);

        $formattedTrips = $trips->getCollection()->map(function ($trip) {
            return [
                'id' => $trip->id,
                'trip_name' => $trip->trip_name,
                'trip_type' => $trip->trip_type,
                'trip_type_label' => $trip->trip_type_label,
                'from' => $trip->departure_city_name,
                'to' => $trip->arrival_city_name,
                'travel_date' => $trip->travel_date->format('Y-m-d'),
                'return_date' => $trip->return_date ? $trip->return_date->format('Y-m-d') : null,
                'departure_time' => $trip->formatted_departure_time,
                'arrival_time' => $trip->formatted_arrival_time,
                'price' => (float) $trip->seat_price,
                'amenities' => $trip->amenities ?? [],
                'available_seats' => $trip->available_seats,
                'total_seats' => $trip->total_seats,
                'additional_notes' => $trip->additional_notes,
                'occupancy_rate' => $trip->occupancy_rate,
                'occupancy_status' => $trip->occupancy_status,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedTrips,
            'pagination' => [
                'current_page' => $trips->currentPage(),
                'last_page' => $trips->lastPage(),
                'per_page' => $trips->perPage(),
                'total' => $trips->total(),
                'from' => $trips->firstItem(),
                'to' => $trips->lastItem(),
            ]
        ]);
    }

    public function search(TripSearchRequest $request)
    {
        $data = $request->validated();

        $query = Trip::available()
            ->where('departure_city_id', $data['departure_city_id'])
            ->where('arrival_city_id', $data['arrival_city_id'])
            ->whereDate('travel_date', $data['travel_date'])
            ->where('available_seats', '>=', $data['passengers']);

        // Filter by trip type
        if ($data['trip_type'] === 'one_way') {
            $query->whereIn('trip_type', ['one_way', 'special_discount']);
        } else {
            $query->where('trip_type', 'round_trip');
        }

        $trips = $query->with(['departureCity', 'arrivalCity'])->orderBy('departure_time')->get();

        $formattedTrips = $trips->map(function ($trip) use ($data) {
            return [
                'id' => $trip->id,
                'from' => $trip->departure_city_name,
                'to' => $trip->arrival_city_name,
                'departure_time' => $trip->formatted_departure_time,
                'arrival_time' => $trip->formatted_arrival_time,
                'date' => $trip->travel_date->format('Y-m-d'),
                'price' => (float) $trip->seat_price,
                'amenities' => $trip->amenities ?? [],
                'available_seats' => $trip->available_seats,
                'notes' => $this->generateNotes($trip, $data),
                'trip_type' => $trip->trip_type,
                'additional_notes' => $trip->additional_notes,
            ];
        });

        return response()->json([
            'success' => true,
            'trips' => $formattedTrips,
            'search_criteria' => [
                'trip_type' => $data['trip_type'],
                'departure_city_id' => $data['departure_city_id'],
                'arrival_city_id' => $data['arrival_city_id'],
                'travel_date' => $data['travel_date'],
                'return_date' => $data['return_date'] ?? null,
                'passengers' => $data['passengers']
            ]
        ]);
    }

    public function show(Trip $trip)
    {
        if (!$trip->enabled) {
            return response()->json([
                'success' => false,
                'message' => 'Trip not available'
            ], 404);
        }

        $trip->load(['departureCity', 'arrivalCity']);

        return response()->json([
            'success' => true,
            'trip' => [
                'id' => $trip->id,
                'trip_name' => $trip->trip_name,
                'trip_type' => $trip->trip_type,
                'trip_type_label' => $trip->trip_type_label,
                'from' => $trip->departure_city_name,
                'to' => $trip->arrival_city_name,
                'travel_date' => $trip->travel_date->format('Y-m-d'),
                'return_date' => $trip->return_date ? $trip->return_date->format('Y-m-d') : null,
                'departure_time' => $trip->formatted_departure_time,
                'arrival_time' => $trip->formatted_arrival_time,
                'price' => (float) $trip->seat_price,
                'amenities' => $trip->amenities ?? [],
                'available_seats' => $trip->available_seats,
                'total_seats' => $trip->total_seats,
                'additional_notes' => $trip->additional_notes,
                'occupancy_rate' => $trip->occupancy_rate,
                'occupancy_status' => $trip->occupancy_status,
            ]
        ]);
    }

    public function book(TripBookingRequest $request)
    {
        $data = $request->validated();
        $trip = Trip::findOrFail($data['trip_id']);

        // Check if trip is available
        if (!$trip->enabled) {
            return response()->json([
                'success' => false,
                'message' => 'Trip is not available'
            ], 400);
        }

        $totalPassengers = $data['adults'] + ($data['children'] ?? 0);

        // Calculate total price
        $totalPrice = $trip->calculatePriceForPassengers($data['adults'], $data['children'] ?? 0);

        // Create booking
        $booking = \App\Models\TripBooking::create([
            'trip_id' => $trip->id,
            'client_id' => $data['client_id'] ?? null,
            'passenger_name' => $data['contact_name'],
            'passenger_email' => $data['contact_email'],
            'passenger_phone' => $data['contact_phone'],
            'adults_count' => $data['adults'],
            'children_count' => $data['children'] ?? 0,
            'number_of_passengers' => $totalPassengers,
            'total_price' => $totalPrice,
            'notes' => $data['special_requests'] ?? null,
            'status' => \App\Models\TripBooking::STATUS_PENDING,
            'booking_reference' => \App\Models\TripBooking::generateBookingReference(),
        ]);

        // Update available seats
        $trip->decrement('available_seats', $totalPassengers);

        return response()->json([
            'success' => true,
            'message' => 'Booking created successfully',
            'booking' => [
                'id' => $booking->id,
                'booking_reference' => $booking->booking_reference,
                'passenger_name' => $booking->passenger_name,
                'passenger_email' => $booking->passenger_email,
                'passenger_phone' => $booking->passenger_phone,
                'adults_count' => $booking->adults_count,
                'children_count' => $booking->children_count,
                'total_passengers' => $booking->number_of_passengers,
                'total_price' => (float) $booking->total_price,
                'price_breakdown' => $booking->price_breakdown,
                'status' => $booking->status,
                'status_label' => $booking->status_label,
                'notes' => $booking->notes,
                'created_at' => $booking->created_at->format('Y-m-d H:i:s'),
            ],
            'trip' => [
                'id' => $trip->id,
                'from' => $trip->departure_city_name,
                'to' => $trip->arrival_city_name,
                'travel_date' => $trip->travel_date->format('Y-m-d'),
                'departure_time' => $trip->formatted_departure_time,
                'arrival_time' => $trip->formatted_arrival_time,
                'available_seats' => $trip->available_seats,
            ]
        ]);
    }

    private function generateNotes(Trip $trip, array $searchData): string
    {
        $notes = [];

        if ($trip->trip_type === 'round_trip') {
            $notes[] = 'Round trip booking';
        }

        if ($trip->trip_type === 'special_discount') {
            $notes[] = 'Special discount applied';
        }

        if ($searchData['passengers'] > 1) {
            $notes[] = "Group booking for {$searchData['passengers']} passengers";
        }

        if ($trip->amenities && count($trip->amenities) > 0) {
            $amenitiesList = implode(', ', $trip->amenities);
            $notes[] = "Amenities: {$amenitiesList}";
        }

        if ($trip->additional_notes) {
            $notes[] = $trip->additional_notes;
        }

        return implode('. ', $notes);
    }
}
