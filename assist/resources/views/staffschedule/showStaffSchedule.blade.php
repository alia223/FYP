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
                                <th>Times Where Staff Members Are <br />Still Required</th>
                                <th>Selected Staff</th>
                            </tr>
                        </thead>
                        <?php $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']; ?>
                        <tbody class="text-center">
                        <tr>
                                <td>{{ $days[$day-1] }}<br />{{ date('d-m-Y', strtotime($dotw[$day-1])) }}</td>
                                <td>
                                <?php
                                //at each possible time that a pupil can leave, check and store how many pupils are still in the club and at what time 
                                //the pupil(s) has left the club
                                for($i = (strtotime($rules->club_start) + ($rules->club_duration_step) * 60);$i <= strtotime($rules->club_end);$i += ($rules->club_duration_step) * 60) {
                                    $lower_bound = date('H:i:s', $i - (($rules->club_duration_step) * 60)); 
                                    $upper_bound = date('H:i:s', $i);
                                    $students_in_club = sizeof($booked_pupils->where('booking_date', $dotw[$day-1])->where('end_time', '>=', $upper_bound));
                                    $number_of_staff_required_at_this_time = ceil($students_in_club / $rules->pupil_ratio) == 1 ? 2 : ceil($students_in_club / $rules->pupil_ratio);
                                    if(sizeof($booked_pupils->where('booking_date', $dotw[$day-1])) > 0 && sizeof($staffSchedule->where('day', $day)->where('working_from', '<=',  $lower_bound)->where('working_until', '>=',  $upper_bound)) < $number_of_staff_required_at_this_time) {
                                        $number_of_staff_still_required = $number_of_staff_required_at_this_time - sizeof($staffSchedule->where('day', $day)->where('working_from', '<=',  $lower_bound)->where('working_until', '>=',  $upper_bound));
                                        echo "$lower_bound - $upper_bound : $number_of_staff_still_required staff required <br />";
                                    }
                                }
                                ?>
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