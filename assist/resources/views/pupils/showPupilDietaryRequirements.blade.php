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
            @if(Gate::denies('clubstaff'))
                <div class="card-header">Children</div>
            @else
                <div class="card-header">Pupil Dietary Requirements</div>
            @endif
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>   
                                <th class="text-center">Child</th>
                                <th class="text-center">Dietary Requirements</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center">{{ $pupil->first_name }} {{ $pupil->last_name }}</td>
                                <td class="text-center">
                                        @if(sizeof($pupil_dietary_requirements) == 0)
                                            None
                                        @endif
                                        @foreach($pupil_dietary_requirements as $pupil_dietary_requirement)
                                            @if($pupil_dietary_requirement->dietary_requirements == 'Other')
                                                {{$pupil_dietary_requirement->other_dietary_requirements}}
                                            @else
                                                {{ $pupil_dietary_requirement->dietary_requirements }}<br />
                                            @endif
                                        @endforeach 
                                </td> 
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            @if(Gate::denies('clubstaff'))
                <a class="btn btn-primary" href="{{action('App\Http\Controllers\PupilController@create')}}">Add Child</a>
            @endif
        </div>
    </div>
</div>
<script type="text/javascript"> 
    $('#pupils').addClass('active');
    $('#registered_club_pupils').addClass('active'); 
    $('#admin_bookings').addClass('active');
</script>
@endsection
</body>
</html>