<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Login - GeoFML</title>
        
        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/vendor.css') }}">
        <link rel="stylesheet" id="theme-style" href="{{ asset('css/app-theme.css') }}">
        <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}">
        <!-- Fonts -->
        <link rel="stylesheet" href="{{ asset('fonts/fontawesome/css/all.min.css') }}">
    </head>
    <body>
        <div class="auth">
            <div style="width: 100%; height: 5px; background-color: #85CE36;"></div>
            <div class="auth-container">



                @if(Session::has('message'))
                <div>
                    <div class="alert alert-{{Session::get('message')['class']}}" role="alert">
                        <div align="center" class="card-content">
                            {{Session::get('message')['msg']}}
                        </div>
                    </div>
                </div>
                @endif



                <div class="card">
                    <div style="width: 100%; height: 5px; background-color: #85CE36;"></div>
                    <header class="auth-header">
                        <h2 class="auth-title">
                        <table style="width: 90%;" align="center">
                                <tr>
                                    <td rowspan="4" width="20%">
                                        <img src="{{ url('images/logo.png') }}" width="70px"></td>
                                    
                                </tr>
                                <tr>
                                    <td style="font-size: 12px">Exército Brasileiro - B Adm Ap / 5ª RM</td>
                                                                   </tr>
                                <tr>
                                    
                                    <td style="font-size: 27px"><b>Forte Marechal Luz</b></td>
                                </tr>
                                 <tr>
                                   
                                    <td>Sistema de Atendimento ao Usuário</td>
                                </tr>

                            </table>
                        </h2>  
                       
                    </header>
                    <div class="auth-content">
                @if(Session::has('message'))
                <div>
                    <div class="alert alert-{{Session::get('message')['class']}}" role="alert">
                        <div align="center" class="card-content">
                            {{Session::get('message')['msg']}}
                        </div>
                    </div>
                </div>
                @endif
   
   
                        @yield('content')

   
                    </div>
                </div>
            </div>
        </div>


<script src="{{ asset('js/vendor.js')}}"></script>
@stack('javascript')
    </body>
</html>