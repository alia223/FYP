<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://code.jquery.com/jquery-2.1.1.min.js" type="text/javascript"></script>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/home.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        * {
            margin: 0;
            box-sizing: border-box;
        }
        .btn {
            background-color: <?php echo $rules->brand_colour?>; 
            color: <?php echo $rules->text_colour?>; 
            margin-top:10px;
            border-style: none;
        }
        .card-header {
            background-color: <?php echo $rules->brand_colour?>;
            color: <?php echo $rules->text_colour?>;
        }
        a.active {
            background-color: <?php echo $rules->brand_colour?>;
            color: <?php echo $rules->text_colour?>;
        }
        .btn:hover {
            background-color: <?php echo $rules->brand_colour?>;
            color: <?php echo $rules->text_colour?>;
        }

        .sidebar{
            height: 94vh;
        }

        .custom-buttons {
            width: 30px; 
            height: 30px; 
            padding:2px; 
            margin: 0;"
        }
        .alert{
            margin: 0;
            padding: 0;
        }
        .card-body {
            margin: 0;
        }
        .red-star {
            color: red;
        }
        .pagination { 
            justify-content: center; 
            margin-top: 10px;
        }

        td {
            height: 30px;
        }
        
        .page-link {
            color: <?php echo $rules->brand_colour?>;
        }

        .page-item.active .page-link {
            background-color: <?php echo $rules->brand_colour?>;
        }

        a:focus,
button:focus,
input:focus,
textarea:focus {
outline: none !important;
}
    </style>
</head>
<body>
    <div id="app">
        <nav id="navbar" class="navbar navbar-expand-md navbar-light shadow-sm" style="background-color: <?php echo $rules->brand_colour; ?>; padding: 0;">
            <div class="container">
            <img src="http://127.0.0.1/assist/storage/app/public/images/{{$rules->brand_logo}}" height="35px" width="35px" style="margin-right: 10px;"/>
                <a class="navbar-brand" id="navbar-brand" href="{{ url('home') }}" style="color:<?php echo $rules->text_colour;?>; padding: 0;">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}" style="color: white;">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}" style="color: white;">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre style="color:<?php echo $rules->text_colour;?>;">
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();" style="color:black;">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>  
    </div>
    @yield('content') 
</body>
</html>
