@extends('layouts.app')
<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="./css/home.css" rel="stylesheet" />
</head>
<body>
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
                <div class="card-header">Children</div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>   
                                <th class="text-center">First Name</th>
                                <th class="text-center">Last Name</th>
                                <th class="text-center">Date of Birth</th>
                                <th class="text-center">Dietary Requirements</th>
                                <th class="text-center">Food Arrangement</th>
                                <th class="text-center" style="color: black;">Attendance</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($students as $student)
                                <tr>
                                    <td class="text-center">{{$student['first_name']}}</td>
                                    <td class="text-center">{{$student['last_name']}}</td>
                                    <td class="text-center">{{$student['date_of_birth']}}</td>
                                    @if($student['dietary_requirements'] == "Other")
                                        <td class="text-center">{{$student['other_dietary_requirements']}}</td>
                                    @else
                                        <td class="text-center">{{$student['dietary_requirements']}}</td>
                                    @endif
                                    <td class="text-center">{{$student['food_arrangement']}}</td>
                                    <td style="display: flex;">
                                    <form class="form-horizontal" method="POST" action="{{ action('App\Http\Controllers\StudentRegisterController@update', $student['id']) }} " enctype="multipart/form-data" >
                                    @method('PATCH')
                                    @csrf
                                    <?php 
                                        $carry_on = true;
                                        foreach($booked_students as $booked_student) {
                                            if($carry_on) {
                                                if($student['id'] == $booked_student['studentid']) {
                                                    if($booked_student['checked_in'] == null) {
                                                        echo '<input type="submit" class="btn" value="Check In" />';
                                                        $carry_on = false;
                                                    }
                                                    else if($booked_student['checked_in'] != null && $booked_student['checked_out'] == null) {
                                                        echo '<input type="submit" class="btn" value="Check Out" />';
                                                        $carry_on = false;
                                                    }
                                                    else if($booked_student['checked_in'] != null && $booked_student['checked_out'] == null) {
                                                        echo '<input type="submit" class="btn" value="Check In" />';
                                                    }
                                                }
                                            }
                                        }
                                        $carry_on = true;
                                    ?>
                                    </form>
                                    <form style="margin-left: 5px;" class="form-horizontal" method="POST" action="{{ action('App\Http\Controllers\StudentRegisterUndoController@update', $student['id']) }} " enctype="multipart/form-data" >
                                    @method('PATCH')
                                    @csrf
                                        <button type="submit" class="btn material-icons" value="Undo">undo</button>
                                    </form>
                                    </td>
                                    </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript"> 
    $('#register').addClass('active'); 
</script>
@endsection
</body>
</html>