@extends('layouts.app')
@section('content')
<div class="container" style="margin:0; padding:0;">
    <div class="row justify-content-center">
        @include('sidebar')
        <div class="offset-md-1 col-md-9" style="margin-top: 50px;">
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
                        <div class="row">
                            <label>
                                <span class="font-weight-bold">Branding</span>
                                <div class="col-md-12">
                                    <label for="brand_logo">Logo: 
                                        <input type="file" name ="brand_logo" id="brand_logo" url="brand_logo" />
                                    </label>
                                </div>
                                <div class="col-md-12">
                                    <label for="brand_colour">Brand Colour:                             
                                        <input type="color" name="brand_colour" id="brand_colour" url="brand_colour" value="<?php echo $rules->brand_colour; ?>" onchange="colourScheme()" />
                                    </label>
                                </div>
                                <div class="col-md-12">
                                    <label for="brand_colour">Text Colour: 
                                        <input type="color" name ="text_colour" id="text_colour" url="text_colour" value="<?php echo $rules->text_colour; ?>" onchange="colourScheme()" />
                                    </label>
                                </div>
                            </label>
                        </div>
                        <div class="row">
                            <label>
                                <span class="font-weight-bold">Booking Rules</span>
                                <div class="col-md-12">
                                    <label for="club_start">Club Start Time: 
                                        <input type="time" name="club_start" id="club_start" url="club_start" value="<?php echo $rules->club_start; ?>"/>
                                    </label>
                                </div>
                                <div class="col-md-12">
                                    <label for="club_end">Club End Time: 
                                        <input type="time" name="club_end" id="club_end" url="club_end" value="<?php echo $rules->club_end; ?>"/>
                                    </label>
                                </div>
                                <div class="col-md-12">
                                    <label for="club_duration_step">Club Duration Step (minutes): 
                                        <input type="number" name="club_duration_step" id="club_duration_step" url="club_duration_step" value="<?php echo $rules->club_duration_step; ?>"/>
                                    </label>
                                </div>
                            </label>
                        </div>
                        <div class="row">
                            <label>
                                <span class="font-weight-bold">Other Club Rules</span>
                                <div class="col-md-12">
                                    <label for="booking_interval">Staff to Pupil Ratio: 
                                        1
                                        <span>:</span>
                                        <input type="number" name="pupil_ratio" url="pupil_ratio" value="<?php echo $rules->pupil_ratio; ?>" style="width: 50px;"/>
                                    </label>
                                </div>
                            </label>
                        </div>
                        <div class="row">
                            <div class="offset-sm-4 cold-sm-4">
                                <input id="submit" type="submit" class="btn btn-primary" />
                                <input id="reset" type="reset" class="btn btn-primary" onclick="resetColourScheme()"/>
                            </div>
                        </div>
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

    $('#club_start').change(function(){
        if($('#club_start').val() > $('#club_end').val()) {
            $('#club_start')[0].setCustomValidity("Club start time must be earlier than club end time."); 
        }
        else {
            $('#club_start')[0].setCustomValidity(""); 
        }
    });

    $('#club_end').change(function(){
        if($('#club_start').val() > $('#club_end').val()) {
            $('#club_end')[0].setCustomValidity("Club end time must be later than club start time."); 
        }
        else {
            $('#club_end')[0].setCustomValidity(""); 
        }
    });
</script>
@endsection