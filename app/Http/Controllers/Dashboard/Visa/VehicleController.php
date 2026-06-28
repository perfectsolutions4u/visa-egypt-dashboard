<?php

namespace App\Http\Controllers\Dashboard\Visa;

use App\DataTables\Visa\VehicleDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Visa\VehicleRequest;
use App\Models\Visa\Vehicle;

class VehicleController extends Controller
{
    public function index(VehicleDataTable $dataTable)
    {
        return $dataTable->render('dashboard.visa.vehicles.index');
    }

    public function create()
    {
        return view('dashboard.visa.vehicles.create');
    }

    public function store(VehicleRequest $request)
    {
        $vehicle = Vehicle::create($request->getSanitized());
        session()->flash('message', 'Vehicle Created Successfully!');
        session()->flash('type', 'success');

        return redirect()->route('dashboard.vehicles.edit', $vehicle);
    }

    public function edit(Vehicle $vehicle)
    {
        return view('dashboard.visa.vehicles.edit', compact('vehicle'));
    }

    public function update(VehicleRequest $request, Vehicle $vehicle)
    {
        $vehicle->update($request->getSanitized());
        session()->flash('message', 'Vehicle Updated Successfully!');
        session()->flash('type', 'success');

        return back();
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();

        return response()->json([
            'message' => 'Vehicle Deleted Successfully!',
        ]);
    }
}
