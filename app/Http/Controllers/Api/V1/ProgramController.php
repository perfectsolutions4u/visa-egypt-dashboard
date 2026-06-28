<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ProgramResource;
use App\Models\Visa\Program;
use App\Traits\Response\HasApiResponse;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    use HasApiResponse;

    public function index(Request $request)
    {
        $query = Program::where('is_active', true)->orderBy('sort_order');

        if ($request->boolean('best_seller')) {
            $query->where('is_best_seller', true);
        }

        return $this->send(ProgramResource::collection($query->get()));
    }

    public function show(Program $program)
    {
        abort_if(! $program->is_active, 404);

        return $this->send(new ProgramResource($program));
    }
}
