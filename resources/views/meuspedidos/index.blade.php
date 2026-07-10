@extends('layouts.app')

@section('content')
<article class="items-list-page">
    <div class="title-search-block">
        <div class="title-block">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="title"> Minhas Solicitações  <a href="{{route('hospede.solicitarinscricao')}}" class="btn btn-primary btn-sm rounded-s"> Add </a></h3> 
                    <small>Minha lista de solicitações de hospedagem!</small>
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
                                <table class="table table-striped table-bordered table-hover flip-content">
                                    <thead class="flip-header">
                                        <tr>
                                          
                                            <th width="">Tipo</th>
                                            <th width="15%" style="text-align: center;">Data Início</th>
                                            <th width="15%" style="text-align: center;">Data Final</th>       
                                            <th width="10%" style="text-align: center;">Status</th>
                                            <th width="15%" style="text-align: center;">Detalhes / Reserva</th>

                                        </tr>
                                    </thead>
                                    <tbody>                                        
                                            @if ($consulta->count() == 0)
                                            <tr>
                                                <td colspan="5" style="text-align: center;"><small>Nenhum registro encontrado</small></td>
                                            </tr>
                                            @else
                                             @foreach ($consulta as $item) 
                                            <tr>
                                                <td>
                                                   
                                                   
                                                {{ $item->tipouh->descricao }}
                                                   
                                                    
                                                    </td>
                                                <td style="text-align: center;">
                                                    {{\Carbon\Carbon::parse($item->data_inicio)->format('d/m/Y')}}
                                                </td>
                                        <td style="text-align: center;">
                                           

                                                @if($item->data_termino)

                                                    {{\Carbon\Carbon::parse($item->data_termino)->format('d/m/Y')}}
                                            
                                                @endif

                                            

                                            
                                        </td>
                                            <td style="text-align: center;">
                                                

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
                                                @if($item->status == 6)

                                                   <h6><span class="badge badge-danger">{{ $item->status_hospedagem->status}}</span></h6>

                                                @endif
                                                @if($item->status == 7)

                                                   <h6><span class="badge badge-info">{{ $item->status_hospedagem->status}}</span></h6>

                                                @endif



                                            </td>
                                            <td style="text-align: center;">
                                                
                                            <a href="{{ route('hospede.meupedido', ['id' => Crypt::encrypt($item->id)]) }}" class="btn btn-secondary btn-sm" title="Visualizar / Confirmar">
                                                                <i class="fas fa-bars"></i></a>


                                            @if($item->status == 0)
                                            <a href="{{ route('hospede.solicitarinscricao.edit', ['id' => Crypt::encrypt($item->id)]) }}" class="btn btn-info btn-sm" title="Editar / Alterar">
                                                                <i class="fas fa-edit" style="color: #fff;"></i></a> 
                                            

                                            <a href="javascript:;" data-toggle="modal" onclick="deleteData('{{ Crypt::encrypt($item->id) }}')" data-target="#DeleteModal" class="btn btn-danger btn-sm" title="Deletar">
                                            <i class="fas fa-trash fa-sm" ></i></a>                  
                                            @endif


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
                               {{$consulta->links()}}
                            </div>
                            <div class="form-group row">
                                <div class="form-group col-sm-12 col-md-12 col-lg-12" style="font-size: 12px;">
                                    Legenda:<br>
                                    - <b>Aguardando Aprovação</b> = Administração está verificando disponibilidade de Unidade Habitacional<br>
                                    - <b>Aguardando Pagamento</b> = Unidade habitacional liberada aguardando a confirmação do pagamento de <b><font color='red'>01 (uma) diária para confirmar sua reserva</font></b><br>
                                    - <b>Aguardando Comprovante</b> = Unidade habitacional liberada <b><font color='red'>aguardando o envio do comprovante para aprovação</font></b><br>
                                    - <b>Aguardando Liberação</b> = Administração verificando o comprovante para liberação da Unidade<br>
                                    - <b>Aprovado</b> = Unidade Habitacional Liberada para o uso. <b><font color='red'>O Sr(a) receberá um e-mail com a reserva confirmada.</font></b><br>
                                    - <b>Fila de Espera</b> = Não tem disponibilidade para o período solicitado<br>
                                    - <b>Negado</b> = Pedido de Inscrição negado pela administração do Forte Marechal Luz<br>
                                    - <b>Cancelado pelo Usuário</b> = Pedido de hospedagem cancelado pelo usuário<br>

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
         var url = '{{ route("hospede.delete.pedido", ":id") }}';
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
