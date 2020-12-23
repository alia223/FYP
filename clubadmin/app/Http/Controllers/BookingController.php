<?php

namespace App\Http\Controllers;
use App\Models\Booking;
use App\Models\ActivityLog;
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
            return view('bookings.upcomingBookings', array('bookings'=>$bookings));
        }
        return view('bookings.upcomingBookings', array('bookings'=>$bookings));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('bookings.createBooking');
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
        'booking_length' => 'required|string',
        'hiddenDay' => 'required|string',
        'hiddenMonth' => 'required|string',
        'hiddenYear' => 'required|string'
        ]);
        // create a Booking object and set its values from the input
        $booking = new Booking;
        $booking->userid = Auth::id();
        $booking->name = Auth::user()->name;
        $booking_length = "+".$request->input('booking_length')." minutes";
        $club_start_time = '15:30:00';
        $booking->start_time = $club_start_time;
        $booking->end_time = date("H:i:s", strtotime($booking_length, strtotime($club_start_time)));
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

        $activity = new ActivityLog;
        $activity->action = "Created a booking";
        $activity->booking_id = $booking->id;
        $activity->userid = Auth::id();
        $activity->user = Auth::user()->name;
        $activity->save();
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
        return view('bookings.showBooking',compact('booking'));
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
        return view('bookings.editBooking',compact('booking'));
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
        
        $activity = new ActivityLog;
        $activity->booking_id = $id;
        $activity->action = "Updated a booking";
        $activity->userid = Auth::id();
        $activity->user = Auth::user()->name;
        $activity->save();
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
            $activity = new ActivityLog;
            $activity->action = "Deleted a booking";
            $activity->booking_id = $id;
            $activity->userid = Auth::id();
            $activity->user = Auth::user()->name;
            $activity->save();
            return redirect('bookings')->with('success','Booking has been deleted');
        
    }
}