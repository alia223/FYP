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
        @include('sidebar')
        <div class="offset-md-1 col-md-9" style="margin-top:50px;">
            <div class="card">
                <div class="card-header">Club Attendance Register</div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>   
                                <th class="text-center">First Name</th>
                                <th class="text-center">Last Name</th>
                                <th class="text-center">Date of Birth</th>
                                <th class="text-center" style="color: black;">Attendance</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($booked_pupils as $booked_pupil)
                                <tr>
                                    <td class="text-center">{{ $pupils->where('id', $booked_pupil->pupil_id)->first()->first_name }}</td>
                                    <td class="text-center">{{ $pupils->where('id', $booked_pupil->pupil_id)->first()->last_name }}</td>
                                    <td class="text-center">{{ $pupils->where('id', $booked_pupil->pupil_id)->first()->date_of_birth }}</td>
                                    <td>
                                    <div class="row">
                                    <form class="form-horizontal offset-md-2" method="POST" action="{{ action('App\Http\Controllers\PupilRegisterController@update', $booked_pupil->pupil_id) }} " enctype="multipart/form-data" >
                                    @method('PATCH')
                                    @csrf
                                        @if($booked_pupil->checked_in == null)
                                            <input type="submit" class="btn" value="Check In" />
                                        @elseif($booked_pupil->checked_in != null && $booked_pupil->checked_out == null)
                                            <input type="submit" class="btn" value="Check Out" />
                                        @elseif($booked_pupil->checked_in != null && $booked_pupil->checked_out == null)
                                            <input type="submit" class="btn" value="Check In" />
                                        @endif
                                    </form>
                                    <form style="margin-left: 5px;" class="form-horizontal" method="POST" 
                                    action="{{ action('App\Http\Controllers\PupilRegisterUndoController@update', $booked_pupil->pupil_id) }} " enctype="multipart/form-data">
                                    @method('PATCH')
                                    @csrf
                                        <button type="submit" class="btn material-icons" value="Undo">undo</button>
                                    </form>
                                    </div>
                                    </td>
                                    </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $booked_pupils->links() }}
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