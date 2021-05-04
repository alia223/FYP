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
                        <form id="pupil_form" class="form-horizontal" method="POST"
                        action="{{url('pupils') }}" enctype="multipart/form-data">
                        @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="font-weight-bold" for="pupil_first_name">First Name:<span class="red-star">*</span>
                                        <input type="text" name="pupil_first_name" id="pupil_first_name" required/>
                                    </label>
                                </div>
                                <div class="col-md-4">
                                    <label class="font-weight-bold" for="pupil_last_name">Last Name:<span class="red-star">*</span>                                
                                        <input type="text" name="pupil_last_name" id="pupil_last_name" required/>
                                    </label>
                                </div>
                            </div> 
                            <div class="row">                        
                                <div class="col-md-4">
                                    <label class="font-weight-bold" for="pupil_date_of_birth">Date of Birth:<span class="red-star">*</span>                                
                                        <input type="date" id="pupil_date_of_birth" name="pupil_date_of_birth" id="pupil_date_of_birth" />
                                    </label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12" id="pupil_dietary_requirements">
                                    <label><span class="font-weight-bold">Dietary Requirements:</span>
                                        <label for="gluten_free_and_coeliac">                                
                                            <input type="checkbox" id="gluten_free_and_coeliac" name="pupil_dietary_requirements[]" value="Gluten Free and Coeliac"/>Gluten Free and Coeliac 
                                        </label><br />
                                        <label for="dairy_free_and_lactose_free">                                
                                            <input type="checkbox" id="dairy_free_and_lactose_free" name="pupil_dietary_requirements[]" value="Dairy Free and Lactose Free"/>Dairy Free and Lactose Free
                                        </label><br />
                                        <label for="tree_nut_and_peanut_allergies">                                
                                            <input type="checkbox" id="tree_nut_and_peanut_allergies" name="pupil_dietary_requirements[]" value="Tree Nut and Peanut Allergies"/>Tree Nut and Peanut Allergies
                                        </label><br />
                                        <label for="fish_allergies">                                
                                            <input type="checkbox" id="fish_allergies" name="pupil_dietary_requirements[]" value="Fish Allergies"/>Fish Allergies 
                                        </label><br />
                                        <label for="halaal">                                
                                            <input type="checkbox" id="halaal" name="pupil_dietary_requirements[]" value="Halaal"/>Halal
                                        </label><br />
                                        <label for="other_option">                                
                                            <input type="checkbox" id="other_option" name="pupil_dietary_requirements[]" value="Other"/>Other
                                        </label>
                                    </label>
                                </div>
                                <div class="col-md-4" id="other">
                                    <label class="font-weight-bold" for="pupil_other">If other, please specify: 
                                        <textarea  name="pupil_other" id="pupil_other"></textarea>
                                    </label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="pupil_food_arrangement" class="font-weight-bold">Select food arrangement:
                                        <select name="pupil_food_arrangement" id="pupil_food_arrangement">
                                            <option value="None">None</option>
                                            <option value="School provides food">School provides food</option>
                                            <option value="Food from home">Food from home</option>
                                        </select>
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
    $('#pupil_date_of_birth').change(function(){
        if(calculateAge($('#pupil_date_of_birth').val()) < 11 && calculateAge($('#pupil_date_of_birth').val()) > 1 && new Date($('#pupil_date_of_birth').val()) < Date.now()) {
            $('#pupil_date_of_birth')[0].setCustomValidity("");
        }
        else {
            $('#pupil_date_of_birth')[0].setCustomValidity("Age of child must be between 1 and 11!");
        }
    });
    $('#other_option').change(function(){
        if($('#other_option').is(':checked')) {
            $('#pupil_other').attr('required', true);
        }
        else {
            $('#pupil_other').attr('required', false);
        }
    
    });
    function calculateAge(pupil_date_of_birth) {
        const diff = Date.now() - new Date(pupil_date_of_birth);
        const ageDate = new Date(diff);
        return Math.abs(ageDate.getUTCFullYear() - 1970);
    }
</script>
@endsection
</body>
</html>