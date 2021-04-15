@extends('layouts.app')
@section('content')
<div class="container" style="margin: 0; padding: 0;">
    <div class="row justify-content-center">
        <div class="col-md-3">
            <div class="sidebar">
                @include('sidebar')            
            </div>
        </div>
        <div class="col-md-9" style="margin-top: 50px;">
            <div class="card">
                <div class="card-header">Display All Booked Students</div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>   
                                <th class="text-center">First Name</th>
                                <th class="text-center">Last Name</th>
                                <th class="text-center">Date of Birth</th>
                                <th class="text-center">Dietary Requirements</th>
                                <th class="text-center">Food Arrangement</th>
                                @if(Gate::denies('clubstaff')) 
                                <th class="text-center" colspan="3" style="color: black">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($children as $child)
                            <tr>
                                <td class="text-center">{{$child['first_name']}}</td>
                                <td class="text-center">{{$child['last_name']}}</td>
                                <td class="text-center">{{$child['date_of_birth']}}</td>
                                <td class="text-center">{{$child['dietary_requirements']}}</td>
                                <td class="text-center">{{$child['food_arrangement']}}</td>
                                @if(Gate::denies('clubstaff')) 
                                <td class="text-center"><a href="{{action('App\Http\Controllers\BookedStudentController@edit', $child['id'])}}" class="btn
                                btn-warning material-icons" title="Edit Details">build</a></td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript"> 
    $('#bookings').addClass('active'); 
</script>
@endsection