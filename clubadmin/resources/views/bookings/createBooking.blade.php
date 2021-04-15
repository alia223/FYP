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
                <div class="card-header">Create a New Booking</div>
                    <!-- display the errors -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul> @foreach ($errors->all() as $error)<li>{{ $error }}</li> @endforeach</ul>
                        </div><br /> 
                    @endif
                    <!-- display the success status -->
                    @if (\Session::has('success'))
                    <div class="alert alert-success">
                        <p>{{ \Session::get('success') }}</p>
                    </div><br /> 
                    @endif
                    <!-- define the form -->
                    <div class="card-body">
                        <p class="text-center font-weight-bold" style="color: red;">Club runs from {{ $rules[2] }} to {{ $rules[3] }}</p>
                        <form id="booking-form" action="{{ url('bookings') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="col-md-12">
                            <label for="booking_length" class="font-weight-bold">How long would you like your child to attend the club for? <span class="red-star">*</span></label>
                            <table style="width:60%;">
                            <tr><th></th></tr>
                            <?php 
                            $termination = (strtotime($rules[3])-strtotime($rules[2]))/60;
                            for($i = 0;$i <= $termination/intval($rules[4]);$i++) {
                                echo '<tr>';
                                for($j = 0; $j < 4; $j++) {
                                    if($i !== $termination/intval($rules[4])) {
                                        $i++;
                                        $val = intval($rules[4]) * $i;
                                        echo '<td><div class="row offset-md-1">';
                                        echo '<input type="radio" id="'.$val.'" name="booking_length" id="booking_length" value="'.$val.'"/>&nbsp&nbsp';
                                        echo '<label for="'.$val.'">';
                                        if($val == 60) {
                                            $val = $val/60;
                                            echo $val." hour";
                                        }
                                        else {
                                            $val = $val/60;
                                            echo $val." hours";
                                        }
                                        echo '</label><br>';
                                        echo '</div></td>';
                                    }
                                }
                                echo '</tr>';
                                
                            }?>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <p class="font-weight-bold">Which children will be attending? <span class="red-star">*</span></p>
                            <?php    
                                if(sizeof($students) == 0) {
                                    echo '<a href="'.action('App\Http\Controllers\StudentController@index').'">Add a child</a>';
                                }
                                else {
                                    foreach($students as $student) {
                                        echo '<input style="margin-right:10px;" type="checkbox" name="students[]" value="'.$student['id'].'" />'
                                        .'<label for="students[]">'.$student['first_name'].' '.$student['last_name'].'</label>&nbsp&nbsp';
                                    }
                                }
                            ?>
                        </div>
                        <div class="col-md-12">
                            <p class="font-weight-bold">Would you like to set up a repeat booking?</p>
                            <input type="checkbox" name="repeat_booking" id="repeat_booking" />
                            <label for="repeat_booking">Repeat Booking</label>
                            <div id="recursive_end_date">
                                <label for="recursive_end_date">Repeat Until: </label><span class="red-star">*</span><br />
                                <input type="date" name="recursive_end_date" /><br />
                            </div>
                            <div id="recursive_days">
                                <input type="checkbox" name="recursive_days[]" value="1">
                                <label for="recursive_days[]">Monday</label>
                                <input type="checkbox" name="recursive_days[]" value="2">
                                <label for="recursive_days[]">Tuesday</label>
                                <input type="checkbox" name="recursive_days[]" value="3">
                                <label for="recursive_days[]">Wednesday</label>
                                <input type="checkbox" name="recursive_days[]" value="4">
                                <label for="recursive_days[]">Thursday</label>
                                <input type="checkbox" name="recursive_days[]" value="5">
                                <label for="recursive_days[]">Friday</label>
                            </div>
                        </div>
                        <input type="hidden" name="date" value="<?php echo $date; ?>"/>
                        <div class="col-md-12">
                            <input type="submit" class="btn" id="submit" />
                            <input type="reset" class="btn"/>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript"> 
    $('#bookings').addClass('active');
    $('#recursive_end_date').hide();
    $('#recursive_days').hide();
    $('#repeat_booking').change(function(){
        if($('#repeat_booking').is(":checked")) {
            $('#recursive_end_date').show();
            $('#recursive_days').show();
            $('#booking-form').attr('action', "{{ url('repeat-bookings') }}");
        }
        else {
            $('#recursive_end_date').hide();
            $('#recursive_days').hide();
            $('#booking-form').attr('action', "{{ url('bookings') }}");
        }
    });

</script>
@endsection