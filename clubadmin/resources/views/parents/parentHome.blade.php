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
        <div class="sidebar" style="margin: 0; padding: 0;">
          <a class="active" href="{{ route('home') }}">Home</a>
          <a href="{{ url('bookings/create') }}?ym=<?php $date = date('Y-m'); echo $date?>">Create a Booking</a>
          <a href="{{ url('bookings') }}">View Upcoming Bookings</a>
          <a href="{{ route('past-bookings') }}">View Past Bookings</a>
          <a href="">Add a Child</a>
          <a href="">Settings</a>
        </div>
      </div>
      <div class="col-md-8" style="margin-top:50px;">
        <div class="card">
          <div class="card-header" style="background-color: rgb(132,0,255); color: white;">{{ __('Dashboard') }}</div>
          <div class="card-body">
            <p>Hello {{Auth::user()->name}}, you are logged in!</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
</body>
</html>