<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TourReviewRequest;
use App\Http\Resources\Api\TourReviewResource;
use App\Models\TourReview;
use App\Services\Query\QueryBuilder;
use App\Traits\Response\HasApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TourReviewController extends Controller
{
    use HasApiResponse;

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function index(Request $request)
    {
        $queryBuilder = new QueryBuilder(new TourReview, $request);
        $tourReviews = $queryBuilder->build()->paginate();
        $collection = TourReviewResource::collection($tourReviews->getCollection());
        $tourReviews->setCollection(collect($collection));
        return $this->send($tourReviews);
    }

    /**
     * Display the specified resource.
     *
     * @param TourReview $tourReview
     * @return JsonResponse
     */
    public function show(TourReview $tourReview)
    {
        $tourReview->load('tour');
        return $this->send(new TourReviewResource($tourReview));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param TourReviewRequest $request
     * @return JsonResponse
     */
    public function store(TourReviewRequest $request)
    {
        try {
            $review = TourReview::create($request->getSanitized());
            
            // Update tour statistics
            $review->tour->increment('rates', $review->rate);
            $review->tour->increment('reviews_number');
            
            // Load the tour relationship for response
            $review->load('tour');
            
            return $this->send(
                data: new TourReviewResource($review),
                message: __('messages.tour.reviews.added'),
                statusCode: 201
            );
        } catch (Exception $e) {
            return $this->send(
                message: 'Error creating review: ' . $e->getMessage(),
                statusCode: 500
            );
        }
    }
}
