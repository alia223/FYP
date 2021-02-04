<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Rule;

class RuleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index() {
        return view('admin.controlPanel');
    }

    public function store(Request $request) {
                //
        // form validation
        $rules = $this->validate(request(), [
            'brand_colour' => 'required|string',
            'text_colour' => 'required|string',
            'club_start' => 'required|String',
            'club_end' => 'required|string',
            'club_duration_step' => 'required|string',
            'booking_interval' => 'required|string',
            'room_capacity' => 'required|string'
        ]);
        $rules = Rule::all();
        foreach($rules as $r) {
            $r->delete();
        }
        $rules = new Rule;
        $brand_colour = $request->input('brand_colour');
        $text_colour = $request->input('text_colour');
        $club_start = $request->input('club_start');
        $club_end = $request->input('club_end');
        $club_duration_step = $request->input('club_duration_step');
        $booking_interval = $request->input('booking_interval');
        $room_capacity = $request->input('room_capacity');
        $rules->brand_colour = $brand_colour;
        $rules->text_colour = $text_colour;
        $rules->club_start = $club_start;
        $rules->club_end = $club_end;
        $rules->club_duration_step = $club_duration_step;
        $rules->booking_interval = $booking_interval;
        $rules->room_capacity = $room_capacity;
        $rules->save();
        return view('admin.controlPanel');
    }
}
