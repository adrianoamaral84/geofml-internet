@extends('layouts.app')

@section('content')
<article class="items-list-page">
    <div class="title-search-block">
        <div class="title-block">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="title"> Usuários do Sistema <a href="{{route('usuario.novo')}}" class="btn btn-primary btn-sm rounded-s"> Add Novo </a></h3>
                </div>
            </div>
        </div>
        <div class="items-search">
            <form class="form-inline" method="POST" action="">
                @csrf
                <div class="input-group">
                    <input type="text" class="form-control boxed rounded-s" placeholder="Nome, CPF ou email" title="Nome, CPF ou email" value="{{ $search}}" name="search" id="search">
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
                                            <th width="30%">Nome</th>
                                            <th width="25%">Email</th>
                                            <th width="10%">CPF</th>
                                            <th width="13%">Perfil</th>
                                            <th width="10%">Status</th>
                                            <th width="10%" style="text-align: center;">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($consulta->count() == 0)
                                            <tr>
                                                <td colspan="6" style="text-align: center;"><small>Nenhum registro encontrado</small></td>
                                            </tr>
                                        @else   

                                            @foreach ($consulta as $item)

                                            <tr class="odd">
                                                <td>{{$item->name}}</td>
                                                <td>{{$item->email}}</td>
                                                <td>{{substr($item->cpf, 0, 3) . '.' . substr($item->cpf, 3, 3) . '.' . substr($item->cpf, 6, 3) . '-' . substr($item->cpf, 9)}}</td>
                                                <td>@foreach ($item->roles as $role)

                                                    {{ $role->display_name }}</br>

                                                    @endforeach
                                                </td>
                                                <td>
                                                    @if ($item->status == 1)
                                                    <h6><span class="badge badge-success">Ativo</span></h6>
                                                    @else
                                                    <h6><span class="badge badge-danger">Inativo</span></h6>
                                                    @endif
                                                </td>
                                                <td style="text-align: center;">
                                                    @if ($item->status == 1)
                                                    <a href="javascript:;" data-toggle="modal" onclick="deleteData({{$item->id}})" data-target="#DeleteModal" title="Inativar"><i class="fas fa-toggle-on fa-lg" style="color: green;"></i></a>
                                                    @else
                                                    <a href="javascript:;" data-toggle="modal" onclick="activateData({{$item->id}})" data-target="#ActivateModal" title="Ativar"><i class="fas fa-toggle-off fa-lg"></i></a>
                                                    @endif
                                                    &nbsp;
                                                    <a href="{{route('profile.user', ['id' => Crypt::encrypt($item->id)])}}" data-toggle="tooltip" data-placement="bottom" title="Ver Dados"><i class="far fa-eye fa-md" style="color: blue;"></i></a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                                <div class="modal fade" id="DeleteModal">
                                    <div class="modal-dialog" role="document">
                                        <form action="" id="deleteForm" method="get">
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
                                                    <p>Tem certeza que deseja inativar este usuário ?</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="formSubmit()">Sim</button>
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Não</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="modal fade" id="ActivateModal">
                                    <div class="modal-dialog" role="document">
                                        <form action="" id="activateForm" method="get">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title"><i class="fa fa-warning"></i> Atenção</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    {{ csrf_field() }}  
                                                    <p>Tem certeza que deseja ativar este usuário ?</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="formSubmitActivate()">Sim</button>
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Não</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
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
         var url = '{{ route("changeStatusUsuario", ":id") }}';
         url = url.replace(':id', id);
         $("#deleteForm").attr('action', url);
     }

     function formSubmit()
     {
         $("#deleteForm").submit();
     }

     function activateData(id)
     {
         var id = id;
         var url = '{{ route("changeStatusUsuario", ":id") }}';
         url = url.replace(':id', id);
         $("#activateForm").attr('action', url);
     }

     function formSubmitActivate()
     {
         $("#activateForm").submit();
     }
</script>
@endpush
@endsection