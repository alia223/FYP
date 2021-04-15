@extends('layouts.app')
<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="./css/home.css" rel="stylesheet" />
</head>
<body>
@section('content')
<div class="container" style="margin:0; padding:0;">
    <div class="row justify-content-center">
        <div class="col-md-3">
            <div class="sidebar">
                @include('sidebar')            
            </div>
        </div>
        <div class="col-md-9" style="margin-top: 50px;">
            <div class="card">
                <div class="card-header">Club Staff Availability</div>
                <!-- display the errors -->
                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul> @foreach ($errors->all() as $error)<li>{{ $error }}</li> @endforeach</ul>
                </div><br /> 
                @endif
                <!-- display the success status -->
                @if (\Session::has('success'))
                <div class="alert alert-success">
                    <p>{{ \Session::get('success') }}</p>
                </div><br /> 
                @endif
                <div class="card-body">
                    <table class="table table-striped">
                        <thead class="text-center">
                            <tr>   
                                <th>Day</th>
                                <th>Times Where More Staff Are Required</th>
                                <th>Assigned Staff</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                        <tr>
                                <td>{{ $dotw[0] }}<br />Monday</td>
                                <td>
                                <?php
                                $student_num_tracker = array();
                                //store times at which the number of students in the club changes
                                $times_where_students_leave_club = array();
                                //store initial number of students at start of club
                                array_push($student_num_tracker, sizeof($booked_students->where('booking_date', $dotw[0])));
                                //store the start time of the club
                                array_push($times_where_students_leave_club, $rules[2]);
                                //at each possible time that a student can leave, check and store how many students are still in the club and at what time 
                                //the student(s) has left the club
                                foreach($club_time_intervals as $cti) {
                                    //get all students leaving at this current time
                                    $temp = sizeof($booked_students->where('booking_date', $dotw[0])->where('end_time', $cti));
                                    if($temp > 0) {
                                        //store num of students remaining in club
                                        array_push($student_num_tracker, ($student_num_tracker[sizeof($student_num_tracker) - 1] - $temp));
                                        //store time at which the change in this number of students changes
                                        array_push($times_where_students_leave_club, $cti);
                                    }
                                }
                                $count = 0;
                                $student_ratio = $rules[8];
                                foreach($student_num_tracker as $snt) {
                                    if(ceil($student_num_tracker[$count] / $student_ratio) < 2) {
                                        $student_num_tracker[$count] = 2;
                                    }
                                    else {
                                        $student_num_tracker[$count] = ceil($student_num_tracker[$count] / $student_ratio);
                                    }
                                    $count++;
                                }
                                ?>
                                @for($i = 0;$i < sizeof($times_where_students_leave_club) - 1;$i++)
                                    @if(sizeof($staffSchedule->where('day', 1)->where('available_from', '<=',  $times_where_students_leave_club[$i])->where('available_until', '>=',  $times_where_students_leave_club[$i+1])) < $student_num_tracker[$i])
                                        {{ $times_where_students_leave_club[$i] }} - {{ $times_where_students_leave_club[$i+1] }}: {{ $student_num_tracker[$i] - sizeof($staffSchedule->where('day', 1)->where('available_from', '<=',  $times_where_students_leave_club[$i])->where('available_until', '>=',  $times_where_students_leave_club[$i+1]))}}  staff required<br />
                                    @endif
                                @endfor
                                </td>
                                <td>
                                <?php 
                                    $min_available_from = DB::table('staff_schedules')->where('day', 1)->groupBy('staffid')->get(['staffid', DB::raw('MIN(available_from) as available_from')]);
                                    $max_available_until = DB::table('staff_schedules')->where('day', 1)->groupBy('staffid')->get(['staffid', DB::raw('MAX(available_until) as available_until')]);
                                    $count = 0;
                                    ?>
                                    @foreach($min_available_from as $sa)
                                        {{ $staff->where('id', $sa->staffid)->first()->name }} {{ $staff->where('id', $sa->staffid)->first()->last_name }}: {{$sa->available_from}} - {{ $max_available_until[$count]->available_until }} <br />
                                        <?php $count++; ?>
                                    @endforeach
                                </td>
                            </tr>
                            <tr>
                                <td>{{ $dotw[1] }}<br />Tuesday</td>
                                <td>
                                <?php
                                $student_num_tracker = array();
                                //store times at which the number of students in the club changes
                                $times_where_students_leave_club = array();
                                //store initial number of students at start of club
                                array_push($student_num_tracker, sizeof($booked_students->where('booking_date', $dotw[2])));
                                //store the start time of the club
                                array_push($times_where_students_leave_club, $rules[2]);
                                //at each possible time that a student can leave, check and store how many students are still in the club and at what time 
                                //the student(s) has left the club
                                foreach($club_time_intervals as $cti) {
                                    //get all students leaving at this current time
                                    $temp = sizeof($booked_students->where('booking_date', $dotw[1])->where('end_time', $cti));
                                    if($temp > 0) {
                                        //store num of students remaining in club
                                        array_push($student_num_tracker, ($student_num_tracker[sizeof($student_num_tracker) - 1] - $temp));
                                        //store time at which the change in this number of students changes
                                        array_push($times_where_students_leave_club, $cti);
                                    }
                                }
                                $count = 0;
                                $student_ratio = $rules[8];
                                foreach($student_num_tracker as $snt) {
                                    if(ceil($student_num_tracker[$count] / $student_ratio) < 2) {
                                        $student_num_tracker[$count] = 2;
                                    }
                                    else {
                                        $student_num_tracker[$count] = ceil($student_num_tracker[$count] / $student_ratio);
                                    }
                                    $count++;
                                }
                                ?>
                                @for($i = 0;$i < sizeof($times_where_students_leave_club) - 1;$i++)
                                    @if(sizeof($staffSchedule->where('day', 2)->where('available_from', '<=',  $times_where_students_leave_club[$i])->where('available_until', '>=',  $times_where_students_leave_club[$i+1])) < $student_num_tracker[$i])
                                        {{ $times_where_students_leave_club[$i] }} - {{ $times_where_students_leave_club[$i+1] }}: {{ $student_num_tracker[$i] - sizeof($staffSchedule->where('day', 2)->where('available_from', '<=',  $times_where_students_leave_club[$i])->where('available_until', '>=',  $times_where_students_leave_club[$i+1]))}}  staff required<br />
                                    @endif
                                @endfor
                                </td>
                                <td>
                                <?php 
                                    $min_available_from = DB::table('staff_schedules')->where('day', 2)->groupBy('staffid')->get(['staffid', DB::raw('MIN(available_from) as available_from')]);
                                    $max_available_until = DB::table('staff_schedules')->where('day', 2)->groupBy('staffid')->get(['staffid', DB::raw('MAX(available_until) as available_until')]);
                                    $count = 0;
                                    ?>
                                    @foreach($min_available_from as $sa)
                                        {{ $staff->where('id', $sa->staffid)->first()->name }} {{ $staff->where('id', $sa->staffid)->first()->last_name }}: {{$sa->available_from}} - {{ $max_available_until[$count]->available_until }} <br />
                                        <?php $count++; ?>
                                    @endforeach
                                </td>
                            </tr>
                            <tr>
                                <td>{{ $dotw[2] }}<br />Wednesday</td>
                                <td>
                                <?php
                                $student_num_tracker = array();
                                //store times at which the number of students in the club changes
                                $times_where_students_leave_club = array();
                                //store initial number of students at start of club
                                array_push($student_num_tracker, sizeof($booked_students->where('booking_date', $dotw[2])));
                                //store the start time of the club
                                array_push($times_where_students_leave_club, $rules[2]);
                                //at each possible time that a student can leave, check and store how many students are still in the club and at what time 
                                //the student(s) has left the club
                                foreach($club_time_intervals as $cti) {
                                    //get all students leaving at this current time
                                    $temp = sizeof($booked_students->where('booking_date', $dotw[2])->where('end_time', $cti));
                                    if($temp > 0) {
                                        //store num of students remaining in club
                                        array_push($student_num_tracker, ($student_num_tracker[sizeof($student_num_tracker) - 1] - $temp));
                                        //store time at which the change in this number of students changes
                                        array_push($times_where_students_leave_club, $cti);
                                    }
                                }
                                $count = 0;
                                $student_ratio = $rules[8];
                                foreach($student_num_tracker as $snt) {
                                    if(ceil($student_num_tracker[$count] / $student_ratio) < 2) {
                                        $student_num_tracker[$count] = 2;
                                    }
                                    else {
                                        $student_num_tracker[$count] = ceil($student_num_tracker[$count] / $student_ratio);
                                    }
                                    $count++;
                                }
                                ?>
                                @for($i = 0;$i < sizeof($times_where_students_leave_club) - 1;$i++)
                                    @if(sizeof($staffSchedule->where('day', 3)->where('available_from', '<=',  $times_where_students_leave_club[$i])->where('available_until', '>=',  $times_where_students_leave_club[$i+1])) < $student_num_tracker[$i])
                                        {{ $times_where_students_leave_club[$i] }} - {{ $times_where_students_leave_club[$i+1] }}: {{ $student_num_tracker[$i] - sizeof($staffSchedule->where('day', 3)->where('available_from', '<=',  $times_where_students_leave_club[$i])->where('available_until', '>=',  $times_where_students_leave_club[$i+1]))}}  staff required<br />
                                    @endif
                                @endfor
                                </td>
                                <td>
                                <?php 
                                    $min_available_from = DB::table('staff_schedules')->where('day', 3)->groupBy('staffid')->get(['staffid', DB::raw('MIN(available_from) as available_from')]);
                                    $max_available_until = DB::table('staff_schedules')->where('day', 3)->groupBy('staffid')->get(['staffid', DB::raw('MAX(available_until) as available_until')]);
                                    $count = 0;
                                    ?>
                                    @foreach($min_available_from as $sa)
                                        {{ $staff->where('id', $sa->staffid)->first()->name }} {{ $staff->where('id', $sa->staffid)->first()->last_name }}: {{$sa->available_from}} - {{ $max_available_until[$count]->available_until }} <br />
                                        <?php $count++; ?>
                                    @endforeach
                                </td>
                            </tr>
                            <tr>
                                <td>{{ $dotw[3] }}<br />Thursday</td>
                                <td>
                                <?php
                                $student_num_tracker = array();
                                //store times at which the number of students in the club changes
                                $times_where_students_leave_club = array();
                                //store initial number of students at start of club
                                array_push($student_num_tracker, sizeof($booked_students->where('booking_date', $dotw[3])));
                                //store the start time of the club
                                array_push($times_where_students_leave_club, $rules[2]);
                                //at each possible time that a student can leave, check and store how many students are still in the club and at what time 
                                //the student(s) has left the club
                                foreach($club_time_intervals as $cti) {
                                    //get all students leaving at this current time
                                    $temp = sizeof($booked_students->where('booking_date', $dotw[3])->where('end_time', $cti));
                                    if($temp > 0) {
                                        //store num of students remaining in club
                                        array_push($student_num_tracker, ($student_num_tracker[sizeof($student_num_tracker) - 1] - $temp));
                                        //store time at which the change in this number of students changes
                                        array_push($times_where_students_leave_club, $cti);
                                    }
                                }
                                $count = 0;
                                $student_ratio = $rules[8];
                                foreach($student_num_tracker as $snt) {
                                    if(ceil($student_num_tracker[$count] / $student_ratio) < 2) {
                                        $student_num_tracker[$count] = 2;
                                    }
                                    else {
                                        $student_num_tracker[$count] = ceil($student_num_tracker[$count] / $student_ratio);
                                    }
                                    $count++;
                                }
                                ?>
                                @for($i = 0;$i < sizeof($times_where_students_leave_club) - 1;$i++)
                                    @if(sizeof($staffSchedule->where('day', 4)->where('available_from', '<=',  $times_where_students_leave_club[$i])->where('available_until', '>=',  $times_where_students_leave_club[$i+1])) < $student_num_tracker[$i])
                                        {{ $times_where_students_leave_club[$i] }} - {{ $times_where_students_leave_club[$i+1] }}: {{ $student_num_tracker[$i] - sizeof($staffSchedule->where('day', 4)->where('available_from', '<=',  $times_where_students_leave_club[$i])->where('available_until', '>=',  $times_where_students_leave_club[$i+1]))}}  staff required<br />
                                    @endif
                                @endfor
                                </td>
                                <td>
                                <?php 
                                    $min_available_from = DB::table('staff_schedules')->where('day', 4)->groupBy('staffid')->get(['staffid', DB::raw('MIN(available_from) as available_from')]);
                                    $max_available_until = DB::table('staff_schedules')->where('day', 4)->groupBy('staffid')->get(['staffid', DB::raw('MAX(available_until) as available_until')]);
                                    $count = 0;
                                    ?>
                                    @foreach($min_available_from as $sa)
                                        {{ $staff->where('id', $sa->staffid)->first()->name }} {{ $staff->where('id', $sa->staffid)->first()->last_name }}: {{$sa->available_from}} - {{ $max_available_until[$count]->available_until }} <br />
                                        <?php $count++; ?>
                                    @endforeach
                                </td>
                            </tr>
                            <tr>
                                <td>{{ $dotw[4] }}<br />Friday</td>
                                <td>
                                <?php
                                $student_num_tracker = array();
                                //store times at which the number of students in the club changes
                                $times_where_students_leave_club = array();
                                //store initial number of students at start of club
                                array_push($student_num_tracker, sizeof($booked_students->where('booking_date', $dotw[4])));
                                //store the start time of the club
                                array_push($times_where_students_leave_club, $rules[2]);
                                //at each possible time that a student can leave, check and store how many students are still in the club and at what time 
                                //the student(s) has left the club
                                foreach($club_time_intervals as $cti) {
                                    //get all students leaving at this current time
                                    $temp = sizeof($booked_students->where('booking_date', $dotw[4])->where('end_time', $cti));
                                    if($temp > 0) {
                                        //store num of students remaining in club
                                        array_push($student_num_tracker, ($student_num_tracker[sizeof($student_num_tracker) - 1] - $temp));
                                        //store time at which the change in this number of students changes
                                        array_push($times_where_students_leave_club, $cti);
                                    }
                                }
                                $count = 0;
                                $student_ratio = $rules[8];
                                foreach($student_num_tracker as $snt) {
                                    if(ceil($student_num_tracker[$count] / $student_ratio) < 2) {
                                        $student_num_tracker[$count] = 2;
                                    }
                                    else {
                                        $student_num_tracker[$count] = ceil($student_num_tracker[$count] / $student_ratio);
                                    }
                                    $count++;
                                }
                                ?>
                                @for($i = 0;$i < sizeof($times_where_students_leave_club) - 1;$i++)
                                    @if(sizeof($staffSchedule->where('day', 5)->where('available_from', '<=',  $times_where_students_leave_club[$i])->where('available_until', '>=',  $times_where_students_leave_club[$i+1])) < $student_num_tracker[$i])
                                        {{ $times_where_students_leave_club[$i] }} - {{ $times_where_students_leave_club[$i+1] }}: {{ $student_num_tracker[$i] - sizeof($staffSchedule->where('day', 5)->where('available_from', '<=',  $times_where_students_leave_club[$i])->where('available_until', '>=',  $times_where_students_leave_club[$i+1]))}}  staff required<br />
                                    @endif
                                @endfor
                                </td>
                                <td>
                                    <?php 
                                        $min_available_from = DB::table('staff_schedules')->where('day', 5)->groupBy('staffid')->get(['staffid', DB::raw('MIN(available_from) as available_from')]);
                                        $max_available_until = DB::table('staff_schedules')->where('day', 5)->groupBy('staffid')->get(['staffid', DB::raw('MAX(available_until) as available_until')]);
                                        $count = 0;
                                    ?>
                                    @foreach($min_available_from as $sa)
                                        {{ $staff->where('id', $sa->staffid)->first()->name }} {{ $staff->where('id', $sa->staffid)->first()->last_name }}: {{$sa->available_from}} - {{ $max_available_until[$count]->available_until }} <br />
                                        <?php $count++; ?>
                                    @endforeach
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
  $('#staff-schedule').addClass('active'); 
</script>
@endsection
</body>
</html>