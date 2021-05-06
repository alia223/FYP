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
        @include('sidebar')
        <div class="offset-md-1 col-md-9" style="margin-top:50px;">
            <div class="card">
                <div class="card-header">Injury Record</div>
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>   
                                <th class="text-center">Date of Injury</th>
                                <th class="text-center">Staff Member</th>
                                <th class="text-center">Child</th>
                                <th class="text-center">Description of Injury</th>
                                @if(!Gate::denies('clubstaff'))
                                    <th class="text-center" colspan="2">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($injuries as $injury)
                            <tr>
                                <td class="text-center">{{ date('d-m-Y', strtotime($injury->date)) }}</td>
                                <td class="text-center">{{ $staff->where('users.id', $injury->staff_id)->first()->name }} {{ $staff->where('users.id', $injury->staff_id)->first()->last_name }}</td>
                                <td class="text-center">{{ $pupils->where('pupils.id', $injury->pupil_id)->first()->first_name }} {{ $pupils->where('pupils.id', $injury->pupil_id)->first()->last_name }}</td>
                                <td class="text-center">{{ $injury->description }}</td>
                                @if(!Gate::denies('clubstaff'))
                                <td>
                                    <a href="{{ action('App\Http\Controllers\PupilInjuryController@edit', $injury->id) }}" class="btn btn-warning material-icons" title="Edit Booking">build</a>
                                </td>
                                <td class="text-center">
                                    <form action="{{ action('App\Http\Controllers\PupilInjuryController@destroy', $injury->id) }}" method="post"> 
                                    @csrf
                                        <input name="_method" type="hidden" value="DELETE">
                                        <button class="btn btn-danger material-icons" title="Remove Injury" type="submit">close</button>
                                    </form>
                                </td>
                                @endif
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