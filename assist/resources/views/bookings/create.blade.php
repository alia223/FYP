@extends('layouts.app')
@section('content')
<?php
// Set your timezone!!
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
    // This month
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

    if ($today == $date) {
        $week .= '<td class="today"><button class="btn btn-primary" data-toggle="modal" data-target="#exampleModal" style="background-color:  rgb(230, 239, 247); border: none; color: black">';
    } else if((($str + 1) % 7 == 0 ) || ($str % 7 == 0)) {
        $week .= '<td><button class="btn btn-primary" data-toggle="modal" data-target="#exampleModal" style="background-color:  rgb(250, 250, 250); border: none; color: red">';
    } else {
        $week .= '<td><button class="btn btn-primary" data-toggle="modal" data-target="#exampleModal" style="background-color:  rgb(250, 250, 250); border: none; color: black">';
    }
    $week .= $day . '</button></td>';

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
            <a href="">View Past Bookings</a>
            <a href="">Add a child</a>
        </div>
        </div>
        <div class="col-md-2"></div>
        <div class="col-md-8">
        <ul class="list-inline">
            <li class="list-inline-item"><a href="?ym=<?= $prev; ?>" class="btn btn-link" style="color: black;">&lt;= prev</a></li>
            <li class="list-inline-item"><span class="title"><?= $title; ?></span></li>
            <li class="list-inline-item"><a href="?ym=<?= $next; ?>" class="btn btn-link" style="color: black;">next =&gt;</a></li>
        </ul>
        <p class="text-right"><a href="{{ url('bookings') }}" style="color: black;">Today</a></p>
        <table id="calendar" class="table table-bordered">
            <thead>
                <tr>
                    <th>M</th>
                    <th>T</th>
                    <th>W</th>
                    <th>T</th>
                    <th>F</th>
                    <th>S</th>
                    <th>S</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach ($weeks as $week) {
                        echo $week;
                    }
                ?>
            </tbody>
        </table>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Select a Time</h5>
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
                                        <ul> @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li> @endforeach
                                        </ul>
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
                                    <form class="form-horizontal" method="POST"
                                    action="{{url('bookings') }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="col-md-8">
                                        <label >Start Time</label>
                                        <input type="time" name="start_time"
                                        placeholder="Booking Start Time" min="03:30" max="19:00" required/>
                                    </div>
                                    <div class="col-md-8">
                                        <label >End Time</label>
                                        <input type="time" name="end_time"
                                        placeholder="Booking End Time"  min="03:30" max="19:00" required/>
                                    </div>
                                    <input type="text" id="hiddenDay" name="hiddenDay" />
                                    <input type="text" id="hiddenMonth" name="hiddenMonth" />
                                    <input type="text" id="hiddenYear" name="hiddenYear" />
                                    <div class="col-md-8"></div>
                                    <div class="col-md-6 col-md-offset-4">
                                        <input type="submit" class="btn btn-primary" />
                                        <input type="reset" class="btn btn-primary" />
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
<script>
    const urlParams = new URLSearchParams(window.location.search);
    const yearandmonth = urlParams.get('ym');
    const formattedyearandmonth = yearandmonth.split("-");
    const month = formattedyearandmonth[1];
    const year = formattedyearandmonth[0];
    var tbl = document.getElementById("calendar");
    if (tbl != null) {
        for (var i = 0; i < tbl.rows.length; i++) {
            for (var j = 0; j < tbl.rows[i].cells.length; j++)
                tbl.rows[i].cells[j].onclick = function () { getVal(this); };
        }
    }
    function getVal(cell) {
        document.cookie =  "day=" + cell.childNodes[0].childNodes[0].data;
        document.cookie =  "month=" + month;
        document.cookie =  "year=" + year;
        var cookieDate = document.cookie.split(";");
        var finalDate = "";
        for(var i = 0;i < cookieDate.length;i++) {
            if(cookieDate[i].trim().match(new RegExp('.*day=(.*)')) || cookieDate[i].trim().match(new RegExp('.*month=(.*)')) || cookieDate[i].trim().match(new RegExp('.*year=(.*)'))) {
                finalDate += cookieDate[i];
            }
        }
        finalDate = finalDate.split(" ");
        for(var i = 0;i < finalDate.length;i++) {
            var temp = finalDate[i].split("=");
            finalDate[i] = temp[1];
        }
        document.getElementById('hiddenDay').value = finalDate[2];
        document.getElementById('hiddenMonth').value = finalDate[0];
        document.getElementById('hiddenYear').value = finalDate[1];
    }
</script>
@endsection