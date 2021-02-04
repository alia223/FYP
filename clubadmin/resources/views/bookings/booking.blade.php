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
    $isToday = $currentD == date('d')+intval($rules[5]) && $currentM == date('m') && $currentY == date('Y');
    $dayIsGreater_MonthAndYearSame = $currentD > date('d')+intval($rules[5]) && $currentM == date('m') && $currentY == date('Y');
    $monthIsGreater_YearIsSame = $currentM > date('m') && $currentY == date('Y');
    $yearIsGreater = $currentY > date('Y');
    $todayOrLater = $isToday || $dayIsGreater_MonthAndYearSame || $monthIsGreater_YearIsSame || $yearIsGreater;
    //remember by today you mean 7 days in advance
    if($todayOrLater) {
        if (strtotime($today) == strtotime($date)) {
            $week .= '<td class="today"><a class="btn btn-primary" style="background-color: white; border-style: solid; border-color:rgb(0,0,0); color: black" href="'.action('App\Http\Controllers\BookingController@create').'">';
            $week .= $day . '</a></td>';
        }
        else if((($str + 1) % 7 == 0 ) || ($str % 7 == 0)) {
            $week .= '<td style="color: red;">';
            $week .= $day . '</a></td>';
        } else {
            $week .= '<td><a class="btn btn-primary" style="background-color: white; border-style: solid; border-color:rgb(0,0,0); color: black" href="'.action('App\Http\Controllers\BookingController@create').'">';
            $week .= $day . '</a></td>';
        }
    }
    else {
        if (strtotime($today) == strtotime($date)) {
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
            <div class="sidebar" style="height:screen-height;">
                <a href="{{ route('home') }}">Home</a>
                @if(Auth::check())
                    @if (!Gate::denies('admin') && Gate::denies('clubstaff'))
                        <a class="active" href="{{ url('bookings') }}">View Upcoming Bookings</a>
                        <a href="{{ route('past-bookings') }}">View Past Bookings</a>
                        <a href="{{ route('activity-log') }}">Activity Log</a>
                        <a href="{{ route('control-panel') }}">Control Panel</a>
                    @elseif (Gate::denies('admin') && Gate::denies('clubstaff'))
                        <a class="active" href="{{ route('create-bookings') }}?ym=<?php $date = date('Y-m'); echo $date; ?>">Create a Booking</a>
                        <a href="{{ url('bookings') }}">View Upcoming Bookings</a>
                        <a href="{{ route('past-bookings') }}">View Past Bookings</a>
                        <a href="{{ route('club-students') }}">Children</a>
                    @elseif (Gate::denies('admin') && !Gate::denies('clubstaff'))
                        <a href="{{ url('bookings') }}">View Upcoming Bookings</a>
                        <a href="{{ route('student-register') }}">Register</a>
                        <a href="{{ route('club-students') }}">Students</a>
                    @endif
                @endif
                <a href="{{ route('settings') }}">Settings</a>
            </div>
        </div>
        <div class="col-md-2"></div>
            <div class="col-md-8">
                    <table style="margin: 0 auto;">
                        <tr>
                            <td><a href="?ym=<?= $prev; ?>" class="btn btn-link" style="color: <?php echo $rules[1];?>; height: 25px; width:25px; padding:0; margin: 0;"><i class="material-icons">arrow_back</i></a></td>
                            <td><span class="text-center" style="color: <?php echo $rules[0]; ?>"><?= $title; ?></span></td>
                            <td><a href="?ym=<?= $next; ?>" class="btn btn-link" style="color: <?php echo $rules[1];?>; height: 25px; width:25px; padding:0; margin: 0;"><i class="material-icons">arrow_forward</i></a></td>
                        </tr>
                    </table>
                <p class="btn btn-dark" style="margin-bottom: 10px;"><a href="{{ url('create-bookings') }}" style="color: <?php echo $rules[1];?>">Today</a></p>
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
<script src="../js/dateRetrieval.js"></script>
@endsection