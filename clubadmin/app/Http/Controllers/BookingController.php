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
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Gate;

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
        //
        $bookings = Booking::all();
        if(Gate::denies('admin') && Gate::denies('clubstaff')) {
            $bookings = $bookings->where('userid', Auth::id());
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
        $students = Student::all();
        $students = $students->where('parentid',Auth::id());
        return view('bookings.createBooking', array('students'=>$students));
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
            'hiddenDay' => 'required|string',
            'hiddenMonth' => 'required|string',
            'hiddenYear' => 'required|string',
            'students' => 'required|array'
        ]);
        // create a Booking object and set its values from the input
        $rules = Rule::all();
        $club_start_time = "";
        foreach($rules as $rule) {
            $club_start_time = $rule->club_start;
        }
        $booking = new Booking;
        $booking->userid = Auth::id();
        $booking->name = Auth::user()->name;
        $duration = $request->input('booking_length');
        $booking_length = "+".$duration." minutes";
        $booking->start_time = $club_start_time;
        $booking->end_time = date("H:i:s", strtotime($booking_length, strtotime($club_start_time)));
        $booking->duration = $duration;
        $day = $request->input('hiddenDay');
        $month = $request->input('hiddenMonth');
        $year = $request->input('hiddenYear');
        $date = $day."-".$month."-".$year;
        $time = strtotime($date);
        $newformat = date('Y-m-d',$time);
        $booking->booking_date = $newformat;
        $bookings = Booking::all();
        $booked_student_ids = array();
        //check to see if booking is valid
        foreach($bookings as $b) {
            $booked_students = BookedStudent::all()->where('parentid', Auth::id());
            foreach($booked_students as $booked_student) {
                array_push($booked_student_ids, $booked_student->studentid);
            }
            if($booking->booking_date == $b->booking_date) {
                $students = $request->input('students');
                foreach($students as $student) {
                    if(in_array($student, $booked_student_ids)) {
                        return back()->withErrors(['errors' => ['You have already made a similar booking on this date']]);
                    }
                }
            }
        }

        // save the Booking object
        //but before you do, update the database to account for the fact that some students are taking up space in the classroom being used for the club
        $room_capacity = 0;
        foreach($rules as $rule) {
            $room_capacity = $rule->room_capacity;
        }
        $rooms = Room::all();
        $roomid = 0;
        $roomFound = false;
            foreach($rooms as $room) {
                if(!$roomFound) {
                $accumulator = 0;
                $bookings = Booking::all()->where('booking_date', $newformat)->where('roomid', $room->id);
                foreach($bookings as $b) {
                    $booked_students = BookedStudent::all()->where('bookingid', $b->id);
                    $accumulator = $accumulator + count($booked_students);
                }
                error_log($accumulator + sizeof($request->input('students')));
                if($accumulator + sizeof($request->input('students')) <= $room_capacity) {
                    $roomid = $room->id;
                    $roomFound = true;
                }
            }
        }
        if(!$roomFound) {
            return back()->withErrors(['errors' => ['There are no more bookings available.']]);
        }
        $booking->roomid = $roomid;
        $booking->save();
        $students = $request->input('students');
        if(!empty($students)) {
            foreach($students as $student) {
                $booked_students = new BookedStudent;
                $booked_students->parentid = Auth::id();
                $booked_students->bookingid = $booking->id;
                $booked_students->studentid = $student;
                $booked_students->roomid = $roomid;
                $booked_students->save();
            }
        }
        $activity = new ActivityLog;
        $activity->action = "Created a booking";
        $activity->booking_id = $booking->id;
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
    public function show($id)
    {
        //
        $booking = Booking::find($id);
        $booked_students = BookedStudent::all();
        $booked_students = $booked_students->where('bookingid', $id);
        $children = array();
        foreach($booked_students as $booked_student) {
            $child = Student::find($booked_student->studentid);
            array_push($children, $child);
        }

        return view('bookings.showBooking', compact('children','booking'));
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
        return view('bookings.editBooking',compact('booking', 'students','booked_students'));
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
            'booking_date' => 'required|string',
            'students' => 'required|array'
        ]);
        $rules = Rule::all();
        $club_start_time = "";
        foreach($rules as $rule) {
            $club_start_time = $rule->club_start;
        }
        $booking = Booking::find($id);
        $duration = $request->input('booking_length');
        $booking_length = "+".$duration." minutes";
        $booking->start_time = $club_start_time;
        $booking->end_time = date("H:i:s", strtotime($booking_length, strtotime($club_start_time)));
        $booking->duration = $duration;
        $booking->booking_date = $request->input('booking_date');
        $bookings = Booking::all()->where('id','!=',$id)->where('userid', $booking['userid']);
        $booked_student_ids = array();
        //check to see if booking is valid
        foreach($bookings as $b) {
            $booked_students = BookedStudent::all()->where('bookingid','!=',$id)->where('parentid', $booking['userid']);
            foreach($booked_students as $booked_student) {
                array_push($booked_student_ids, $booked_student->studentid);
            }
            if($booking->booking_date == $b->booking_date) {
                $students = $request->input('students');
                foreach($students as $student) {
                    if(in_array($student, $booked_student_ids)) {
                        return back()->withErrors(['errors' => ['You have already made a similar booking on this date']]);
                    }
                }
            }
        }
        // save the Booking object
        //but before you do, update the database to account for the fact that some students are taking up space in the classroom being used for the club
        $room_capacity = 0;
        foreach($rules as $rule) {
            $room_capacity = $rule->room_capacity;
        }
        $rooms = Room::all();
        $roomid = 0;
        $roomFound = false;
            foreach($rooms as $room) {
                if(!$roomFound) {
                $accumulator = 0;
                $bookings = Booking::all()->where('booking_date',  $request->input('booking_date'))->where('roomid', $room->id)->where('id','!=', $id);
                foreach($bookings as $b) {
                    $booked_students = BookedStudent::all()->where('bookingid', $b->id);
                    $accumulator = $accumulator + count($booked_students);
                }
                error_log($accumulator + sizeof($request->input('students')));
                if($accumulator + sizeof($request->input('students')) <= $room_capacity) {
                    $roomid = $room->id;
                    $roomFound = true;
                }
            }
        }
        if(!$roomFound) {
            return back()->withErrors(['errors' => ['There are no more bookings available.']]);
        }
        $booking->roomid = $roomid;
        $booking->save();
        //Check is complete so just update booked students related to this booking by clearing whatever choice is already stored in db
        //and replacing students with newly selected(and checked) students
        $booked_students = BookedStudent::withTrashed()->where('bookingid', $id)->get();
        $parentid = 0;
        foreach($booked_students as $booked_student) {
            $parents = User::all()->where('id', $booked_student->parentid);
            foreach($parents as $parent) {
                $parentid = $parent->id;
            }
        }
        $roomids = array();
        foreach($booked_students as $booked_student) {
            array_push($roomids, $booked_student->roomid);
            $booked_student->forceDelete();
        }
        $students = $request->input('students');
        $i = 0;
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
                $booked_students->roomid = $roomids[$i];
                $booked_students->save();
            }
            $i++;
        }
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
        $booked_students = BookedStudent::all();
        $booked_students = $booked_students->where('bookingid', $id);
        foreach($booked_students as $booked_student) {
            $booked_student->delete();
        }

        $activity = new ActivityLog;
        $activity->action = "Deleted a booking";
        $activity->booking_id = $id;
        $activity->userid = Auth::id();
        $activity->user = Auth::user()->name;
        $activity->save();
        return redirect('bookings')->with('success','Booking has been deleted');
        
    }


}