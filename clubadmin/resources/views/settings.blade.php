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
      <div class="col-md-4">
        <div class="sidebar">
        <a href="{{ route('home') }}">Home</a>
            @if(Auth::check())
                @if (!Gate::denies('admin') && Gate::denies('clubstaff'))
                    <a href="{{ url('bookings') }}">View Upcoming Bookings</a>
                    <a href="{{ route('past-bookings') }}">View Past Bookings</a>
                    <a href="{{ route('club-rooms') }}">Rooms</a>
                    <a href="{{ route('activity-log') }}">Activity Log</a>
                    <a href="{{ route('control-panel') }}">Control Panel</a>
                @elseif (Gate::denies('admin') && Gate::denies('clubstaff'))
                    <a href="{{ route('create-bookings') }}?ym=<?php $date = date('Y-m'); echo $date?>">Create a Booking</a>
                    <a href="{{ url('bookings') }}">View Upcoming Bookings</a>
                    <a href="{{ route('past-bookings') }}">View Past Bookings</a>
                    <a href="{{ route('club-students') }}">Children</a>
                @elseif (Gate::denies('admin') && !Gate::denies('clubstaff'))
                    <a href="{{ url('bookings') }}">View Upcoming Bookings</a>
                    <a href="{{ route('student-register') }}">Register</a>
                    <a href="{{ route('club-students') }}">Students</a>
                @endif
            @endif
            <a class="active" id="active" href="{{ route('settings') }}">Settings</a>
        </div>
      </div>
      <div class="col-md-8" style="margin-top: 50px;">
        <div class="card">
            <div class="card-header">Settings</div>
                <div class="card-body">
                @foreach($user as $u)
                    <form class="form-horizontal" method="POST" action="{{ action('App\Http\Controllers\SettingsController@update', $u['id']) }} " enctype="multipart/form-data" >
                        @method('PATCH')
                        @csrf
                        <div class="col-md-8">
                            <label >First Name: </label>
                            <input type="text" name="user_first_name" value="{{$u->name}}"/>
                        </div>
                        <div class="col-md-8">
                            <label >Last Name: </label>
                            <input type="text" name="user_last_name" value="{{$u->last_name}}"/>
                        </div>
                        <div class="col-md-8">
                            <label>Email: </label>
                            <input type="text" name="user_email" value="{{$u->email}}" />
                        </div>
                        <div class="col-md-8">
                            <label>Telephone: </label>
                            <input type="text" name="user_telephone" value="{{$u->telephone}}" />
                        </div>
                        <div class="col-md-8">
                            <label>Mobile: </label>
                            <input type="text" name="user_mobile" value="{{$u->mobile}}" />
                        </div>
                        <div class="col-md-6 col-md-offset-4">
                            <input type="submit" class="btn btn-primary" value="Save Changes"/>
                            <input type="reset" class="btn btn-primary" />
                            </a>
                        </div>
                        @endforeach
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