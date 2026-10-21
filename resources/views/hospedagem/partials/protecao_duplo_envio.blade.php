<script>
(function () {
    // Protege tanto "Próximo" quanto "Confirmar pedido" contra envios repetidos.
    window.addEventListener('load', function () {
        var formulario = document.getElementById('password-form');
        if (!formulario) return;

        var enviado = false;
        formulario.addEventListener('submit', function (evento) {
            if (enviado) {
                evento.preventDefault();
                return;
            }

            // Mantém a validação nativa e as validações já registradas pela tela.
            if (evento.defaultPrevented || !formulario.checkValidity()) return;

            enviado = true;
            var botoes = formulario.querySelectorAll('button[type="submit"], input[type="submit"]');
            botoes.forEach(function (botao) {
                botao.disabled = true;
                if (botao.tagName === 'BUTTON') {
                    botao.textContent = 'Processando...';
                } else {
                    botao.value = 'Processando...';
                }
            });
        });
    });
})();
</script>
