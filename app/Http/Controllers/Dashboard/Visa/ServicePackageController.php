<?php

namespace App\Http\Controllers\Dashboard\Visa;

use App\DataTables\Visa\ServicePackageDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Visa\ServicePackageRequest;
use App\Models\Visa\ServicePackage;

class ServicePackageController extends Controller
{
    public function index(ServicePackageDataTable $dataTable)
    {
        return $dataTable->render('dashboard.visa.service-packages.index');
    }

    public function create()
    {
        return view('dashboard.visa.service-packages.create');
    }

    public function store(ServicePackageRequest $request)
    {
        $servicePackage = ServicePackage::create($request->getSanitized());
        session()->flash('message', 'Service Package Created Successfully!');
        session()->flash('type', 'success');

        return redirect()->route('dashboard.service-packages.edit', $servicePackage);
    }

    public function edit(ServicePackage $servicePackage)
    {
        return view('dashboard.visa.service-packages.edit', compact('servicePackage'));
    }

    public function update(ServicePackageRequest $request, ServicePackage $servicePackage)
    {
        $servicePackage->update($request->getSanitized());
        session()->flash('message', 'Service Package Updated Successfully!');
        session()->flash('type', 'success');

        return back();
    }

    public function destroy(ServicePackage $servicePackage)
    {
        $servicePackage->delete();

        return response()->json([
            'message' => 'Service Package Deleted Successfully!',
        ]);
    }
}
