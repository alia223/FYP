@extends('layouts.app')
@section('content')
<div class="container" style="margin: 0; padding: 0;">
    <div class="row justify-content-center">
        <div class="col-md-3">
            <div class="sidebar">
                <a href="{{ route('home') }}">Home</a>
                @if(Auth::check())
                    @if (!Gate::denies('admin') && Gate::denies('clubstaff'))
                        <a href="{{ url('bookings') }}">View Upcoming Bookings</a>
                        <a href="{{ route('past-bookings') }}">View Past Bookings</a>
                        <a href="{{ route('activity-log') }}">Activity Log</a>
                        <a href="{{ route('control-panel') }}">Control Panel</a>
                    @elseif (Gate::denies('admin') && Gate::denies('clubstaff'))
                        <a class="active" href="{{ route('create-bookings') }}?ym=<?php $date = date('Y-m'); echo $date; ?>">Create a Booking</a>
                        <a href="{{ url('bookings') }}">View Upcoming Bookings</a>
                        <a href="{{ route('past-bookings') }}">View Past Bookings</a>
                        <a href="{{ route('club-students') }}">Children</a>
                    @elseif (Gate::denies('admin') && !Gate::denies('clubstaff'))
                        <a href="{{ url('bookings') }}">View Upcoming Bookings</a>
                        <a href="{{ route('student-register') }}">Register</a>
                        <a href="{{ route('club-students') }}">Students</a>
                    @endif
                @endif
                <a href="{{ route('settings') }}">Settings</a>
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
                        <form class="form-horizontal" method="POST"
                        action="{{url('bookings') }}" enctype="multipart/form-data">
                        @csrf
                        <label for="booking_length" class="font-weight-bold">How long would you like your child to attend the club for?</label>
                        <table style="width:60%;">
                        <tr><th></th></tr>
                        <?php 
                        $termination = (strtotime($rules[3])-strtotime($rules[2]))/60;
                        for($i = 1;$i <= $termination/intval($rules[4]);$i++) {
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
                        <p class="font-weight-bold">Which children will be attending?</p>
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
                        <input type="hidden" id="hiddenDay" name="hiddenDay" />
                        <input type="hidden" id="hiddenMonth" name="hiddenMonth" />
                        <input type="hidden" id="hiddenYear" name="hiddenYear" />
                        <div class="row">
                            <div class="col-md-12 offset-md-1">
                                <input type="submit" class="btn"/>
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
<script>
function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}
document.getElementById('hiddenDay').value = readCookie('day');
document.getElementById('hiddenMonth').value = readCookie('month');
document.getElementById('hiddenYear').value = readCookie('year');


$(function(){
    var requiredCheckboxes = $('.browsers :checkbox[required]');
    requiredCheckboxes.change(function(){
        if(requiredCheckboxes.is(':checked')) {
            requiredCheckboxes.removeAttr('required');
        } else {
            requiredCheckboxes.attr('required', 'required');
        }
    });
});
</script>
@endsection