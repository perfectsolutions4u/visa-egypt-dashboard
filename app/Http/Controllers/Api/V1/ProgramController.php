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
            ->with(['destinations', 'days'])
            ->orderBy('display_order')
            ->orderBy('id');

        if ($request->boolean('best_seller')) {
            $query->where('featured', true);
        }

        return $this->send(ProgramResource::collection($query->get()));
    }

    public function show(string $slug)
    {
        $tour = Tour::query()
            ->with(['destinations', 'days'])
            ->where('slug', $slug)
            ->firstOrFail();

        return $this->send(new ProgramResource($tour));
    }
}
