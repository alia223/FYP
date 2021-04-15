<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\BookedStudent;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Gate;

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
        //if parent is logged in, show their own children to them
        if(Gate::denies('clubstaff') && Gate::denies('admin')) {
            $students = Student::all()->where('parentid',Auth::id());
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
            $students = Student::all()->where('parentid', Auth::id());
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
        //fetch all instances where this student is associated with a booking
        $booked_students = BookedStudent::all()->where('studentid', $id);
        //array to store the id of the booking that the soon to be deleted booekd_student instance relates to
        $booked_student_bookingids = array();
        foreach($booked_students as $booked_student) {
            //store the booking id of this instance of the student that is booked in
            array_push($booked_student_bookingids, $booked_student->bookingid);
            //delete this instance of this student being booked in
            $booked_student->forceDelete();
        }
        //delete all bookings that this student is associated with
        foreach($booked_student_bookingids as $bsbid) {
            //fetch all bookings this student is associated with
            $booked_student = BookedStudent::all()->where('bookingid', $bsbid)->first();
            //if these bookings have other students attatched to it as well (i.e. siblings of soon to be deleted student)
            //then don't delete the booking, jsut delete the student from the sysetm and booked_students table and then the booking will appear
            //without showing this child as it doesn't exist anymore
            if(empty($booked_student)) {
                //this student was the only student that belongs to this booking, so delete whole booking as student no longer exists in system anyway
                Booking::find($bsbid)->forceDelete();
            }
        }
        //delete student
        Student::find($id)->delete();
        return redirect('students');
    }
}
