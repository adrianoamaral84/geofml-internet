<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	        <script async src="https://www.googletagmanager.com/gtag/js?id=G-GCX8SQ34HM"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', 'G-GCX8SQ34HM');
        </script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Novo acesso | GeoFml</title>

            
    
        <link rel="stylesheet" id="theme-style" href="{{ asset('css/app.css') }}">  
    <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/vendor.css') }}">
        <link rel="stylesheet" id="theme-style" href="{{ asset('css/app-theme.css') }}">
        <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}">
        <!-- Fonts -->
        <link rel="stylesheet" href="{{ asset('fonts/fontawesome/css/all.min.css') }} ">
</head>
<body>
  <div class="main-wrapper">
            <div class="app" id="app">
                <header class="header">
                    <div class="header-block header-block-collapse d-lg-none d-xl-none">
                        <button class="collapse-btn" id="sidebar-collapse-btn">
                            <i class="fa fa-bars"></i>
                        </button>
                    </div>
                   
                </header>
                <aside class="sidebar">
                    <div class="sidebar-container">
                        <div class="sidebar-header">
                            <div class="brand">
                                <div class="logo">
                                    <span class="l l1"></span>
                                    <span class="l l2"></span>
                                    <span class="l l3"></span>
                                    <span class="l l4"></span>
                                    <span class="l l5"></span>
                                </div> GEOFML
                            </div>
                        </div>
                     
                    </div>
                </aside>
                <div class="sidebar-overlay" id="sidebar-overlay"></div>
                <div class="sidebar-mobile-menu-handle" id="sidebar-mobile-menu-handle"></div>
                <div class="mobile-menu-handle"></div>
                <article class="content">
                @if(Session::has('message'))
                <div>
                    <div class="alert alert-{{Session::get('message')['class']}}" role="alert">
                        <div align="center" class="card-content">
                            {{Session::get('message')['msg']}}
                        </div>
                    </div>
                </div>
                @endif
                @if($errors->any())
                <div class="col-md-12 col-sm-6 col-xs-12">
                    <div class="alert alert-danger" role="alert">
                        <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                        </ul>
                    </div>
                </div>
                @endif
                @yield('content')
                </article>
            </div>
        </div>

        <div class="ref" id="ref">
            <div class="color-primary"></div>
            <div class="chart">
                <div class="color-primary"></div>
                <div class="color-secondary"></div>
            </div>
        </div>




<script src="{{ asset('js/vendor.js')}}"></script>
<script src="{{ asset('js/app.js') }}"></script>
@stack('javascript')
</body>
</html>
 
