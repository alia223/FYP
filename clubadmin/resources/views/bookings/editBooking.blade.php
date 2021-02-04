@extends('layouts.app')
@section('content')
<div class="container" style="margin:0; padding:0;">
    <div class="row justify-content-center">
        <div class="col-md-3">
            <div class="sidebar">
                <a href="{{ route('home') }}">Home</a>
                @if(Auth::check())
                    @if (!Gate::denies('admin') && Gate::denies('clubstaff'))
                        <a class="active"  href="{{ url('bookings') }}">View Upcoming Bookings</a>
                        <a href="{{ route('past-bookings') }}">View Past Bookings</a>
                        <a href="{{ route('club-rooms') }}">Rooms</a>
                        <a href="{{ route('activity-log') }}">Activity Log</a>
                        <a href="{{ route('control-panel') }}">Control Panel</a>
                    @elseif (Gate::denies('admin') && Gate::denies('clubstaff'))
                        <a href="{{ route('create-bookings') }}?ym=<?php $date = date('Y-m'); echo $date?>">Create a Booking</a>
                        <a class="active" href="{{ url('bookings') }}">View Upcoming Bookings</a>
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
                    <form class="form-horizontal" method="POST" action="{{ action('App\Http\Controllers\BookingController@update', $booking['id']) }} " enctype="multipart/form-data" >
                        @method('PATCH')
                        @csrf
                        <div class="col-md-8">
                            <label class="font-weight-bold">Date</label><br />
                            <input type="date" name="booking_date" value="{{$booking->booking_date}}"/>
                        </div>
                        <label for="food-arrangement" class="font-weight-bold">How long would you like your child to attend the club for?</label>
                                        <?php for($i = 1; $i <= 6;$i+=1) {
                                            $val = 30 * $i;
                                            echo '<div class="row offset-md-1">';
                                            if($booking->duration == $val) {
                                                echo '<input type="radio" id="'.$val.'" name="booking_length" value="'.$val.'" checked/>';
                                            }
                                            else {
                                                  echo '<input type="radio" id="'.$val.'" name="booking_length" value="'.$val.'" />';
                                            }
                                            echo '&nbsp&nbsp';
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
                                            echo '</div>';
                                        }?>
                        <p class="font-weight-bold">Which children will be attending?</p>
                        <?php    
                            $i = 0;
                            foreach($students as $student) {
                                if(sizeof($booked_students->where('studentid', $student['id'])) > 0) {
                                    echo '<input style="margin-right:10px;" type="checkbox" name="students[]" value="'.$student['id'].'"  checked/>'
                                .'<label for="students[]">'.$student['first_name'].' '.$student['last_name'].'</label>&nbsp&nbsp';
                                }
                                elseif($booked_students->where('studentid', $student['id'])) {
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

                        ?>
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