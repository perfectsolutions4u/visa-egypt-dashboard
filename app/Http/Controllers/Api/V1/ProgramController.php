<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ProgramResource;
use App\Models\Tour;
use App\Traits\Response\HasApiResponse;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    use HasApiResponse;

    public function index(Request $request)
    {
        $query = Tour::query()
            ->with(['destinations', 'days', 'options'])
            ->orderBy('display_order')
            ->orderBy('id');

        if ($request->boolean('best_seller')) {
            $query->where('featured', true);
        }

        $tours = $query->get();
        $this->markFavorites($tours, $request);

        return $this->send(ProgramResource::collection($tours));
    }

    public function show(Request $request, string $slug)
    {
        $tour = Tour::query()
            ->with(['destinations', 'days', 'options'])
            ->where('slug', $slug)
            ->firstOrFail();

        $this->markFavorites(collect([$tour]), $request);

        return $this->send(new ProgramResource($tour));
    }

    private function markFavorites($tours, Request $request): void
    {
        $client = $request->user('sanctum') ?? auth('sanctum')->user();
        if (! $client || ! method_exists($client, 'toursWishlist')) {
            return;
        }

        $ids = $client->toursWishlist()->pluck('tours.id')->all();
        foreach ($tours as $tour) {
            $tour->setAttribute('is_favorited', in_array($tour->id, $ids, true));
        }
    }
}
