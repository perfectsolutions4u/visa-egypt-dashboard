<?php

namespace App\Http\Controllers\Dashboard;

use App\DataTables\HotelDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\HotelRequest;
use App\Models\Amenity;
use App\Models\Hotel;
use App\Models\Destination;

class HotelController extends Controller
{

    public function index(HotelDataTable $dataTable)
    {
        return $dataTable->render('dashboard.hotels.index');
    }

    public function store(HotelRequest $request)
    {
        $hotel = Hotel::create($request->getSanitized());
        $hotel->seo()->create($request->get('seo'));
        $hotel->amenities()->attach($request->get('amenities'));
        session()->flash('message', 'Hotel Created Successfully!');
        session()->flash('type', 'success');
        return redirect()->route('dashboard.hotels.edit', $hotel);
    }

    public function create()
    {
        $amenities = Amenity::all();
        $destinations = Destination::all();
        return view('dashboard.hotels.create', compact('amenities', 'destinations'));
    }

    public function show(Hotel $hotel)
    {
        //
    }


    public function edit(Hotel $hotel)
    {
        $amenities = Amenity::all();
        $destinations = Destination::all();
        return view('dashboard.hotels.edit', compact('hotel', 'amenities', 'destinations'));
    }


    public function update(HotelRequest $request, Hotel $hotel)
    {
        $hotel->update($request->getSanitized());
        $hotel->amenities()->sync($request->get('amenities'));
        $hotel->seo ?
            $hotel->seo->update($request->get('seo')) :
            $hotel->seo()->create($request->get('seo'));
        session()->flash('message', 'Hotel Updated Successfully!');
        session()->flash('type', 'success');
        return back();
    }


    public function destroy(Hotel $hotel)
    {
        $hotel->delete();
        return response()->json([
            'message' => 'Hotel Deleted Successfully!'
        ]);
    }
}
