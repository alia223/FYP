<?php

namespace App\Http\Controllers;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\StaffAvailability;
use App\Models\User;
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
    {
        $next_week_monday = date('w', strtotime("today")) == 7 ? date('Y-m-d', strtotime("monday next week")) : date('Y-m-d', strtotime("monday this week"));
        $next_week_tuesday = date('Y-m-d', strtotime($next_week_monday."+1 days"));
        $next_week_wednesday = date('Y-m-d', strtotime($next_week_monday."+2 days"));
        $next_week_thursday = date('Y-m-d', strtotime($next_week_monday."+3 days"));
        $next_week_friday = date('Y-m-d', strtotime($next_week_monday."+4 days"));
        $dotw = [$next_week_monday, $next_week_tuesday, $next_week_wednesday, $next_week_thursday, $next_week_friday];
        if(!Gate::denies('admin')) {
            $staffAvailability = StaffAvailability::all();
            $staff = User::all()->where('clubstaff', 1);
            return view('admin.staffAvailability', compact('staffAvailability', 'staff', 'dotw'));
        }
        $staffAvailability = StaffAvailability::all()->where('staffid', Auth::id());
        return view('clubstaff.staffAvailability', compact('staffAvailability', 'dotw'));
    }

    public function store(Request $request) {
        $days = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday');
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
        for($i = 0;$i < 5;$i++) {
            if($request->input("$days[$i]_available_from") != '' && $request->input("$days[$i]_available_until") != '' && $request->input("$days[$i]_available_from") >= $request->input("$days[$i]_available_until")) {
                return back()->withErrors(['errors' => [
                    'On '.ucwords($days[$i]).', Available From must be earlier than Available Until'

                ]]);
                return back()->with();
            }
        }
        $staffAvailablity = StaffAvailability::all()->where('staffid', Auth::id());
        foreach($staffAvailablity as $sa) {
            $sa->delete();
        }
        for($i = 0;$i <= 4;$i++) {
            $staffAvailablity = new StaffAvailability;
            $staffAvailablity->staffid = Auth::id();
            $staffAvailablity->day = $i+1;
            $staffAvailablity->available_from = $request->input($days[$i].'_available_from');
            $staffAvailablity->available_until = $request->input($days[$i].'_available_until');
            $staffAvailablity->available_for = (strtotime($request->input($days[$i].'_available_until')) - strtotime($request->input($days[$i].'_available_from'))) / 60;
            $staffAvailablity->max_hours = $request->input('max_hours');
            $staffAvailablity->save();
        }
        return redirect('staff-availability');
    }
    
}