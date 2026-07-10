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

        <!-- Datatables -->
        <link rel="stylesheet" href="{{ asset('js/DataTables/DataTables-1.10.22/css/dataTables.bootstrap4.min.css') }} ">
        <link rel="stylesheet" href="{{ asset('js/DataTables/Buttons-1.6.5/css/buttons.bootstrap4.min.css') }} ">
        <link rel="stylesheet" href="{{ asset('js/DataTables/Select-1.3.1/css/select.bootstrap4.min.css') }} ">
        <!-- Datatables -->
   


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
                    <div class="header-block header-block-nav">
                        <ul class="nav-profile">
                        @guest
                            <li class="profile dropdown">
                                <a class="nav-link" href="">{{ __('Login') }}</a>
                            </li>
                        @else
                     
                            <li class="profile dropdown">
                                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-user-circle fa-lg"></i>
                                    <small class="name">{{ Auth::user()->name }}</small>
                                </a>

                                <div class="dropdown-menu profile-dropdown-menu" aria-labelledby="dropdownMenu1">
                                    <a class="dropdown-item" href="{{ route('profile') }}">
                                        <small><i class="fa fa-user icon"></i> Perfil </small>
                                    </a>
                                    <a class="dropdown-item" href="{{ route('senha') }}">
                                        <small><i class="fas fa-key icon"></i> Alterar Senha </small>
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        <small><i class="fa fa-power-off icon"></i> {{ __('Logout') }}</small>
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>

                        @endguest
                        </ul>
                    </div>
                </header>
                <aside class="sidebar">
                    <div class="sidebar-container">
                        <div class="sidebar-header">
                            <div class="brand">
                                <img src="{{ url('images/logo.png') }}" width="40px">
                                        <span style="left: 20px; position: relative;"> GEOFML</span> 
                            </div>
                        </div>
                        <nav class="menu">
                            <ul class="sidebar-menu metismenu" id="sidebar-menu">
                                <li class="">
                                    <a href="{{url('/admin')}}">
                                        <i class="fa fa-home"></i> Dashboard 
                                    </a>
                                </li>


                                @role('administrador_especial')
                                    @include('layouts.menu._adm_esp')
                                @endrole

                                @role('administrador_geral')
                                     @include('layouts.menu._adm_esp')
                                      @include('layouts.menu._hospede')
                                @endrole

                                @role('atendente')
                                    @include('layouts.menu._atendente')
                                @endrole

                                @role('hospede') 
                                                                  
                                    @include('layouts.menu._hospede')
                                @endrole

                                @role('usuario')
                                    usuario
                                @endrole



                                <!--
                                @can('administrador_especial')                        
                                    isAdmEsp
                                @endcan

                                @can('administrador_geral')
                                    isAdmGeral
                                @endcan

                                @can('atendente')
                                    isAtendente
                                @endcan
                                
                                @can('hospede') 
                                    isHospede
                                @endcan

                                @can('usuario')
                                    isUsuario
                                @endcan
                                -->
                               
                               
                               
                                
                            </ul>
                        </nav>
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
                       <strong>Whoops!</strong> Houve alguns problemas com sua entrada.<br><br>
                        @foreach ($errors->all() as $error)
                       
                        <ul>
                            <li>{{ $error }}</li>
                        </ul>
                        @endforeach
                        
                    </div>
                </div>
                @endif
                @yield('content')
                </article>
            </div>
        
        </div>









@stack('javascript')
</body>
</html>
 
