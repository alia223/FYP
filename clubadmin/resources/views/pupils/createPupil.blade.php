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
                <div class="card-header">Add a Child</div>
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
                    <!-- define the form -->
                    <div class="card-body">
                        <form class="form-horizontal" method="POST"
                        action="{{url('pupils') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="col-md-8">
                            <label class="font-weight-bold" for="pupil_first_name">First Name: </label>
                            <input type="text" name="pupil_first_name" id="pupil_first_name" required/>
                        </div>
                        <div class="col-md-8">
                            <label class="font-weight-bold" for="pupil_last_name">Last Name: </label>
                            <input type="text" name="pupil_last_name" id="pupil_last_name" required/>
                        </div>
                        <div class="col-md-8">
                            <label class="font-weight-bold" for="pupil_date_of_birth">Date of Birth: </label>
                            <input type="date" name="pupil_date_of_birth" id="pupil_date_of_birth" required/>
                        </div>
                        <div class="col-md-8" id="pupil_dietary_requirements">
                            <label class="font-weight-bold">Dietary Requirements: </label><br />
                            <input type="checkbox" id="none" name="pupil_dietary_requirements[]" value="none"/>
                            <label for="none">None </label><br />
                            <input type="checkbox" id="gluten_free_and_coeliac" name="pupil_dietary_requirements[]" value="gluten_free_and_coeliac"/>
                            <label for="gluten_free_and_coeliac">Gluten Free and Coeliac </label><br />
                            <input type="checkbox" id="dairy_free_and_lactose_free" name="pupil_dietary_requirements[]" value="gluten_free_and_coeliac"/>
                            <label for="dairy_free_and_lactose_free">Dairy Free and Lactose Free </label><br />
                            <input type="checkbox" id="tree_and_peanut_allergies" name="pupil_dietary_requirements[]" value="tree_and_peanut_allergies"/>
                            <label for="tree_and_peanut_allergies">Tree Nut and Peanut Allergies </label><br />
                            <input type="checkbox" id="fish_allergies" name="pupil_dietary_requirements[]" value="fish_allergies"/>
                            <label for="fish_allergies">Fish Allergies </label><br />
                            <input type="checkbox" id="halal" name="pupil_dietary_requirements[]" value="halal"/>
                            <label for="halal">Halal </label>
                        </div>
                        </div>
                        <div class="col-md-8" id="other">
                            <label class="font-weight-bold" for="pupil_other">If other, please specify: </label><br />
                            <textarea  name="pupil_other" id="pupil_other"></textarea>
                        </div>
                        <div class="col-md-10">
                            <label for="pupil_food_arrangement" class="font-weight-bold">Select food arrangement: </label>
                            <select name="pupil_food_arrangement" id="pupil_food_arrangement">
                                <option value="None">None</option>
                                <option value="School provides food">School provides food</option>
                                <option value="Food from home">Food from home</option>
                            </select>
                        </div>
                        <div class="col-md-12 offset-md-1">
                            <input type="submit" class="btn btn-primary" style="margin-bottom: 5px;"/>
                            <input type="reset" class="btn btn-primary" style="margin-bottom: 5px;"/>
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
</script>
@endsection
</body>
</html>