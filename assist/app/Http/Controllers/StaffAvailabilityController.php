<?php

namespace App\Http\Controllers;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\StaffAvailability;
use App\Models\User;
use App\Models\Rule;
use Gate;

class StaffAvailabilityController extends Controller
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
    {   //get days of current week

        $dotw = $this->get_dates_of_current_week();
        //Show times of avilability belonging to logged in staff member requesting this resource
        if(!Gate::denies('admin')) {
            $staffAvailability = StaffAvailability::all();
            $staff = User::all()->where('clubstaff', 1);
            return view('admin.staffAvailability', compact('staffAvailability', 'staff', 'dotw'));
        }
        $staffAvailability = StaffAvailability::all()->where('staff_id', Auth::id());
        return view('clubstaff.staffAvailability', compact('staffAvailability', 'dotw'));
    }

    public function store(Request $request) {
        $days = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday');
        $rules = Rule::all()->first();
        $request->validate([
            'monday_available_from',
            'monday_available_until',
            'tuesday_available_from',
            'tuesday_available_until',
            'wednesday_available_from',
            'wednesday_available_until',
            'thursday_available_from',
            'thursday_available_until',
            'friday_available_from',
            'friday_available_until',
            'max_hours' => 'integer|required'
        ]);
        //validate that staff member etners correct times that they are available from and until. 
        //E.g. being available from 19:00 and until 17:00 does not make sense
        for($i = 0;$i < 5;$i++) {
            if($request->input("$days[$i]_available_from") != '' && $request->input("$days[$i]_available_until") != '') {
                if($request->input("$days[$i]_available_from") >= $request->input("$days[$i]_available_until")) {
                    return back()->withErrors(['errors' => [
                        "On ucwords($days[$i]), Available From must be earlier than Available Until"

                    ]]);
                }
                else if(strtotime($request->input("$days[$i]_available_from")) < strtotime($rules->club_start)) {
                    return back()->WithErrors(['errors' => ["On".ucwords($days[$i]).", Available From must be $rules->club_start or later"]]);
                }
                else if(strtotime($request->input("$days[$i]_available_until")) > strtotime($rules->club_end)) {
                    return back()->WithErrors(['errors' => ["On".ucwords($days[$i]).", Available Until must be $rules->club_end or earlier"]]);
                }
            }
        }
        
        //clear any previous times of availability that staff sets, as they are updating their times of availability
        $staffAvailablity = StaffAvailability::all()->where('staff_id', Auth::id());
        foreach($staffAvailablity as $sa) {
            $sa->delete();
        }
        for($i = 0;$i <= 4;$i++) {
            $available_from = $request->input($days[$i].'_available_from');
            $available_until = $request->input($days[$i].'_available_until');
            $staffAvailablity = new StaffAvailability;
            $staffAvailablity->staff_id = Auth::id();
            $staffAvailablity->day = $i+1;
            $staffAvailablity->available_from = $available_from;
            $staffAvailablity->available_until = $available_until;
            $staffAvailablity->available_for = (strtotime($available_until) - strtotime($available_from)) / 60;
            $staffAvailablity->max_hours = $request->input('max_hours')*60;
            $staffAvailablity->save();
        }
        $this->log_activity("Staff member updated availability");
        return redirect('staff-availability');
    }
    
    public function get_dates_of_current_week() {
        $next_week_monday = date('w', strtotime("today")) == 7 ? date('Y-m-d', strtotime("monday next week")) : date('Y-m-d', strtotime("monday this week"));
        $next_week_tuesday = date('Y-m-d', strtotime($next_week_monday."+1 days"));
        $next_week_wednesday = date('Y-m-d', strtotime($next_week_monday."+2 days"));
        $next_week_thursday = date('Y-m-d', strtotime($next_week_monday."+3 days"));
        $next_week_friday = date('Y-m-d', strtotime($next_week_monday."+4 days"));
        return [$next_week_monday, $next_week_tuesday, $next_week_wednesday, $next_week_thursday, $next_week_friday];
    }

    public function log_activity($message) {
        $activity = new ActivityLog;
        $activity->action = $message;
        $activity->user_id = Auth::id();
        $activity->save();
    }
}