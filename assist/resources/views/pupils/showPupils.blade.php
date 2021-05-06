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
                <div class="card-header">Pupils</div>
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
                                @if(!Gate::denies('admin') || !Gate::denies('clubstaff'))
                                    <th class="text-center">Parent</th>
                                @endif
                                <th class="text-center">First Name</th>
                                <th class="text-center">Last Name</th>
                                <th class="text-center">Date of Birth</th>
                                <th class="text-center">Food Arrangement</th>
                                <th class="text-center" colspan="4" style="color: black">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($pupils as $pupil)
                            <tr>
                                @if(!Gate::denies('admin') || !Gate::denies('clubstaff'))
                                    <td class="text-center">{{ $parents->where('id', $pupil->parent_id)->first()->name }} {{ $parents->where('id', $pupil->parent_id)->first()->last_name }}</td>
                                @endif
                                <td class="text-center">{{ $pupil->first_name }}</td>
                                <td class="text-center">{{ $pupil->last_name }}</td>
                                <td class="text-center">{{ date('d-m-Y', strtotime($pupil->date_of_birth)) }}</td>                              
                                <td class="text-center">{{ $pupil->food_arrangement }}</td>
                                <td class="text-center"><a href="{{ action('App\Http\Controllers\PupilDietaryRequirementController@show', $pupil->id) }}" class="btn
                                btn-warning custom-buttons" title="Dietary Requirements"><i class="material-icons">lunch_dining</i></a></td>
                                <td class="text-center"><a href="{{ action('App\Http\Controllers\PupilInjuryController@show', $pupil->id) }}" class="btn
                                btn-warning custom-buttons" title="Injuries"><i class="material-icons">error</i></a></td>
                                @if(Gate::denies('admin') && Gate::denies('clubstaff'))
                                <td class="text-center"><a href="{{ action('App\Http\Controllers\PupilController@edit', $pupil->id) }}" class="btn
                                btn-warning custom-buttons" title="Edit Details"><i class="material-icons">build</i></a></td>
                                <td>
                                    <form action="{{ action('App\Http\Controllers\PupilController@destroy', $pupil->id) }}"
                                    method="post"> @csrf
                                        <input name="_method" type="hidden" value="DELETE">
                                        <button class="btn btn-danger text-center custom-buttons" type="submit" title="Remove Child"><i class="material-icons">delete</i></button>
                                    </form>
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $pupils->links() }}
                </div>
            </div>
            @if(Gate::denies('clubstaff'))
                <a class="btn btn-primary" href="{{action('App\Http\Controllers\PupilController@create')}}" style="margin-top:10px;">Add Child</a>
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