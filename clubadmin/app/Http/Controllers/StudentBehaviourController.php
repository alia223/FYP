<?php

namespace App\Http\Controllers;
use Auth;
use App\Models\Behaviour;
use App\Models\Student;
use Illuminate\Http\Request;
use Gate;

class StudentBehaviourController extends Controller
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
        return view('clubstaff.createBehaviourRecord');
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
            'date' => 'required|date',
            'stars' => 'required|string',
            'comment' => 'required|string',

        ]);
        $behaviour = new Behaviour;
        $behaviour->staffid = Auth::id();
        $behaviour->studentid = $request->input('studentid');
        $behaviour->date = $request->input('date');
        $behaviour->stars = $request->input('stars');
        $behaviour->comment = $request->input('comment');
        $behaviour->save();
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
        $behaviours = Behaviour::all()->where('studentid', $id);
        return view('parents.showBehaviourRecord', compact('behaviours', 'pass'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Behaviour  $behaviour
     * @return \Illuminate\Http\Response
     */
    public function edit(Behaviour $behaviour)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Behaviour  $behaviour
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Behaviour $behaviour)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Behaviour  $behaviour
     * @return \Illuminate\Http\Response
     */
    public function destroy(Behaviour $behaviour)
    {
        //
    }
}
