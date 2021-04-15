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
          <div class="card-header">{{ __('Injury Record') }}</div>
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
          <form class="form-horizontal" method="POST" action="{{url('injuries') }}" enctype="multipart/form-data">
          @csrf
            <div class="col-md-8">
                <input type="hidden" id="studentid" name="studentid" value=""/>
                <div class="col-md-8">
                    <label class="font-weight-bold">Date of Injury: </label>
                    <input type="date" name="date_of_injury" required/>
                </div>
                <div class="col-md-8">
                    <label class="font-weight-bold">Comment: </label>
                    <textarea name="comment" required></textarea>
                </div>
            </div>
            <div class="col-md-8">
                <input type="submit" class="btn"/>
                <input type="reset" class="btn"/>
            </div>
            </div>
          </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  $('#students').addClass('active'); 
  var cookie = document.cookie.split(';');
  var cookieSplit = cookie[0].split('=');
  var sid = cookieSplit[1];
  console.log(sid);
  $('#studentid').value = sid;
</script>
@endsection
</body>
</html>