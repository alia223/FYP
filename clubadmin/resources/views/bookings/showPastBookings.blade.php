@extends('layouts.app')
@section('content')

<div class="container" style="margin: 0; padding: 0;">
    <div class="row justify-content-center">
        <div class="col-md-3 ">
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
                                        btn-primary material-icons" title="Further Details of Booking">description</a></td>
                                    @endif
                                    @if(!Gate::denies('admin')) 
                                        <td class="text-center">
                                            <form action="{{action('App\Http\Controllers\PastBookingsController@destroy', $booking['id'])}}"
                                            method="post"> @csrf
                                                <input name="_method" type="hidden" value="DELETE">
                                                <button class="btn btn-danger material-icons" title="Delete Booking" type="submit">cancel</button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                {{$bookings->links()}}
            </div>
        </div>
    </div>
</div>
<script type="text/javascript"> 
    $('#past_bookings').addClass('active'); 
</script>
@endsection