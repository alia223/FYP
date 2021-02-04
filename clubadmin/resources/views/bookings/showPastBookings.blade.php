@extends('layouts.app')
@section('content')

<div class="container" style="margin: 0; padding: 0;">
    <div class="row justify-content-center">
        <div class="col-md-3 ">
            <div class="sidebar">
            <a href="{{ route('home') }}">Home</a>
            @if(Auth::check())
                    @if (!Gate::denies('admin') && Gate::denies('clubstaff'))
                        <a href="{{ url('bookings') }}">View Upcoming Bookings</a>
                        <a class="active" href="{{ route('past-bookings') }}">View Past Bookings</a>
                        <a href="{{ route('club-rooms') }}">Rooms</a>
                        <a href="{{ route('activity-log') }}">Activity Log</a>
                        <a href="{{ route('control-panel') }}">Control Panel</a>
                    @elseif (Gate::denies('admin') && Gate::denies('clubstaff'))
                        <a href="{{ route('create-bookings') }}?ym=<?php $date = date('Y-m'); echo $date?>">Create a Booking</a>
                        <a href="{{ url('bookings') }}">View Upcoming Bookings</a>
                        <a class="active" href="{{ route('past-bookings') }}">View Past Bookings</a>
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
                <div class="card-header">Display all bookings</div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>   
                                    <th class="text-center">Booking ID</th>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Start Time</th>
                                    <th class="text-center">End Time</th>
                                    @if(!Gate::denies('admin'))
                                        <th class="text-center" style="color:black;">Cancelled</th>
                                    @endif
                                        <th class="text-center" colspan="3" style="color: black;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookings as $booking)
                                <tr>
                                    <td class="text-center">{{$booking['id']}}</td>
                                    <td class="text-center">{{$booking['booking_date']}}</td>
                                    <td class="text-center">{{$booking['start_time']}}</td>
                                    <td class="text-center">{{$booking['end_time']}}</td>
                                    @if(!empty($booking['deleted_at']) && !Gate::denies('admin'))
                                        <td class="text-center">{{$booking['deleted_at']}}</td>
                                    @elseif(empty($booking['deleted_at']) && !Gate::denies('admin'))
                                        <td class="text-center">No</td>
                                    @endif
                                    @if(Gate::denies('clubstaff'))
                                        <td class="text-center"><a href="{{action('App\Http\Controllers\PastBookingsController@show', $booking['id'])}}" class="btn
                                        btn-primary">Details</a></td>
                                    @endif
                                    @if(!Gate::denies('admin')) 
                                        <td class="text-center">
                                            <form action="{{action('App\Http\Controllers\PastBookingsController@destroy', $booking['id'])}}"
                                            method="post"> @csrf
                                                <input name="_method" type="hidden" value="DELETE">
                                                <button class="btn btn-danger" type="submit"> Delete</button>
                                            </form>
                                        </td>
                                    @endif
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