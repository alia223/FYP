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
                    <?php $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']; ?>
                    <div class="row">
                        <button id="button1" class="btn offset-md-1 col-md-2">Monday</button>
                        <button id="button2" class="btn col-md-2">Tuesday</button>
                        <button id="button3" class="btn col-md-2">Wednesday</button>
                        <button id="button4" class="btn col-md-2">Thursday</button>
                        <button id="button5" class="btn col-md-2">Friday</button>
                    </div>
                    <table class="table">
                        <thead class="text-center">
                            <tr>   
                                <th>Day</th>
                                <th>Available Staff</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @for($i = 0;$i < 5;$i++)
                            <tr id="{{ $days[$i] }}">
                                <td>{{$dotw[$i]}}<br />{{ $days[$i] }}</td>
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
                                        @if(!Gate::denies('admin'))
                                            <input type="submit" class="col-md-8 btn btn-primary" value="Calculate Staff Schedule" />
                                        @else 
                                            <input type="submit" class="col-md-8 btn btn-primary" value="See Staff Schedule" />
                                        @endif
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
    $('#Monday').show();
    $('#button1').css('background-color','#8400ff');
    $('#button2').css({'background-color':'#ffffff', 'color':'#8400ff'});
    $('#button3').css({'background-color':'#ffffff', 'color':'#8400ff'});
    $('#button4').css({'background-color':'#ffffff', 'color':'#8400ff'});
    $('#button5').css({'background-color':'#ffffff', 'color':'#8400ff'});
    $('#Tuesday').hide();
    $('#Wednesday').hide();
    $('#Thursday').hide();
    $('#Friday').hide();
  $('#button1').on('click', function(){
    $('#Monday').show();
    $('#button1').css({'background-color':'#8400ff', 'color':'#ffffff'});
    $('#button2').css({'background-color':'#ffffff', 'color':'#8400ff'});
    $('#button3').css({'background-color':'#ffffff', 'color':'#8400ff'});
    $('#button4').css({'background-color':'#ffffff', 'color':'#8400ff'});
    $('#button5').css({'background-color':'#ffffff', 'color':'#8400ff'});
    $('#Tuesday').hide();
    $('#Wednesday').hide();
    $('#Thursday').hide();
    $('#Friday').hide();
  });
  $('#button2').on('click', function(){
    $('#Monday').hide();
    $('#Tuesday').show();
    $('#button1').css({'background-color':'#ffffff', 'color':'#8400ff'});
    $('#button2').css({'background-color':'#8400ff', 'color':'#ffffff'});
    $('#button3').css({'background-color':'#ffffff', 'color':'#8400ff'});
    $('#button4').css({'background-color':'#ffffff', 'color':'#8400ff'});
    $('#button5').css({'background-color':'#ffffff', 'color':'#8400ff'});
    $('#Wednesday').hide();
    $('#Thursday').hide();
    $('#Friday').hide();
  });
  $('#button3').on('click', function(){
    $('#Monday').hide();
    $('#Tuesday').hide();
    $('#Wednesday').show();
    $('#button1').css({'background-color':'#ffffff', 'color':'#8400ff'});
    $('#button2').css({'background-color':'#ffffff', 'color':'#8400ff'});
    $('#button3').css({'background-color':'#8400ff', 'color':'#ffffff'});
    $('#button4').css({'background-color':'#ffffff', 'color':'#8400ff'});
    $('#button5').css({'background-color':'#ffffff', 'color':'#8400ff'});
    $('#Thursday').hide();
    $('#Friday').hide();
  });
  $('#button4').on('click', function(){
    $('#Monday').hide();
    $('#Tuesday').hide();
    $('#Wednesday').hide();
    $('#Thursday').show();
    $('#button1').css({'background-color':'#ffffff', 'color':'#8400ff'});
    $('#button2').css({'background-color':'#ffffff', 'color':'#8400ff'});
    $('#button3').css({'background-color':'#ffffff', 'color':'#8400ff'});
    $('#button4').css({'background-color':'#8400ff', 'color':'#ffffff'});
    $('#button5').css({'background-color':'#ffffff', 'color':'#8400ff'});
    $('#Friday').hide();
  });
  $('#button5').on('click', function(){
    $('#Monday').hide();
    $('#Tuesday').hide();
    $('#Wednesday').hide();
    $('#Thursday').hide();
    $('#Friday').show();
    $('#button1').css({'background-color':'#ffffff', 'color':'#8400ff'});
    $('#button2').css({'background-color':'#ffffff', 'color':'#8400ff'});
    $('#button3').css({'background-color':'#ffffff', 'color':'#8400ff'});
    $('#button4').css({'background-color':'#ffffff', 'color':'#8400ff'});
    $('#button5').css({'background-color':'#8400ff', 'color':'#ffffff'});
  });

</script>
@endsection
</body>
</html>