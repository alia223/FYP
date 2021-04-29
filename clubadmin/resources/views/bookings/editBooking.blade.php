@extends('layouts.app')
@section('content')
<div class="container" style="margin:0; padding:0;">
    <div class="row justify-content-center">
        @include('sidebar')
        <div class="offset-md-1 col-md-9" style="margin-top:50px;">
            <div class="card">
                <div class="card-header">Edit and update the Booking</div>
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
                    <form class="form-horizontal" id="form" action="{{ action('App\Http\Controllers\BookingController@update', $booking->id) }}" method="POST" enctype="multipart/form-data" >
                        @method('PATCH')
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <label for="booking_length"><p class="font-weight-bold" style="margin:0;">What time would you like your child to attend the club until?<span class="red-star">*</span></p>                                   
                                    @for($i = strtotime($rules->club_start) + $rules->club_duration_step*60;$i <= strtotime($rules->club_end);$i += $rules->club_duration_step*60)
                                        <label for="booking_length">
                                            <input type="radio" id="booking_length" name="booking_length" value="{{ ($i - strtotime($rules->club_start)) / 60 }}" 
                                            <?php 
                                                if($booking->duration == (($i - strtotime($rules->club_start)) / 60)) {
                                                    echo 'checked';
                                                }
                                            ?>
                                            />
                                            {{ date('H:i', $i) }}
                                        </label>
                                    @endfor
                                </label>
                            </div>
                        </div>
                        <p class="font-weight-bold">Which children will be attending?</p>
                        <?php $i = 0; ?>
                            @foreach($pupils as $pupil)
                                @if(sizeof($booked_pupils->where('pupil_id', $pupil->id)) > 0)
                                    <label for="pupils[]">
                                        <input style="margin-right:10px;" type="checkbox" name="pupils[]" value="{{ $pupil->id }}"  checked/>
                                        {{ $pupil->first_name }} {{ $pupil->last_name }}
                                    </label>
                                @else
                                <label for="pupils[]">
                                        <input style="margin-right:10px;" type="checkbox" name="pupils[]" value="{{ $pupil->id }}" />
                                        {{ $pupil->first_name }} {{ $pupil->last_name }}
                                    </label>
                                @endif
                                <? $i++; ?>
                            @endforeach
                            @if(sizeof($bookings->where('event_id', $booking->event_id)) > 1)
                                <label for="repeat_apply" class="font-weight-bold">This is a repeat booking. <br />Tick the box below if you would like to make this change to all of the other bookings too.</label><br />
                                <input type="checkbox" id="repeat_apply" /><br />
                            @endif
                            <div class="row">
                            <div class="offset-md-4 col-md-4">
                                <input type="submit" class="btn" id="submit" />
                                <input type="reset" class="btn"/>
                            </div>
                        </div>
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript"> 
    $('#admin_bookings').addClass('active');
    $('#clubstaff_bookings').addClass('active');
    $('#parent_bookings').addClass('active');
    $('#repeat_apply').change(function() {
        if($('#repeat_apply').is(":checked")) {
            $('#form').attr('action', "{{ action('App\Http\Controllers\RepeatBookingController@update', $booking->id) }}");
        }
        else {
            $('#form').attr('action', "{{ action('App\Http\Controllers\BookingController@update', $booking->id) }}");
        }
    });
</script>
@endsection