<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\VehicleResource;
use App\Models\Visa\Vehicle;
use App\Traits\Response\HasApiResponse;

class VehicleController extends Controller
{
    use HasApiResponse;

    public function index()
    {
        return $this->send(VehicleResource::collection(Vehicle::where('is_active', true)->get()));
    }
}
