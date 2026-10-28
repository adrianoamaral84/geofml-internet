@extends('layouts.auth')

@section('content')
<h3 class="text-center" style="margin-bottom: 12px;">Recuperar senha</h3>
<p class="text-muted text-center">
    <small>Informe seu e-mail cadastrado. Se houver uma conta associada, enviaremos as instruções para criar uma nova senha.</small>
</p>
<form method="POST" action="{{ route('password.email') }}" id="recuperar-senha-form">
    @csrf
    <div class="form-group">
        <label for="email">E-mail</label>
        <input type="email" class="form-control underlined @error('email') is-invalid @enderror"
               name="email" id="email" value="{{ old('email') }}"
               placeholder="Seu e-mail" autocomplete="email" required autofocus>
    </div>
    <div class="form-group">
        <button type="submit" id="recuperar-senha-enviar" class="btn btn-block btn-primary"
                {{ session('status') ? 'disabled' : '' }}>
            {{ session('status') ? 'Solicitação registrada' : 'Enviar link de recuperação' }}
        </button>
    </div>
    <div class="form-group clearfix">
        <a class="pull-left" href="{{ route('login') }}"><small>Voltar para o login</small></a>
    </div>
</form>
@push('javascript')
<script>
(function () {
    var form = document.getElementById('recuperar-senha-form');
    var button = document.getElementById('recuperar-senha-enviar');
    if (!form || !button) return;
    form.addEventListener('submit', function (event) {
        if (button.disabled) {
            event.preventDefault();
            return;
        }
        // Desabilita após a validação nativa e impede duplo envio.
        button.disabled = true;
        button.textContent = 'Enviando...';
    });
})();
</script>
@endpush
@endsection
