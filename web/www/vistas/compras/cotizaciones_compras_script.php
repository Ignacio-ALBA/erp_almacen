<script nonce="<?php echo $nonce; ?>">

document.addEventListener('DOMContentLoaded', function () {
    // Evento para abrir el modal
    $(document).on('show.bs.modal', '#modalCRUDcotizaciones_compras', function () {
        // Obtener el campo de fecha
        const fechaCotizacionField = document.getElementById('fecha_cotizacion');

        // Validar si el campo existe
        if (fechaCotizacionField) {
            // Obtener la fecha actual en formato YYYY-MM-DD
            const today = new Date().toISOString().split('T')[0];

            // Asignar la fecha actual al campo si está vacío
            if (!fechaCotizacionField.value) {
                fechaCotizacionField.value = today;
            }
        }
    });
});
</script>