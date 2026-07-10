@extends('layouts.app')

@section('content')
<article class="items-list-page">
    <div class="title-search-block">
        <div class="title-block">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="title"> Hospedagem  </h3>
                    <small>Lista de Pedidos de Usuários para hospedagem </small> 
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

                                <a href="{{ route('hospedagem.aguardando_liberacao') }}" title="Ver Aguardando Liberação" class="btn btn-success btn-xl" style="color: #fff">
                                   <i class="fas fa-angle-double-up"></i>
                                Ver Aguardando Liberação
                                </a>

                                
                                
                                <table class="table table-striped table-bordered table-hover flip-content" id="tabela">
                                    <thead class="flip-header">
                                        <tr>
                                           
                                            <th width="10%">Posto / Grad</th>
                                            <th width="35%">Nome</th>
                                            <th width="20%" style="text-align: center;">Tipo</th>
                                            <th width="10%">Data Ini</th>
                                            <th width="10%" style="text-align: center;">Data Term</th>
                                            <th width="10%" style="text-align: center;">Status</th>
                                          
                                          <!--  <th width="10%" style="text-align: center;">Ação</th> -->
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
                                            
                                               <td> {{$item->user->posto->sigla}} </td>
                                                <td> 
                                                <a href="{{ route('hospedagem.verdados', ['id' => Crypt::encrypt($item->id)]) }}" title="Ver Dados">            
                                                        {{$item->user->name}}
                                                    </a> 
                                                </td>
                                                <td>{{$item->tipouh->descricao}}</td>
                                                <td style="text-align: center;">
{{ \Carbon\Carbon::parse($item->data_inicio)->format('d/m/Y') }}
                                                </td>
                                                <td style="text-align: center;">
                                                    {{ \Carbon\Carbon::parse($item->data_termino)->format('d/m/Y') }}
                                                    
                                                </td>

                                                <td>
                                                    @if($item->status == 0)

                                                <h6><span class="badge badge-secondary">{{ $item->status_hospedagem->status}}</span></h6>

                                                @endif

                                                @if($item->status == 1)

                                                    <h6><span class="badge badge-danger">{{ $item->status_hospedagem->status}}</span></h6>

                                                @endif

                                                @if($item->status == 2)

                                                   <h6><span class="badge badge-success">{{ $item->status_hospedagem->status}}</span></h6>

                                                @endif

                                                 @if($item->status == 3)

                                                   <h6><span class="badge badge-secondary">{{ $item->status_hospedagem->status}}</span></h6>

                                                @endif

                                                @if($item->status == 4)

                                                   <h6><span class="badge badge-info">{{ $item->status_hospedagem->status}}</span></h6>

                                                @endif

                                                @if($item->status == 5)

                                                   <h6><span class="badge badge-secondary">{{ $item->status_hospedagem->status}}</span></h6>

                                                @endif
                                                    </td>
                                               <!--
                                                <td style="text-align: center;">
                                                   


                        <a href="javascript:;" data-toggle="modal" onclick="liberaHospedagem('{{ Crypt::encrypt($item->id) }}')" data-target="#AprovaModal" class="btn btn-success btn-sm" title="Liberar">
                            <i class="fas fa-check fa-sm" style="color: #ffffff"></i></a>
                    
                                                   
                        <a href="javascript:;" data-toggle="modal" onclick="negaHospedagem('{{ Crypt::encrypt($item->id) }}')" data-target="#NegarModal" class="btn btn-danger btn-sm" title="Negar">
                            <i class="fas fa-ban fa-sm" ></i></a>

                                                   
                                                </td>
                                            -->
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
                                                    <p>Tem certeza que deseja deletar ?</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="formSubmit()">Sim</button>
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Não</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                 <div class="modal fade" id="AprovaModal">
                                    <div class="modal-dialog" role="document">
                                        <form action="" id="aprovapedido" method="get">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title"><i class="fa fa-warning"></i> Atenção</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    {{ csrf_field() }}  
                                                    {{ method_field('PUT') }}
                                                    <p>Deseja realmente Aprovar esse Pedido?</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="formSubmitAprova()">Sim</button>
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Não</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>


                                <div class="modal fade" id="NegarModal">
                                    <div class="modal-dialog" role="document">
                                        <form action="" id="negarpedido" method="get">
                                            <div class="modal-content">
                                                <div class="modal-header-dangeri">
                                                    <h4 class="modal-title"><i class="fa fa-warning"></i> Atenção</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    {{ csrf_field() }}  
                                                    {{ method_field('PUT') }}
                                                    <p>Deseja realmente Negar esse Pedido?</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="formSubmitNegar()">Sim</button>
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Não</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                
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
         var url = '{{ route("uh.delete", ":id") }}';
         url = url.replace(':id', id);
         $("#deletearea").attr('action', url);
     }
     function liberaHospedagem(id)
     {
         var id = id;
         var url = '{{ route("hospedagem.liberar", ":id") }}';
         url = url.replace(':id', id);
         $("#aprovapedido").attr('action', url);
     }

     function negaHospedagem(id)
     {
         var id = id;
         var url = '{{ route("hospedagem.negar", ":id") }}';
         url = url.replace(':id', id);
         $("#negarpedido").attr('action', url);
     }

     function formSubmit()
     {
         $("#deletearea").submit();
     }

    function formSubmitAprova()
     {
         $("#aprovapedido").submit();
     }

     function formSubmitNegar()
     {
         $("#negarpedido").submit();
     }

  


    $(document).ready(function() {
        var table = $('#tabela1').DataTable({
    
    lengthChange: false,
    lengthMenu: [
            [ 10, 25, 50, -1 ],
            [ '10 registros', '25 registros', '50 registros', 'Mostrar todos' ]
        ],
    buttons: [

            {
                extend: 'excelHtml5',              
                title: 'GEOFML - Lista Pedidos Hospedagem'
            },
            {
                extend: 'pdfHtml5',
                title: 'GEOFML - Lista Pedidos Hospedagem'
            },
            'print','pageLength',
    ],
    select: true,
    "processing": true,
    "order": [[ 1, "asc" ]],
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
    table.buttons().container().appendTo( '#tabela_wrapper .col-md-6:eq(0)' );
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