<?php

namespace App\Http\Controllers;

use App\Models\Injury;
use App\Models\Student;
use Illuminate\Http\Request;
use Auth;

class StudentInjuryController extends Controller
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
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('clubstaff.createInjuryRecord');
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
        $student = $this->validate(request(), [
            'studentid' => 'required|string',
            'date_of_injury' => 'required|date',
            'comment' => 'required|string'
        ]);
        $injury = new Injury;
        $injury->staffid = Auth::id();
        $injury->studentid = $request->input('studentid');
        $injury->date = $request->input('date_of_injury');
        $injury->comment = $request->input('comment');
        $injury->save();
        $students = Student::all();
        return view('students.showStudents', compact('students'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pass = [$id];
        $injuries = Injury::all()->where('studentid', $id);
        return view('parents.showInjuryRecord', compact('injuries','pass'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Injury  $injury
     * @return \Illuminate\Http\Response
     */
    public function edit(Injury $injury)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Injury  $injury
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Injury $injury)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Injury  $injury
     * @return \Illuminate\Http\Response
     */
    public function destroy(Injury $injury)
    {
        //
    }
}
