@extends('layouts.app')
@section('content')
<div class="container" style="margin-top: 80px;">
    <div class="row justify-content-center">
        <div class="col-md-8 ">
            <div class="card">
                <div class="card-header" style="background-color: rgb(132,0,255); color: white;">Edit and update the Booking</div>
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div><br />
                    @endif
                    @if (\Session::has('success'))
                        <div class="alert alert-success">
                            <p>{{ \Session::get('success') }}</p>
                        </div><br />
                    @endif
                    <div class="card-body">
                    <form class="form-horizontal" method="POST" action="{{ action('App\Http\Controllers\BookingController@update', $booking['id']) }} " enctype="multipart/form-data" >
                        @method('PATCH')
                        @csrf
                        <div class="col-md-8">
                            <label >Date</label>
                            <input type="date" name="booking_date" value="{{$booking->booking_date}}"/>
                        </div>
                        <div class="col-md-8">
                            <label>Start Time</label>
                            <input type="time" name="start_time" value="{{ $booking->start_time }}" />
                        </div>
                        <div class="col-md-8">
                            <label>End Time</label>
                            <input type="time" name="end_time" value="{{ $booking->end_time }}" />
                        </div>
                        <div class="col-md-6 col-md-offset-4">
                            <input type="submit" class="btn btn-primary" />
                            <input type="reset" class="btn btn-primary" />
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection