<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\BookedPupil;
use App\Models\Pupil;
use Gate;

class HomeController extends Controller
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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if(Gate::denies('admin') && Gate::denies('clubstaff')) {
            $booked_pupils = BookedPupil::all()->where('parent_id', Auth::id())
            ->where('booking_date', date('Y-m-d', strtotime("today")))
            ->where('checked_in', '!=', NULL);
            $pupils = array();
            foreach($booked_pupils as $booked_pupil) {
                array_push($pupils, Pupil::find($booked_pupil->pupil_id));
            }
            return view('home', compact('pupils', 'booked_pupils'));
        }
        return view('home');
    }
}
