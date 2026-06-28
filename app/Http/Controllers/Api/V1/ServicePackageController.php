<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ServicePackageResource;
use App\Models\Visa\ServicePackage;
use App\Traits\Response\HasApiResponse;
use Illuminate\Http\Request;

class ServicePackageController extends Controller
{
    use HasApiResponse;

    public function index(Request $request)
    {
        $query = ServicePackage::where('is_active', true);

        if ($request->filled('service_type')) {
            $query->where('service_type', $request->service_type);
        }

        return $this->send(ServicePackageResource::collection($query->orderBy('price')->get()));
    }
}
