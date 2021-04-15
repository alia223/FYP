<?php

namespace App\Http\Controllers;
use App\Models\Booking;
use App\Models\Student;
use App\Models\User;
use App\Models\BookedStudent;
use App\Models\ClashedBooking;
use App\Models\ClashedStudent;
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
        $rules = Rule::all();
        $bookings = Booking::all();
        if(Gate::denies('admin') && Gate::denies('clubstaff')) {
            $bookings = $bookings->where('userid', Auth::id());
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
        $students = Student::all();
        $students = $students->where('parentid', Auth::id());
        return view('bookings.createBooking', compact('students'))->with('date', $date);
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
            'students' => 'required|array'
        ],
        [
            'booking_length.required' => 'Please select the duration of your booking.',
            'students.required' => 'Please select at least one child'
        ]);
        // create a Booking object and set its values from the input
        $rules = Rule::all();
        $club_start_time = "";
        foreach($rules as $rule) {
            $club_start_time = $rule->club_start;
        }
        //calculate eventid for this set of repeat bookings
        $event = Booking::orderBy('created_at', 'desc')->first();
        $eventid = 1;
        if(!empty($event)) {
            $eventid = $event->eventid + 1;
        }
        //store booking details based on inputs
        $booking = new Booking;
        $booking->userid = Auth::id();
        $booking->name = Auth::user()->name;
        $duration = $request->input('booking_length');
        $booking_length = "+".$duration." minutes";
        $booking->start_time = $club_start_time;
        //calculate end time by using values of start time and duration selected by customer
        $booking->end_time = date("H:i:s", strtotime($booking_length, strtotime($club_start_time)));
        $booking->duration = $duration;
        //store date of booking in yyyy-mm-dd format
        $date = date('Y-m-d', strtotime($request->input('date')));
        $booking->booking_date = $date;
        $booking->booking_day = date('w', strtotime($request->input('date')));
        $booking->eventid = $eventid;
        //before saving the booking, get all bookings by all customers
        $bookings = Booking::all()->where('booking_date', $date);
        //array that contains id's of already booked students that are saved in database
        $booked_student_ids = array();
        //check to see if booking is valid
        foreach($bookings as $b) {
            //loop through all booked students for this particular parent, and add the id's of the students to array
            $booked_students = BookedStudent::all()->where('parentid', Auth::id())->where('booking_date', $date);
            foreach($booked_students as $booked_student) {
                array_push($booked_student_ids, $booked_student->studentid);
            }
            //whilst looping through all bookings, if parent is making a booking on a day where they have already made a booking
            //a check needs to be done to ensure that they are not make the booking on the same day for a child already booked in
            //same day booking, different child(ren) is totally fine
            if($booking->booking_date == $b->booking_date) {
                //get student(s) associated with booking that the parent is trying to make 
                $students = $request->input('students');
                //loop through students
                foreach($students as $student) {
                    //check if id of this/these student(s) in id array
                    //if so, this means aprent has already made a booking for this child on this day so return error message to user
                    if(in_array($student, $booked_student_ids)) {
                        return back()->withErrors(['errors' => ['Booking unsuccessful as it clashes with the following booking:', Student::find($student)->first_name.' '.Student::find($student)->last_name.' on '.$b->booking_date.' at '.$b->start_time.' - '.$b->end_time]]);
                    }
                }
            }
        }
        $booking->save();
        //students associated with this booking
        $students = $request->input('students');
        //as long as there are actually students selected
        if(!empty($students)) {
            //loop through students and create a new booking for that student
            foreach($students as $student) {
                $booked_students = new BookedStudent;
                $booked_students->parentid = Auth::id();
                $booked_students->bookingid = $booking->id;
                $booked_students->studentid = $student;
                $booked_students->booking_date = $date;
                $booked_students->booking_day = date('w', strtotime($request->input('date')));
                $booked_students->start_time = $booking->start_time;
                $booked_students->end_time = $booking->end_time;
                $booked_students->eventid = $eventid;
                $booked_students->save();
            }
        }
        
        //activity log info
        $activity = new ActivityLog;
        $activity->action = "Created a booking";
        $activity->bookingid = $booking->id;
        $activity->userid = Auth::id();
        $activity->user = Auth::user()->name;
        $activity->save();
        // generate a redirect HTTP response with a success message
        return redirect('bookings')->with('success', 'Booking has been added');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($date)
    {
        $bookings = Booking::all()->where('booking_date', $date);
        if(Gate::denies('admin') && Gate::denies('clubstaff')) {
            $bookings = $bookings->where('userid', Auth::id());
        }
        return view('bookings.showBookings', compact('bookings'))->with('date', $date);
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
        $booked_students = BookedStudent::all()->where('bookingid', $id);
        if(Gate::denies('admin')) {
            $students = Student::all()->where('parentid', Auth::id());
        }
        else {
            $parentid = 0;
            foreach($booked_students as $booked_student) {
                $parents = User::all()->where('id', $booked_student->parentid);
                foreach($parents as $parent) {
                    $parentid = $parent->id;
                }
            }
            $students = Student::all()->where('parentid', $parentid);
        }
        $bookings = Booking::all();
        return view('bookings.editBooking',compact('booking', 'students','booked_students', 'bookings'));
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
        $this->validate(request(), [
            'booking_length' => 'required|string',
            'students' => 'required|array'
        ]);
        $rules = Rule::all();
        $club_start_time = "";
        foreach($rules as $rule) {
            $club_start_time = $rule->club_start;
        }
        //find booking which user is requesting to update
        $booking = Booking::find($id);
        //update booking with new info
        $duration = $request->input('booking_length');
        $booking_length = "+".$duration." minutes";
        $booking->start_time = date('H:i:s', strtotime($club_start_time));
        $booking->end_time = date("H:i:s", strtotime($booking_length, strtotime($club_start_time)));
        $booking->duration = $duration;
        $booking->booking_date = $booking->booking_date;
        $booking->booking_day = date('w', strtotime($request->input('date')));
        $booking->eventid = $booking->eventid;
        //besides the booking itself, get all other bookings
        $bookings = Booking::all()->where('id','!=',$id)->where('userid', $booking['userid']);
        $booked_student_ids = array();
        //check to see if booking is valid
        foreach($bookings as $b) {
            //get all bookings that this parent has made, except booking being updated
            //reason for this is because I am going to be chcecking to make sure that the booking with the new information isn't already an existing booking
            //However, if I include the updated booking too then I will be comparing the updated booking WITH the updated booking and so of course this updated booking will seemingly exist
            //therefore, compared this updated booking to all other bookings except itself (obviously)
            $booked_students = BookedStudent::all()->where('bookingid','!=',$id)->where('parentid', $booking['userid'])->where('booking_date', $booking->booking_date);
            foreach($booked_students as $booked_student) {
                //store ids of students that are already booked
                array_push($booked_student_ids, $booked_student->studentid);
            }
            //if the newly updated info means that the booking is another the same date as another booking with the same students selected
            //this is a clash and so inform user of this
            if($booking->booking_date == $b->booking_date) {
                $students = $request->input('students');
                foreach($students as $student) {
                    if(in_array($student, $booked_student_ids)) {
                        return back()->withErrors(['errors' => ['Booking unsuccessful as it clashes with the following booking:', Student::find($student)->first_name.' '.Student::find($student)->last_name.' on '.$b->booking_date.' at '.$b->start_time.' - '.$b->end_time]]);
                    }
                }
            }
        }
        $booking->save();
        //Check is complete so just update booked students related to this booking by clearing whatever choice is already stored in db
        //and replacing students with newly selected(and checked) students
        $booked_students = BookedStudent::withTrashed()->where('bookingid', $id)->get();
        //rather than using Auth::id, because admin can also update booked students and thus admin will use the id associated with Auth::id 
        //Auth::id woudnt give the correct booked students as admin doesn't have booked students
        //instead use the booking id that is stored in the buttons value
        //when user clicks edit booking button, id is sent to controller,
        //find any booked student and just get the parentid associated with that booked student
        //now admin can use parents id without needing Auth::id()
        $parentid = 0;
        foreach($booked_students as $booked_student) {
            $parents = User::all()->where('id', $booked_student->parentid);
            foreach($parents as $parent) {
                $parentid = $parent->id;
            }
        }
        foreach($booked_students as $booked_student) {
            //delete the booked student record
            $booked_student->forceDelete();
        }
        //get booked students that user is selecting
        $students = $request->input('students');
        //save new booked student records as long as parent has actually seleceted atleast one student
        if(!empty($students)) {
            foreach($students as $student) {
                $booked_students = new BookedStudent;
                if(!Gate::denies('admin')) {
                    $booked_students->parentid = $parentid;
                }
                else {
                    $booked_students->parentid = Auth::id();
                }
                $booked_students->bookingid = $booking->id;
                $booked_students->studentid = $student;
                $booked_students->booking_date = $booking->booking_date;
                $booked_students->booking_day = date('w', strtotime($request->input('date')));
                $booked_students->start_time = $booking->start_time;
                $booked_students->end_time = $booking->end_time;
                $booked_students->eventid = $booking->eventid;
                $booked_students->save();
            }
        }
        $activity = new ActivityLog;
        $activity->bookingid = $id;
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
        //First, delete students associate with booking due to foregin key constraints
        $booked_students = BookedStudent::all();
        $booked_students = $booked_students->where('bookingid', $id);
        foreach($booked_students as $booked_student) {
            $booked_student->delete();
        }
        $booking = Booking::find($id);
        $booking->delete();


        $activity = new ActivityLog;
        $activity->action = "Deleted a booking";
        $activity->bookingid = $id;
        $activity->userid = Auth::id();
        $activity->user = Auth::user()->name;
        $activity->save();
        return redirect('bookings')->with('success','Booking has been deleted');
        
    }
}