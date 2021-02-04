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
                        <a class="active" href="{{ route('club-students') }}">Children</a>
                    @elseif (Gate::denies('admin') && !Gate::denies('clubstaff'))
                        <a href="{{ url('bookings') }}">View Upcoming Bookings</a>
                        <a href="{{ route('student-register') }}">Register</a>
                        <a class="active" href="{{ route('club-students') }}">Students</a>
                    @endif
                @endif
                <a href="{{ route('settings') }}">Settings</a>
            </div>
        </div>
        <div class="col-md-9" style="margin-top: 50px;">
            <div class="card">
                <div class="card-header">Behaviour Record</div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>   
                                <th class="text-center">Date</th>
                                <th class="text-center">Staff ID</th>
                                <th class="text-center">Student ID</th>
                                <th class="text-center">Stars</th>
                                <th class="text-center">Comment</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($behaviours as $behaviour)
                            <tr>
                                <td class="text-center">{{$behaviour['date']}}</td>
                                <td class="text-center">{{$behaviour['staffid']}}</td>
                                <td class="text-center">{{$behaviour['studentid']}}</td>
                                <td class="text-center">{{$behaviour['stars']}}</td>
                                <td class="text-center">{{$behaviour['comment']}}</td>
                        @endforeach
                        <input type="hidden" id ="sid" value="<?php foreach($pass as $p) echo $p; ?>"/>
                        </tbody>
                    </table>
                </div>
            </div>
            @if(!Gate::denies('clubstaff'))
            <a class="btn btn-primary" href="{{action('App\Http\Controllers\StudentBehaviourController@create')}}">Add Behaviour Record</a>
            @endif
        </div>
    </div>
</div>
<script>
    document.cookie = "sid= " + document.getElementById('sid').value;
</script>
@endsection
</body>
</html>