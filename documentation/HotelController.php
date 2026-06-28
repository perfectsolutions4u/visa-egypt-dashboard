<?php

namespace Documentation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Api\HotelResource;
use App\Models\Hotel;
use App\Traits\Response\HasApiResponse;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Http\JsonResponse;
use App\Services\Query\QueryBuilder;
use OpenApi\Annotations as OA;

class HotelController extends Controller
{
    use HasApiResponse;

    /**
     * Get List Hotel
     * @OA\Get (
     *     path="/api/hotels",
     *     tags={"Hotels"},
     *    @OA\Parameter(
     *         description="Filter Hotels By Name",
     *         in="query",
     *         name="name",
     *         required=false,
     *         @OA\Schema(type="string"),
     *     ),
     *    @OA\Parameter(
     *         description="this key is used to select the columns the need to return instead of return all columns example: id,created_at,updated_at",
     *         in="query",
     *         name="columns",
     *         required=false,
     *         @OA\Schema(type="string"),
     *     ),
     *    @OA\Parameter(
     *         description="this key is used to serialize related objects by includes the objects name using comma separated example: type,user",
     *         in="query",
     *         name="includes",
     *         required=false,
     *         @OA\Schema(type="string"),
     *     ),
     *    @OA\Parameter(
     *         description="this key is used to select the page number that need to return example: page=2",
     *         in="query",
     *         name="page",
     *         required=false,
     *         @OA\Schema(type="number"),
     *     ),
     *    @OA\Parameter(
     *         description="this key is used to the max result that should return per page example: limit=25",
     *         in="query",
     *         name="page_limit",
     *         required=false,
     *         @OA\Schema(type="number"),
     *     ),
     *    @OA\Parameter(
     *         description="this key is used to sort the result that return per page example: order_by=id,asc|id,desc",
     *         in="query",
     *         name="order_by",
     *         required=false,
     *         @OA\Schema(type="string"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *         @OA\Property(
     *             property="current_page",
     *             type="number",
     *             example=1
     *             ),
     *         @OA\Property(
     *             property="from",
     *             type="number",
     *             example=1
     *             ),
     *         @OA\Property(
     *             property="last_page",
     *             type="number",
     *             example=10
     *             ),
     *         @OA\Property(
     *             property="next_page_url",
     *             type="string",
     *             example="http://baseURL/hotels?page=2"
     *             ),
     *         @OA\Property(
     *             property="path",
     *             type="string",
     *             example="http://baseURL/hotels"
     *             ),
     *         @OA\Property(
     *             property="per_page",
     *             type="number",
     *             example=15
     *             ),
     *         @OA\Property(
     *             property="prev_page_url",
     *             type="string",
     *             example="null"
     *             ),
     *         @OA\Property(
     *             property="to",
     *             type="number",
     *             example=15
     *             ),
     *         @OA\Property(
     *             property="total",
     *             type="number",
     *             example=350
     *             ),
     *             @OA\Property(
     *                 type="array",
     *                 property="data",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="id",
     *                         type="number",
     *                         example="1"
     *                     ),
     *                     @OA\Property(
     *                         property="updated_at",
     *                         type="string",
     *                         example="2023-12-11T09:25:53.000000Z"
     *                     ),
     *                     @OA\Property(
     *                         property="created_at",
     *                         type="string",
     *                         example="2023-12-11T09:25:53.000000Z"
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
    }

    /**
     * Get Detail Hotel
     * @OA\Get (
     *     path="/api/hotels/{id}",
     *     tags={"Hotels"},
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *                     @OA\Property(
     *                         property="id",
     *                         type="number",
     *                         example="1"
     *                     ),
     *                     @OA\Property(
     *                         property="updated_at",
     *                         type="string",
     *                         example="2023-12-11T09:25:53.000000Z"
     *                     ),
     *                     @OA\Property(
     *                         property="created_at",
     *                         type="string",
     *                         example="2023-12-11T09:25:53.000000Z"
     *                     )
     *         )
     *     )
     * )
     */
    public function show(mixed $id)
    {
    }

