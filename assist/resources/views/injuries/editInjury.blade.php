@extends('layouts.app')
@section('content')
<div class="container" style="margin: 0; padding: 0;">
    <div class="row justify-content-center">
        @include('sidebar')
        <div class="offset-md-1 col-md-9" style="margin-top:50px;">
            <div class="card">
                <div class="card-header">Edit and Update Injury Details</div>
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div><br />
                    @endif
                    @if (\Session::has('success'))
                        <div class="alert alert-success">
                            <p>{{ \Session::get('success') }}</p>
                        </div><br />
                    @endif
                    <div class="card-body">
                    <form class="form-horizontal" method="POST" action="{{ action('App\Http\Controllers\PupilInjuryController@update', $injury->id) }} " enctype="multipart/form-data" >
                    @method('PATCH')
                    @csrf
                        <input type="hidden" name="date_of_injury" value="<?php echo date('Y-m-d', strtotime("today")); ?>" required/>
                        <div class="col-md-8">
                            <label >Description of Injury: </label>
                            <input type="text" name="description_of_injury" value="{{ $injury->description }}"/>
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
<script type="text/javascript"> 
    $('#pupils').addClass('active');
    $('#registered_club_pupils').addClass('active');
</script>
@endsection