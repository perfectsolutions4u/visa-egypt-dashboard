<?php

namespace Documentation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Api\TripResource;
use App\Models\Trip;
use App\Traits\Response\HasApiResponse;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Http\JsonResponse;
use App\Services\Query\QueryBuilder;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *  schema="Trip",
 *  title="Trip Schema",
 *  @OA\Property(
 *      property="id",
 *      type="number",
 *      example="1"
 *  ),
 *  @OA\Property(
 *      property="title",
 *      type="string",
 *      example="Egypt Pyramids Adventure"
 *  ),
 *  @OA\Property(
 *      property="slug",
 *      type="string",
 *      example="egypt-pyramids-adventure"
 *  ),
 *  @OA\Property(
 *      property="overview",
 *      type="string",
 *      example="Explore the ancient wonders of Egypt"
 *  ),
 *  @OA\Property(
 *      property="highlights",
 *      type="string",
 *      example="Pyramids, Sphinx, Nile Cruise"
 *  ),
 *  @OA\Property(
 *      property="rate",
 *      type="number",
 *      example=4.5
 *  ),
 *  @OA\Property(
 *      property="duration_in_days",
 *      type="number",
 *      example=7
 *  ),
 *  @OA\Property(
 *      property="reviews_number",
 *      type="number",
 *      example=25
 *  ),
 *  @OA\Property(
 *      property="itinary",
 *      type="string",
 *      example="Day 1: Arrival, Day 2: Pyramids..."
 *  ),
 *  @OA\Property(
 *      property="included",
 *      type="string",
 *      example="Accommodation, Meals, Guide"
 *  ),
 *  @OA\Property(
 *      property="excluded",
 *      type="string",
 *      example="Flights, Personal expenses"
 *  ),
 *  @OA\Property(
 *      property="duration",
 *      type="string",
 *      example="7 days / 6 nights"
 *  ),
 *  @OA\Property(
 *      property="type",
 *      type="string",
 *      example="Cultural"
 *  ),
 *  @OA\Property(
 *      property="run",
 *      type="string",
 *      example="Daily"
 *  ),
 *  @OA\Property(
 *      property="pickup_time",
 *      type="string",
 *      example="08:00 AM"
 *  ),
 *  @OA\Property(
 *      property="enabled",
 *      type="boolean",
 *      example=true
 *  ),
 *  @OA\Property(
 *      property="featured",
 *      type="boolean",
 *      example=false
 *  ),
 *  @OA\Property(
 *      property="featured_image",
 *      type="string",
 *      example="https://example.com/image.jpg"
 *  ),
 *  @OA\Property(
 *      property="price",
 *      type="number",
 *      example=1200.00
 *  ),
 *  @OA\Property(
 *      property="currency",
 *      type="string",
 *      example="USD"
 *  ),
 *  @OA\Property(
 *      property="created_at",
 *      type="string",
 *      example="2023-12-11T09:25:53.000000Z"
 *  ),
 *  @OA\Property(
 *      property="updated_at",
 *      type="string",
 *      example="2023-12-11T09:25:53.000000Z"
 *  )
 * )
 */

/**
 * @OA\Schema(
 *  schema="Booking",
 *  title="Booking Schema",
 *  @OA\Property(
 *      property="id",
 *      type="number",
 *      example="1"
 *  ),
 *  @OA\Property(
 *      property="trip_id",
 *      type="number",
 *      example="1"
 *  ),
 *  @OA\Property(
 *      property="client_id",
 *      type="number",
 *      example="1"
 *  ),
 *  @OA\Property(
 *      property="booking_date",
 *      type="string",
 *      example="2023-06-15"
 *  ),
 *  @OA\Property(
 *      property="adults",
 *      type="number",
 *      example=2
 *  ),
 *  @OA\Property(
 *      property="children",
 *      type="number",
 *      example=1
 *  ),
 *  @OA\Property(
 *      property="infants",
 *      type="number",
 *      example=0
 *  ),
 *  @OA\Property(
 *      property="total_price",
 *      type="number",
 *      example=2400.00
 *  ),
 *  @OA\Property(
 *      property="status",
 *      type="string",
 *      example="confirmed"
 *  ),
 *  @OA\Property(
 *      property="payment_status",
 *      type="string",
 *      example="paid"
 *  ),
 *  @OA\Property(
 *      property="created_at",
 *      type="string",
 *      example="2023-12-11T09:25:53.000000Z"
 *  ),
 *  @OA\Property(
 *      property="updated_at",
 *      type="string",
 *      example="2023-12-11T09:25:53.000000Z"
 *  )
 * )
 */

