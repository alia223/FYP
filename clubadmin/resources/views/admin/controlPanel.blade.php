@extends('layouts.app')
@section('content')
<div class="container" style="margin:0; padding:0;">
    <div class="row justify-content-center">
        <div class="col-md-3">
            <div class="sidebar">
                @include('sidebar')
            </div>
        </div>
        <div class="col-md-9" style="margin-top: 50px;">
            <div class="card">
                <div class="card-header" id="card-header">Control Panel</div>
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
                    <form class="form-horizontal" method="POST"
                        action="{{ url('rules') }}" enctype="multipart/form-data">
                        @csrf
                        <p class="font-weight-bold">Branding</p>
                        <div class="col-md-8">
                            <label for="brand_logo">Logo: </label>
                            <input type="file" name ="brand_logo" id="brand_logo" url="brand_logo" />
                        </div>
                        <div class="col-md-8">
                            <label for="brand_colour">Brand Colour: </label>
                            <input type="color" name="brand_colour" id="brand_colour" url="brand_colour" value="<?php echo $rules[0]; ?>" onchange="colourScheme()" />
                        </div>
                        <div class="col-md-8">
                            <label for="brand_colour">Text Colour: </label>
                            <input type="color" name ="text_colour" id="text_colour" url="text_colour" value="<?php echo $rules[1]; ?>" onchange="colourScheme()" />
                        </div>
                        <p class="font-weight-bold">Booking Rules</p>
                        <div class="col-md-8">
                            <label for="club_start">Club Start Time: </label>
                            <input type="time" name="club_start" id="club_start" url="club_start" value="<?php echo $rules[2]; ?>"/>
                        </div>
                        <div class="col-md-8">
                            <label for="club_end">Club End Time: </label>
                            <input type="time" name="club_end" id="club_end" url="club_end" value="<?php echo $rules[3]; ?>"/>
                        </div>
                        <div class="col-md-8">
                            <label for="club_duration_step">Club Duration Step (minutes): </label>
                            <input type="number" name="club_duration_step" id="club_duration_step" url="club_duration_step" value="<?php echo $rules[4]; ?>"/>
                        </div>
                        <div class="col-md-8">
                            <label for="booking_interval">Booking in Advance (days): </label>
                            <input type="number" name="booking_interval" id="booking_interval" url="booking_interval" value="<?php echo $rules[5]; ?>"/>
                        </div>
                        <p class="font-weight-bold">Other Club Rules</p>
                            <div class="row">
                                <label class="col-md-3" for="booking_interval">Staff to Student Ratio: </label>
                                <div class="col-md-9">
                                    <input type="number" value="1" style="width: 50px;"/>
                                    <span>:</span>
                                    <input type="number" name="student_ratio" url="student_ratio" value="<?php echo $rules[8]; ?>" style="width: 50px;"/>
                                </div>
                            </div>
                        <input id="submit" type="submit" class="btn btn-primary" />
                        <input id="reset" type="reset" class="btn btn-primary" onclick="resetColourScheme()"/>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $('#control_panel').addClass('active');
    function colourScheme() {
        var element_to_change = ["#navbar", "#navbar", "#card-header", "#control_panel", "#submit", "#reset"];
        for(var i = 0;i < element_to_change.length;i++) {
            $(element_to_change[i]).css({"background-color":$("#brand_colour").val(), "color": $("#text_colour").val()});
        }
    }

    function resetColourScheme() {
        location.reload();
    }
</script>
@endsection