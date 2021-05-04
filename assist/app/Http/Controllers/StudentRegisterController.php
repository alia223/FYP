<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\BookedStudent;
use App\Models\Booking;
use App\Models\Room;
use DB;
use Gate;
class StudentRegisterController extends Controller
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
        $students = Student::all();
        if(Gate::denies('clubstaff')) {
            $students = Student::all()->where('parentid',Auth::id());
        }
        else {
            $students = DB::table('students')->join('booked_students', 'students.id', '=', 'booked_students.studentid')->where('booking_date', date('Y-m-d'))->select('students.*')->get();
            $students = json_decode(json_encode($students), true);
        }
        $booked_students = BookedStudent::all();
        return view('clubstaff.studentRegister',compact('students', 'booked_students'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Register  $register
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $booking_of_attended_student = 0;
        $booked_students = BookedStudent::all()->where('studentid', $id);
        foreach($booked_students as $booked_student) {
            $students = Booking::all()->where('id', $booked_student->bookingid)->where('booking_date', date('Y-m-d'));
            foreach($students as $student) {
                $booking_of_attended_student = $student;
            }
        }
        $students = BookedStudent::all()->where('bookingid', $booking_of_attended_student->id)->where('studentid', $id);
        error_log($students);
        foreach($students as $student) {
            if($student->checked_in == null && $student->checked_out == null) {
                $student->checked_in = date('H:i:s');
            }
            else if($student->checked_in != null && $student->checked_out == null) {
                $student->checked_out = date('H:i:s');
            }
            else if($student->checked_in != null && $student->checked_out != null) {

            }
            $student->save();
        }
        return redirect('student-register');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        //
    }
}
