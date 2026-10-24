<style>
a.js-ver-documento, a.js-ver-documento:link, a.js-ver-documento:visited {
    background-color: #6c757d !important;
    border-color: #6c757d !important;
    color: #fff !important;
}
a.js-ver-documento:hover, a.js-ver-documento:focus {
    background-color: #545b62 !important;
    border-color: #4e555b !important;
    color: #fff !important;
}
#modalDocumentoCompartilhado .modal-header, #modalDocumentoCompartilhado .modal-header * {
    color: #334155 !important;
}
#modalDocumentoCompartilhado .modal-footer .btn {
    background-color: #475569 !important;
    border-color: #475569 !important;
    color: #fff !important;
}
</style>
<div class="modal fade" id="modalDocumentoCompartilhado" tabindex="-1" role="dialog" aria-labelledby="tituloDocumentoCompartilhado" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document" style="max-width:min(1100px,96vw)">
        <div class="modal-content" style="border:0;border-radius:12px;overflow:hidden;box-shadow:0 20px 70px rgba(0,0,0,.25)">
            <div class="modal-header" style="background:#f8fafc">
                <h5 class="modal-title" id="tituloDocumentoCompartilhado">Documento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body p-2 p-md-3" style="background:#e9edf2">
                <iframe id="previewDocumentoCompartilhado" title="Visualizador do documento" style="width:100%;height:70vh;border:0;background:#fff;border-radius:6px"></iframe>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button></div>
        </div>
    </div>
</div>
@push('javascript')
<script>
$(document).on('click', 'a.js-ver-documento', function (event) {
    event.preventDefault();
    $('#tituloDocumentoCompartilhado').text($(this).attr('data-documento-titulo') || 'Documento');
    $('#previewDocumentoCompartilhado').attr('src', $(this).attr('href'));
    $('#modalDocumentoCompartilhado').modal('show');
});
$('#modalDocumentoCompartilhado').on('hidden.bs.modal', function () {
    $('#previewDocumentoCompartilhado').attr('src', 'about:blank');
});
</script>
@endpush
