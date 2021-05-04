<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\BookedStudent;
use App\Models\Booking;
use App\Models\Room;
class StudentRegisterUndoController extends Controller
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
            if($student->checked_in != null && $student->checked_out == null) {
                $student->checked_in = null;
            }
            else if($student->checked_in != null && $student->checked_out != null) {
                $student->checked_out = null;
            }
            $student->save();
        }
        return redirect('student-register');
    }
}
