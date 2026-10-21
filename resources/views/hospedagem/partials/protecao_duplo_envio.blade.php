<script>
(function () {
    function instalarProtecao() {
        var formulario = document.getElementById('password-form');
        if (!formulario || formulario.dataset.protecaoDuploEnvio === '1') return;
        formulario.dataset.protecaoDuploEnvio = '1';

        var enviando = false;
        formulario.addEventListener('submit', function (evento) {
            if (enviando) {
                evento.preventDefault();
                return;
            }
            if (evento.defaultPrevented || !formulario.checkValidity()) return;
            enviando = true;

            // Mantém o primeiro submit nativo; desativa os botões no próximo ciclo.
            // Assim o navegador ainda envia os valores normalmente.
            window.setTimeout(function () {
                formulario.querySelectorAll('button[type="submit"], input[type="submit"]').forEach(function (botao) {
                    botao.disabled = true;
                    if (botao.tagName === 'BUTTON') {
                        botao.textContent = 'Processando pedido...';
                    } else {
                        botao.value = 'Processando pedido...';
                    }
                });
            }, 0);
        });

        // Caso o navegador restaure a página pelo histórico, habilita novo envio.
        window.addEventListener('pageshow', function (evento) {
            if (!evento.persisted) return;
            enviando = false;
            formulario.querySelectorAll('button[type="submit"], input[type="submit"]').forEach(function (botao) {
                if (botao.dataset.rotuloOriginal) {
                    if (botao.tagName === 'BUTTON') botao.innerHTML = botao.dataset.rotuloOriginal;
                    else botao.value = botao.dataset.rotuloOriginal;
                }
                botao.disabled = false;
            });
        });
        formulario.querySelectorAll('button[type="submit"], input[type="submit"]').forEach(function (botao) {
            botao.dataset.rotuloOriginal = botao.tagName === 'BUTTON' ? botao.innerHTML : botao.value;
        });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', instalarProtecao);
    } else {
        instalarProtecao();
    }
})();
</script>
