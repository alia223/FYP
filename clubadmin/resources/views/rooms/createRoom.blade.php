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
                        <a class="active" href="{{ route('club-rooms') }}">Rooms</a>
                        <a href="{{ route('activity-log') }}">Activity Log</a>
                        <a href="{{ route('control-panel') }}">Control Panel</a>
                    @elseif (Gate::denies('admin') && Gate::denies('clubstaff'))
                        <a href="{{ route('create-bookings') }}?ym=<?php $date = date('Y-m'); echo $date?>">Create a Booking</a>
                        <a href="{{ url('bookings') }}">View Upcoming Bookings</a>
                        <a href="{{ route('past-bookings') }}">View Past Bookings</a>
                        <a href="{{ route('club-students') }}">Children</a>
                    @elseif (Gate::denies('admin') && !Gate::denies('clubstaff'))
                        <a href="{{ url('bookings') }}">View Upcoming Bookings</a>
                        <a href="{{ route('student-register') }}">Student Register</a>
                        <a href="{{ route('student-behaviour-report') }}">Behaviour Record</a>
                        <a href="{{ route('student-injury-report') }}">Injury Record</a>
                    @endif
                @endif
                <a href="{{ route('settings') }}">Settings</a>
            </div>
        </div>
        <div class="col-md-9" style="margin-top: 50px;">
            <div class="card">
                <div class="card-header">Add a Room</div>
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
                        <form class="form-horizontal" method="POST"
                        action="{{url('rooms') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="col-md-8">
                                <label for="name" class="font-weight-bold">Room name: </label>
                                <input type="text" name="name" id="name" />
                            </div>
                            <div class="col-md-8">
                            <label for="staffid" class="font-weight-bold">Select staff member: </label>
                            <select name="staffid" id="staffid">
                                @foreach($staffs as $staff) 
                                    <option value="{{ $staff['id'] }}">{{ $staff['name'] }} {{ $staff['last_name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                            <div class="col-md-12 offset-md-1">
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
@endsection
</body>
</html>