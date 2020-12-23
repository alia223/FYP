@extends('layouts.app')
@section('content')

<div class="container" style="margin: 0; padding: 0;">
    <div class="row justify-content-center">
        <div class="col-md-3 ">
            <div class="sidebar" style="height:screen-height;">
            <a href="{{ route('home') }}">Home</a>
            @if(Auth::check())
                @if (!Gate::denies('admin'))
                    <a class="active" href="{{ url('bookings') }}">View Upcoming Bookings</a>
                    <a href="{{ route('past-bookings') }}">View Past Bookings</a>
                    <a href="{{ route('activity-log') }}">Activity Log</a>
                    <a href="">Control Panel</a>
                @else
                    <a href="{{ url('bookings/create') }}?ym=<?php $date = date('Y-m'); echo $date?>">Create a Booking</a>
                    <a class="active" href="{{ url('bookings') }}">View Upcoming Bookings</a>
                    <a href="{{ route('past-bookings') }}">View Past Bookings</a>
                    <a href="">Add a Child</a>
                @endif
            @endif
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
                                    <th class="text-center">Booking ID</th>
                                    <th class="text-center">Parent/Guardian</th>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Start Time</th>
                                    <th class="text-center">End Time</th>
                                    <th class="text-center" colspan="3" style="color: black;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookings as $booking)
                                <tr>
                                    <td class="text-center">{{$booking['id']}}</td>
                                    <td class="text-center">{{$booking['name']}}</td>
                                    <td class="text-center">{{$booking['booking_date']}}</td>
                                    <td class="text-center">{{$booking['start_time']}}</td>
                                    <td class="text-center">{{$booking['end_time']}}</td>
                                    <td class="text-center"><a href="{{action('App\Http\Controllers\BookingController@show', $booking['id'])}}" class="btn
                                    btn- primary">Details</a></td>
                                    <td><a href="{{action('App\Http\Controllers\BookingController@edit', $booking['id'])}}" class="btn
                                    btn- warning">Edit</a></td>
                                    <td class="text-center">
                                        <form action="{{action('App\Http\Controllers\BookingController@destroy', $booking['id'])}}"
                                        method="post"> @csrf
                                            <input name="_method" type="hidden" value="DELETE">
                                            <button class="btn btn-danger" type="submit"> Cancel</button>
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