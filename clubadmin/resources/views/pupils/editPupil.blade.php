@extends('layouts.app')
@section('content')
<div class="container" style="margin: 0; padding: 0;">
    <div class="row justify-content-center">
        @include('sidebar')
        <div class="offset-md-1 col-md-9" style="margin-top:50px;">
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
                    <form class="form-horizontal" method="POST" action="{{ action('App\Http\Controllers\PupilController@update', $pupil->id) }} " enctype="multipart/form-data" >
                        @method('PATCH')
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <label class="font-weight-bold">First Name:<span class="red-star">*</span>
                                    <input type="text" name="pupil_first_name" value="{{ $pupil->first_name }}"/>
                                </label>
                            </div>
                            <div class="col-md-4">
                                <label class="font-weight-bold">Last Name:<span class="red-star">*</span>
                                    <input type="text" name="pupil_last_name" value="{{ $pupil->last_name }}" />
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label class="font-weight-bold">Date of Birth:<span class="red-star">*</span>
                                    <input type="date" id="pupil_date_of_birth" name="pupil_date_of_birth" value="{{ $pupil->date_of_birth }}" />
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4" id="pupil_dietary_requirements">
                                <label><span class="font-weight-bold">Dietary Requirements:</span>
                                    <label for="gluten_free_and_coeliac">
                                        <input type="checkbox" id="gluten_free_and_coeliac" name="pupil_dietary_requirements[]" value="Gluten Free and Coeliac"
                                        <?php 
                                        if(sizeof($pupil_dietary_requirements_collection->where('pupil_id', $pupil->id)->where('dietary_requirements', 'Gluten Free and Coeliac')) > 0) {
                                            echo "checked";
                                        }
                                        ?>
                                        />Gluten Free and Coeliac 
                                    </label><br />
                                    <label for="dairy_free_and_lactose_free">
                                        <input type="checkbox" id="dairy_free_and_lactose_free" name="pupil_dietary_requirements[]" value="Dairy Free and Lactose Free"
                                            <?php 
                                            if(sizeof($pupil_dietary_requirements_collection->where('pupil_id', $pupil->id)->where('dietary_requirements', 'Dairy Free and Lactose Free')) > 0) {
                                                echo "checked";
                                            }
                                            ?>
                                        />Dairy Free and Lactose Free 
                                    </label><br />
                                    <label for="tree_and_peanut_allergies">
                                        <input type="checkbox" id="tree_nut_and_peanut_allergies" name="pupil_dietary_requirements[]" value="Tree Nut and Peanut Allergies"
                                            <?php 
                                            if(sizeof($pupil_dietary_requirements_collection->where('pupil_id', $pupil->id)->where('dietary_requirements', 'Tree Nut and Peanut Allergies')) > 0) {
                                                echo "checked";
                                            }
                                            ?>
                                        />Tree Nut and Peanut Allergies
                                    </label><br />
                                    <label for="fish_allergies">
                                        <input type="checkbox" id="fish_allergies" name="pupil_dietary_requirements[]" value="Halal"
                                            <?php 
                                            if(sizeof($pupil_dietary_requirements_collection->where('pupil_id', $pupil->id)->where('dietary_requirements', 'Fish Allergies')) > 0) {
                                                echo "checked";
                                            }
                                            ?>
                                        />Fish Allergies
                                    </label><br />
                                    <label for="halaal">
                                        <input type="checkbox" id="halaal" name="pupil_dietary_requirements[]" value="Halaal"
                                            <?php 
                                            if(sizeof($pupil_dietary_requirements_collection->where('pupil_id', $pupil->id)->where('dietary_requirements', 'Halaal')) > 0) {
                                                echo "checked";
                                            }
                                            ?>
                                        />Halaal 
                                    </label><br />
                                    <label for="other_option">
                                        <input type="checkbox" id="other_option" name="pupil_dietary_requirements[]" value="Other"
                                            <?php 
                                            if(sizeof($pupil_dietary_requirements_collection->where('pupil_id', $pupil->id)->where('dietary_requirements', 'Other')) > 0) {
                                                echo "checked";
                                            }
                                            ?>
                                        />Other
                                    </label>
                            </div>
                            <div class="col-md-4" id="other">
                                <label class="font-weight-bold" for="pupil_other">If other, please specify: </label><br />
                                <textarea  name="pupil_other" id="pupil_other">{{ $pupil->other_dietary_requirements }}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="pupil_food_arrangement" class="font-weight-bold">Select food arrangement:
                                    <select name="pupil_food_arrangement" id="pupil_food_arrangement">
                                        @if($pupil->food_arrangement == "None")
                                            <option value="None" selected>None</option>
                                            <option value="School provides food">School provides food</option>
                                            <option value="Food from home">Food from home</option>
                                        @elseif($pupil->food_arrangement == "School provides food")
                                            <option value="None">None</option>
                                            <option value="School provides food" selected>School provides food</option>
                                            <option value="Food from home">Food from home</option>
                                        @elseif($pupil->food_arrangement == "Food from home")
                                            <option value="None">None</option>
                                            <option value="School provides food">School provides food</option>
                                            <option value="Food from home" selected>Food from home</option>
                                        @endif
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
<script type="text/javascript"> 
    $('#pupils').addClass('active');
    $('#admin_bookings').addClass('active');
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