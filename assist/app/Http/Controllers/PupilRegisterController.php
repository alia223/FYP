<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Pupil;
use App\Models\Booking;
use App\Models\BookedPupil;
use App\Models\PupilDietaryRequirement;
use App\Models\ActivityLog;
use DB;
use Gate;
class PupilRegisterController extends Controller
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
        $pupils = Pupil::all();
        $pupil_dietary_requirements = PupilDietaryRequirement::all();
        $booked_pupils = DB::table('booked_pupils')->select('*')->where('booking_date', date('Y-m-d'))->whereNull('deleted_at')->paginate(5);
        return view('clubstaff.pupilRegister',compact('pupils', 'booked_pupils', 'pupil_dietary_requirements'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Register  $register
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $booking_of_attended_pupil = 0;
        $booked_pupils = BookedPupil::all()->where('pupil_id', $id);
        foreach($booked_pupils as $booked_pupil) {
            $pupils = Booking::all()->where('id', $booked_pupil->booking_id)->where('booking_date', date('Y-m-d'));
            foreach($pupils as $pupil) {
                $booking_of_attended_pupil = $pupil;
            }
        }
        $pupils = BookedPupil::all()->where('booking_id', $booking_of_attended_pupil->id)->where('pupil_id', $id);
        error_log($pupils);
        foreach($pupils as $pupil) {
            if($pupil->checked_in == null && $pupil->checked_out == null) {
                $pupil->checked_in = date('H:i:s');
                $this->log_activity("Checked a pupil into club");
            }
            else if($pupil->checked_in != null && $pupil->checked_out == null) {
                $pupil->checked_out = date('H:i:s');
                $this->log_activity("Checked a pupil out of club");
            }
            else if($pupil->checked_in != null && $pupil->checked_out != null) {

            }
            $pupil->save();
        }
        return redirect('pupil-register');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        //
    }

    public function log_activity($message) {
        $activity = new ActivityLog;
        $activity->action = $message;
        $activity->user_id = Auth::id();
        $activity->save();
    }
}
