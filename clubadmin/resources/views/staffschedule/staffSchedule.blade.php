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
        <div class="offset-md-1 col-md-9" style="margin-top: 50px;">
            <div class="card">
                <div class="card-header">Club Staff Schedule</div>
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
                            @for($i = 0;$i < 5;$i++)
                            <tr>
                                <td>{{$dotw[$i]}}<br />Monday</td>
                                <td>
                                    @foreach($staffAvailability->where('day', $i+1)->where('available_until', '!=', 0) as $sa)
                                        {{ $staff->where('id', $sa->staff_id)->first()->name }} {{ $staff->where('id', $sa->staff_id)->first()->last_name }}: <?php echo $rules->club_start ?> - {{ $sa->available_until }} <br />
                                    @endforeach
                                </td>
                                <td>            
                                    @if(!Gate::denies('admin'))
                                        <form method="POST" action="{{ action('App\Http\Controllers\StaffScheduleController@store') }}">
                                    @else
                                        <a href="{{ action('App\Http\Controllers\StaffScheduleController@show', $i+1) }}">
                                    @endif
                                    @csrf
                                        <input type="hidden" name="day" value="{{$i+1}}" />
                                        <input type="submit" class="col-md-8 btn btn-primary" value="See Staff Schedule" />
                                    </form>
                                </td>
                            </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
  $('#staff_schedule').addClass('active'); 
</script>
@endsection
</body>
</html>