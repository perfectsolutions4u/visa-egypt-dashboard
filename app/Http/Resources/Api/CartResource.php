<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\JsonResource;
use App\Models\CartItem;
use App\Models\CartRental;
use App\Models\CartHotelRoomBooking;
use Carbon\Carbon;


/**
 * @property CartItem|CartRental|CartHotelRoomBooking $resource
 */
class CartResource extends JsonResource
{
    public function toArray($request): array
    {
        $itemType = $this->resource->item_type;

        $baseData = [
            'type' => $itemType,
            'id' => $this->resource->id,
        ];

        // Add common fields for tour and rental
        if (in_array($itemType, ['tour', 'rental'])) {
            $baseData['adults'] = $this->resource->adults;
            $baseData['children'] = $this->resource->children;
        }

        return array_merge($baseData, $this->{str_replace('_', '', $itemType)}());
    }

    private function tour(): array
    {
        return [
            'tour' => new TourResource($this->resource->tour),
            'options' => TourOptionResource::collection($this->resource->options()),
            'infants' => $this->resource->infants,
            'start_date' => $this->resource->start_date,
        ];
    }

    private function rental(): array
    {
        return [
            'car_image' => "https://cdn2.rcstatic.com/images/car_images_b/web/toyota/corolla_lrg.jpg",
            'pickup' => new LocationResource($this->resource->pickup),
            'destination' => new LocationResource($this->resource->destination),
            'stops' => $this->resource->stops,
            'pickup_date' => $this->resource->pickup_date->toDateString(),
            'car_route_price' => $this->resource->car_route_price,
            'car_type' => $this->resource->car_type,
            'oneway' => $this->resource->oneway,
            'pickup_time' => $this->resource->pickup_time ? Carbon::parse($this->resource->pickup_time)
                ->format('H:i') : $this->resource->pickup_time,
        ];
    }

    private function hotelroombooking(): array
    {
        return [
            'hotel' => $this->resource->hotel ? [
                'id' => $this->resource->hotel->id,
                'name' => $this->resource->hotel->name,
                'slug' => $this->resource->hotel->slug ?? '',
                'featured_image' => $this->resource->hotel->featured_image,
            ] : null,
            'room' => $this->resource->room ? [
                'id' => $this->resource->room->id,
                'name' => $this->resource->room->name,
                'description' => $this->resource->room->description,
                'featured_image' => $this->resource->room->featured_image,
                'night_price' => $this->resource->room->night_price,
                'extra_bed_available' => $this->resource->room->extra_bed_available,
                'extra_bed_price' => $this->resource->room->extra_bed_price,
                'max_extra_beds' => $this->resource->room->max_extra_beds,
                'total_capacity' => $this->resource->room->getTotalCapacity(),
            ] : null,
            'name' => $this->resource->name,
            'email' => $this->resource->email,
            'phone' => $this->resource->phone,
            'nationality' => $this->resource->nationality,
            'start_date' => $this->resource->start_date->toDateString(),
            'end_date' => $this->resource->end_date->toDateString(),
            'guests_count' => $this->resource->guests_count,
            'extra_beds_count' => $this->resource->extra_beds_count ?? 0,
            'extra_beds_total_price' => $this->resource->extra_beds_total_price ?? 0,
            'total_price' => $this->resource->total_price ?? 0,
            'status' => $this->resource->status,
            'nights' => $this->resource->start_date->diffInDays($this->resource->end_date),
        ];
    }
}
