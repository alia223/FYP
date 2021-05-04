@extends('layouts.app')
@section('content')
<div class="container" style="margin: 0; padding: 0;">
    <div class="row justify-content-center">
        @include('sidebar')
        <div class="offset-md-1 col-md-9" style="margin-top: 50px;">
            <div class="card">
                <div class="card-header">Create a New Booking</div>
                    <!-- display the errors -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul> 
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <!-- display the success status -->
                    @if (\Session::has('success'))
                    <div class="alert alert-success">
                        <p>{{ \Session::get('success') }}</p>
                    </div><br /> 
                    @endif
                    <!-- define the form -->
                    <div class="card-body">
                        <p class="text-center font-weight-bold" style="color: red;">Club runs from {{ $rules->club_start }} to {{ $rules->club_end }}</p>
                        <form id="booking_form" action="{{ url('bookings') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="booking_length"><p class="font-weight-bold" style="margin:0;">What time would you like your child to attend the club until?<span class="red-star">*</span></p>                                   
                                        @for($i = strtotime($rules->club_start) + $rules->club_duration_step*60;$i <= strtotime($rules->club_end);$i += $rules->club_duration_step*60)
                                            <label for="booking_length">
                                                <input type="radio" id="booking_length" name="booking_length" value="{{ ($i - strtotime($rules->club_start)) / 60 }}" />
                                                {{ date('H:i', $i) }}
                                            </label>
                                        @endfor
                                    </label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label><span class="font-weight-bold">Which children will be attending?<span class="red-star">*</span></span><br />
                                        @if(sizeof($pupils) == 0)
                                            <a href="action('App\Http\Controllers\PupilController@index')">Add a child</a>
                                        @else
                                            @foreach($pupils as $pupil)
                                                <label for="pupils[]">
                                                    <input type="checkbox" name="pupils[]" value="{{ $pupil->id }}">
                                                    {{ $pupil->first_name }} {{ $pupil->last_name }}
                                                </label>
                                            @endforeach
                                        @endif
                                    </label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label><span class="font-weight-bold">Would you like to set up a repeat booking?</span><br />
                                        <label for="repeat_booking">
                                            <input type="checkbox" name="repeat_booking" id="repeat_booking" />Repeat Booking
                                        </label>
                                    </label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div id="recursive_days">
                                        <label for="recursive_days[]"><span class="font-weight-bold">Days:<span class="red-star">*</span></span><br />
                                        <label for="recursive_days[]">
                                            <input type="checkbox" name="recursive_days[]" value="1"> Monday
                                        </label>
                                        <label for="recursive_days[]">
                                            <input type="checkbox" name="recursive_days[]" value="2">Tuesday
                                        </label>
                                        <label for="recursive_days[]">
                                            <input type="checkbox" name="recursive_days[]" value="3">Wednesday
                                        </label>
                                        <label for="recursive_days[]">
                                            <input type="checkbox" name="recursive_days[]" value="4">Thursday
                                        </label>
                                        <label for="recursive_days[]">
                                            <input type="checkbox" name="recursive_days[]" value="5">Friday
                                        </label>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div id="recursive_end_date">
                                        <label for="recursive_end_date" class="font-weight-bold" >Repeat Until:<span class="red-star">*</span><br />
                                            <input type="date" name="recursive_end_date" />
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="date" value="{{ $date }}"/>
                            <div class="row">
                                <div class="offset-md-4 col-md-4">
                                    <input type="submit" class="btn" id="submit" />
                                    <input type="reset" class="btn"/>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript"> 
    $('#admin_bookings').addClass('active');
    $('#clubstaff_bookings').addClass('active');
    $('#parent_bookings').addClass('active');
    $('#recursive_end_date').hide();
    $('#recursive_days').hide();
    $('#repeat_booking').change(function(){
        if($('#repeat_booking').is(":checked")) {
            $('#recursive_end_date').show();
            $('#recursive_days').show();
            $('#booking_form').attr('action', "{{ url('repeat-bookings') }}");
        } else {
            $('#recursive_end_date').hide();
            $('#recursive_days').hide();
            $('#booking_form').attr('action', "{{ url('bookings') }}");
        }
    });
</script>
@endsection