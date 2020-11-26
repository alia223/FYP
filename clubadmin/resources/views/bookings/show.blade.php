@extends('layouts.app')
@section('content')
<div class="container" style="margin-top: 80px;">
<div class="row justify-content-center">
<div class="col-md-8 ">
<div class="card">
<div class="card-header" style="background-color: rgb(132,0,255); color: white;">Display All Bookings</div>
<div class="card-body">
<table class="table table-striped">
    <thead>
        <tr>   
            <th>Booking ID</th>
            <th>User ID</th>
            <th>Date</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th colspan="3" style="color: black">Action</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{$booking['id']}}</td>
            <td>{{$booking['userid']}}</td>
            <td>{{$booking['booking_date']}}</td>
            <td>{{$booking['start_time']}}</td>
            <td>{{$booking['end_time']}}</td>
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
    </tbody>
</table>
</div>
</div>
</div>
</div>
</div>
@endsection