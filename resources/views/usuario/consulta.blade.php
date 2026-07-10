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
           

        </div>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                   <a href="{{route('user.list.inativos')}}" class="btn btn-danger btn-sm rounded-s"> Inativos </a>  
                   <a href="{{route('user.index')}}" class="btn btn-success btn-sm" style="color: white;"> Ativos </a>

                <div class="card">
                    <div class="card-block">
                        <section class="example">
                            <div class="table-flip-scroll">
                                <table class="table table-striped table-bordered table-hover flip-content" id="example">
                                    <thead class="flip-header">
                                        <tr>
                                           
                                            <th>Nome</th>
                                            
                                            <th>CPF</th>
                                            <th>Perfil</th>
                                            <th style="text-align: center;">Status</th>
                                            <th style="text-align: center;min-width: 8%;">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($consulta->count() == 0)
                                            <tr>
                                                <td colspan="6" style="text-align: center;"><small>Nenhum registro encontrado</small></td>
                                            </tr>
                                        @else   

                                            @foreach ($consulta as $item)

                                            <tr>
                                              
                                                <td>{{$item->name}}</td>
                                               
                                                <td>{{substr($item->cpf, 0, 3) . '.' . substr($item->cpf, 3, 3) . '.' . substr($item->cpf, 6, 3) . '-' . substr($item->cpf, 9)}}</td>
                                                <td>@foreach ($item->roles as $role)

                                                    {{ $role->display_name }}</br>

                                                    @endforeach
                                                </td>
                                                <td style="text-align: center;">
                                                    @if ($item->status == 1)
                                                    <h6><span class="badge badge-success">Ativo</span></h6>
                                                    @elseif($item->status == 2)
                                                    <h6><span class="badge badge-danger">Inativo</span></h6>
                                                    @elseif($item->status == 3)
                                                    <h6><span class="badge badge-secondary">Aguardando</span></h6>
                                                    @elseif($item->status == 3)
                                                    <h6><span class="badge badge-danger">Expirado</span></h6>
                                                    @endif
                                                </td>
                                                <td style="text-align: center;">
                                                    @if ($item->status == 1)
                                                    <a href="javascript:;" data-toggle="modal" onclick="deleteData({{$item->id}})" data-target="#DeleteModal" title="Inativar"><i class="fas fa-toggle-on fa-lg" style="color: green;"></i></a>
                                                    @else
                                                    <a href="javascript:;" data-toggle="modal" onclick="activateData({{$item->id}})" data-target="#ActivateModal" title="Ativar"><i class="fas fa-toggle-off fa-lg"></i></a>
                                                    @endif
                                                    &nbsp;
                                                    <a href="{{route('usuario.verdados', ['id' => Crypt::encrypt($item->id)])}}" data-toggle="tooltip" data-placement="bottom" title="Ver Dados"><i class="far fa-eye fa-md" style="color: blue;"></i></a>
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
    $(document).ready(function() {
    var table = $('#example').DataTable({
    
    lengthChange: false,
    lengthMenu: [
            [ 10, 25, 50, -1 ],
            [ '10 registros', '25 registros', '50 registros', 'Mostrar todos' ]
        ],
   buttons: [

            {
                extend: 'excelHtml5',              
                title: 'GEOFML - Usuarios do Sistema'
            },
            {
                extend: 'pdfHtml5',
                title: 'GEOFML - Usuarios do Sistema'
            },
            'print','pageLength',
    ],
    select: true,
    "processing": true,
    "order": [[ 0, "asc" ]],
    stateSave: true,
    language: {          
    "sEmptyTable": "Nenhum registro encontrado",
    "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
    "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
    "sInfoFiltered": "(Filtrados de _MAX_ registros)",
    "sInfoPostFix": "",
    "sInfoThousands": ".",
    "sLengthMenu": "_MENU_ resultados por página",
    "sLoadingRecords": "Carregando...",
    "sProcessing": "Processando...",
    "sZeroRecords": "Nenhum registro encontrado",
    "sSearch": "Pesquisar",
    "oPaginate": {
        "sNext": "Próximo",
        "sPrevious": "Anterior",
        "sFirst": "Primeiro",
        "sLast": "Último"
    },
    "oAria": {
        "sSortAscending": ": Ordenar colunas de forma ascendente",
        "sSortDescending": ": Ordenar colunas de forma descendente"
    },
    "select": {
        "rows": {
            "_": " Selecionado %d linhas",
            "0": " Nenhuma linha selecionada",
            "1": " Selecionado 1 linha"
        }
    },
    "buttons": {
        "copy": "Copiar para a área de transferência",
        "copyTitle": "Cópia bem sucedida",
        "copySuccess": {
            "1": "Uma linha copiada com sucesso",
            "_": "%d linhas copiadas com sucesso"
        }
    }


    }
    });
    table.buttons().container().appendTo( '#example_wrapper .col-md-6:eq(0)' );
    } ); 
</script>
    <script src="{{ asset('js/DataTables/datatables.min.js') }}" ></script>  
    <script src="{{ asset('js/DataTables/DataTables-1.10.22/js/dataTables.bootstrap4.min.js') }}" ></script>  
    <script src="{{ asset('js/DataTables/Buttons-1.6.5/js/dataTables.select.min.js') }}" ></script>
    <script src="{{ asset('js/DataTables/Buttons-1.6.5/js/buttons.bootstrap4.min.js') }}" ></script>
    <script src="{{ asset('js/DataTables/Buttons-1.6.5/js/dataTables.buttons.min.js') }}" ></script>
    <script src="{{ asset('js/DataTables/Buttons-1.6.5/js/buttons.colVis.min.js') }}" ></script>
    <script src="{{ asset('js/DataTables/Buttons-1.6.5/js/buttons.html5.min.js') }}" ></script>
    <script src="{{ asset('js/DataTables/Select-1.3.1/js/select.bootstrap4.min.js') }}" ></script>   
@endpush
@endsection