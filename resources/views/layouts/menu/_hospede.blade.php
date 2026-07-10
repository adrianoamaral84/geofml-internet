<li class="@if(request()->is('hospede/*')) active open @endif">
    <a href="">
        <i class="fa fa-bed"></i> Reserva <i class="fa arrow"></i>
    </a>
    <ul class="sidebar-nav">
        
        <li>
            <a href="{{route('hospede.solicitarinscricao')}}"> Solicitar Inscrição </a>
        </li>

        <li>
            <a href="{{route('hospede.meuspedidos')}}"> Minhas Solicitações </a>
        </li>
        


    </ul>
</li>