    /**
     * Search Hotel Availability
     * @OA\Post (
     *     path="/api/hotels/search-availability",
     *     tags={"Hotels"},
     *     summary="Search for available rooms in hotels by city and dates",
     *     description="Search for hotels with available rooms in a specific city for given check-in and check-out dates",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"check_in","check_out","city"},
     *             @OA\Property(
     *                 property="check_in",
     *                 type="string",
     *                 format="date",
     *                 description="Check-in date (YYYY-MM-DD format)",
     *                 example="2024-12-25"
     *             ),
     *             @OA\Property(
     *                 property="check_out",
     *                 type="string",
     *                 format="date",
     *                 description="Check-out date (YYYY-MM-DD format)",
     *                 example="2024-12-28"
     *             ),
     *             @OA\Property(
     *                 property="city",
     *                 type="string",
     *                 description="City name to search for hotels",
     *                 example="Cairo"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Hotels found with available rooms",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="search_criteria",
     *                     type="object",
     *                     @OA\Property(
     *                         property="city",
     *                         type="string",
     *                         example="Cairo"
     *                     ),
     *                     @OA\Property(
     *                         property="check_in",
     *                         type="string",
     *                         example="2024-12-25"
     *                     ),
     *                     @OA\Property(
     *                         property="check_out",
     *                         type="string",
     *                         example="2024-12-28"
     *                     ),
     *                     @OA\Property(
     *                         property="nights",
     *                         type="integer",
     *                         example=3
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="total_hotels",
     *                     type="integer",
     *                     example=2
     *                 ),
     *                 @OA\Property(
     *                     property="hotels",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(
     *                             property="id",
     *                             type="integer",
     *                             example=1
     *                         ),
     *                         @OA\Property(
     *                             property="name",
     *                             type="string",
     *                             example="Cairo Grand Hotel"
     *                         ),
     *                         @OA\Property(
     *                             property="stars",
     *                             type="integer",
     *                             example=5
     *                         ),
     *                         @OA\Property(
     *                             property="city",
     *                             type="string",
     *                             example="Cairo"
     *                         ),
     *                         @OA\Property(
     *                             property="address",
     *                             type="string",
     *                             example="123 Main Street, Cairo"
     *                         ),
     *                         @OA\Property(
     *                             property="featured_image",
     *                             type="string",
     *                             example="https://example.com/hotel-image.jpg"
     *                         ),
     *                         @OA\Property(
     *                             property="available_rooms",
     *                             type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(
     *                                     property="id",
     *                                     type="integer",
     *                                     example=1
     *                                 ),
     *                                 @OA\Property(
     *                                     property="name",
     *                                     type="string",
     *                                     example="Deluxe Room"
     *                                 ),
     *                                 @OA\Property(
     *                                     property="price",
     *                                     type="number",
     *                                     format="float",
     *                                     example=150.00
     *                                 ),
     *                                 @OA\Property(
     *                                     property="capacity",
     *                                     type="integer",
     *                                     example=2
     *                                 )
     *                             )
     *                         )
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Found 2 hotels with available rooms"
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=true
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No hotels found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items()
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="No hotels found in Cairo"
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=false
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Check-in date cannot be in the past"
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="check_in",
     *                     type="array",
     *                     @OA\Items(type="string"),
     *                     example={"Check-in date cannot be in the past"}
     *                 ),
     *                 @OA\Property(
     *                     property="check_out",
     *                     type="array",
     *                     @OA\Items(type="string"),
     *                     example={"Check-out date must be after check-in date"}
     *                 ),
     *                 @OA\Property(
     *                     property="city",
     *                     type="array",
     *                     @OA\Items(type="string"),
     *                     example={"City is required"}
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function searchAvailability()
    {
    }
}
