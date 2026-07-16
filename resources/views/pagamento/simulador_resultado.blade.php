<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">

    <title>{{ $titulo }}</title>

    <link
        rel="stylesheet"
        href="{{ asset('css/app.css') }}"
    >
</head>

<body>
    <div class="container py-5">
        <div
            class="alert alert-{{ $sucesso ? 'success' : 'warning' }}"
        >
            <h4>{{ $titulo }}</h4>

            <p class="mb-0">
                {{ $mensagem }}
            </p>
        </div>

        <button
            type="button"
            class="btn btn-secondary"
            onclick="window.close()"
        >
            Fechar janela
        </button>
    </div>
</body>
</html>