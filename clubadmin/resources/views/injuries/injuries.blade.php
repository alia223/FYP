@extends('layouts.app')
<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                <div class="card-header">Injury Record</div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>   
                                <th class="text-center">Date of Injury</th>
                                <th class="text-center">Staff ID</th>
                                <th class="text-center">Pupil ID</th>
                                <th class="text-center">Comment</th>
                                <th class="text-center" colspan="2">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($injuries as $injury)
                            <tr>
                                <td class="text-center">{{ $injury->date }}</td>
                                <td class="text-center">{{ $injury->staff_id }}</td>
                                <td class="text-center">{{ $injury->pupil_id }}</td>
                                <td class="text-center">{{ $injury->description }}</td>
                                @if(!Gate::denies('clubstaff'))
                                    <td><a href="{{ action('App\Http\Controllers\PupilInjuryController@edit', $injury->id) }}" class="btn
                                    btn-warning material-icons" title="Edit Booking">build</a></td>
                                @endif
                                    <td class="text-center">
                                    <form action="{{ action('App\Http\Controllers\PupilInjuryController@destroy', $injury->id) }}"
                                    method="post"> @csrf
                                        <input name="_method" type="hidden" value="DELETE">
                                        <button class="btn btn-danger material-icons" title="Remove Injury" type="submit">close</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @if(!Gate::denies('clubstaff'))
            <a class="btn btn-primary" href="{{action('App\Http\Controllers\PupilInjuryController@create')}}">Add Injury Record</a>
            @endif
        </div>
    </div>
</div>
<script type="text/javascript"> 
    $('#injuries').addClass('active');
</script>
@endsection
</body>
</html>