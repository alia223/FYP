<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\User;

class RoomController extends Controller
{
    //
    public function index() {
        $rooms = Room::all();
        return view('rooms.showRooms', compact('rooms'));
    }

    public function create() {
        $staffs = User::all()->where('clubstaff', 1);
        return view('rooms.createRoom', compact('staffs'));
    }

    public function store(Request $request) {
        // form validation
        $room = $this->validate(request(), [
            'name' => 'required|string',
            'staffid' => 'required|string'
        ]);
        $room = new Room;
        $room->room_name = $request->input('name');
        $room->staffid = $request->input('staffid');
        $room->save();
        $rooms = Room::all();
        return redirect('rooms');
    }

    public function update() {
        
    }

    public function destroy() {
        
    }
}
