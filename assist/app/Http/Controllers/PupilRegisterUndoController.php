<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Pupil;
use App\Models\BookedPupil;
use App\Models\Booking;
use App\Models\ActivityLog;
class PupilRegisterUndoController extends Controller
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
            if($pupil->checked_in != null && $pupil->checked_out == null) {
                $pupil->checked_in = null;
            }
            else if($pupil->checked_in != null && $pupil->checked_out != null) {
                $pupil->checked_out = null;
            }
            $pupil->save();
        }
        return redirect('pupil-register');
    }
}
