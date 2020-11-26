<?php

namespace App\Http\Controllers;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Gate;

class BookingController extends Controller
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
            return view('bookings.index', array('bookings'=>$bookings));
        }
        return view('bookings.adminindex', array('bookings'=>$bookings));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('bookings.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        // form validation
        $booking = $this->validate(request(), [
        'start_time' => 'required|date_format:H:i',
        'end_time' => 'required|date_format:H:i',
        'hiddenDay' => 'required|string',
        'hiddenMonth' => 'required|string',
        'hiddenYear' => 'required|string'
        ]);
        // create a Booking object and set its values from the input
        $booking = new Booking;
        $booking->userid = Auth::id();
        $booking->name = Auth::user()->name;
        $booking->start_time = $request->input('start_time');
        $booking->end_time = $request->input('end_time');
        $day = $request->input('hiddenDay');
        $month = $request->input('hiddenMonth');
        $year = $request->input('hiddenYear');
        error_log($day);
        error_log($month);
        error_log($year);
        $date = $day."-".$month."-".$year;
        $time = strtotime($date);
        $newformat = date('Y-m-d',$time);
        error_log($newformat);
        $booking->booking_date = $newformat;
        // save the Booking object
        $booking->save();
        // generate a redirect HTTP response with a success message
        return back()->with('success', 'Booking has been added');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $booking = Booking::find($id);
        return view('bookings.show',compact('booking'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $booking = Booking::find($id);
        return view('bookings.edit',compact('booking'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $booking = Booking::find($id);
        $this->validate(request(), [
            'booking_date' => 'required|string',
            'start_time' => 'required|string',
            'end_time' => 'required|string',
        ]);
        $booking->booking_date = $request->input('booking_date');
        $booking->start_time = $request->input('start_time');
        $booking->end_time = $request->input('end_time');
        $booking->save();
        return redirect('bookings')->with('success','Booking has been updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //

            $booking = Booking::find($id);
            $booking->delete();
            return redirect('bookings')->with('success','Booking has been deleted');
        
    }
}