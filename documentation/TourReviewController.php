<?php

namespace Documentation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Api\TourReviewResource;
use App\Models\TourReview;
use App\Traits\Response\HasApiResponse;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Http\JsonResponse;
use App\Services\Query\QueryBuilder;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *  schema="TourReview",
 *  title="Tour Review Schema",
 *  description="Tour review data structure",
 *  @OA\Property(
 *      property="id",
 *      type="integer",
 *      description="Unique identifier for the review",
 *      example=1
 *  ),
 *  @OA\Property(
 *      property="rate",
 *      type="number",
 *      format="float",
 *      description="Rating from 0 to 5",
 *      minimum=0,
 *      maximum=5,
 *      example=4.5
 *  ),
 *  @OA\Property(
 *      property="content",
 *      type="string",
 *      description="Review content/comment",
 *      maxLength=500,
 *      example="This tour was absolutely amazing! The guide was knowledgeable and the experience was unforgettable."
 *  ),
 *  @OA\Property(
 *      property="reviewer_name",
 *      type="string",
 *      description="Name of the person who wrote the review",
 *      maxLength=255,
 *      example="John Doe"
 *  ),
 *  @OA\Property(
 *      property="created_at",
 *      type="string",
 *      format="date-time",
 *      description="When the review was created",
 *      example="2024-12-01T10:30:00.000000Z"
 *  ),
 *  @OA\Property(
 *      property="updated_at",
 *      type="string",
 *      format="date-time",
 *      description="When the review was last updated",
 *      example="2024-12-01T10:30:00.000000Z"
 *  ),
 *  @OA\Property(
 *      property="tour",
 *      type="object",
 *      description="Associated tour information",
 *      @OA\Property(
 *          property="id",
 *          type="integer",
 *          example=1
 *      ),
 *      @OA\Property(
 *          property="title",
 *          type="string",
 *          example="Cairo Pyramids Tour"
 *      ),
 *      @OA\Property(
 *          property="slug",
 *          type="string",
 *          example="cairo-pyramids-tour"
 *      ),
 *      @OA\Property(
 *          property="featured_image",
 *          type="string",
 *          example="https://example.com/pyramids.jpg"
 *      )
 *  )
 * )
 * 
 * @OA\Schema(
 *  schema="TourReviewList",
 *  title="Tour Review List Response",
 *  description="Paginated list of tour reviews",
 *  @OA\Property(
 *      property="current_page",
 *      type="integer",
 *      example=1
 *  ),
 *  @OA\Property(
 *      property="data",
 *      type="array",
 *      @OA\Items(ref="#/components/schemas/TourReview")
 *  ),
 *  @OA\Property(
 *      property="total",
 *      type="integer",
 *      example=50
 *  ),
 *  @OA\Property(
 *      property="per_page",
 *      type="integer",
 *      example=15
 *  ),
 *  @OA\Property(
 *      property="last_page",
 *      type="integer",
 *      example=4
 *  )
 * )
 * 
 * @OA\Schema(
 *  schema="TourReviewResponse",
 *  title="Tour Review Response",
 *  description="Standard API response for tour reviews",
 *  @OA\Property(
 *      property="data",
 *      ref="#/components/schemas/TourReview"
 *  ),
 *  @OA\Property(
 *      property="message",
 *      type="string",
 *      example="Tour review retrieved successfully"
 *  ),
 *  @OA\Property(
 *      property="status",
 *      type="boolean",
 *      example=true
 *  )
 * )
 * 
 * @OA\Schema(
 *  schema="TourReviewCreateRequest",
 *  title="Tour Review Create Request",
 *  description="Request body for creating a new tour review",
 *  required={"rate","content","reviewer_name"},
 *  @OA\Property(
 *      property="rate",
 *      type="number",
 *      format="float",
 *      minimum=0,
 *      maximum=5,
 *      description="Rating from 0 to 5",
 *      example=4.5
 *  ),
 *  @OA\Property(
 *      property="content",
 *      type="string",
 *      maxLength=500,
 *      description="Review content/comment",
 *      example="This tour was absolutely amazing! The guide was knowledgeable and the experience was unforgettable."
 *  ),
 *  @OA\Property(
 *      property="reviewer_name",
 *      type="string",
 *      maxLength=255,
 *      description="Name of the person writing the review",
 *      example="John Doe"
 *  )
 * )
 */
class TourReviewController extends Controller
{
    use HasApiResponse;

    /**
     * Get List Tour Reviews
     * @OA\Get (
     *     path="/api/tour-reviews",
     *     tags={"Tour Reviews"},
     *     summary="Get list of tour reviews",
     *     description="Retrieve a paginated list of tour reviews with optional filtering",
     *     operationId="getTourReviews",
     *     @OA\Parameter(
     *         description="Filter reviews by tour ID",
     *         in="query",
     *         name="tour_id",
     *         required=false,
     *         @OA\Schema(type="integer"),
     *         example=1
     *     ),
     *     @OA\Parameter(
     *         description="Filter reviews by rating",
     *         in="query",
     *         name="rate",
     *         required=false,
     *         @OA\Schema(type="number"),
     *         example=5
     *     ),
     *     @OA\Parameter(
     *         description="Page number for pagination",
     *         in="query",
     *         name="page",
     *         required=false,
     *         @OA\Schema(type="integer", default=1),
     *         example=1
     *     ),
     *     @OA\Parameter(
     *         description="Number of items per page",
     *         in="query",
     *         name="page_limit",
     *         required=false,
     *         @OA\Schema(type="integer", default=15),
     *         example=10
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved tour reviews",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/TourReviewList"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Tour reviews retrieved successfully"
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=true
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Invalid parameters"
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
    }

    /**
     * Get Detail Tour Review
     * @OA\Get (
     *     path="/api/tour-reviews/{id}",
     *     tags={"Tour Reviews"},
     *     summary="Get specific tour review details",
     *     description="Retrieve detailed information about a specific tour review by ID",
     *     operationId="getTourReview",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         description="Tour review ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved tour review",
     *         @OA\JsonContent(ref="#/components/schemas/TourReviewResponse")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Review not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Review not found"
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=false
     *             )
     *         )
     *     )
     * )
     */
    public function show($id)
    {
    }

    /**
     * Create Tour Review
     * @OA\Post (
     *     path="/api/tour-reviews/{tour}",
     *     tags={"Tour Reviews"},
     *     summary="Create a new tour review",
     *     description="Create a new review for a specific tour. Requires authentication.",
     *     operationId="createTourReview",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         in="path",
     *         name="tour",
     *         required=true,
     *         description="Tour ID for which to create the review",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Tour review data",
     *         @OA\JsonContent(ref="#/components/schemas/TourReviewCreateRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Review created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/TourReview"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Review added successfully"
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=true
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Authentication required",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthenticated."
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
     *                 example="The given data was invalid."
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="rate",
     *                     type="array",
     *                     @OA\Items(type="string"),
     *                     example={"The rate field is required."}
     *                 ),
     *                 @OA\Property(
     *                     property="content",
     *                     type="array",
     *                     @OA\Items(type="string"),
     *                     example={"The content field is required."}
     *                 ),
     *                 @OA\Property(
     *                     property="reviewer_name",
     *                     type="array",
     *                     @OA\Items(type="string"),
     *                     example={"The reviewer name field is required."}
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tour not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Tour not found"
     *             )
     *         )
     *     )
     * )
     */
    public function store()
    {
    }
}
