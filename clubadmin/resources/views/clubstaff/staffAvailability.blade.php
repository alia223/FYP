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
        <form class="form-horizontal" name="availability-form" method="POST" action="{{url('staff-availability') }}" enctype="multipart/form-data">
        @csrf
            <div class="card">
                <div class="card-header">Club Staff Availability</div>
                <!-- display the errors -->
                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul> @foreach ($errors->all() as $error)<li>{{ $error }}</li> @endforeach</ul>
                </div><br /> 
                @endif
                <!-- display the success status -->
                @if (\Session::has('success'))
                <div class="alert alert-success">
                    <p>{{ \Session::get('success') }}</p>
                </div><br /> 
                @endif
                <div class="card-body">
                    <table class="table table-striped">
                        <thead class="text-center">
                            <tr>   
                                <th>Day</th>
                                <th>Available From</th>
                                <th>Available Until</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                        <?php $days = ["monday", "tuesday", "wednesday", "thursday", "friday"];?>
                        @for($i=0;$i < 5;$i++)
                            <tr>
                                <td>{{ ucwords($days[$i]) }}</td>
                                <td>
                                    <input type="time" id="{{ $days[$i] }}_available_from" name="{{ $days[$i] }}_available_from" min="{{$rules->club_start}}"
                                    max="<?php echo date("H:i", strtotime('-'.$rules->club_duration.'minutes', strtotime($rules->club_end))); ?>" 
                                    value="<?php if(sizeof($staffAvailability) > 0) { echo $staffAvailability->where('day', $i+1)->first()->available_from; } ?>"/>
                                </td>
                                <td>
                                    <input type="time" id="{{ $days[$i] }}_available_until" name="{{ $days[$i] }}_available_until" 
                                    min="<?php echo date("H:i", strtotime('+'.$rules->club_duration.'minutes', strtotime($rules->club_start))); ?>" 
                                    max="{{$rules->club_end}}" value="<?php if(sizeof($staffAvailability) > 0) { echo $staffAvailability->where('day', $i+1)->first()->available_until; } ?>"/>
                                </td>
                            </tr>
                        @endfor
                        <tr>
                            <td>Preferred Hours Per Week</td>
                            <td><input name="max_hours" style="width:80px;" type="number" 
                            min = "0" value="<?php if(sizeof($staffAvailability) > 0) { echo $staffAvailability->where('staff_id', Auth::user()->id)->first()->max_hours; } ?>"></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="offset-md-5">
                <input type="submit" id="submit" class="btn" />
                <input type="reset" class="btn"/>
            </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
$('#staff_availability').addClass('active'); 

</script>
@endsection
</body>
</html>