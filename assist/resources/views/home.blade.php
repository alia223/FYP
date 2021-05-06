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
          <div class="card-header">{{ __('Dashboard') }}</div>
            <div class="card-body">
              <p>Hello {{ Auth::user()->name }}, you are logged in!</p>
            </div>
        </div>
        @if(Gate::denies('admin') && Gate::denies('clubstaff'))
                @foreach($pupils as $pupil)
                @if($booked_pupils->where('pupil_id', $pupil->id)->first()->checked_in)
                  <div class="card" style="margin-top: 10px;">
                    <div style="background-colour: lightgreen;" class="card-header">{{ __('Attendance Notification') }}</div>
                    <div class="card-body">
                      <p style="color: lightgreen;">{{ $pupil->first_name }} {{ $pupil->last_name }} has arrived at the club at 
                      {{ $booked_pupils->where('pupil_id', $pupil->id)->first()->checked_in }}!</p>
                    </div>
                  </div>
                @endif
                @if($booked_pupils->where('pupil_id', $pupil->id)->first()->checked_out)
                  <div class="card" style="margin-top: 10px;">
                    <div style="background-colour: lightgreen;" class="card-header">{{ __('Attendance Notification') }}</div>
                    <div class="card-body">
                      <p style="color: red;">{{ $pupil->first_name }} {{ $pupil->last_name }} has just left the club at 
                      {{ $booked_pupils->where('pupil_id', $pupil->id)->first()->checked_out }}!</p>
                    </div>
                  </div>
                @endif
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