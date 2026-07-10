$('#radioPF').on('click', () => {
    $('#cnpjCpf').val('');
    $('#cnpjCpf').attr('maxlength', '14');
    $('#cnpjCpf').mask('###.###.###-##');

});


$('#radioPJ').on('click', () => {
    $('#cnpjCpf').val('');
    $('#cnpjCpf').attr('maxlength', '18');
    $('#cnpjCpf').mask('##.###.###/####-##');
});


$(document).ready(function () {
    $('#cnpjCpf').attr('maxlength', '14');
    $('#cnpjCpf').mask('###.###.###-##');
    $('#competencia').mask('##/####');
    $('#referencia').mask('####################');
    $('#vencimento').mask('##/##/####');
    $('.currency').mask('#.##0,00', { reverse: true });

    $('.currency').attr('maxlength', '16');
});

$('.currency').on('blur', (e) => {
    let campo = e.target;

    if (campo.value.length < 1) {
        campo.value = campo.value + "0,00";
        return;
    }

    if (campo.value.length < 3) {
        campo.value = campo.value + ",00";
        return;
    }
});


$('#btnMudarUg').on('click', (e) => {
    let select = e.target.parentNode.querySelector('select')
    let current = select.dataset.current
    let toGo = select.value

    if (current != toGo) {
        window.location.href = "/pagtesouro/index.php?ug="+toGo;
    }
});