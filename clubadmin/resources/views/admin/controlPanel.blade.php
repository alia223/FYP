@extends('layouts.app')
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
                        <a href="{{ route('club-rooms') }}">Rooms</a>
                        <a href="{{ route('activity-log') }}">Activity Log</a>
                        <a class="active" id="active" href="{{ route('control-panel') }}">Control Panel</a>
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
                <a href="{{ route('settings') }}">Settings</a>
            </div>
        </div>
        <div class="col-md-9" style="margin-top: 50px;">
            <div class="card">
                <div class="card-header" id="card-header">Control Panel</div>
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div><br />
                    @endif
                    @if (\Session::has('success'))
                        <div class="alert alert-success">
                            <p>{{ \Session::get('success') }}</p>
                        </div><br />
                    @endif
                    <div class="card-body">
                    <form class="form-horizontal" method="POST"
                        action="{{ url('rules') }}" enctype="multipart/form-data">
                        @csrf
                        <p class="font-weight-bold">Colour Scheme</p>
                        <div class="col-md-8">
                            <label for="brand_colour">Brand Colour: </label>
                            <input type="color" id="brand_colour" name="brand_colour" value="<?php echo $rules[0]; ?>" onchange="colourScheme()" />
                        </div>
                        <div class="col-md-8">
                            <label for="brand_colour">Text Colour: </label>
                            <input type="color" id="text_colour" name="text_colour" value="<?php echo $rules[1]; ?>" onchange="colourScheme()" />
                        </div>
                        <p class="font-weight-bold">Booking Rules</p>
                        <div class="col-md-8">
                            <label for="club_start">Club Start Time: </label>
                            <input type="time" id="club_start" name="club_start" value="<?php echo $rules[2]; ?>"/>
                        </div>
                        <div class="col-md-8">
                            <label for="club_end">Club End Time: </label>
                            <input type="time" id="club_end" name="club_end" value="<?php echo $rules[3]; ?>"/>
                        </div>
                        <div class="col-md-8">
                            <label for="room_capacity">Room Capacity: </label>
                            <input type="number" id="room_capacity" name="room_capacity" value="<?php echo $rules[6]; ?>"/>
                        </div>
                        <div class="col-md-8">
                            <label for="club_duration_step">Club Duration Step (minutes): </label>
                            <input type="number" id="club_duration_step" name="club_duration_step" value="<?php echo $rules[4]; ?>"/>
                        </div>
                        <div class="col-md-8">
                            <label for="booking_interval">Booking Interval (days): </label>
                            <input type="number" id="booking_interval" name="booking_interval" value="<?php echo $rules[5]; ?>"/>
                        </div>
                        <input id="submit" type="submit" class="btn btn-primary" />
                        <input id="reset" type="reset" class="btn btn-primary" onclick="resetColourScheme()"/>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="../js/colourScheme.js"></script>
@endsection