/**
 * @OA\Schema(
 *  schema="Review",
 *  title="Review Schema",
 *  @OA\Property(
 *      property="id",
 *      type="number",
 *      example="1"
 *  ),
 *  @OA\Property(
 *      property="trip_id",
 *      type="number",
 *      example="1"
 *  ),
 *  @OA\Property(
 *      property="user_id",
 *      type="number",
 *      example="1"
 *  ),
 *  @OA\Property(
 *      property="rating",
 *      type="number",
 *      example=5
 *  ),
 *  @OA\Property(
 *      property="comment",
 *      type="string",
 *      example="Amazing experience! Highly recommended."
 *  ),
 *  @OA\Property(
 *      property="created_at",
 *      type="string",
 *      example="2023-12-11T09:25:53.000000Z"
 *  ),
 *  @OA\Property(
 *      property="updated_at",
 *      type="string",
 *      example="2023-12-11T09:25:53.000000Z"
 *  )
 * )
 */

class TripController extends Controller
{
    use HasApiResponse;

    /**
     * Get All Trips
     * @OA\Get (
     *     path="/api/trips",
     *     tags={"Trips"},
     *     summary="Get all available trips",
     *     description="Retrieve a paginated list of all available trips with optional filtering",
     *     @OA\Parameter(
     *         description="Filter by trip type",
     *         in="query",
     *         name="trip_type",
     *         required=false,
     *         @OA\Schema(type="string", enum={"one_way", "round_trip", "special_discount"}),
     *     ),
     *     @OA\Parameter(
     *         description="Filter by departure city ID",
     *         in="query",
     *         name="departure_city_id",
     *         required=false,
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\Parameter(
     *         description="Filter by arrival city ID",
     *         in="query",
     *         name="arrival_city_id",
     *         required=false,
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\Parameter(
     *         description="Filter by travel date from (YYYY-MM-DD)",
     *         in="query",
     *         name="date_from",
     *         required=false,
     *         @OA\Schema(type="string", format="date"),
     *     ),
     *     @OA\Parameter(
     *         description="Filter by travel date to (YYYY-MM-DD)",
     *         in="query",
     *         name="date_to",
     *         required=false,
     *         @OA\Schema(type="string", format="date"),
     *     ),
     *     @OA\Parameter(
     *         description="Filter by minimum price",
     *         in="query",
     *         name="price_min",
     *         required=false,
     *         @OA\Schema(type="number"),
     *     ),
     *     @OA\Parameter(
     *         description="Filter by maximum price",
     *         in="query",
     *         name="price_max",
     *         required=false,
     *         @OA\Schema(type="number"),
     *     ),
     *     @OA\Parameter(
     *         description="Number of items per page",
     *         in="query",
     *         name="per_page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15),
     *     ),
     *     @OA\Parameter(
     *         description="Page number",
     *         in="query",
     *         name="page",
     *         required=false,
     *         @OA\Schema(type="integer", default=1),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="id",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="trip_name",
     *                         type="string",
     *                         example="Cairo to Alexandria"
     *                     ),
     *                     @OA\Property(
     *                         property="trip_type",
     *                         type="string",
     *                         example="one_way"
     *                     ),
     *                     @OA\Property(
     *                         property="trip_type_label",
     *                         type="string",
     *                         example="One Way"
     *                     ),
     *                     @OA\Property(
     *                         property="from",
     *                         type="string",
     *                         example="Cairo"
     *                     ),
     *                     @OA\Property(
     *                         property="to",
     *                         type="string",
     *                         example="Alexandria"
     *                     ),
     *                     @OA\Property(
     *                         property="travel_date",
     *                         type="string",
     *                         example="2025-08-15"
     *                     ),
     *                     @OA\Property(
     *                         property="return_date",
     *                         type="string",
     *                         nullable=true,
     *                         example=null
     *                     ),
     *                     @OA\Property(
     *                         property="departure_time",
     *                         type="string",
     *                         example="08:00"
     *                     ),
     *                     @OA\Property(
     *                         property="arrival_time",
     *                         type="string",
     *                         example="11:30"
     *                     ),
     *                     @OA\Property(
     *                         property="price",
     *                         type="number",
     *                         example=220.00
     *                     ),
     *                     @OA\Property(
     *                         property="amenities",
     *                         type="array",
     *                         @OA\Items(type="string"),
     *                         example={"Wi-Fi", "Snacks"}
     *                     ),
     *                     @OA\Property(
     *                         property="available_seats",
     *                         type="integer",
     *                         example=17
     *                     ),
     *                     @OA\Property(
     *                         property="total_seats",
     *                         type="integer",
     *                         example=20
     *                     ),
     *                     @OA\Property(
     *                         property="additional_notes",
     *                         type="string",
     *                         example="Professional driver and clean vehicle"
     *                     ),
     *                     @OA\Property(
     *                         property="occupancy_rate",
     *                         type="number",
     *                         example=85.0
     *                     ),
     *                     @OA\Property(
     *                         property="occupancy_status",
     *                         type="string",
     *                         example="High"
     *                     ),
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="pagination",
     *                 type="object",
     *                 @OA\Property(
     *                     property="current_page",
     *                     type="integer",
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="last_page",
     *                     type="integer",
     *                     example=5
     *                 ),
     *                 @OA\Property(
     *                     property="per_page",
     *                     type="integer",
     *                     example=15
     *                 ),
     *                 @OA\Property(
     *                     property="total",
     *                     type="integer",
     *                     example=75
     *                 ),
     *                 @OA\Property(
     *                     property="from",
     *                     type="integer",
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="to",
     *                     type="integer",
     *                     example=15
     *                 ),
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
    }

    /**
     * Search Trips
     * @OA\Post(
     *     path="/api/trips/search",
     *     tags={"Trips"},
     *     summary="Search trips",
     *     description="Search for trips based on various criteria",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="trip_type",
     *                     type="string",
     *                     enum={"one_way", "round_trip", "special_discount"},
     *                     example="one_way",
     *                     description="Type of trip"
     *                 ),
     *                 @OA\Property(
     *                     property="departure_city_id",
     *                     type="integer",
     *                     example=1,
     *                     description="ID of departure city (required)"
     *                 ),
     *                 @OA\Property(
     *                     property="arrival_city_id",
     *                     type="integer",
     *                     example=2,
     *                     description="ID of arrival city (required, must be different from departure)"
     *                 ),
     *                 @OA\Property(
     *                     property="travel_date",
     *                     type="string",
     *                     format="date",
     *                     example="2025-08-15",
     *                     description="Travel date (required, must be today or future)"
     *                 ),
     *                 @OA\Property(
     *                     property="return_date",
     *                     type="string",
     *                     format="date",
     *                     example="2025-08-20",
     *                     description="Return date (required for round trips, optional for one-way)"
     *                 ),
     *                 @OA\Property(
     *                     property="passengers",
     *                     type="integer",
     *                     minimum=1,
     *                     maximum=50,
     *                     example=2,
     *                     description="Number of passengers (required, 1-50)"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="trips",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="id",
     *                         type="number",
     *                         example="1"
     *                     ),
     *                     @OA\Property(
     *                         property="from",
     *                         type="string",
     *                         example="Cairo"
     *                     ),
     *                     @OA\Property(
     *                         property="to",
     *                         type="string",
     *                         example="Alexandria"
     *                     ),
     *                     @OA\Property(
     *                         property="departure_time",
     *                         type="string",
     *                         example="08:00"
     *                     ),
     *                     @OA\Property(
     *                         property="arrival_time",
     *                         type="string",
     *                         example="11:30"
     *                     ),
     *                     @OA\Property(
     *                         property="date",
     *                         type="string",
     *                         example="2025-08-15"
     *                     ),
     *                     @OA\Property(
     *                         property="price",
     *                         type="number",
     *                         example=220.00
     *                     ),
     *                     @OA\Property(
     *                         property="amenities",
     *                         type="array",
     *                         @OA\Items(type="string"),
     *                         example={"Wi-Fi", "Snacks"}
     *                     ),
     *                     @OA\Property(
     *                         property="available_seats",
     *                         type="integer",
     *                         example=17
     *                     ),
     *                     @OA\Property(
     *                         property="notes",
     *                         type="string",
     *                         example="Comfortable journey with modern amenities"
     *                     ),
     *                     @OA\Property(
     *                         property="trip_type",
     *                         type="string",
     *                         example="one_way"
     *                     ),
     *                     @OA\Property(
     *                         property="additional_notes",
     *                         type="string",
     *                         example="Professional driver and clean vehicle"
     *                     ),
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="search_criteria",
     *                 type="object",
     *                 @OA\Property(
     *                     property="trip_type",
     *                     type="string",
     *                     example="one_way"
     *                 ),
     *                 @OA\Property(
     *                     property="departure_city_id",
     *                     type="integer",
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="arrival_city_id",
     *                     type="integer",
     *                     example=2
     *                 ),
     *                 @OA\Property(
     *                     property="travel_date",
     *                     type="string",
     *                     example="2025-08-15"
     *                 ),
     *                 @OA\Property(
     *                     property="return_date",
     *                     type="string",
     *                     nullable=true,
     *                     example=null
     *                 ),
     *                 @OA\Property(
     *                     property="passengers",
     *                     type="integer",
     *                     example=2
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="The given data was invalid."
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="departure_city_id",
     *                     type="array",
     *                     @OA\Items(type="string"),
     *                     example={"Departure city is required."}
     *                 ),
     *                 @OA\Property(
     *                     property="arrival_city_id",
     *                     type="array",
     *                     @OA\Items(type="string"),
     *                     example={"Arrival city is required."}
     *                 ),
     *             )
     *         )
     *     )
     * )
     */
    public function search(Request $request)
    {
    }

