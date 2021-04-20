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
                                <th>Available Staff</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <tr>
                                <td>{{$dotw[0]}}<br />Monday</td>
                                <td>
                                    @foreach($staffAvailability->where('day', 1)->where('available_until', '!=', 0) as $sa)
                                        {{ $staff->where('id', $sa->staff_id)->first()->name }} {{ $staff->where('id', $sa->staff_id)->first()->last_name }}: <?php echo $rules->club_start ?> - {{ $sa->available_until }} <br />
                                    @endforeach
                                </td>
                                <td>            
                                    <form method="POST" action="{{ action('App\Http\Controllers\StaffScheduleController@store') }}">
                                    @csrf
                                        <input type="hidden" name="day" value="1" />
                                        <input type="submit" class="col-md-8 btn btn-primary" value="Assign Staff" />
                                    </form>
                                </td>
                            </tr>
                            <tr>
                                <td>{{$dotw[1]}}<br />Tuesday</td>
                                <td>
                                    @foreach($staffAvailability->where('day', 2)->where('available_until', '!=', 0) as $sa)
                                        {{ $staff->where('id', $sa->staff_id)->first()->name }} {{ $staff->where('id', $sa->staff_id)->first()->last_name }}: <?php echo $rules->club_start ?> - {{ $sa->available_until }} <br />
                                    @endforeach
                                </td>
                                <td>            
                                    <form method="POST" action="{{ action('App\Http\Controllers\StaffScheduleController@store') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                                    @csrf
                                        <input type="hidden" name="day" value="2" />
                                        <input type="submit" class="col-md-8 btn btn-primary" value="Assign Staff" />
                                    </form>
                                </td>
                            </tr>
                            <tr>
                                <td>{{$dotw[2]}}<br />Wednesday</td>
                                <td>
                                @foreach($staffAvailability->where('day', 3)->where('available_until', '!=', 0) as $sa)
                                        {{ $staff->where('id', $sa->staff_id)->first()->name }} {{ $staff->where('id', $sa->staff_id)->first()->last_name }}: <?php echo $rules->club_start ?> - {{ $sa->available_until }} <br />
                                    @endforeach
                                </td>
                                <td>            
                                    <form method="POST" action="{{ action('App\Http\Controllers\StaffScheduleController@store') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                                    @csrf
                                        <input type="hidden" name="day" value="3" />
                                        <input type="submit" class="col-md-8 btn btn-primary" value="Assign Staff" />
                                    </form>
                                </td>
                            </tr>
                            <tr>
                                <td>{{$dotw[3]}}<br />Thursday</td>
                                <td>
                                @foreach($staffAvailability->where('day', 4)->where('available_until', '!=', 0) as $sa)
                                        {{ $staff->where('id', $sa->staff_id)->first()->name }} {{ $staff->where('id', $sa->staff_id)->first()->last_name }}: <?php echo $rules->club_start ?> - {{ $sa->available_until }} <br />
                                    @endforeach
                                </td>
                                <td>            
                                    <form method="POST" action="{{ action('App\Http\Controllers\StaffScheduleController@store') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                                    @csrf
                                        <input type="hidden" name="day" value="4" />
                                        <input type="submit" class="col-md-8 btn btn-primary" value="Assign Staff" />
                                    </form>
                                </td>
                            </tr>
                            <tr>
                                <td>{{$dotw[4]}}<br />Friday</td>
                                <td>
                                @foreach($staffAvailability->where('day', 5)->where('available_until', '!=', 0) as $sa)
                                        {{ $staff->where('id', $sa->staff_id)->first()->name }} {{ $staff->where('id', $sa->staff_id)->first()->last_name }}: <?php echo $rules->club_start ?> - {{ $sa->available_until }} <br />
                                    @endforeach
                                </td>
                                <td>            
                                    <form method="POST" action="{{ action('App\Http\Controllers\StaffScheduleController@store') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                                    @csrf
                                        <input type="hidden" name="day" value="5" />
                                        <input type="submit" class="col-md-8 btn btn-primary" value="Assign Staff" />
                                    </form>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
  $('#staff_availability').addClass('active'); 
</script>
@endsection
</body>
</html>