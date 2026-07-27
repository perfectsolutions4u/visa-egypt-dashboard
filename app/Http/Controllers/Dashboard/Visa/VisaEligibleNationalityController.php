<?php

namespace App\Http\Controllers\Dashboard\Visa;

use App\DataTables\Visa\VisaEligibleNationalityDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Visa\VisaEligibleNationalityRequest;
use App\Models\Visa\VisaEligibleNationality;
use App\Services\Visa\VisaOnArrivalContentService;

class VisaEligibleNationalityController extends Controller
{
    public function index(VisaEligibleNationalityDataTable $dataTable, VisaOnArrivalContentService $content)
    {
        $content->seedDefaultNationalitiesIfEmpty();

        return $dataTable->render('dashboard.visa.visa-nationalities.index');
    }

    public function create()
    {
        return view('dashboard.visa.visa-nationalities.create');
    }

    public function store(VisaEligibleNationalityRequest $request)
    {
        $nationality = VisaEligibleNationality::create($request->getSanitized());

        session()->flash('message', 'Eligible nationality created successfully!');
        session()->flash('type', 'success');

        return redirect()->route('dashboard.visa-nationalities.edit', $nationality);
    }

    public function edit(VisaEligibleNationality $visa_nationality)
    {
        return view('dashboard.visa.visa-nationalities.edit', [
            'nationality' => $visa_nationality,
        ]);
    }

    public function update(VisaEligibleNationalityRequest $request, VisaEligibleNationality $visa_nationality)
    {
        $visa_nationality->update($request->getSanitized());

        session()->flash('message', 'Eligible nationality updated successfully!');
        session()->flash('type', 'success');

        return back();
    }

    public function destroy(VisaEligibleNationality $visa_nationality)
    {
        $visa_nationality->delete();

        return response()->json([
            'message' => 'Eligible nationality deleted successfully!',
        ]);
    }
}
