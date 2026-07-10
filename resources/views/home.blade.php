@extends('layouts.app')

@section('content')


<article class="items-list-page">
    <div class="title-search-block">
        <div class="title-block">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="title"> Dashboard </h3>
                </div>
            </div>
        </div>
        <div class="items-search">
            
        </div>
    </div>

   
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-block">
                        <section class="example">
                            <div class="table-flip-scroll">
                                

                                @if($count > 0)
                                <div class="alert alert-primary" role="alert">
                                <center><i class="fa fa-envelope" aria-hidden="true"></i>
                                <a href="" style="color: white; text-decoration: none;">
                                @if($count == 1)
                                Você tem uma nova mensagem!
                                @else
                                Você tem {{$count}} novas mensagens!
                                @endif
                                </a></center>
                                </div>

                                @if($count > 1)
                                <div class="alert alert-primary" role="alert">
                                <center><i class="fa fa-envelope" aria-hidden="true"></i>
                                <a href="" style="color: white; text-decoration: none;"> Você tem {{$count}} novas mensagens! </a></center>
                                </div>
                                @endif

                                @endif
                                       <!-- corpo do documento aqui -->
                                
                               
                            </div>




                    </div>
                </div>
            </div>
        </div>
    </section>

    

 
</article>



@endsection