    /**
     * Get Detail Trip
     * @OA\Get (
     *     path="/api/trips/{id}",
     *     tags={"Trips"},
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *          @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  example="Data has been loaded successfully."
     *              ),
     *              @OA\Property(
     *                  property="status",
     *                  type="boolean",
     *                  example="true"
     *              ),
     *              @OA\Property(
     *                  property="trip",
     *                  type="object",
     *                  @OA\Property(
     *                      property="id",
     *                      type="integer",
     *                      example="1"
     *                  ),
     *                  @OA\Property(
     *                      property="trip_name",
     *                      type="string",
     *                      example="Cairo to Alexandria"
     *                  ),
     *                  @OA\Property(
     *                      property="trip_type",
     *                      type="string",
     *                      example="one_way"
     *                  ),
     *                  @OA\Property(
     *                      property="trip_type_label",
     *                      type="string",
     *                      example="One Way"
     *                  ),
     *                  @OA\Property(
     *                      property="from",
     *                      type="string",
     *                      example="Cairo"
     *                  ),
     *                  @OA\Property(
     *                      property="to",
     *                      type="string",
     *                      example="Alexandria"
     *                  ),
     *                  @OA\Property(
     *                      property="travel_date",
     *                      type="string",
     *                      example="2025-08-15"
     *                  ),
     *                  @OA\Property(
     *                      property="return_date",
     *                      type="string",
     *                      nullable=true,
     *                      example=null
     *                  ),
     *                  @OA\Property(
     *                      property="departure_time",
     *                      type="string",
     *                      example="08:00"
     *                  ),
     *                  @OA\Property(
     *                      property="arrival_time",
     *                      type="string",
     *                      example="11:30"
     *                  ),
     *                  @OA\Property(
     *                      property="price",
     *                      type="number",
     *                      example=220.00
     *                  ),
     *                  @OA\Property(
     *                      property="amenities",
     *                      type="array",
     *                      @OA\Items(type="string"),
     *                      example={"Wi-Fi", "Snacks"}
     *                  ),
     *                  @OA\Property(
     *                      property="available_seats",
     *                      type="integer",
     *                      example=17
     *                  ),
     *                  @OA\Property(
     *                      property="total_seats",
     *                      type="integer",
     *                      example=20
     *                  ),
     *                  @OA\Property(
     *                      property="additional_notes",
     *                      type="string",
     *                      example="Professional driver and clean vehicle"
     *                  ),
     *                  @OA\Property(
     *                      property="occupancy_rate",
     *                      type="number",
     *                      example=85.0
     *                  ),
     *                  @OA\Property(
     *                      property="occupancy_status",
     *                      type="string",
     *                      example="High"
     *                  ),
     *              ),
     *         )
     *     )
     * )
     */
    public function show(mixed $trip)
    {
    }

    /**
     * Book Trip
     * @OA\Post(
     *     path="/api/trips/book",
     *     tags={"Trips"},
     *     summary="Book a trip",
     *     description="Create a booking for a specific trip",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="trip_id",
     *                     type="number",
     *                     example=1,
     *                 ),
     *                 @OA\Property(
     *                     property="booking_date",
     *                     type="string",
     *                     example="2023-06-15",
     *                 ),
     *                 @OA\Property(
     *                     property="adults",
     *                     type="number",
     *                     example=2,
     *                 ),
     *                 @OA\Property(
     *                     property="children",
     *                     type="number",
     *                     example=1,
     *                 ),
     *                 @OA\Property(
     *                     property="infants",
     *                     type="number",
     *                     example=0,
     *                 ),
     *                 @OA\Property(
     *                     property="special_requests",
     *                     type="string",
     *                     example="Vegetarian meals preferred",
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Trip booked successfully"
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="booking_id",
     *                     type="number",
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="total_price",
     *                     type="number",
     *                     example=2400.00
     *                 ),
     *             )
     *         )
     *     )
     * )
     */
    public function book(Request $request)
    {
    }
} 