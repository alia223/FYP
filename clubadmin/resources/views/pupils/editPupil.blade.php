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
                    <form class="form-horizontal" method="POST" action="{{ action('App\Http\Controllers\PupilController@update', $pupil->id) }} " enctype="multipart/form-data" >
                        @method('PATCH')
                        @csrf
                        <div class="col-md-8">
                            <label class="font-weight-bold">First Name: </label>
                            <input type="text" name="pupil_first_name" value="{{ $pupil->first_name }}"/>
                        </div>
                        <div class="col-md-8">
                            <label class="font-weight-bold">Last Name: </label>
                            <input type="text" name="pupil_last_name" value="{{ $pupil->last_name }}" />
                        </div>
                        <div class="col-md-8">
                            <label class="font-weight-bold">Date of Birth: </label>
                            <input type="date" name="pupil_date_of_birth" value="{{ $pupil->date_of_birth }}" />
                        </div>
                        <div class="col-md-8" id="pupil_dietary_requirements">
                            <label class="font-weight-bold">Dietary Requirements: </label><br />
                            <input type="checkbox" id="none" name="pupil_dietary_requirements[]" value="None"
                            <?php 
                            if(sizeof($pupil_dietary_requirements_collection->where('pupil_id', $pupil->id)->where('dietary_requirements', 'None')) > 0) {
                                echo "checked";
                            }
                            ?>
                            />
                            <label for="none">None </label><br />
                            <input type="checkbox" id="gluten_free_and_coeliac" name="pupil_dietary_requirements[]" value="Gluten Free and Coeliac"
                            <?php 
                            if(sizeof($pupil_dietary_requirements_collection->where('pupil_id', $pupil->id)->where('dietary_requirements', 'Gluten Free and Coeliac')) > 0) {
                                echo "checked";
                            }
                            ?>
                            />
                            <label for="gluten_free_and_coeliac">Gluten Free and Coeliac </label><br />
                            <input type="checkbox" id="dairy_free_and_lactose_free" name="pupil_dietary_requirements[]" value="Dairy Free and Lactose Free"
                            <?php 
                            if(sizeof($pupil_dietary_requirements_collection->where('pupil_id', $pupil->id)->where('dietary_requirements', 'Dairy Free and Lactose Free')) > 0) {
                                echo "checked";
                            }
                            ?>
                            />
                            <label for="dairy_free_and_lactose_free">Dairy Free and Lactose Free </label><br />
                            <input type="checkbox" id="tree_and_peanut_allergies" name="pupil_dietary_requirements[]" value="Tree nut and Peanut Allergies"/>
                            <label for="tree_and_peanut_allergies">Tree Nut and Peanut Allergies </label><br />
                            <input type="checkbox" id="fish_allergies" name="pupil_dietary_requirements[]" value="Fish Allergies"
                            <?php 
                            if(sizeof($pupil_dietary_requirements_collection->where('pupil_id', $pupil->id)->where('dietary_requirements', 'Fish Allergies')) > 0) {
                                echo "checked";
                            }
                            ?>
                            />
                            <label for="fish_allergies">Fish Allergies </label><br />
                            <input type="checkbox" id="halal" name="pupil_dietary_requirements[]" value="Halal"
                            <?php 
                            if(sizeof($pupil_dietary_requirements_collection->where('pupil_id', $pupil->id)->where('dietary_requirements', 'Halal')) > 0) {
                                echo "checked";
                            }
                            ?>
                            />
                            <label for="halal">Halal </label>
                        </div>
                        <div class="col-md-8" id="other">
                            <label class="font-weight-bold" for="pupil_other">If other, please specify: </label><br />
                            <textarea  name="pupil_other" id="pupil_other">{{ $pupil->other_dietary_requirements }}</textarea>
                        </div>
                        <div class="col-md-10">
                            <label for="pupil_food_arrangement" class="font-weight-bold">Select food arrangement: </label>
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