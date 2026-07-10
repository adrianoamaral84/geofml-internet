@extends('layouts.app')

@section('content')
<article class="items-list-page">
    <div class="title-search-block">
        <div class="title-block">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="title"> Cadastra Guarnição </h3>
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
                                <form id="password-form" action="{{ route('guarnicao.create') }}" method="POST">
                                    @csrf
                                    <div class="row form-group has-error">
                                        <div class="col-4">
                                            <label class="control-label">Guarnição</label>
                                            <input type="text" name="nome" id="nome" class="form-control boxed" value="{{old('nome')}}" maxlength="100" onpaste="return false;" autofocus>
                                            @error('nome')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                                        </div>
                                    </div>

                                    <div class="row form-group has-error">
                                        <div class="col-4">
                                            <label class="control-label">Estado</label>
                                             <select name="estado" class="form-control boxed">
                                  <option value="">Selecione Estado</option>
                                  @foreach ($estados as $estado) 
                                            
                                  <option value="{{$estado->id}}">{{$estado->descricao}}</option>
                                 @endforeach
                                </select> 
                                @error('estado')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                                        </div>
                                    </div>
                                    
                                    <hr>
                                    <div class="form-group row">
                                        <div class="col-sm-10 col-sm-offset-2">
                                            <button type="submit" class="btn btn-primary btn-sm rounded-s"><i class="fas fa-check-circle btn-sm fa-sm"></i>  Cadastrar </button>
                                        </div>
                                    </div>
                                </form>
                               
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-10 col-sm-offset-2">
                                   
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </section>
</article>
@endsection