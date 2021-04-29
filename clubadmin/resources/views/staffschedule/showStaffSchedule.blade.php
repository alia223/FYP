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
        @include('sidebar')
        <div class="offset-md-1 col-md-9" style="margin-top: 50px;">
            <div class="card">
                <div class="card-header">Club Staff Schedule</div>
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
                                <th>Times Where Staff Still Required</th>
                                <th>Selected Staff</th>
                            </tr>
                        </thead>
                        <?php $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']; ?>
                        <tbody class="text-center">
                        <tr>
                                <td>{{ $dotw[$day-1] }}<br />{{ $days[$day-1] }}</td>
                                <td>
                                <?php
                                $pupil_num_tracker = array();
                                //store times at which the number of pupils in the club changes
                                $times_where_pupils_leave_club = array();
                                //store initial number of pupils at start of club
                                array_push($pupil_num_tracker, sizeof($booked_pupils->where('booking_date', $dotw[$day-1])));
                                //store the start time of the club
                                array_push($times_where_pupils_leave_club, $rules->club_start);
                                //at each possible time that a pupil can leave, check and store how many pupils are still in the club and at what time 
                                //the pupil(s) has left the club
                                foreach($club_time_intervals as $cti) {
                                    //get all pupils leaving at this current time
                                    $temp = sizeof($booked_pupils->where('booking_date', $dotw[$day-1])->where('end_time', '>=', $cti));
                                    //store num of pupils remaining in club
                                    array_push($pupil_num_tracker, ($pupil_num_tracker[sizeof($pupil_num_tracker) - 1] - $temp));
                                    //store time at which the change in this number of pupils changes
                                    array_push($times_where_pupils_leave_club, $cti);
                                }
                                $count = 0;
                                $pupil_ratio = $rules->pupil_ratio;
                                foreach($pupil_num_tracker as $snt) {
                                    if(ceil($pupil_num_tracker[$count] / $pupil_ratio) < 2) {
                                        $pupil_num_tracker[$count] = 2;
                                    }
                                    else {
                                        $pupil_num_tracker[$count] = ceil($pupil_num_tracker[$count] / $pupil_ratio);
                                    }
                                    $count++;
                                }
                                ?>
                                @for($i = 0;$i < sizeof($times_where_pupils_leave_club) - 1;$i++)
                                    @if(sizeof($booked_pupils->where('booking_date', $dotw[$day-1])) > 0 && sizeof($staffSchedule->where('day', $day)->where('working_from', '<=',  $times_where_pupils_leave_club[$i])->where('working_until', '>=',  $times_where_pupils_leave_club[$i+1])) < $pupil_num_tracker[$i])
                                        {{ $times_where_pupils_leave_club[$i] }} - {{ $times_where_pupils_leave_club[$i+1] }}: {{ $pupil_num_tracker[$i] - 
                                        sizeof($staffSchedule->where('day', $day)->where('working_from', '<=',  $times_where_pupils_leave_club[$i])->where('working_until', '>=',  $times_where_pupils_leave_club[$i+1]))}}  staff required<br />
                                    @endif
                                @endfor
                                </td>
                                <td>
                                <?php 
                                    $min_working_from = DB::table('staff_schedules')->where('day', $day)->groupBy('staff_id')->get(['staff_id', DB::raw('MIN(working_from) as working_from')]);
                                    $max_working_until = DB::table('staff_schedules')->where('day', $day)->groupBy('staff_id')->get(['staff_id', DB::raw('MAX(working_until) as working_until')]);
                                    $count = 0;
                                    ?>
                                    @foreach($min_working_from as $sa)
                                        {{ $staff->where('id', $sa->staff_id)->first()->name }} {{ $staff->where('id', $sa->staff_id)->first()->last_name }}: {{$sa->working_from}} - {{ $max_working_until[$count]->working_until }} <br />
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
  $('#staff_schedule').addClass('active'); 
</script>
@endsection
</body>
</html>