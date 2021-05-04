<?php

namespace App\Http\Controllers;

use App\Models\Pupil;
use App\Models\BookedPupil;
use App\Models\PupilDietaryRequirement;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use Gate;

class PupilController extends Controller
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
        $pupils =  DB::table('pupils')->select('*')->paginate(5);
        //if parent is logged in, show their own children to them
        if(Gate::denies('clubstaff')) {
            $pupils = DB::table('pupils')->select('*')->where('parent_id', Auth::id())->paginate(5);
        }
        $pupil_dietary_requirements = PupilDietaryRequirement::all();
        return view('pupils.showPupils',compact('pupils', 'pupil_dietary_requirements'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pupils.createPupil');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $pupil = $this->validate(request(), [
            'pupil_first_name' => 'string',
            'pupil_last_name' => 'string',
            'pupil_date_of_birth' => 'date',
            'pupil_dietary_requirements' => 'array',
            'pupil_other' => 'nullable|string',
            'pupil_food_arrangement' => 'required|string'
            ]);
            if(sizeof(Pupil::where('first_name', $request->input('pupil_first_name'))->where('last_name', $request->input('pupil_last_name'))->get()) > 0 ) {
                return back()->withErrors(['errors' => ['This child already exists.']]);
            }
            if(date_diff(date_create(date('Y-m-d')), date_create(date('Y-m-d', strtotime($request->input('pupil_date_of_birth')))))->y > 11 || date_diff(date_create(date('Y-m-d')), date_create(date('Y-m-d', strtotime($request->input('pupil_date_of_birth')))))->y < 1) {
                return back()->withErrors(['errors' => ['Child must be btween the age of 1 - 11.']]);
            }
            if(!empty($request->input('pupil_dietary_requirements'))) {
                if(in_array('Other', $request->input('pupil_dietary_requirements')) && $request->input('pupil_other') == null) {
                    return back()->withErrors(['errors' => ['You have selected Other, but have not provided details.']]);
                }
            }
            // create a Booking object and set its values from the input
            $pupil = new Pupil;
            $pupil->parent_id = Auth::id();
            $pupil->first_name = $request->input('pupil_first_name');
            $pupil->last_name = $request->input('pupil_last_name');
            $pupil->date_of_birth = $request->input('pupil_date_of_birth');
            $pupil->food_arrangement = $request->input('pupil_food_arrangement');
            $pupils = Pupil::all()->where('parent_id', Auth::id());
            foreach($pupils as $s) {
                if($s->first_name == $pupil->first_name && $s->last_name == $pupil->last_name && $s->date_of_birth == $pupil->date_of_birth) {
                    return back()->withErrors(['error' => ['You have already have this child\'s details saved.']]);
                }
            }
            // save the Booking object
            $pupil->save();
            $pupil_dietary_requirements = $request->input('pupil_dietary_requirements');
            if(!empty($pupil_dietary_requirements)) {
                foreach($pupil_dietary_requirements as $pupil_dietary_requirement) {
                    $dietary_requirement = new PupilDietaryRequirement;
                    $dietary_requirement->pupil_id = $pupil->id;
                    $dietary_requirement->dietary_requirements = $pupil_dietary_requirement;
                    $dietary_requirement->other_dietary_requirements = $request->input('pupil_other');
                    $dietary_requirement->save();
                }
            }
            return redirect('pupils')->withSuccess('Details of child have been saved successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Pupil  $pupil
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pupils = DB::table('pupils')->join('booked_pupils', 'pupils.id', '=', 'booked_pupils.pupil_id')->select('pupils.*')->where('booked_pupils.booking_id', $id)->paginate(5);
        $pupil_dietary_requirements = PupilDietaryRequirement::all();
        return view('pupils.showPupils',compact('pupils', 'pupil_dietary_requirements'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Pupil  $pupil
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $pupil = Pupil::find($id);
        $pupil_dietary_requirements_collection = PupilDietaryRequirement::all();
        return view('pupils.editPupil',compact('pupil', 'pupil_dietary_requirements_collection'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pupil  $pupil
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // form validation
        $pupil = $this->validate(request(), [
            'pupil_first_name' => 'string',
            'pupil_last_name' => 'string',
            'pupil_date_of_birth' => 'date',
            'pupil_dietary_requirements' => 'array',
            'pupil_other' => 'nullable|string',
            'pupil_food_arrangement' => 'string'
        ]);
        if(sizeof(Pupil::where('first_name', $request->input('pupil_first_name'))->where('id', '!=', $id)->where('last_name', $request->input('pupil_last_name'))->get()) > 0 ) {
            return back()->withErrors(['errors' => ['This child already exists.']]);
        }
        if(date_diff(date_create(date('Y-m-d')), date_create(date('Y-m-d', strtotime($request->input('pupil_date_of_birth')))))->y > 11 || date_diff(date_create(date('Y-m-d')), date_create(date('Y-m-d', strtotime($request->input('pupil_date_of_birth')))))->y < 1) {
            return back()->withErrors(['errors' => ['Child must be btween the age of 1 - 11.']]);
        }
        if(!empty($request->input('pupil_dietary_requirements'))) {
            if(in_array('Other', $request->input('pupil_dietary_requirements')) && $request->input('pupil_other') == null) {
                return back()->withErrors(['errors' => ['You have selected Other, but have not provided details.']]);
            }
        }
        //create a Booking object and set its values from the input
        $pupil = Pupil::find($id);
        $pupil->parent_id = Auth::id();
        $pupil->first_name = $request->input('pupil_first_name');
        $pupil->last_name = $request->input('pupil_last_name');
        $pupil->date_of_birth = $request->input('pupil_date_of_birth');
        $pupil->food_arrangement = $request->input('pupil_food_arrangement');
        // save the Booking object
        $pupil->save();
        $pupil_dietary_requirements = PupilDietaryRequirement::all()->where('pupil_id', $pupil->id);
        foreach($pupil_dietary_requirements as $pupil_dietary_requirement) {
            $pupil_dietary_requirement->delete();
        }
        $pupil_dietary_requirements = $request->input('pupil_dietary_requirements');
        if(!empty($pupil_dietary_requirements)) {
            foreach($pupil_dietary_requirements as $pupil_dietary_requirement) {
                $dietary_requirement = new PupilDietaryRequirement;
                $dietary_requirement->pupil_id = $pupil->id;
                $dietary_requirement->dietary_requirements = $pupil_dietary_requirement;
                $dietary_requirement->other_dietary_requirements = $request->input('pupil_other');
                $dietary_requirement->save();
            }
        }   
        return redirect('pupils')->withSuccess('Child has been updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Pupil  $pupil
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //fetch all bookings associated with this pupil
        $booked_pupils = BookedPupil::all()->where('pupil_id', $id);
        //array to store the id of the booking that the soon to be deleted booekd_pupil instance relates to
        $booked_pupil_booking_ids = array();
        foreach($booked_pupils as $booked_pupil) {
            //store the booking id of this instance of the pupil that is booked in
            array_push($booked_pupil_booking_ids, $booked_pupil->booking_id);
            //delete this instance of this pupil being booked in
            $booked_pupil->forceDelete();
        }
        //delete all bookings that this pupil is associated with
        foreach($booked_pupil_booking_ids as $bpb_id) {
            //fetch all bookings this pupil is associated with
            $booked_pupil = BookedPupil::all()->where('booking_id', $bpd_id)->first();
            //if these bookings have other pupils attatched to it as well (i.e. siblings of soon to be deleted pupil)
            //then don't delete the booking, just delete the pupil from the system and booked_pupils table and then the booking will appear
            //without showing this child as it doesn't exist anymore
            if(empty($booked_pupil)) {
                //this pupil was the only pupil that belongs to this booking, so delete whole booking as pupil no longer exists in system anyway
                Booking::find($bpd_id)->forceDelete();
            }
        }
        //clear dietary requirements for this student
        $pupil_dietary_requirements = pupilDietaryRequirement::all()->where('pupil_id', $id);
        foreach($pupil_dietary_requirements as $pupil_dietary_requirement) {
            $pupil_dietary_requirement->forceDelete();
        }
        //delete pupil
        Pupil::find($id)->forceDelete();
        return redirect('pupils')->withSuccess('Details of child have successfully been removed!');
    }
}
