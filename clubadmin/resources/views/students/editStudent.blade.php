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
                        <a href="{{ route('create-bookings') }}?ym=<?php $date = date('Y-m'); echo $date?>">Create a Booking</a>
                        <a href="{{ url('bookings') }}">View Upcoming Bookings</a>
                        <a href="{{ route('past-bookings') }}">View Past Bookings</a>
                        <a class="active" href="{{ route('club-students') }}">Children</a>
                    @elseif (Gate::denies('admin') && !Gate::denies('clubstaff'))
                        <a href="{{ url('bookings') }}">View Upcoming Bookings</a>
                        <a href="{{ route('student-register') }}">Register</a>
                        <a class="active" href="{{ route('club-students') }}">Students</a>
                    @endif
                @endif
                <a href="{{ route('settings') }}">Settings</a>
            </div>
        </div>
        <div class="col-md-9" style="margin-top: 50px">
            <div class="card">
                <div class="card-header">Edit and Update Child Details</div>
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
                    <form class="form-horizontal" method="POST" action="{{ action('App\Http\Controllers\StudentController@update', $student['id']) }} " enctype="multipart/form-data" >
                        @method('PATCH')
                        @csrf
                        <div class="col-md-8">
                            <label >First Name: </label>
                            <input type="text" name="student_first_name" value="{{$student->first_name}}"/>
                        </div>
                        <div class="col-md-8">
                            <label>Last Name: </label>
                            <input type="text" name="student_last_name" value="{{ $student->last_name}}" />
                        </div>
                        <div class="col-md-8">
                            <label>Date of Birth: </label>
                            <input type="date" name="student_date_of_birth" value="{{ $student->date_of_birth}}" />
                        </div>
                        <div class="col-md-8">
                            <label class="font-weight-bold" for="student_dietary_requirements">Dietary Requirements: </label>
                            <select name="student_dietary_requirements" id="student_dietary_requirements" required>
                            @if($student->dietary_requirements == "None")
                                <option value="None" selected>None</option>
                                <option value="Gluten free and coeliac">Gluten free and coeliac</option>
                                <option value="Dairy Free and Lactose Free">Dairy Free and Lactose Free</option>
                                <option value="Tree Nut and Peanut Allergies">Tree Nut and Peanut Allergies</option>
                                <option value="Fish Allergies">Fish Allergies</option>
                                <option value="Halal">Halal</option>
                                <option value="Other">Other</option>
                            @elseif($student->dietary_requirements == "None")
                                <option value="None">None</option>
                                <option value="Gluten free and coeliac" selected>Gluten free and coeliac</option>
                                <option value="Dairy Free and Lactose Free">Dairy Free and Lactose Free</option>
                                <option value="Tree Nut and Peanut Allergies">Tree Nut and Peanut Allergies</option>
                                <option value="Fish Allergies">Fish Allergies</option>
                                <option value="Halal">Halal</option>
                                <option value="Other">Other</option>
                            @elseif($student->dietary_requirements == "Dairy Free and Lactose Free")
                                <option value="None">None</option>
                                <option value="Gluten free and coeliac">Gluten free and coeliac</option>
                                <option value="Dairy Free and Lactose Free" selected>Dairy Free and Lactose Free</option>
                                <option value="Tree Nut and Peanut Allergies">Tree Nut and Peanut Allergies</option>
                                <option value="Fish Allergies">Fish Allergies</option>
                                <option value="Halal">Halal</option>
                                <option value="Other">Other</option>
                            @elseif($student->dietary_requirements == "Tree Nut and Peanut Allergies")
                                <option value="None">None</option>
                                <option value="Gluten free and coeliac">Gluten free and coeliac</option>
                                <option value="Dairy Free and Lactose Free">Dairy Free and Lactose Free</option>
                                <option value="Tree Nut and Peanut Allergies" selected>Tree Nut and Peanut Allergies</option>
                                <option value="Fish Allergies">Fish Allergies</option>
                                <option value="Halal">Halal</option>
                                <option value="Other">Other</option>
                            @elseif($student->dietary_requirements == "Fish Allergies")
                                <option value="None">None</option>
                                <option value="Gluten free and coeliac">Gluten free and coeliac</option>
                                <option value="Dairy Free and Lactose Free">Dairy Free and Lactose Free</option>
                                <option value="Tree Nut and Peanut Allergies">Tree Nut and Peanut Allergies</option>
                                <option value="Fish Allergies" selected>Fish Allergies</option>
                                <option value="Halal">Halal</option>
                                <option value="Other">Other</option>
                            @elseif($student->dietary_requirements == "Halal")
                                <option value="None">None</option>
                                <option value="Gluten free and coeliac">Gluten free and coeliac</option>
                                <option value="Dairy Free and Lactose Free">Dairy Free and Lactose Free</option>
                                <option value="Tree Nut and Peanut Allergies">Tree Nut and Peanut Allergies</option>
                                <option value="Fish Allergies">Fish Allergies</option>
                                <option value="Halal" selected>Halal</option>
                                <option value="Other">Other</option>                                
                            @elseif($student->dietary_requirements == "Other")
                                <option value="None">None</option>
                                <option value="Gluten free and coeliac">Gluten free and coeliac</option>
                                <option value="Dairy Free and Lactose Free">Dairy Free and Lactose Free</option>
                                <option value="Tree Nut and Peanut Allergies">Tree Nut and Peanut Allergies</option>
                                <option value="Fish Allergies">Fish Allergies</option>
                                <option value="Halal">Halal</option>
                                <option value="Other" selected>Other</option>
                            @endif
                            </select>
                        </div>
                        <div class="col-md-8" id="other">
                            <label class="font-weight-bold" for="student_other">If other, please specify: </label>
                            <textarea  name="student_other" id="student_other"></textarea>
                        </div>
                        <div class="col-md-10">
                            <label for="student_food_arrangement" class="font-weight-bold">Select food arrangement: </label>
                            <select name="student_food_arrangement" id="student_food_arrangement">
                                @if($student->food_arrangement == "None")
                                    <option value="None" selected>None</option>
                                    <option value="School provides food">School provides food</option>
                                    <option value="Food from home">Food from home</option>
                                @elseif($student->food_arrangement == "School provides food")
                                    <option value="None">None</option>
                                    <option value="School provides food" selected>School provides food</option>
                                    <option value="Food from home">Food from home</option>
                                @elseif($student->food_arrangement == "Food from home")
                                    <option value="None">None</option>
                                    <option value="School provides food">School provides food</option>
                                    <option value="Food from home" selected>Food from home</option>
                                @endif
                            </select>
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
<script>
    $('#student_dietary_requirements').change(function() {
        if(document.getElementById('student_dietary_requirements').options[document.getElementById('student_dietary_requirements').selectedIndex].value === "Other") {
            document.getElementById('other').innerHTML = '<label class="font-weight-bold" for="student_other">If other, please specify: </label><textarea  name="student_other" id="student_other" required></textarea>';
        }
        else {
            document.getElementById('other').innerHTML = '<label class="font-weight-bold" for="student_other">If other, please specify: </label><textarea  name="student_other" id="student_other"></textarea>';
        }
    });
</script>
@endsection