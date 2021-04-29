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
        $dotw = $this->get_dates_of_current_week();
        $this->reset_hours_worked_on_this_day_for_all_staff($day);
        $staff_schedule = StaffSchedule::all()->where('day', $day);
        foreach($staff_schedule as $ss) {
            $ss->delete();
        }
        $club_time_intervals = $this->get_club_time_intervals();
        $num_staff_required = array();
        //store booked_pupils for given date
        $booked_pupils = array();
        //store number of pupils at any given time of club
        $pupil_num_tracker = $this->get_pupil_num_tracker($club_time_intervals, $day, $dotw)[0];
        //store times at which the number of pupils in the club changes
        $times_where_pupils_leave_club = $this->get_pupil_num_tracker($club_time_intervals, $day, $dotw)[1];
        //store all of the dates for the following week from mon - fri. Reason for this is, the dat of the week is passed as a parameter($day)
        // so then we can collect all the pupils booked in on the $day of this week. E.g. if admin wants to assig staff to monday, 
        //then the system finds all pupils booked in on the monday in this week and the algorithm works from there
        $pupil_num_tracker = $this->adjust_pupil_num_tracker($pupil_num_tracker);
        $count = 0;
        $times_with_no_staff = array();
        foreach($pupil_num_tracker as $pnt) {
            if($count < sizeof($pupil_num_tracker) - 1) {
                //Order staff from staff longest duration of availability to shortest. This ensures least number of staff are called in.
                $available_staff_members = StaffAvailability::orderBy('available_for', 'desc')->where('day', $day)->where('available_from', '<=',  $times_where_pupils_leave_club[$count])->where('available_until', '>=',  $times_where_pupils_leave_club[$count+1])->get();
                $selected_staff = array();
                foreach($available_staff_members as $sm) {
                    $hours_worked_this_week = DB::table('staff_availabilities')->select(DB::raw('SUM(total_duration_worked_this_day) as hours_worked_this_week'))->where('staff_id', $sm->staff_id)->groupBy('staff_id')->first();
                    $duration_staff_member_is_required_for = (strtotime($times_where_pupils_leave_club[$count+1]) - strtotime($times_where_pupils_leave_club[$count])) / 60;
                    $max_hours_of_staff_member = ($sm->max_hours);
                    $staff_members_still_required_at_this_time = sizeof($selected_staff) < $pupil_num_tracker[$count];
                    $staff_member_is_required_and_available = (($hours_worked_this_week->hours_worked_this_week + $duration_staff_member_is_required_for) <=  $max_hours_of_staff_member) && ($staff_members_still_required_at_this_time) && !in_array($sm, $selected_staff);
                    if($staff_member_is_required_and_available) {
                        array_push($selected_staff, $sm);
                        $working_from = strtotime($times_where_pupils_leave_club[$count]);
                        $working_until = strtotime($times_where_pupils_leave_club[$count+1]);
                        $this->store_selected_staff_members($sm, $day, $working_from, $working_until);
                    }
                }
                if(sizeof($selected_staff) == 0) {
                    array_push($times_with_no_staff, $times_where_pupils_leave_club[$count]);
                    array_push($times_with_no_staff, $times_where_pupils_leave_club[$count+1]);
                }
                $count++;
            }
        }
        return redirect('staff-schedule/'.$day);
    }

    public function show($day) {
        $staffSchedule = StaffSchedule::all();
        $booked_pupils = BookedPupil::all();
        $staffAvailability = StaffAvailability::all();
        $bookings = Booking::all();
        $staff = User::all()->where('clubstaff', 1);
        $club_start_time = strtotime(Rule::all()->first()->club_start) + ((Rule::all()->first()->club_duration_step) * 60);
        $club_end_time = strtotime(Rule::all()->first()->club_end);
        $club_duration_step = (Rule::all()->first()->club_duration_step) * 60;
        $club_time_intervals = array();
        //store all of the possible times one or more students can leave the club
        for($i = $club_start_time;$i <= $club_end_time; $i += $club_duration_step) {
            array_push($club_time_intervals, date('H:i:s', $i));
        }
        $dotw = $this->get_dates_of_current_week();
        return view('staffschedule.showStaffSchedule', compact('staffSchedule', 'booked_pupils', 'bookings', 'staff', 'club_time_intervals', 'dotw', 'staffAvailability'))->withDay($day);
    }
    
    public function get_dates_of_current_week() {
        $next_week_monday = (date('w', strtotime("today")) == 7 ||  date('w', strtotime("today")) == 0) ? date('Y-m-d', strtotime("monday next week")) : date('Y-m-d', strtotime("monday this week"));
        $next_week_tuesday = date('Y-m-d', strtotime($next_week_monday."+1 days"));
        $next_week_wednesday = date('Y-m-d', strtotime($next_week_monday."+2 days"));
        $next_week_thursday = date('Y-m-d', strtotime($next_week_monday."+3 days"));
        $next_week_friday = date('Y-m-d', strtotime($next_week_monday."+4 days"));
        return [$next_week_monday, $next_week_tuesday, $next_week_wednesday, $next_week_thursday, $next_week_friday];
    }

    public function get_club_time_intervals() {
        $club_start_time = strtotime(Rule::all()->first()->club_start) + ((Rule::all()->first()->club_duration_step) * 60);
        $club_end_time = strtotime(Rule::all()->first()->club_end);
        $club_duration_step = (Rule::all()->first()->club_duration_step) * 60;
        $club_time_intervals = array();
        //store all of the possible times one or more pupils can leave the club
        for($i = $club_start_time;$i <= $club_end_time; $i += $club_duration_step) {
            array_push($club_time_intervals, date('H:i:s', $i));
        }
        return $club_time_intervals;
    }

    public function get_pupil_num_tracker($club_time_intervals, $day, $dotw) {
        $pupil_num_tracker = array();
        $times_where_pupils_leave_club = array();
        //store initial number of pupils at start of club
        array_push($pupil_num_tracker, sizeof(BookedPupil::all()->where('booking_date', $dotw[$day-1])));
        //store the start time of the club
        array_push($times_where_pupils_leave_club, Rule::all()->first()->club_start);
        //at each possible time that a pupil can leave, check and store how many pupils are still in the club and at what time 
        //the pupil(s) has left the club
        foreach($club_time_intervals as $cti) {
            //get all pupils leaving at this current time
            $temp = sizeof(BookedPupil::all()->where('booking_date', $dotw[$day-1])->where('end_time', '>=', $cti));
            //store num of pupils remaining in club
            array_push($pupil_num_tracker, $temp);
            //store time at which the change in this number of pupils changes
            array_push($times_where_pupils_leave_club, $cti);
        }
        return [$pupil_num_tracker, $times_where_pupils_leave_club];
    }
    
    public function adjust_pupil_num_tracker($pupil_num_tracker) {
        //Right now, $pupil_num_tracker stores number of pupils at the club at each given time interval. 
        //However, we don't want to assign 1 staff for each pupil in the class
        //typically there is a staff to pupil ratio. E.g. if the ratio is 1 staff to 6 pupils, we need 6 pupils / 6 = 1 staff member. 
        //Also, say we have 4 pupils in the club with a 1:6 ratio, then we need 4/6 ~ 0.67 staff members so for this reason we use ceil() 
        //i.e. 4/6 is rounded to 1 staff member
        $count = 0;
        $pupil_ratio = Rule::all()->first()->pupil_ratio;
        foreach($pupil_num_tracker as $pnt) {
            if(ceil($pupil_num_tracker[$count] / $pupil_ratio) < 2) {
                $pupil_num_tracker[$count] = 2;
            }
            else {
                $pupil_num_tracker[$count] = ceil($pupil_num_tracker[$count] / $pupil_ratio);
            }
            $count++;
        }
        return $pupil_num_tracker;
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
}