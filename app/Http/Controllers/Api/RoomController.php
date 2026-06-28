<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Api\RoomResource;
use App\Models\Room;
use App\Traits\Response\HasApiResponse;
use Illuminate\Http\JsonResponse;
use App\Services\Query\QueryBuilder;
use Exception;

class RoomController extends Controller
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
        $queryBuilder = new QueryBuilder(new Room, $request);
        $rooms = $queryBuilder->build()->paginate();
        $collection = RoomResource::collection($rooms->getCollection());
        $rooms->setCollection(collect($collection));
        return $this->send($rooms);
    }

    /**
     * Display the specified resource.
     *
     * @param  mixed  $id
     * @return JsonResponse
     */
    public function show(mixed $id)
    {
        $room = Room::where(function ($query) use ($id) {
            $query->where('id', $id)->orWhere('slug', $id);
        })->firstOrFail();
        return $this->send(new RoomResource($room));
    }
}
