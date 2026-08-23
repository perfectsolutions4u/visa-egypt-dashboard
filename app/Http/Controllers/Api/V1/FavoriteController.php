<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ProgramResource;
use App\Models\Tour;
use App\Traits\Response\HasApiResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    use HasApiResponse;

    public function index(Request $request)
    {
        $tours = $request->user()
            ->toursWishlist()
            ->with(['destinations', 'days'])
            ->get();

        $tours->each(fn (Tour $tour) => $tour->setAttribute('is_favorited', true));

        return $this->send(ProgramResource::collection($tours));
    }

    public function ids(Request $request)
    {
        return $this->send([
            'ids' => $request->user()->toursWishlist()->pluck('tours.id')->values()->all(),
        ]);
    }

    public function toggle(Request $request, int $program)
    {
        $tour = Tour::query()->findOrFail($program);
        $request->user()->toursWishlist()->toggle([$tour->id]);

        $favorited = $request->user()->toursWishlist()->where('tours.id', $tour->id)->exists();

        return $this->send([
            'id' => $tour->id,
            'favorited' => $favorited,
        ]);
    }
}
