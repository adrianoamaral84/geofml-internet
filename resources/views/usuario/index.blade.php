@extends('layouts.app')

@section('content')
<article class="items-list-page">
    <div class="title-search-block">
        <div class="title-block">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="title"> Lista de Usuários  <a href="{{Route('uf.create')}}" class="btn btn-primary btn-sm rounded-s"> Add Usuário </a></h3> 
                </div>
            </div>
        </div>
        <div class="items-search">
            <form class="form-inline" method="POST" action="{{Route('indexuf')}}">
            @csrf       
                <div class="input-group">
                    <input type="text" name="search" id="search" class="form-control boxed rounded-s" placeholder="Procurar" value="">
                    <span class="input-group-btn">
                        <button class="btn btn-secondary rounded-s" type="submit">
                            <i class="fa fa-search"></i>
                        </button>
                    </span>
                </div>
            </form>
        </div>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-block">
                        <section class="example">
                            <div class="table-flip-scroll">
                                <table class="table table-striped table-bordered table-hover flip-content">
                                    <thead class="flip-header">
                                        <tr>
                                            <th width="10%">ID</th>
                                            <th width="5%">Nome Completo</th>
                                            
                                            <th width="">E-mail</th>
                                            
                                            <th width="15%">Ação</th>
                                        </tr>
                                    </thead>
                                    <tbody>                                        
                                            @if ($consulta->count() == 0)
                                            <tr>
                                                <td colspan="3" style="text-align: center;"><small>Nenhum registro encontrado</small></td>
                                            </tr>
                                            @else
                                             @foreach ($consulta as $item) 
                                            <tr>
                                                 <td>                                               
                                                    <small>{{$item->id}}</small>                                                    
                                                </td>
                                                <td>                                               
                                                    <small>{{$item->sigla}}</small>                                                    
                                                </td>
                                                <td>{{$item->descricao}}</td>
                                                <td>
                                                    <h6>
                                                    

                <a href="{{ route('edituf', ['id' => Crypt::encrypt($item->id)]) }}" class="btn btn-info btn-sm">
                    <i class="fas fa-edit" style="color: #ffffff"></i></a>

                     <a href="{{ route('cidade.index', ['id' => Crypt::encrypt($item->id)]) }}" title="Mostra Cidades" class="btn btn-info btn-sm">
                    <i class="fas fa-angle-double-down" style="color: #ffffff"></i></a>
                                                   
                <a href="javascript:;" data-toggle="modal" onclick="deleteData({{$item->id}})" data-target="#DeleteModal" class="btn btn-danger btn-sm" title="Inativar">
                    <i class="fas fa-trash fa-sm" ></i></a>

                                                       
                                                    </h6>
                                                </td>
                                            </tr>
                                            @endforeach
                                            @endif
                                    </tbody>
                                </table>
                                <div class="modal fade" id="DeleteModal">
                                    <div class="modal-dialog" role="document">
                                        <form action="" id="deletearea" method="get">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title"><i class="fa fa-warning"></i> Atenção</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    {{ csrf_field() }}  
                                                    {{ method_field('DELETE') }}
                                                    <p>Tem certeza que deseja deletar esta UF ?</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="formSubmit()">Sim</button>
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Não</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                               {{$consulta->links()}}
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
@push('javascript')
<script type="text/javascript">
     function deleteData(id)
     {
         var id = id;
         var url = '{{ route("deleteuf", ":id") }}';
         url = url.replace(':id', id);
         $("#deletearea").attr('action', url);
     }

     function formSubmit()
     {
         $("#deletearea").submit();
     }

     
</script>
@endpush
@endsection