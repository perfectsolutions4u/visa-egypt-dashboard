<?php

namespace App\Http\Controllers\Dashboard;

use App\DataTables\RoomDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\RoomRequest;
use App\Models\Amenity;
use App\Models\Hotel;
use App\Models\Room;

class RoomController extends Controller
{

    public function index(RoomDataTable $dataTable)
    {
        return $dataTable->render('dashboard.rooms.index');
    }

    public function store(RoomRequest $request)
    {
        $room = Room::create($request->getSanitized());
        $room->seo()->create($request->get('seo'));
        session()->flash('message', 'Room Created Successfully!');
        session()->flash('type', 'success');
        return redirect()->route('dashboard.rooms.edit', $room);
    }

    public function create()
    {
        $hotels = Hotel::all();
        $amenities = Amenity::all();
        return view('dashboard.rooms.create', compact('hotels', 'amenities'));
    }

    public function show(Room $room)
    {
        //
    }

    public function edit(Room $room)
    {
        $hotels = Hotel::all();
        $amenities = Amenity::all();
        return view('dashboard.rooms.edit', compact('room', 'amenities', 'hotels'));
    }

    public function update(RoomRequest $request, Room $room)
    {
        $room->update($request->getSanitized());
        $room->seo ?
            $room->seo->update($request->get('seo')) :
            $room->seo()->create($request->get('seo'));
        session()->flash('message', 'Room Updated Successfully!');
        session()->flash('type', 'success');
        return back();
    }


    public function destroy(Room $room)
    {
        $room->delete();
        return response()->json([
            'message' => 'Room Deleted Successfully!'
        ]);
    }
}
