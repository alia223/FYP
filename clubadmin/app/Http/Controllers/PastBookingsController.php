<?php

namespace App\Http\Controllers;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Gate;

class PastBookingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $bookings = Booking::all();
        if(Gate::denies('admin')) {
            $bookings = $bookings->where('userid', Auth::id());
            return view('bookings.pastBookings', array('bookings'=>$bookings));
        }
        return view('bookings.pastBookings', array('bookings'=>$bookings));
    }
}