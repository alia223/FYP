@extends('layouts.app')
@section('content')

<div class="container" style="margin: 0; padding: 0;">
    <div class="row justify-content-center">
        <div class="col-md-3">
            <div class="sidebar">
                @include('sidebar')            
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
                                    <th class="text-center">Parent ID</th>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Start Time</th>
                                    <th class="text-center">End Time</th>
                                    @if(!Gate::denies('admin'))
                                        <th class="text-center" style="color: black;">Cancelled</th>
                                    @endif
                                    <th class="text-center" colspan="5" style="color: black;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookings as $booking)
                                <tr>
                                    <td class="text-center">{{ $booking->id }}</td>
                                    <td class="text-center">{{ $booking->user_id }}</td>
                                    <td class="text-center">{{ $booking->booking_date }}</td>
                                    <td class="text-center">{{ $booking->start_time }}</td>
                                    <td class="text-center">{{ $booking->end_time }}</td>
                                    @if(!Gate::denies('admin'))
                                        <td class="text-center" style="color:black;">{{ $booking->deleted_at }}</td>
                                    @endif
                                    @if(Gate::denies('clubstaff'))
                                        @if($bookings->where('id', $booking->id)->first()->deleted_at == null)
                                            <td><a href="{{ action('App\Http\Controllers\BookingController@edit', $booking->id) }}" class="btn
                                            btn-warning material-icons" title="Edit Booking">build</a></td>
                                        @else
                                            <td><a href="" class="btn
                                            btn-warning material-icons" style="background-color: grey" title="Edit Booking">build</a></td>
                                        @endif
                                        <td class="text-center">
                                            <form action="{{ action('App\Http\Controllers\BookingController@destroy', $booking->id) }}"
                                            method="post"> @csrf
                                                <input name="_method" type="hidden" value="DELETE">
                                                <button class="btn btn-danger material-icons" title="Cancel Single Booking" type="submit">close</button>
                                            </form>
                                        </td>
                                        <td class="text-center">
                                            <form action="{{ action('App\Http\Controllers\RepeatBookingController@destroy', $booking->id) }}"
                                            method="post"> @csrf
                                                <input name="_method" type="hidden" value="DELETE">
                                                <button class="btn btn-danger material-icons" title="Cancel Repeat Booking" type="submit">cancel</button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @if(Gate::denies('admin') && Gate::denies('clubstaff') && $date >= date("Y-m-d")) 
                <a class="btn" href="<?php echo action('App\Http\Controllers\BookingController@create', ['date' => date('Y-m-d', strtotime($date))]) ?>">Add a Booking</a>
                @endif
            </div>
        </div>
    </div>
</div>
<script type="text/javascript"> 
    $('#bookings').addClass('active'); 
</script>
@endsection