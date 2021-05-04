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

class RepeatBookingController extends Controller
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
        $rules = Rule::all();
        $bookings = Booking::all();
        if(Gate::denies('admin') && Gate::denies('clubstaff')) {
            $bookings = $bookings->where('user_id', Auth::id());
        }
        return view('bookings.booking', compact('bookings', 'rules'));
    }
    
        /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
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
        $booking = $this->validate(request(), [
            'date' => 'required|string',
            'booking_length' => 'required|string',
            'pupils' => 'required|array',
            'recursive_end_date' => 'required|string',
            'recursive_days' => 'required|array'
        ],
        [
            'booking_length.required' => 'Please select the duration of your booking.',
            'pupils.required' => 'Please select at least one child',
            'recursive_end_date.required' => 'Please select the date you would like your repeat booking to end.',
            'recursive_days.required' => 'Please select at least one day for your repeat booking'
        ]);
        $recursive_days = $request->input('recursive_days');
        $recursive_start_date = date('Y-m-d', strtotime($request->input('date')));
        $recursive_end_date = $request->input('recursive_end_date');
        $current_date = $recursive_start_date;
        $club_start_time = Rule::all()->first()->club_start;
        $club_end_time = Rule::all()->first()->club_end;
        $booking_duration = $request->input('booking_length');
        $booking_end_time = date("H:i:s", strtotime("+$booking_duration minutes", strtotime($club_start_time)));
        //pupils associated with this booking
        $pupils_associated_with_booking = $request->input('pupils');
        //either set event_id to 1 as there are no previously existing events, or find the last eventid used for a booking and increment by 1
        $event_id = !empty(Booking::withTrashed()->orderBy('created_at', 'desc')->first()) ? Booking::withTrashed()->orderBy('created_at', 'desc')->first()->event_id + 1 : 1;
        //either set event_id to 1 as there are no previously existing events, or find the last eventid used for a booking and increment by 1
        $checked_bookings = array();
        while(strtotime($current_date) <= strtotime($recursive_end_date)) {
            if(in_array(date('N', strtotime($current_date)), $recursive_days)) {
                //store booking details based on inputs
                $booking = new Booking;
                //each booking has an event id, this is mainly useful for when a repeat booking is made so that those bookings are linked together
                $booking->event_id = $event_id;
                $booking->parent_id = Auth::id();
                $booking->start_time = $club_start_time;
                $booking_end_time = $booking_end_time;
                if($booking_end_time > $club_end_time) {
                    return back()->withErrors(['errors' => ['Booking duration exceeds limit. Please select one of the options displayed below.']]);
                }
                $booking->end_time = $booking_end_time;
                $booking->duration = $booking_duration;
                $booking->booking_date = $current_date;
                $booking->booking_day = date('w', strtotime($current_date));
                //before saving the booking, get all bookings by all customers
                $bookings_to_check = Booking::all()->where('parent_id', Auth::id())->where('booking_date', $current_date);
                $pupils_already_booked_in = BookedPupil::all()->where('booking_id','!=',$booking->id)->where('parent_id', $booking->parent_id)->where('booking_date', $booking->booking_date)->pluck('pupil_id')->toArray();
                if(sizeof($pupils_already_booked_in) > 0) {
                    return back()->withErrors(['errors' => [
                        implode(",", $pupils_already_booked_in), $current_date]
                    ]);                    
                }
                array_push($checked_bookings, $booking);
            }
            $current_date= date("Y-m-d",strtotime("+1 day",strtotime($current_date)));
        }
        foreach($checked_bookings as $checked_booking) {
            $checked_booking->save();
            $this->store_pupils_associated_with_booking($request, $checked_booking, $pupils_associated_with_booking, Auth::id());
        }
        $this->log_activity("Created a Repeat Booking");
        // generate a redirect HTTP response with a success message
        return redirect('bookings/'.$request->input('date'))->withSuccess('Repeat Booking created successfully!');
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
        $initial_booking = Booking::find($id);
        $event_id = $initial_booking->event_id;
        $bookings = Booking::all()->where('event_id', $initial_booking->event_id);
        $checked_bookings = array();
        foreach($bookings as $booking) {
            //update booking with new info
            $booking->start_time = $club_start_time;
            $booking->end_time = $booking_end_time;
            $booking->duration = $booking_duration;
            $booking->booking_date = $booking->booking_date;
            $booking->booking_day = $booking->booking_day;
            $booking->event_id = $booking->event_id;
            //besides the booking itself, get all other bookings
            $bookings_to_check = Booking::all()->where('event_id', '!=', $booking->event_id)->where('parent_id', $booking->parent_id)->where('booking_date', $booking->booking_date);
            //check to see if booking is valid
            $pupils_already_booked_in = BookedPupil::all()->where('booking_id','!=',$booking->id)->where('parent_id', $booking->parent_id)->where('booking_date', $booking->booking_date)->pluck('pupil_id')->toArray();
            if(sizeof($pupils_already_booked_in) > 0) {
                return back()->withErrors(
                    ['errors' => [implode(",",$pupils_already_booked_in), $booking->booking_date]
                ]);
            }
            array_push($checked_bookings, $booking);
        }
        //clearing whatever choice of booked pupils is already stored in db and replacing pupils with newly selected(and checked) pupils
        $booked_pupils = BookedPupil::withTrashed()->where('event_id', $event_id)->get();
        //rather than using Auth::id, because admin can also update booked pupils and thus admin will use the id associated with Auth::id 
        //Auth::id wouldn't give the correct booked pupils as admin doesn't have booked pupils
        //instead use the booking id that is stored in the buttons value
        $parent_id = $initial_booking->parent_id;
        foreach($booked_pupils as $booked_pupil) {
            $booked_pupil->forceDelete();
        }
        foreach($checked_bookings as $checked_booking) {
            $checked_booking->save();
            //pupils associated with this booking
            $pupils_associated_with_booking = $request->input('pupils');
            $this->store_pupils_associated_with_booking($request, $checked_booking, $pupils_associated_with_booking, $parent_id);
        }
        $this->log_activity("Updated a Repeat Booking");
        return redirect('bookings/'.$bookings->first()->booking_date)->withSuccess('Repeat Booking updated successfully!');
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
        $event_id = BookedPupil::withTrashed()->where('booking_id', $id)->get()->first()->event_id;
        $booked_pupils = BookedPupil::withTrashed()->where('event_id', $event_id)->get();
        foreach($booked_pupils as $booked_pupil) {
            $booked_pupil->delete();
            if(!Gate::denies('admin')) {
                $booked_pupil->forceDelete();
            }
        }
        $bookings = Booking::withTrashed()->where('event_id', $event_id)->get();
        $booking_date = $bookings->first()->booking_date;
        foreach($bookings as $booking) {
            $booking->delete();
            if(!Gate::denies('admin')) {
                $booking->forceDelete();
            }
        }
        $this->log_activity("Deleted a Repeat Booking");
        return redirect('bookings/'.$booking_date)->withSuccess('Repeat Booking cancelled successfully!');
    }

    public function store_pupils_associated_with_booking($request, $booking, $pupils_associated_with_booking, $parent_id) {
        //save new booked pupil records as long as parent has actually seleceted atleast one pupil
        if(!empty($pupils_associated_with_booking)) {
            foreach($pupils_associated_with_booking as $pupil) {
                $booked_pupil = new BookedPupil;
                $booked_pupil->parent_id = Auth::id();
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