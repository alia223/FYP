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
                                <th class="text-center">Student ID</th>
                                <th class="text-center">Comment</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($injuries as $injury)
                            <tr>
                                <td class="text-center">{{$injury['date']}}</td>
                                <td class="text-center">{{$injury['staffid']}}</td>
                                <td class="text-center">{{$injury['studentid']}}</td>
                                <td class="text-center">{{$injury['comment']}}</td>
                            </tr>
                            @endforeach
                            <input type="hidden" id ="sid" value="<?php foreach($pass as $p) echo $p; ?>"/>
                        </tbody>
                    </table>
                </div>
            </div>
            @if(!Gate::denies('clubstaff'))
            <a class="btn btn-primary" href="{{action('App\Http\Controllers\StudentInjuryController@create')}}">Add Injury Record</a>
            @endif
        </div>
    </div>
</div>
<script type="text/javascript"> 
    $('#students').addClass('active');
    document.cookie = "sid= " + document.getElementById('sid').value;
</script>
@endsection
</body>
</html>