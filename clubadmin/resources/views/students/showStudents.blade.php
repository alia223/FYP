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
                                <th class="text-center" colspan="4" style="color: black">Action</th>
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
                                @if(Gate::denies('clubstaff'))
                                <td class="text-center"><a href="{{action('App\Http\Controllers\StudentController@edit', $student['id'])}}" class="btn
                                btn-warning custom-buttons" title="Edit Details"><i class="material-icons">build</i></a></td>
                                <td>
                                    <form action="{{action('App\Http\Controllers\StudentController@destroy', $student['id'])}}"
                                    method="post"> @csrf
                                        <input name="_method" type="hidden" value="DELETE">
                                        <button class="btn btn-danger text-center custom-buttons" type="submit" title="Remove Child"><i class="material-icons">delete</i></button>
                                    </form>
                                </td>
                                @endif
                                <td>
                                <a href="{{action('App\Http\Controllers\StudentInjuryController@show', $student['id'])}}" class="btn
                                btn-warning custom-buttons" title="Injury Record"><i class="material-icons">error</i></a></td>
                                <td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @if(Gate::denies('clubstaff'))
                <a class="btn btn-primary" href="{{action('App\Http\Controllers\StudentController@create')}}">Add Child</a>
            @endif
        </div>
    </div>
</div>
<script type="text/javascript"> 
    $('#students').addClass('active'); 
</script>
@endsection
</body>
</html>