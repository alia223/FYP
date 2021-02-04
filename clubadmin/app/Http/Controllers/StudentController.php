<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\BookedStudent;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Gate;
use DB;

class StudentController extends Controller
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
            $roomid = 0;
            $booked_students = BookedStudent::all();
            $rooms = Room::all()->where('staffid', Auth::id());
            foreach($rooms as $room) {
                $roomid = $room->id;
                $booked_students = $booked_students->where('roomid', $roomid);
            }
            $students = DB::table('students')->join('booked_students', 'students.id', '=', 'booked_students.studentid')->where('roomid', $roomid)->select('students.*')->get();
            error_log($students);
            $students = json_decode(json_encode($students), true);
        }
        return view('students.showStudents',compact('students'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('students.createStudent');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $student = $this->validate(request(), [
            'student_first_name' => 'required|string',
            'student_last_name' => 'required|string',
            'student_date_of_birth' => 'required|date',
            'student_dietary_requirements' => 'nullable|string',
            'student_food_arrangement' => 'required|string'
            ]);
            // create a Booking object and set its values from the input
            $student = new Student;
            $student->parentid = Auth::id();
            $student->first_name = $request->input('student_first_name');
            $student->last_name = $request->input('student_last_name');
            $student->date_of_birth = $request->input('student_date_of_birth');
            $student->dietary_requirements = $request->input('student_dietary_requirements');
            $student->food_arrangement = $request->input('student_food_arrangement');
            $students = Student::all();
            foreach($students as $s) {
                if($s->first_name == $student->first_name && $s->last_name == $student->last_name && $s->date_of_birth == $student->date_of_birth) {
                    return back()->withErrors(['error' => ['You have already have this child\'s details saved.']]);
                }
            }
            // save the Booking object
            $student->save();
            return redirect('students')->with('success','Child saved');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $students = Student::all();
        if(Gate::denies('clubstaff')) {
            $students = Student::find($id);
        }
        return view('students.showStudents',compact('students'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $student = Student::find($id);
        return view('students.editStudent',compact('student'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // form validation
        $student = $this->validate(request(), [
            'student_first_name' => 'required|string',
            'student_last_name' => 'required|string',
            'student_date_of_birth' => 'required|date',
            'student_dietary_requirements' => 'required|string',
            'student_other' => 'nullable|string',
            'student_food_arrangement' => 'required|string'
        ]);
        // create a Booking object and set its values from the input
        $student = Student::find($id);
        $student->parentid = Auth::id();
        $student->first_name = $request->input('student_first_name');
        $student->last_name = $request->input('student_last_name');
        $student->date_of_birth = $request->input('student_date_of_birth');
        $student->dietary_requirements = $request->input('student_dietary_requirements');
        $student->other_dietary_requirements = $request->input('student_other');
        $student->food_arrangement = $request->input('student_food_arrangement');
        // save the Booking object
        $student->save();
        return redirect('students')->with('success','Booking has been updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $student = Student::find($id);
        $booked_students = BookedStudent::withTrashed()->get();
        $booked_students = $booked_students->where('studentid', $id);
        $booking_id = 0;
        foreach($booked_students as $booked_student) {
            $booking_id = $booked_student->bookingid;
            $booked_student->forceDelete();
        }
        $booking = Booking::find($booking_id);
        error_log($booking);
        if(!empty($booking)) {
            $booked_students = BookedStudent::where('bookingid', $booking->id)->first();
            if(empty($booked_students)) {
            $booking->forceDelete(); 
            }
        }
        $student->forceDelete();
        return redirect('students');
    }
}
