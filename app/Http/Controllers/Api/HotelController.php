<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\HotelResource;
use App\Http\Resources\Api\RoomAvailabilityResource;
use App\Http\Requests\Api\RoomAvailabilityRequest;
use App\Models\Hotel;
use App\Services\Query\QueryBuilder;
use App\Traits\Response\HasApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HotelController extends Controller
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
        $queryBuilder = new QueryBuilder(new Hotel, $request);
        $hotels = $queryBuilder->build()->paginate();
        $collection = HotelResource::collection($hotels->getCollection());
        $hotels->setCollection(collect($collection));
        return $this->send($hotels);
    }

    /**
     * Display the specified resource.
     *
     * @param mixed $id
     * @return JsonResponse
     */
    public function show(mixed $id)
    {
        $hotel = Hotel::where(function ($query) use ($id) {
            $query->where('id', $id)->orWhere('slug', $id);
        })->firstOrFail();
        return $this->send(new HotelResource($hotel));
    }

    /**
     * Search for available rooms in hotels by city and dates
     *
     * @param RoomAvailabilityRequest $request
     * @return JsonResponse
     */
    public function searchAvailability(RoomAvailabilityRequest $request)
    {
        try {
            $data = $request->getSanitized();
            
            // Get all enabled hotels in the specified city
            $hotels = Hotel::where('enabled', true)
                ->byCity($data['city'])
                ->with(['amenities'])
                ->get();

            if ($hotels->isEmpty()) {
                return $this->send(
                    data: [],
                    message: "No hotels found in {$data['city']}",
                    statusCode: 404
                );
            }

            // Load available rooms for each hotel
            $hotelsWithAvailableRooms = $hotels->map(function ($hotel) use ($data) {
                return $hotel->loadAvailableRooms(
                    $data['check_in'], 
                    $data['check_out']
                );
            })->filter(function ($hotel) {
                // Only return hotels that have available rooms
                return $hotel->availableRooms->isNotEmpty();
            });

            if ($hotelsWithAvailableRooms->isEmpty()) {
                return $this->send(
                    data: [],
                    message: "No available rooms found in {$data['city']} for the selected dates",
                    statusCode: 200
                );
            }

            $searchResults = RoomAvailabilityResource::collection($hotelsWithAvailableRooms);

            return $this->send(
                data: [
                    'search_criteria' => [
                        'city' => $data['city'],
                        'check_in' => $data['check_in'],
                        'check_out' => $data['check_out'],
                        'nights' => \Carbon\Carbon::parse($data['check_in'])->diffInDays($data['check_out'])
                    ],
                    'total_hotels' => $hotelsWithAvailableRooms->count(),
                    'hotels' => $searchResults
                ],
                message: "Found {$hotelsWithAvailableRooms->count()} hotels with available rooms"
            );

        } catch (\Exception $e) {
            return $this->send(
                message: 'Error searching for available rooms: ' . $e->getMessage(),
                statusCode: 500
            );
        }
    }
}
