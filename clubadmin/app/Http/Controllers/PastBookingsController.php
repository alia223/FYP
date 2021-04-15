<?php

namespace App\Http\Controllers;
use App\Models\Booking;
use App\Models\BookedStudent;
use App\Models\Student;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Gate;

class PastBookingsController extends Controller
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
        if(Gate::denies('admin')) {
            $bookings = Booking::withTrashed()->where('booking_date','<', date('Y-m-d'))->orWhereNotNull('deleted_at');
            $bookings = $bookings->where('userid', Auth::id())->paginate(3);
            return view('bookings.showPastBookings', array('bookings'=>$bookings));
        }
        else {
            $bookings = Booking::withTrashed()->where('booking_date','<', date('Y-m-d'))->orWhereNotNull('deleted_at')->paginate(3);
        }
        return view('bookings.showPastBookings', array('bookings'=>$bookings));
    }

    public function show($id) {
        //
        $booking = Booking::find($id);
        $booked_students = BookedStudent::withTrashed()->get();
        $booked_students = $booked_students->where('bookingid', $id);
        $children = array();
        foreach($booked_students as $booked_student) {
            $child = Student::find($booked_student->studentid);
            array_push($children, $child);
        }

        return view('bookings.showPastBookedStudents', compact('children','booking'));
    }

    public function edit($id) {
        $student = Student::find($id);
        return view('bookings.editPastBookedStudent',compact('student'));
    }

    public function update(Request $request, $id) {
        // form validation
        $student = $this->validate(request(), [
            'student_first_name' => 'required|string',
            'student_last_name' => 'required|string',
            'student_date_of_birth' => 'required|date',
            'student_dietary_requirements' => 'nullable|string',
            'student_food_arrangement' => 'required|string'
            ]);
            // create a Booking object and set its values from the input
            $student = Student::find($id);
            $student->first_name = $request->input('student_first_name');
            $student->last_name = $request->input('student_last_name');
            $student->date_of_birth = $request->input('student_date_of_birth');
            $student->dietary_requirements = $request->input('student_dietary_requirements');
            $student->food_arrangement = $request->input('student_food_arrangement');
            // save the Booking object
            $student->save();
            return redirect('past-bookings')->with('success','Booking has been updated');
    }

    /**
     * Delete resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $booked_students = BookedStudent::withTrashed()->get();
        $booked_students = $booked_students->where('bookingid', $id);
        foreach($booked_students as $booked_student) {
            $booked_student->forceDelete();
        }
        $booking = Booking::withTrashed()->find($id);
        if(!Gate::denies('admin')) {
            $booking->forceDelete();
        }

        $activity = new ActivityLog;
        $activity->action = "Deleted a booking";
        $activity->booking_id = $id;
        $activity->userid = Auth::id();
        $activity->user = Auth::user()->name;
        $activity->save();
        return redirect('past-bookings')->with('success','Booking has been permanently deleted');
    }
}