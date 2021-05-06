<?php

namespace App\Http\Controllers;
use App\Models\Booking;
use App\Models\Pupil;
use App\Models\User;
use App\Models\BookedPupil;
use App\Models\ClashedBooking;
use App\Models\ClashedPupil;
use App\Models\ActivityLog;
use App\Models\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Gate;
use DatePeriod;
use DateInterval;
use DB;
class BookingController extends Controller
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Get all bookings that belong to the logged in parent requesting the resource
        $bookings = Booking::withTrashed()->get();
        if(Gate::denies('admin') && Gate::denies('clubstaff')) {
            $bookings = Booking::all()->where('parent_id', Auth::id());
        }
        else if(!Gate::denies('clubstaff')) {
            $bookings = Booking::all();
        }
        return view('bookings.booking', compact('bookings'));
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {   
        $this->validate(request(), [
            'date' => 'required|string'
        ]);
        //Get the date that the parent wants to make a booking, and redirect parent to "create a booking" page with an array of their children
        //so they can select which children to book in
        $date = $request->input('date');
        $pupils = Pupil::all()->where('parent_id', Auth::id());
        return view('bookings.createBooking', compact('pupils'))->with('date', $date);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // form validation
        $booking = $this->validate(request(), [
            'booking_length' => 'required|string',
            'date' => 'required|string',
            'pupils' => 'required|array'
        ],
        [
            'booking_length.required' => 'Please select the duration of your booking.',
            'pupils.required' => 'Please select at least one child'
        ]);
        $club_start_time = Rule::all()->first()->club_start;
        $club_end_time = Rule::all()->first()->club_end;
        $booking_duration = $request->input('booking_length');
        $booking_end_time = date("H:i:s", strtotime("+$booking_duration minutes", strtotime($club_start_time)));
        if($booking_end_time > $club_end_time) {
            return back()->withErrors(['errors' => ['Booking duration exceeds limit. Please select one of the options displayed below.']]);
        }
        $booking_date = date('Y-m-d', strtotime($request->input('date')));
        //pupils associated with this booking
        $pupils_associated_with_booking = $request->input('pupils');
        //either set event_id to 1 as there are no previously existing events, or find the last eventid used for a booking and increment by 1
        $event_id = !empty(Booking::withTrashed()->orderBy('created_at', 'desc')->first()) ? Booking::withTrashed()->orderBy('created_at', 'desc')->first()->event_id + 1 : 1;
        //store booking details based on inputs
        $booking = new Booking;
        //each booking has an event id, this is mainly useful for when a repeat booking is made so that those bookings are linked together
        $booking->event_id = $event_id;
        $booking->parent_id = Auth::id();
        $booking->start_time = $club_start_time;
        $booking_end_time = $booking_end_time;
        $booking->end_time = $booking_end_time;
        $booking->duration = $booking_duration;
        $booking->booking_date = $booking_date;
        $booking->booking_day = date('w', strtotime($booking_date));
        //before saving the booking, get all bookings by all customers
        $bookings_to_check = Booking::all()->where('parent_id', Auth::id())->where('booking_date', $booking_date);
        $pupils_already_booked_in = BookedPupil::all()->where('booking_id','!=',$booking->id)->where('parent_id', $booking->parent_id)->where('booking_date', $booking->booking_date)->pluck('pupil_id')->toArray();
        foreach($request->input('pupils') as $pupil) {
            if(in_array($pupil, $pupils_already_booked_in)) {
                $pupil_already_booked_in = Pupil::where('id', $pupil)->first();
                return back()->withErrors(['errors' => ["$pupil_already_booked_in->first_name $pupil_already_booked_in->last_name"]]);
            }
        }
        $booking->save();
        $this->store_pupils_associated_with_booking($booking, $pupils_associated_with_booking, Auth::id());
        $this->log_activity("Created a booking");
        // generate a redirect HTTP response with a success message
        return redirect('bookings/'.$booking_date)->withSuccess('Booking created successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($date)
    {
        //parent has clicked on a date on the calendar, so show bookings that belong to only that parent, on that date
        //or if it is the admin/cklubstaff clicking on a date on the calendar, 
        //then show all bookings on that regardless of who the booking belongs to 
        $bookings = Booking::withTrashed()->where('booking_date', $date)->paginate(5);
        if(Gate::denies('admin') && Gate::denies('clubstaff')) {
            $bookings = Booking::where('parent_id', Auth::id())->where('booking_date', $date)->paginate(5);
        }
        else if(!Gate::denies('clubstaff')) {
            $bookings = Booking::where('booking_date', $date)->paginate(5);
        }
        $booked_pupils = BookedPupil::withTrashed()->get();
        $pupils = Pupil::all();
        return view('bookings.showBookings', compact('bookings', 'booked_pupils', 'pupils'))->with('date', $date);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //Find booking that is being requested to be edited
        $booking = Booking::find($id);
        $booked_pupils = BookedPupil::all()->where('booking_id', $id);
        if(Gate::denies('admin')) {
            $pupils = Pupil::all()->where('parent_id', Auth::id());
        }
        else {
            //Admin is requesting to edit a booking so cannot use Auth::id() to get parent id
            //Instead, find the parent associated with that booking by searching database
            $parent_id = User::all()->where('id', $booking->parent_id)->first()->id;
            $pupils = Pupil::all()->where('parent_id', $parent_id);
        }
        $bookings = Booking::all();
        return view('bookings.editBooking',compact('booking', 'pupils','booked_pupils', 'bookings'));
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
        $this->validate(request(), [
            'booking_length' => 'required|string',
            'pupils' => 'required|array'
        ]);
        $club_start_time = Rule::all()->first()->club_start;
        $club_end_time = Rule::all()->first()->club_end;
        $booking_duration = $request->input('booking_length');
        $booking_end_time = date("H:i:s", strtotime("+$booking_duration minutes", strtotime($club_start_time)));
        if($booking_end_time > $club_end_time) {
            return back()->withErrors(['errors' => ['Booking duration exceeds limit. Please select one of the options displayed below.']]);
        }
        //get pupils that user has selected
        $pupils_associated_with_booking = $request->input('pupils');
        //find booking which user is requesting to update
        $booking = Booking::find($id);
        //update booking with new info
        $booking->start_time = $club_start_time;
        $booking->end_time = $booking_end_time;
        $booking->duration = $booking_duration;
        $booking->booking_date = $booking->booking_date;
        $booking->booking_day = $booking->booking_day;
        $booking->event_id = $booking->event_id;
        //besides the booking itself, get all other bookings
        $bookings_to_check = Booking::all()->where('id','!=',$id)->where('parent_id', $booking->parent_id);
        $pupils_already_booked_in = BookedPupil::all()->where('booking_id','!=',$booking->id)->where('parent_id', $booking->parent_id)->where('booking_date', $booking->booking_date)->pluck('pupil_id')->toArray();
        foreach($request->input('pupils') as $pupil) {
            if(in_array($pupil, $pupils_already_booked_in)) {
                $pupil_already_booked_in = Pupil::where('id', $pupil)->first();
                return back()->withErrors(['errors' => ["$pupil_already_booked_in->first_name $pupil_already_booked_in->last_name"]]);
            }
        }
        $booking->save();
        //Check is complete so just update booked pupils related to this booking by clearing whatever choice is already stored in db
        //and replacing pupils with newly selected(and checked) pupils
        $booked_pupils = BookedPupil::withTrashed()->where('booking_id', $id)->get();
        //delete pupils already associated with booking, ready to replace with newly selected pupils
        foreach($booked_pupils as $booked_pupil) {
            $booked_pupil->forceDelete();
        }
        $this->store_pupils_associated_with_booking($booking, $pupils_associated_with_booking);
        $this->log_activity("Updated a booking");
        return redirect('bookings/'.$booking->booking_date)->withSuccess('Booking updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //First, delete pupils associate with booking due to foregin key constraints
        $booked_pupils = BookedPupil::withTrashed()->where('booking_id', $id)->get();
        foreach($booked_pupils as $booked_pupil) {
            $booked_pupil->delete();
            if(!Gate::denies('admin')) {
                $booked_pupil->forceDelete();
            }
        }
        $booking = Booking::withTrashed()->where('id', $id)->first();
        $booking_date = $booking->booking_date;
        $booking->delete();
        if(!Gate::denies('admin')) {
            $booking->forceDelete();
        }
        $this->log_activity("Deleted a booking");
        return redirect('bookings/'.$booking_date)->withSuccess('Booking cancelled successfully!');
    }

    public function store_pupils_associated_with_booking($booking, $pupils_associated_with_booking) {
        //save new booked pupil records as long as parent has actually seleceted atleast one pupil
        if(!empty($pupils_associated_with_booking)) {
            foreach($pupils_associated_with_booking as $pupil) {
                $booked_pupil = new BookedPupil;
                $booked_pupil->parent_id = $booking->parent_id;
                $booked_pupil->booking_id = $booking->id;
                $booked_pupil->pupil_id = $pupil;
                $booked_pupil->booking_date = $booking->booking_date;
                $booked_pupil->booking_day = $booking->booking_day;
                $booked_pupil->start_time = $booking->start_time;
                $booked_pupil->end_time = $booking->end_time;
                $booked_pupil->event_id = $booking->event_id;
                $booked_pupil->save();
            }
        }
    }

    public function log_activity($message) {
        $activity = new ActivityLog;
        $activity->action = $message;
        $activity->user_id = Auth::id();
        $activity->save();
    }
}