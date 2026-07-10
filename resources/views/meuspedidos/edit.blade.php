@extends('layouts.app')

@section('content')
<article class="items-list-page">
    <div class="title-search-block">
        <div class="title-block">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="title"> Editar Temporada </h3>
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
                                <form id="password-form" action="{{ route('temporada.update') }}" method="POST">
                                    @csrf

                                   <div class="row form-group has-error">
                                        <div class="col-4">
                                             <label class="control-label">{{ __('Temporada') }}</label>
                            <input type="hidden" name="id" value="{{ $consulta->id }}">
                                <select name="temporada" id="temporada" required class="custom-select  @error('temporada') is-invalid @enderror">
                                     <option value="" disabled="">Selecione temporada</option>
                                
                                    @foreach($temporadas as $temporada)

                                         <option value="{{$temporada->id}}" @if($consulta->tipo_temporada_id == $temporada->id)selected="selected" @endif>{{$temporada->tipo_temporada}}</option>
                                    @endforeach
                            </select>
                            @error('temporada')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                                        </div>
                                
                                    

                                  
                                        <div class="col-4">
                                            <label class="control-label">Data Início</label>
                                            <input type="date" name="dataini" id="dataini" class="form-control boxed" value="{{ $consulta->data_inicio }}" maxlength="50" minlength="2" onpaste="return false;" autofocus>
                                            @error('dataini')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror 
                                        </div>
                                    


                                           <div class="col-4">
                                            <label class="control-label">Data Término</label>
                                            <input type="date" name="datater" id="datater" class="form-control boxed" value="{{ $consulta->data_termino }}" maxlength="50" minlength="2" onpaste="return false;" autofocus>
                                            @error('datater')
                                <span class="has-error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                                        </div>


                                    </div>
                                    
                                    <hr>
                                    <div class="form-group row">
                                        <div class="col-sm-10 col-sm-offset-2">
                                            <button type="submit" class="btn btn-primary btn-sm rounded-s"><i class="fas fa-check-circle fa-sm"></i>  Atualizar </button>
                                             <a href="{{ route('temporada.index') }}" class="btn btn-danger btn-sm rounded-s"><i class="fas fa-window-close fa-sm"></i>  Cancelar </a>
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