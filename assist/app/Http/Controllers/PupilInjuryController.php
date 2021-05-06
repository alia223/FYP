<?php

namespace App\Http\Controllers;

use App\Models\Injury;
use App\Models\Pupil;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Gate;
use DB;

class PupilInjuryController extends Controller
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
        $injuries = DB::table('injuries')->join('pupils', 'injuries.pupil_id','=','pupils.id')->select('injuries.*')->where('pupils.parent_id', Auth::id())->where('injuries.deleted_at', '=', null)->get();
        $pupils = DB::table('pupils')->join('injuries','pupils.id','=','injuries.pupil_id')->select('pupils.*');
        $staff = DB::table('users')->join('injuries','users.id','=','injuries.staff_id')->select('users.*');
        return view('injuries.injuries', compact('injuries', 'pupils', 'staff'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $pupils = Pupil::all()->where('id', $request->input('pupil_id'))->first();
        error_log($pupils);
        return view('injuries.createInjury', compact('pupils'));
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
        $pupil = $this->validate(request(), [
            'pupil_id' => 'required|string',
            'date_of_injury' => 'required|date',
            'description_of_injury' => 'required|string'
        ]);
        $injury = new Injury;
        $injury->staff_id = Auth::id();
        $injury->pupil_id = $request->input('pupil_id');
        $injury->date = $request->input('date_of_injury');
        $injury->description = $request->input('description_of_injury');
        $injury->save();
        $pupils = Pupil::all();
        $this->log_activity("Added an injury");
        return redirect('injuries/'.$request->input('pupil_id'))->withSuccess('Description of injury added successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $injuries = Injury::all()->where('pupil_id', $id);
        if(!Gate::denies('clubstaff')) {
            $injuries = Injury::withTrashed()->where('pupil_id', $id)->get();
        }
        return view('injuries.showInjury', compact('injuries', 'id'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $injury = Injury::find($id);
        if(!Gate::denies('clubstaff')) {
            $injury = Injury::withTrashed()->where('id', $id)->first();
        }
        return view('injuries.editInjury', compact('injury'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // form validation
        $pupil = $this->validate(request(), [
            'date_of_injury' => 'required|date',
            'description_of_injury' => 'required|string'
        ]);
        $injury = Injury::find($id);
        $injury->staff_id = Auth::id();
        $injury->description = $request->input('description_of_injury');
        $injury->save();
        $this->log_activity("Edited an injury");
        return redirect('injuries/'.$injury->pupil_id)->withSuccess('Description of injury updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Injury::where('id', $id)->forceDelete();
        $this->log_activity("Removed an injury");
        return back()->withSuccess('Injury Removed.');
    }

    public function log_activity($message) {
        $activity = new ActivityLog;
        $activity->action = $message;
        $activity->user_id = Auth::id();
        $activity->save();
    }
}
