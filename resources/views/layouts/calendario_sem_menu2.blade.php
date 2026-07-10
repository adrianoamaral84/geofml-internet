<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>{{ config('app.name', 'Administrator FML') }}</title>


        <link rel="stylesheet" id="theme-style" href="{{ asset('css/app.css') }}">  
        <link rel="stylesheet" href="{{ asset('css/vendor.css') }}">
        <link rel="stylesheet" id="theme-style" href="{{ asset('css/app-theme.css') }}">
        <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}">

        <!-- Fonts -->
        <link rel="stylesheet" href="{{ asset('fonts/fontawesome/css/all.min.css') }} ">

        <!-- FullCalendar -->
        <link href="{{ asset('fullcalendar5/lib/main.css') }}" rel='stylesheet' />
        

       
       

</head>
<body>
    
    <div style="width: 100%; height: 0px; display: flex; flex-direction: row; justify-content: center; align-items: center;">
        <div style="width: 60%; height: 60%;">
            @yield('content')
        </div>
    </div>
    

<script src="{{ asset('js/vendor.js')}}"></script>
<script src="{{ asset('js/app.js') }}"></script>
<!-- jQuery Version 1.11.1 -->
     
        
        <!-- FullCalendar -->
        <script src="{{ asset('fullcalendar5/lib/main.js') }}"></script>
        
        @include ('calendario.scriptsCalendario2')
        
@stack('javascript')
</body>
</html>
 
