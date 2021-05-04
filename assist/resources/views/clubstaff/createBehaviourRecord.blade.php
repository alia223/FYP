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
                  <a class="active" href="{{ route('club-students') }}">Students</a>
              @endif
          @endif
          <a href="{{ route('settings') }}">Settings</a>
        </div>
      </div>
      <div class="col-md-8" style="margin-top:50px;">
        <div class="card">
          <div class="card-header">{{ __('Behaviour Record') }}</div>
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
                    <form class="form-horizontal" method="POST" action="{{url('behaviours') }}" enctype="multipart/form-data">
                    @csrf
                      <div class="col-md-8">
                          <input type="hidden" id="studentid" name="studentid" value=""/>
                          <div class="col-md-8">
                              <label class="font-weight-bold">Date: </label>
                              <input type="date" name="date" required/>
                          </div>
                          <div class="col-md-8">
                              <label class="font-weight-bold">Stars: </label>
                              <input type="text" name="stars" required></textarea>
                          </div>
                          <div class="col-md-8">
                              <label class="font-weight-bold">Comment: </label>
                              <textarea name="comment" required></textarea>
                          </div>
                      </div>
                          <div class="col-md-8">
                              <input type="submit" class="btn"/>
                              <input type="reset" class="btn"/>
                          </div>
                      </div>
                    </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
console.log(document.cookie);
  var cookie = document.cookie.split(';');
  var cookieSplit = cookie[0].split('=');
  var sid = cookieSplit[1];
  console.log(sid);
  document.getElementById('studentid').value = sid;
</script>
@endsection
</body>
</html>