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
        <div class="offset-md-1 col-md-9" style="margin-top: 50px;">
            <div class="card">
                <div class="card-header">Display all activities</div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>   
                                    <th class="text-center">User ID</th>
                                    <th class="text-center">Activity</th>
                                    <th class="text-center">Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activities as $activity)
                                <tr>
                                    <td class="text-center">{{ $activity->user_id }}</td>
                                    <td class="text-center">{{ $activity->action }}</td>
                                    <td class="text-center">{{ $activity->created_at }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $activities->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>
</div>
<script type="text/javascript"> $('#activity_log').addClass('active'); </script>
@endsection
</body>
</html>