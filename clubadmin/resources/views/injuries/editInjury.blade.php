@extends('layouts.app')
@section('content')
<div class="container" style="margin: 0; padding: 0;">
    <div class="row justify-content-center">
        <div class="col-md-3">
            <div class="sidebar">
                @include('sidebar')            
            </div>
        </div>
        <div class="col-md-9" style="margin-top: 50px">
            <div class="card">
                <div class="card-header">Edit and Update Child Details</div>
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
                        <input type="hidden" id ="pupil_id" name="pupil_id" value="<?php foreach($pupil_id as $p_id) echo $p_id; ?>"/>
                        <div class="col-md-8">
                            <label >Description of Injury: </label>
                            <input type="text" name="description_of_injury" value="{{ $injury->description }}"/>
                        </div>

                        <div class="col-md-6 col-md-offset-4">
                            <input type="submit" class="btn btn-primary" />
                            <input type="reset" class="btn btn-primary" />
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript"> 
    $('#pupils').addClass('active');
</script>
@endsection