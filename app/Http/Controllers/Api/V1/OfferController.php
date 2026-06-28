<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\OfferResource;
use App\Models\Visa\Offer;
use App\Traits\Response\HasApiResponse;

class OfferController extends Controller
{
    use HasApiResponse;

    public function index()
    {
        return $this->send(OfferResource::collection(
            Offer::where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('active_to')->orWhere('active_to', '>=', now());
                })
                ->get()
        ));
    }
}
