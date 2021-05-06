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
          <div class="card-header">{{ __('Add Injury') }}</div>
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
                  <input type="hidden" id="pupil_id" name="pupil_id" value=""/>
                  <input type="hidden" name="date_of_injury" value="<?php echo date('Y-m-d', strtotime("today")); ?>" required/>
                  <div class="col-md-8">
                      <label class="font-weight-bold">Description of Injury: 
                        <textarea name="description_of_injury" required></textarea>
                      </label>
                  </div>
              </div>
              <div class="row">
                  <div class="offset-md-4 col-md-4">
                      <input type="submit" class="btn" id="submit" />
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
  $('#pupils').addClass('active'); 
  $('#registered_club_pupils').addClass('active');
  var cookie = document.cookie.split(';');
  var cookieSplit = cookie[0].split('=');
  var pupil_id = cookieSplit[1];
  $('#pupil_id').val(pupil_id);
</script>
@endsection
</body>
</html>