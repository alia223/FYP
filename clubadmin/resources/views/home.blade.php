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
      <div class="col-md-4">
        <div class="sidebar">
          @include('sidebar')
        </div>
      </div>
      <div class="col-md-8" style="margin-top:50px;">
        <div class="card">
          <div class="card-header">{{ __('Dashboard') }}</div>
            <div class="card-body">
              <p>Hello {{Auth::user()->name}}, you are logged in!</p>
            </div>
        </div>
        @if(Gate::denies('admin') && Gate::denies('clubstaff'))
                @foreach($students as $student)
                <div class="card" style="margin-top: 10px;">
                <div style="background-colour: lightgreen;" class="card-header">{{ __('Notification') }}</div>
                  <div class="card-body">
                    <p style="color: lightgreen;">{{$student->first_name}} {{$student->last_name }} has arrived at the club at {{ $booked_students->where('studentid', $student->id)->first()->checked_in }}!</p>
                  </div>
                </div>
                @endforeach
        @endif
      </div>
    </div>
  </div>
</div>
<script type="text/javascript"> $('#home').addClass('active'); </script>
@endsection
</body>
</html>