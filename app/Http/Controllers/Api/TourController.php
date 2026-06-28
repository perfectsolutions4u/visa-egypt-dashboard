<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\TourResource;
use App\Models\Tour;
use App\Services\Cache\AppCache;
use App\Services\Query\QueryBuilder;
use App\Traits\Response\HasApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TourController extends Controller
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
        $queryBuilder = new QueryBuilder(new Tour, $request);
        $tours = $queryBuilder->build()->paginate();
        $collection = TourResource::collection($tours->getCollection());
        $tours->setCollection(collect($collection));
        return $this->send($tours);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param string $slug
     * @return JsonResponse
     */
    public function show(Request $request, string $slug)
    {
        $request->merge([
            'slug' => $slug
        ]);
        $queryBuilder = new QueryBuilder(new Tour, $request);
        $tour = $queryBuilder->build()->firstOrFail();
        return $this->send(new TourResource($tour));
    }


    /**
     * Display the stats of tours.
     * @return JsonResponse
     */
    public function stats()
    {
        $key = 'tour_stats';

        if (AppCache::has($key)) {
            $pricing = AppCache::get($key, ['min_price' => 0, 'max_price' => 11000]);
        } else {
            $tours = new Tour;
            $pricing = \DB::table($tours->getTable())
                ->selectRaw('min(adult_price) as min_price ,max(adult_price) as max_price')
                ->first();
            AppCache::put($key, $pricing, now()->addDay());
        }
        return $this->send([
            'pricing' => $pricing,
        ]);
    }
}
