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
            <th class="text-center">Booking ID</th>
            <th class="text-center">User ID</th>
            <th class="text-center">Date</th>
            <th class="text-center">Start Time</th>
            <th class="text-center">End Time</th>
            <th class="text-center" colspan="3" style="color: black">Action</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="text-center">{{$booking['id']}}</td>
            <td class="text-center">{{$booking['userid']}}</td>
            <td class="text-center">{{$booking['booking_date']}}</td>
            <td class="text-center">{{$booking['start_time']}}</td>
            <td class="text-center">{{$booking['end_time']}}</td>
            <td class="text-center"><a href="{{action('App\Http\Controllers\BookingController@edit', $booking['id'])}}" class="btn
            btn- warning">Edit</a></td>
            <td>
                <form action="{{action('App\Http\Controllers\BookingController@destroy', $booking['id'])}}"
                method="post"> @csrf
                    <input name="_method" type="hidden" value="DELETE">
                    <button class="btn btn-danger text-center" type="submit"> Cancel</button>
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