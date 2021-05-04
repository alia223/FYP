<?php

namespace App\Http\Controllers;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\StaffSchedule;
use App\Models\StaffAvailability;
use App\Models\BookedPupil;
use App\Models\Booking;
use App\Models\Rule;
use App\Models\User;
use DB;
use Gate;

class StaffScheduleController extends Controller
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
        $dotw = $this->get_dates_of_current_week();
        $staffAvailability = StaffAvailability::all();
        $staff = User::all()->where('clubstaff', 1);
        return view('staffschedule.staffSchedule', compact('dotw', 'staffAvailability', 'staff'));

    }    

    public function store(Request $request) {
        $request->validate([
            'day' => 'string|required'
        ]);
        $day = $request->input('day');
        $rules = Rule::all()->first();
        $dotw = $this->get_dates_of_current_week();
        $this->reset_hours_worked_on_this_day_for_all_staff($day);
        $staff_schedule = StaffSchedule::all()->where('day', $day);
        foreach($staff_schedule as $ss) {
            $ss->delete();
        }
        //store all of the dates for the following week from mon - fri. Reason for this is, the dat of the week is passed as a parameter($day)
        // so then we can collect all the pupils booked in on the $day of this week. E.g. if admin wants to assig staff to monday, 
        //then the system finds all pupils booked in on the monday in this week and the algorithm works from there
        for($i = (strtotime($rules->club_start) + ($rules->club_duration_step) * 60);$i < strtotime($rules->club_end);$i += ($rules->club_duration_step) * 60) {
            $lower_bound = date('H:i:s', $i - (($rules->club_duration_step) * 60)); 
            $upper_bound = date('H:i:s', $i);
            $students_in_club = sizeof(BookedPupil::all()->where('booking_date', $dotw[$day-1])->where('end_time', '>=', $upper_bound));
            $number_staff_required = ceil($students_in_club / $rules->pupil_ratio) == 1 ? 2 : ceil($students_in_club / $rules->pupil_ratio);
            //Order staff from staff longest duration of availability to shortest. This ensures least number of staff are called in.
            $available_staff_members = StaffAvailability::orderBy('available_for', 'desc')->where('day', $day)->where('available_from', '<=',  $lower_bound)->where('available_until', '>=',  $upper_bound)->get();
            $selected_staff = array();
            foreach($available_staff_members as $sm) {
                $hours_worked_this_week = DB::table('staff_availabilities')->select(DB::raw('SUM(total_duration_worked_this_day) as hours_worked_this_week'))->where('staff_id', $sm->staff_id)->groupBy('staff_id')->first();
                $duration_staff_member_is_required_for = (strtotime($upper_bound) - strtotime($lower_bound)) / 60;
                $max_hours_of_staff_member = ($sm->max_hours);
                $staff_members_still_required_at_this_time = sizeof($selected_staff) < $number_staff_required;
                $staff_member_is_required_and_available = (($hours_worked_this_week->hours_worked_this_week + $duration_staff_member_is_required_for) <=  $max_hours_of_staff_member) && ($staff_members_still_required_at_this_time) && !in_array($sm, $selected_staff);
                if($staff_member_is_required_and_available) {
                    array_push($selected_staff, $sm);
                    $working_from = strtotime($lower_bound);
                    $working_until = strtotime($upper_bound);
                    $this->store_selected_staff_members($sm, $day, $working_from, $working_until);
                }
            }
        }
        $this->log_activity("Staff schedule calculated");
        return redirect('staff-schedule/'.$day);
    }

    public function show($day) {
        $staffSchedule = StaffSchedule::all();
        $booked_pupils = BookedPupil::all();
        $staffAvailability = StaffAvailability::all();
        $bookings = Booking::all();
        $staff = User::all()->where('clubstaff', 1);
        $rules = Rule::all()->first();
        $dotw = $this->get_dates_of_current_week();
        return view('staffschedule.showStaffSchedule', compact('rules', 'staffSchedule', 'booked_pupils', 'bookings', 'staff', 'dotw', 'staffAvailability'))->withDay($day);
    }
    
    public function get_dates_of_current_week() {
        $next_week_monday = (date('w', strtotime("today")) == 7 ||  date('w', strtotime("today")) == 0) ? date('Y-m-d', strtotime("monday next week")) : date('Y-m-d', strtotime("monday this week"));
        $next_week_tuesday = date('Y-m-d', strtotime($next_week_monday."+1 days"));
        $next_week_wednesday = date('Y-m-d', strtotime($next_week_monday."+2 days"));
        $next_week_thursday = date('Y-m-d', strtotime($next_week_monday."+3 days"));
        $next_week_friday = date('Y-m-d', strtotime($next_week_monday."+4 days"));
        return [$next_week_monday, $next_week_tuesday, $next_week_wednesday, $next_week_thursday, $next_week_friday];
    }

    public function reset_hours_worked_on_this_day_for_all_staff($day) {
        $staff_hours = StaffAvailability::all()->where('day', $day);
        foreach($staff_hours as $sh_to_reset) {
            $sh_to_reset->total_duration_worked_this_day = 0;
            $sh_to_reset->save();
        }
    }

    public function store_selected_staff_members($sm, $day, $working_from, $working_until) {
        $new_staff_schedule = new StaffSchedule;
        $new_staff_schedule->staff_id = $sm->staff_id;
        $new_staff_schedule->day = $day;
        $new_staff_schedule->working_from = date('H:i:s', $working_from);
        $new_staff_schedule->working_until = date('H:i:s', $working_until);
        $staff_hours = StaffAvailability::all()->where('staff_id', $sm->staff_id)->where('day', $day)->first();
        $staff_hours->total_duration_worked_this_day = 
        $staff_hours->total_duration_worked_this_day + ($working_until - $working_from) / 60;
        $staff_hours->save();
        $new_staff_schedule->save();
    }

    public function log_activity($message) {
        $activity = new ActivityLog;
        $activity->action = $message;
        $activity->user_id = Auth::id();
        $activity->save();
    }
}