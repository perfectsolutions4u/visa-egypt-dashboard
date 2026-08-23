<?php

namespace App\Http\Controllers\Dashboard\Visa;

use App\DataTables\Visa\AdditionalServiceDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Visa\AdditionalServiceRequest;
use App\Models\Visa\AdditionalService;

class AdditionalServiceController extends Controller
{
    public function index(AdditionalServiceDataTable $dataTable)
    {
        return $dataTable->render('dashboard.visa.additional-services.index');
    }

    public function create()
    {
        return view('dashboard.visa.additional-services.create');
    }

    public function store(AdditionalServiceRequest $request)
    {
        $additionalService = AdditionalService::create($request->getSanitized());
        session()->flash('message', 'Additional Service Created Successfully!');
        session()->flash('type', 'success');

        return redirect()->route('dashboard.additional-services.edit', $additionalService);
    }

    public function edit(AdditionalService $additionalService)
    {
        return view('dashboard.visa.additional-services.edit', compact('additionalService'));
    }

    public function update(AdditionalServiceRequest $request, AdditionalService $additionalService)
    {
        $additionalService->update($request->getSanitized());
        session()->flash('message', 'Additional Service Updated Successfully!');
        session()->flash('type', 'success');

        return back();
    }

    public function destroy(AdditionalService $additionalService)
    {
        $additionalService->delete();

        return response()->json([
            'message' => 'Additional Service Deleted Successfully!',
        ]);
    }
}
