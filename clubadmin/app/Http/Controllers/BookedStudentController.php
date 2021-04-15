<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\BookedStudent;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Models\Rule;
use Illuminate\Support\Facades\Auth;

class BookedStudentController extends Controller
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {    
        $booking = Booking::find($id);
        $booked_students = BookedStudent::all();
        $booked_students = $booked_students->where('bookingid', $id);
        $children = array();
        foreach($booked_students as $booked_student) {
        $child = Student::find($booked_student->studentid);
        array_push($children, $child);
        }
        return view('bookings.showBookedStudents', compact('children','booking'));
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
        return view('students.editBookedStudent',compact('student'));
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
            return redirect('bookings')->with('success','Booking has been updated');
        }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $booked_students = BookedStudent::all();
        $rules = Rule::all();
        $booked_students = $booked_students->where('studentid', $id);
        foreach($booked_students as $booked_student) {
            $booked_student->forceDelete();
        }
        return redirect('bookings');
    }
}
