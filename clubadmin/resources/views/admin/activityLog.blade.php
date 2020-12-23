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
        <div class="sidebar" style="margin: 0; padding: 0; height:100%;">
          <a href="{{ route('home') }}">Home</a>
          <a href="{{ url('bookings') }}">View Upcoming Bookings</a>
          <a href="{{ route('past-bookings') }}">View Past Bookings</a>
          <a class="active" href="{{ route('activity-log') }}">Activity Log</a>
          <a href="">Control Panel</a>
          <a href="">Settings</a>
        </div>
      </div>
      <div class="col-md-8" style="margin-top: 50px;">
            <div class="card">
                <div class="card-header" style="background-color: rgb(132,0,255); color: white;">Display all activities</div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>   
                                    <th  class="text-center">Action</th>
                                    <th  class="text-center">Booking ID</th>
                                    <th  class="text-center">User ID</th>
                                    <th  class="text-center">User</th>
                                    <th  class="text-center">Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activities as $activity)
                                <tr>
                                    <td class="text-center">{{$activity['action']}}</td>
                                    <td  class="text-center">{{$activity['booking_id']}}</td>
                                    <td  class="text-center">{{$activity['userid']}}</td>
                                    <td  class="text-center">{{$activity['user']}}</td>
                                    <td  class="text-center">{{$activity['created_at']}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>
</div>
@endsection
</body>
</html>