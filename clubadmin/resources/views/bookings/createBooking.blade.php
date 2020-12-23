@extends('layouts.app')
@section('content')
<?php
// Set timezone
date_default_timezone_set('Europe/London');

// Get prev & next month
if (isset($_GET['ym'])) {
    $ym = $_GET['ym'];
} else {
    // This month
    $ym = date('Y-m');
}

if (isset($_GET['ymd'])) {
    $ymd = $_GET['ymd'];
} else {
    //today
    $ymd = date('Y-m-d');
}

// Check format
$timestamp = strtotime($ym . '-01');  // the first day of the month
if ($timestamp === false) {
    $ym = date('Y-m');
    $timestamp = strtotime($ym . '-01');
}

// Today (Format:2018-08-8)
$today = date('Y-m-d');
// Title (Format:August, 2018)
$title = date('F, Y', $timestamp);

// Create prev & next month link
$prev = date('Y-m', strtotime('-1 month', $timestamp));
$next = date('Y-m', strtotime('+1 month', $timestamp));

// Number of days in the month
$day_count = date('t', $timestamp);

// 1:Mon 2:Tue 3: Wed ... 7:Sun
$str = date('N', $timestamp);

// Array for calendar
$weeks = [];
$week = '';

// Add empty cell(s)
$week .= str_repeat('<td></td>', $str - 1);

for ($day = 1; $day <= $day_count; $day++, $str++) {

    $date = $ym . '-' . $day;
    $currentDate = preg_split("[-]", $date);
    $currentD = $currentDate[2];
    $currentM = $currentDate[1];
    $currentY = $currentDate[0];
    $yearIsGreater = $currentY > date('Y');
    $dAndMAreEqualOrGreater = $currentD >= date('d') && $currentM >= date('m') && $currentY == date('Y');
    $todayOrLater = $yearIsGreater || $dAndMAreEqualOrGreater;
    if($todayOrLater) {
        if ($today == $date) {
            $week .= '<td class="today"><button class="btn btn-primary" data-toggle="modal" data-target="#bookingModal" style="background-color:  rgb(230, 239, 247); border-color: rgb(0, 0, 0); color: black">';
            $week .= $day . '</td>';
        } else if((($str + 1) % 7 == 0 ) || ($str % 7 == 0)) {
            $week .= '<td style="color: red">';
            $week .= $day . '</button></td>';
        } else {
            $week .= '<td><button class="btn btn-primary" data-toggle="modal" data-target="#bookingModal" style="background-color: white; border-color:rgb(0,0,0); color: black">';
            $week .= $day . '</button></td>';
        }
    }
    else {
        if ($today == $date) {
            $week .= '<td class="today">';
        } else if((($str + 1) % 7 == 0 ) || ($str % 7 == 0)) {
            $week .= '<td>';
        } else {
            $week .= '<td>';
        }
        $week .= $day . '</td>';
    }

    // Sunday OR last day of the month
    if ($str % 7 == 0 || $day == $day_count) {

        // last day of the month
        if ($day == $day_count && $str % 7 != 0) {
            // Add empty cell(s)
            $week .= str_repeat('<td></td>', 7 - $str % 7);
        }

        $weeks[] = '<tr>' . $week . '</tr>';

        $week = '';
    }
}
?>
<div class="container" style="margin: 0; padding: 0;">
    <div class="row">
        <div class="col-md-2">
            <div class="sidebar">
                <a href="{{ route('home') }}">Home</a>
                <a class="active" href="{{ url('bookings/create') }}?ym=<?php $date = date('Y-m'); echo $date?>">Create a Booking</a>
                <a href="{{ url('bookings') }}">View Upcoming Bookings</a>
                <a href="{{ route('past-bookings') }}">View Past Bookings</a>
                <a href="">Add a Child</a>
                <a href="">Settings</a>
            </div>
        </div>
        <div class="col-md-2"></div>
            <div class="col-md-8">
                <ul class="list-inline">
                    <li class="list-inline-item"><a href="?ym=<?= $prev; ?>" class="btn btn-link" style="color: black;">&lt;= prev</a></li>
                    <li class="list-inline-item"><span class="title"><?= $title; ?></span></li>
                    <li class="list-inline-item"><a href="?ym=<?= $next; ?>" class="btn btn-link" style="color: black;">next =&gt;</a></li>
                </ul>
                <p class="text-right"><a href="{{ url('bookings/create') }}" style="color: black;">Today</a></p>
                <table id="calendar" class="table table-bordered">
                    <thead>
                        <tr><th>M</th><th>T</th><th>W</th><th>T</th><th>F</th><th>S</th><th>S</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($weeks as $week) { echo $week;} ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1" role="dialog" aria-labelledby="bookingModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bookingModalLabel">Select a Time</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- inherite master template app.blade.php -->
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-10 ">
                            <div class="card">
                                <div class="card-header">Create a New Booking</div>
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
                                    <!-- define the form -->
                                    <div class="card-body">
                                        <p class="text-center font-weight-bold">Club begins at 15:30</p>
                                        <form class="form-horizontal" method="POST"
                                        action="{{url('bookings') }}" enctype="multipart/form-data">
                                        @csrf
                                        <?php for($i = 1; $i <= 6;$i+=1) {
                                            $val = 30 * $i;
                                            echo '<div class="row offset-md-1">';
                                            echo '<input type="radio" id="'.$val.'" name="booking_length" value="'.$val.'"/>&nbsp&nbsp';
                                            echo '<label for="'.$val.'">';
                                            if($val == 60) {
                                                $val = $val/60;
                                                echo $val." hour";
                                            }
                                            else if($val > 60) {
                                                $val = $val/60;
                                                echo $val." hours";
                                            }
                                            else {
                                                echo $val." minutes";
                                            }
                                            echo '</label><br>';
                                            echo '</div>';
                                        }?>
                                        <input type="hidden" id="hiddenDay" name="hiddenDay" />
                                        <input type="hidden" id="hiddenMonth" name="hiddenMonth" />
                                        <input type="hidden" id="hiddenYear" name="hiddenYear" />
                                        <div class="row">
                                            <div class="col-md-12 offset-md-1">
                                                <input type="submit" class="btn" style="background-color: rgb(132,0,255); color: white;"/>
                                                <input type="reset" class="btn" style="background-color: rgb(132,0,255); color: white;"/>
                                            </div>
                                        </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="../js/dateRetrieval.js"></script>
@endsection