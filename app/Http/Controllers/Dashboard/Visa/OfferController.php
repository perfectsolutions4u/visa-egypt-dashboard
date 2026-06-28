<?php

namespace App\Http\Controllers\Dashboard\Visa;

use App\DataTables\Visa\OfferDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Visa\OfferRequest;
use App\Models\Visa\Offer;

class OfferController extends Controller
{
    public function index(OfferDataTable $dataTable)
    {
        return $dataTable->render('dashboard.visa.offers.index');
    }

    public function create()
    {
        return view('dashboard.visa.offers.create');
    }

    public function store(OfferRequest $request)
    {
        $offer = Offer::create($request->getSanitized());
        session()->flash('message', 'Offer Created Successfully!');
        session()->flash('type', 'success');

        return redirect()->route('dashboard.offers.edit', $offer);
    }

    public function edit(Offer $offer)
    {
        return view('dashboard.visa.offers.edit', compact('offer'));
    }

    public function update(OfferRequest $request, Offer $offer)
    {
        $offer->update($request->getSanitized());
        session()->flash('message', 'Offer Updated Successfully!');
        session()->flash('type', 'success');

        return back();
    }

    public function destroy(Offer $offer)
    {
        $offer->delete();

        return response()->json([
            'message' => 'Offer Deleted Successfully!',
        ]);
    }
}
