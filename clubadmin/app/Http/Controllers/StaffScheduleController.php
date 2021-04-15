<?php

namespace App\Http\Controllers;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\StaffSchedule;
use App\Models\StaffAvailability;
use App\Models\BookedStudent;
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
        $staffSchedule = StaffSchedule::all();
        $booked_students = BookedStudent::all();
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
        $next_week_monday = date('w', strtotime("today")) == 7 ? date('Y-m-d', strtotime("monday next week")) : date('Y-m-d', strtotime("monday this week"));
        $next_week_tuesday = date('Y-m-d', strtotime($next_week_monday."+1 days"));
        $next_week_wednesday = date('Y-m-d', strtotime($next_week_monday."+2 days"));
        $next_week_thursday = date('Y-m-d', strtotime($next_week_monday."+3 days"));
        $next_week_friday = date('Y-m-d', strtotime($next_week_monday."+4 days"));
        $dotw = [$next_week_monday, $next_week_tuesday, $next_week_wednesday, $next_week_thursday, $next_week_friday];
        return view('staffSchedule', compact('staffSchedule', 'booked_students', 'bookings', 'staff', 'club_time_intervals', 'dotw', 'staffAvailability'));
    }    

    public function store(Request $request) {
        $request->validate([
            'day' => 'string|required'
        ]);
        $day = $request->input('day');
        //clear current staff schedule for this day
        $ss = StaffSchedule::all()->where('day', $day);
        foreach($ss as $ss_to_delete){
            $ss_to_delete->delete();
        }
        //reset hours worked this day as they will be recalculated depending on most recently updated staff availabilities
        $staff_hours = StaffAvailability::all()->where('day', $day);
        foreach($staff_hours as $sh_to_reset) {
            $sh_to_reset->total_duration_worked_this_day = 0;
            $sh_to_reset->save();
        }
        $club_start_time = strtotime(Rule::all()->first()->club_start) + ((Rule::all()->first()->club_duration_step) * 60);
        $club_end_time = strtotime(Rule::all()->first()->club_end);
        $club_duration_step = (Rule::all()->first()->club_duration_step) * 60;
        $club_time_intervals = array();
        $num_staff_required = array();
        $dotw = array();
        //store booked_students for given date
        $booked_students = array();
        //store number of students at any given time of club
        $student_num_tracker = array();
        //store times at which the number of students in the club changes
        $times_where_students_leave_club = array();
        //store all of the possible times one or more students can leave the club
        for($i = $club_start_time;$i <= $club_end_time; $i += $club_duration_step) {
            array_push($club_time_intervals, date('H:i:s', $i));
        }
        //store all of the dates for the following week from mon - fri
        //reason for this is, the dat of the week is passed as a parameter($day)
        // so then we can collect all the students booked in on the $day of this week
        //e.g. if admin wants to assig staff to monday, then the system finds all students booked in on the monday in this week
        //and the algorithm works from there
        $next_week_monday = date('w', strtotime("today")) == 7 ? date('Y-m-d', strtotime("monday next week")) : date('Y-m-d', strtotime("monday this week"));
        $next_week_tuesday = date('Y-m-d', strtotime($next_week_monday."+1 days"));
        $next_week_wednesday = date('Y-m-d', strtotime($next_week_monday."+2 days"));
        $next_week_thursday = date('Y-m-d', strtotime($next_week_monday."+3 days"));
        $next_week_friday = date('Y-m-d', strtotime($next_week_monday."+4 days"));
        $dotw = [$next_week_monday, $next_week_tuesday, $next_week_wednesday, $next_week_thursday, $next_week_friday];
        //store initial number of students at start of club
        array_push($student_num_tracker, sizeof(BookedStudent::all()->where('booking_date', $dotw[$day-1])));
        //store the start time of the club
        array_push($times_where_students_leave_club, Rule::all()->first()->club_start);
        //at each possible time that a student can leave, check and store how many students are still in the club and at what time 
        //the student(s) has left the club
        foreach($club_time_intervals as $cti) {
            //get all students leaving at this current time
            $temp = sizeof(BookedStudent::all()->where('booking_date', $dotw[$day-1])->where('end_time', $cti));
            if($temp > 0) {
                //store num of students remaining in club
                array_push($student_num_tracker, ($student_num_tracker[sizeof($student_num_tracker) - 1] - $temp));
                //store time at which the change in this number of students changes
                array_push($times_where_students_leave_club, $cti);
            }
        }
        //right now, $student_num_tracker stores number of students at the club at each given time interval
        //However, we don't want to assign 1 staff for each student in the class
        //typically there is a staff to student ratio
        //E.g. if the ratio is 1 staff to 6 students, then if there are 6 students at the start of the club
        //we need 6 students / 6 = 1 staff member
        //Also, say we have 4 students in the club with a 1:6 ratio, then we need 4/6 ~ 0.67 staff members so for this reason we use ceil()
        //which means 4/6 would be rounded up to become 1 staff member required
        $count = 0;
        $student_ratio = Rule::all()->first()->student_ratio;
        foreach($student_num_tracker as $snt) {
            if(ceil($student_num_tracker[$count] / $student_ratio) < 2) {
                $student_num_tracker[$count] = 2;
            }
            else {
                $student_num_tracker[$count] = ceil($student_num_tracker[$count] / $student_ratio);
            }
            $count++;
        }
        $count = 0;
        $times_with_no_staff = array();
        foreach($student_num_tracker as $snt) {
            if($count < sizeof($student_num_tracker) - 1) {
                //order staff from staff who are available for the longest duration, to the shortest duration
                //because of this we can then say that when the staff memebers are selected, we ensure that the least number of staff members are called in
                //E.g. if we need 1 staff member from 15:30 - 16:00 and 1 staff member from 16:00 - 17:00
                //then if we didnt order by most available to least available staff
                //say we had staff member one available from 15:30 - 16:00 and staff member two from 15:30 - 19:30,
                //then the system would first see staff memeber one and assign them to 15:30 - 16:00 and then assign staff member two to
                //16:00 - 17:00 as staff member one is no longer available. This would result in two staff being called in,
                //However, if we order by longest duration of availability, then staff member two would be found in the db first
                //and so staff member two would be assigned to 15:30 - 16:00 and then staff member two would be, again, found first
                //when searching for a staff member available from 16:00 - 17:00
                //In this way, 1 staff member was called in instead of an unnecessary extra staff member
                $available_staff_members = StaffAvailability::orderBy('available_for', 'desc')->where('day', $day)->where('available_from', '<=',  $times_where_students_leave_club[$count])->where('available_until', '>=',  $times_where_students_leave_club[$count+1])->get();
                $selected_staff = array();
                foreach($available_staff_members as $sm) {
                    $hours_worked_this_week = DB::table('staff_availabilities')->select(DB::raw('SUM(total_duration_worked_this_day) as hours_worked_this_week'))->where('staffid', $sm->staffid)->groupBy('staffid')->first();
                    $duration_staff_member_is_required_for = ((strtotime($times_where_students_leave_club[$count+1]) - strtotime($times_where_students_leave_club[$count])) / 60);
                    $max_hours_of_staff_member = ($sm->max_hours * 60);
                    $staff_members_still_required_at_this_time = sizeof($selected_staff) < $student_num_tracker[$count];
                    $staff_member_is_required_and_available = ($hours_worked_this_week->hours_worked_this_week +  $duration_staff_member_is_required_for <=  $max_hours_of_staff_member) && ($staff_members_still_required_at_this_time);
                    if($staff_member_is_required_and_available) {
                        array_push($selected_staff, $sm);
                        $new_staff_schedule = new StaffSchedule;
                        $new_staff_schedule->staffid = $sm->staffid;
                        $new_staff_schedule->day = $day;
                        $new_staff_schedule->available_from = $times_where_students_leave_club[$count];
                        $new_staff_schedule->available_until = $times_where_students_leave_club[$count+1];
                        $staff_hours = StaffAvailability::all()->where('staffid', $sm->staffid)->where('day', $day)->first();
                        $staff_hours->total_duration_worked_this_day = $staff_hours->total_duration_worked_this_day + (strtotime($times_where_students_leave_club[$count+1]) - strtotime($times_where_students_leave_club[$count])) / 60;
                        $staff_hours->save();
                        $new_staff_schedule->save();
                    }
                }

                if(sizeof($selected_staff) == 0) {
                    array_push($times_with_no_staff, $times_where_students_leave_club[$count]);
                    array_push($times_with_no_staff, $times_where_students_leave_club[$count+1]);
                }
                $count++;
            }
        }
        return redirect('staff-schedule');
    }
}