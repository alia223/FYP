<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\BookedStudent;
use App\Models\Student;
use Gate;

class HomeController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if(Gate::denies('admin') && Gate::denies('clubstaff')) {
            $booked_students = BookedStudent::all()->where('parentid', Auth::id())->where('booking_date', date('Y-m-d', strtotime("today")))->where('checked_in', '!=', NULL);
            $students = array();
            foreach($booked_students as $booked_student) {
                array_push($students, Student::find($booked_student->studentid));
            }
            return view('home', compact('students', 'booked_students'));
        }
        return view('home');
    }
}
