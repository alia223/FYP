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
                    <form class="form-horizontal" id="form" action="{{ action('App\Http\Controllers\BookingController@update', $booking['id']) }}" method="POST" enctype="multipart/form-data" >
                        @method('PATCH')
                        @csrf
                        <label for="booking_length" class="font-weight-bold">How long would you like your child to attend the club for?</label>
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
                                    if($booking->duration == $val) {
                                        echo '<input type="radio" id="'.$val.'" name="booking_length" id="booking_length" value="'.$val.'" checked/>&nbsp&nbsp';
                                    }
                                    else {
                                        echo '<input type="radio" id="'.$val.'" name="booking_length" id="booking_length" value="'.$val.'"/>&nbsp&nbsp';
                                    }
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
                        <p class="font-weight-bold">Which children will be attending?</p>
                        <?php    
                            $i = 0;
                            foreach($students as $student) {
                                if(sizeof($booked_students->where('studentid', $student['id'])) > 0) {
                                    echo '<input style="margin-right:10px;" type="checkbox" name="students[]" value="'.$student['id'].'"  checked/>'
                                .'<label for="students[]">'.$student['first_name'].' '.$student['last_name'].'</label>&nbsp&nbsp';
                                }
                                else {
                                    echo '<input style="margin-right:10px;" type="checkbox" name="students[]" value="'.$student['id'].'" />'
                                .'<label for="students[]">'.$student['first_name'].' '.$student['last_name'].'</label>&nbsp&nbsp';
                                }
                                $i++;
                            }
                            if(!Gate::denies('admin')) {
                                if(sizeof($booked_students) == 0) {
                                    echo '<p style="color: red">No children were selected</p>';
                                }
                            }
                        ?><br />
                        @if(sizeof($bookings->where('eventid', $booking['eventid'])) > 1)
                            <label for="repeat_apply" class="font-weight-bold">This is a repeat booking. <br />Tick the box below if you would like to make this change to all of the other bookings too.</label><br />
                            <input type="checkbox" id="repeat_apply" /><br />
                        @endif
                        <input type="submit" id="submit" class="btn btn-primary" />
                        <input type="reset" class="btn btn-primary" />
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript"> 
    $('#bookings').addClass('active'); 
    $('#repeat_apply').change(function() {
        if($('#repeat_apply').is(":checked")) {
            $('#form').attr('action', "{{ action('App\Http\Controllers\RepeatBookingController@update', $booking['id']) }}");
        }
        else {
            $('#form').attr('action', "{{ action('App\Http\Controllers\BookingController@update', $booking['id']) }}");
        }
    });
</script>
@endsection