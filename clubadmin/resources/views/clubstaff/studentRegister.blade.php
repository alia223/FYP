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
                <a href="{{ route('home') }}">Home</a>
                @if(Auth::check())
                    @if (!Gate::denies('admin') && Gate::denies('clubstaff'))
                        <a href="{{ url('bookings') }}">View Upcoming Bookings</a>
                        <a href="{{ route('past-bookings') }}">View Past Bookings</a>
                        <a href="{{ route('activity-log') }}">Activity Log</a>
                        <a href="{{ route('control-panel') }}">Control Panel</a>
                    @elseif (Gate::denies('admin') && Gate::denies('clubstaff'))
                        <a href="{{ route('create-bookings') }}?ym=<?php $date = date('Y-m'); echo $date?>">Create a Booking</a>
                        <a href="{{ url('bookings') }}">View Upcoming Bookings</a>
                        <a href="{{ route('past-bookings') }}">View Past Bookings</a>
                        <a href="{{ route('club-students') }}">Children</a>
                    @elseif (Gate::denies('admin') && !Gate::denies('clubstaff'))
                        <a href="{{ url('bookings') }}">View Upcoming Bookings</a>
                        <a class="active" href="{{ route('student-register') }}">Register</a>
                        <a href="{{ route('club-students') }}">Students</a>
                    @endif
                @endif
                <a href="{{ route('settings') }}">Settings</a>
            </div>
        </div>
        <div class="col-md-9" style="margin-top: 50px;">
            <div class="card">
                <div class="card-header">Children</div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>   
                                <th class="text-center">First Name</th>
                                <th class="text-center">Last Name</th>
                                <th class="text-center">Date of Birth</th>
                                <th class="text-center">Dietary Requirements</th>
                                <th class="text-center">Food Arrangement</th>
                                <th class="text-center" style="color: black;">Attendance</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($students as $student)
                                <tr>
                                    <td class="text-center">{{$student['first_name']}}</td>
                                    <td class="text-center">{{$student['last_name']}}</td>
                                    <td class="text-center">{{$student['date_of_birth']}}</td>
                                    @if($student['dietary_requirements'] == "Other")
                                        <td class="text-center">{{$student['other_dietary_requirements']}}</td>
                                    @else
                                        <td class="text-center">{{$student['dietary_requirements']}}</td>
                                    @endif
                                    <td class="text-center">{{$student['food_arrangement']}}</td>
                                    <td>
                                    <form class="form-horizontal" method="POST" action="{{ action('App\Http\Controllers\StudentRegisterController@update', $student['id']) }} " enctype="multipart/form-data" >
                                    @method('PATCH')
                                    @csrf
                                    <?php 
                                        $carry_on = true;
                                        foreach($booked_students as $booked_student) {
                                            if($carry_on) {
                                                if($student['id'] == $booked_student['studentid']) {
                                                    if($booked_student['attendance'] == 0) {
                                                        echo '<input type="submit" class="btn" value="Check" />';
                                                        $carry_on = false;
                                                    }
                                                    else {
                                                        echo '<input type="submit" class="btn" value="Check"style="background-color: green;" />';
                                                        $carry_on = false;
                                                    }
                                                }
                                            }
                                        }
                                        $carry_on = true;
                                    ?>
                                    </form>
                                    </td>
                                    </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
</body>
</html>