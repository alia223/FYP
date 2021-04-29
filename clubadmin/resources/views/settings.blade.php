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
            <div class="card-header">Settings</div>
                <div class="card-body">
                @foreach($user as $u)
                    <form class="form-horizontal" method="POST" action="{{ action('App\Http\Controllers\SettingsController@update', $u->id) }} " enctype="multipart/form-data" >
                        @method('PATCH')
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <label for="user_first_name" class="font-weight-bold">First Name: 
                                    <input type="text" id="user_first_name" name="user_first_name" value="{{ $u->name }}"/>
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="user_last_name" class="font-weight-bold">Last Name: 
                                    <input type="text" id="user_last_name" name="user_last_name" value="{{ $u->last_name }}"/>
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="user_email" class="font-weight-bold">Email: 
                                    <input type="text" id="user_email" name="user_email" value="{{ $u->email }}" pattern=".+@.+" title="Please include the @ symbol." />
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="user_telephone" class="font-weight-bold">Telephone: 
                                    <input type="text" id="user_telephone" name="user_telephone" value="{{ $u->telephone }}" pattern="[0-9]{4} [0-9]{3} [0-9]{4}" title="XXXX XXX XXXX"/>
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="user_mobile" class="font-weight-bold">Mobile: 
                                    <input type="text" id="user_mobile" name="user_mobile" value="{{ $u->mobile }}" pattern="[0-9]{11}" title="11 Digits"/>
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="offset-md-4 col-md-4">
                                <input type="submit" class="btn btn-primary" value="Save Changes"/>
                                <input type="reset" class="btn btn-primary" />
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </form>
                </div>
            </div>
        </div>
    </div>
  </div>
</div>
<script type="text/javascript"> $('#settings').addClass('active'); </script>
@endsection
</body>
</html>