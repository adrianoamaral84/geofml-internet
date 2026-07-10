<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>{{ config('app.name', 'Administrator FML') }}</title>

            
    
        <link rel="stylesheet" id="theme-style" href="{{ asset('css/app.css') }}">  
	<!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/vendor.css') }}">
        <link rel="stylesheet" id="theme-style" href="{{ asset('css/app-theme.css') }}">
        <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}">
        <!-- Fonts -->
        <link rel="stylesheet" href="{{ asset('fonts/fontawesome/css/all.min.css') }} ">
</head>
<body>
    <div class="auth">


<article class="content">
                
               
               
                
                </article>
     



            <div class="auth-container">
               
                 
           



                <div class="card">

                        <div style="width: 100%; height: 5px; background-color: #85CE36;">

                        </div>

                    <header class="auth-header">

                        <h1 class="auth-title">
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
                            
                        </h1>

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

                <font color="red">
                <small><b>Exclusivo para:</b><br> 
                       - Oficiais, Subtenentes e Sargentos das Forças Armadas <br>
                        - Pensionistas de militares do Exército Brasileiro<br></small>
                        </font> <br>
 @yield('content')
                </div>
                </div>

               
            </div>
        </div>




<script src="{{ asset('js/vendor.js')}}"></script>
<script src="{{ asset('js/app.js') }}"></script>
@stack('javascript')
</body>
</html>
 
