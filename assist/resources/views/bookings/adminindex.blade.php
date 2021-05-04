@extends('layouts.app')
@section('content')

<div class="container" style="margin: 0; padding: 0;">
    <div class="row justify-content-center">
        <div class="col-md-3">
            <div class="sidebar">
            <a href="{{ route('home') }}">Home</a>
            <a class="active" href="{{ url('bookings') }}">View Upcoming Bookings</a>
            <a href="">View Past Bookings</a>
            <a href="">Control Panel</a>
            <a href="">Settings</a>
            </div>
        </div>
        <div class="col-md-9" style="margin-top: 50px;">
            <div class="card">
                <div class="card-header" style="background-color: rgb(132,0,255); color: white;">Display all bookings</div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>   
                                    <th>Booking ID</th>
                                    <th>user ID</th>
                                    <th>Parent/Guardian</th>
                                    <th>Date</th>
                                    <th>Start Time</th>
                                    <th style="color: black;">End Time</th>
                                    <th class="text-center" colspan="3" style="color: black">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookings as $booking)
                                <tr>
                                    <td>{{$booking['id']}}</td>
                                    <td>{{$booking['userid']}}</td>
                                    <td>{{$booking['name']}}</td>
                                    <td>{{$booking['booking_date']}}</td>
                                    <td>{{$booking['start_time']}}</td>
                                    <td style="color: black;">{{$booking['end_time']}}</td>
                                    <td><a href="{{action('App\Http\Controllers\BookingController@show', $booking['id'])}}" class="btn
                                    btn- primary">Details</a></td>
                                    <td><a href="{{action('App\Http\Controllers\BookingController@edit', $booking['id'])}}" class="btn
                                    btn- warning">Edit</a></td>
                                    <td>
                                        <form action="{{action('App\Http\Controllers\BookingController@destroy', $booking['id'])}}"
                                        method="post"> @csrf
                                            <input name="_method" type="hidden" value="DELETE">
                                            <button class="btn btn-danger" type="submit"> Delete</button>
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
</div>
@endsection