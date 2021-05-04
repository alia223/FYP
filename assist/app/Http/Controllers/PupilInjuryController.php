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
        $injuries = DB::table('injuries')->join('pupils', 'injuries.pupil_id','=','pupils.id')->select('injuries.*')->where('pupils.parent_id', Auth::id())->get();
        $pupils = DB::table('pupils')->join('injuries','pupils.id','=','injuries.pupil_id')->select('pupils.*');
        $staff = DB::table('users')->join('injuries','users.id','=','injuries.pupil_id')->select('users.*');
        return view('injuries.injuries', compact('injuries', 'pupils', 'staff'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $pupils = Pupil::all();
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
        $pupil_ids = [$id];
        $injuries = Injury::all()->where('pupil_id', $id);
        if(!Gate::denies('clubstaff')) {
            $injuries = Injury::withTrashed()->where('pupil_id', $id)->get();
        }
        return view('injuries.showInjury', compact('injuries','pupil_ids'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $pupil_id = [$id];
        $injury = Injury::find($id);
        return view('injuries.editInjury', compact('injury', 'pupil_id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $injury = Injury::find($id);
        // form validation
        $pupil = $this->validate(request(), [
            'pupil_id' => 'required|string',
            'date_of_injury' => 'required|date',
            'description_of_injury' => 'required|string'
        ]);
        $injury->staff_id = Auth::id();
        $injury->pupil_id = $injury->pupil_id;
        $injury->date = $injury->date;
        $injury->description = $request->input('description_of_injury');
        $injury->save();
        $pupils = Pupil::all();
        $this->log_activity("Edited an injury");
        return redirect('pupils')->withSuccess("Descrition of injury has been udpated successfully!");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $injury = Injury::find($id);
        if(Gate::denies('clubstaff')) {
            $injury->delete();
        }
        else {
            $injury = Injury::where('id', $id)->forceDelete();
        }
        $this->log_activity(Auth::id(), "Removed an injury");
        return back()->withSuccess('Injury Removed.');
    }

    public function log_activity($message) {
        $activity = new ActivityLog;
        $activity->action = $message;
        $activity->user_id = Auth::id();
        $activity->save();
    }
}
