<?php
$nonce_value = isset($nonce) ? htmlspecialchars($nonce) : '';
?>

<script nonce="<?php echo $nonce_value; ?>">
    

document.addEventListener('DOMContentLoaded', function() {
    $('#modalCRUDdetalles_cotizaciones_compras-View').on('shown.bs.modal.setTitle', function() {
    const modalTitle = $(this).find('.modal-title').first();
    if (modalTitle.length) {
        modalTitle.html('<span>Detalle de Cotización: </span><strong>' + (window.currentCotizacionName ?? '') + '</strong>');
    }
});
    // ---------- CÁLCULOS AUTOMÁTICOS EN FORMULARIOS -----------
    function setupCalculations(form) {
        const cantidad = form.querySelector('input[name="cantidad"], #cantidad');
        const costoUnitarioTotal = form.querySelector('input[name="costo_unitario_total"], #costo_unitario_total');
        const costoUnitarioNeto = form.querySelector('input[name="costo_unitario_neto"], #costo_unitario_neto');
        const montoTotal = form.querySelector('input[name="monto_total"], #monto_total');
        const montoNeto = form.querySelector('input[name="monto_neto"], #monto_neto');
        const porcentajeDescuento = form.querySelector('input[name="porcentaje_descuento"], #porcentaje_descuento');

        const camposRequeridos = { cantidad, costoUnitarioTotal, costoUnitarioNeto, montoTotal, montoNeto, porcentajeDescuento };
        const camposFaltantes = Object.entries(camposRequeridos)
            .filter(([_, elemento]) => !elemento)
            .map(([nombre]) => nombre);

        if (camposFaltantes.length > 0) return;

        costoUnitarioNeto.readOnly = true;
        montoTotal.readOnly = true;
        montoNeto.readOnly = true;

        function calcularMontos() {
            try {
                const cantidadVal = parseFloat(cantidad.value) || 0;
                const costoUnitarioTotalVal = parseFloat(costoUnitarioTotal.value) || 0;
                const descuentoVal = parseFloat(porcentajeDescuento.value) || 0;
                const costoUnitarioNetoVal = costoUnitarioTotalVal * 1.16;
                costoUnitarioNeto.value = costoUnitarioNetoVal.toFixed(2);
                const montoTotalVal = cantidadVal * costoUnitarioTotalVal * (1 - (descuentoVal/100));
                montoTotal.value = montoTotalVal.toFixed(2);
                const montoNetoVal = cantidadVal * costoUnitarioNetoVal * (1 - (descuentoVal/100));
                montoNeto.value = montoNetoVal.toFixed(2);
            } catch (error) {}
        }

        [cantidad, costoUnitarioTotal, porcentajeDescuento].forEach(campo => {
            ['input', 'change'].forEach(evento => {
                campo.addEventListener(evento, calcularMontos);
            });
        });

        calcularMontos();
    }

    function initializeModal() {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            const forms = modal.querySelectorAll('form');
            forms.forEach(setupCalculations);
        });
    }

    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (node.nodeType === 1 && node.classList.contains('modal')) {
                    setupCalculations(node.querySelector('form'));
                }
            });
        });
    });

    observer.observe(document.body, { childList: true, subtree: true });
    document.body.addEventListener('shown.bs.modal', function(event) {
        const modal = event.target;
        const forms = modal.querySelectorAll('form');
        forms.forEach(setupCalculations);
    });
    initializeModal();

    // ---------- FECHA POR DEFAULT AL ABRIR MODAL PRINCIPAL -----------
    $(document).on('show.bs.modal', '#modalCRUDcotizaciones_compras', function () {
        const fechaCotizacionField = document.getElementById('fecha_cotizacion');
        if (fechaCotizacionField) {
            const today = new Date().toISOString().split('T')[0];
            if (!fechaCotizacionField.value) {
                fechaCotizacionField.value = today;
            }
        }
    });

    // ---------- LÓGICA DE MODAL DE DETALLES DE COTIZACIÓN -----------
    let currentCotizacionId = '';
    let currentCotizacionName = '';

    // Handler ÚNICO para actualizar el título del modal
    $('#modalCRUDdetalles_cotizaciones_compras-View').on('shown.bs.modal.setTitle', function() {
        const modalTitle = $(this).find('.modal-title');
        if (modalTitle.length) {
            modalTitle.html('<span>Detalle de Cotización: </span><strong>' + (window.currentCotizacionName ?? '') + '</strong>');
        }
    });

    // Al hacer click en "Ver Detalles", solo actualiza la variable global y abre el modal
    $(document).on('click', '.ModalNewAdd3', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        const modalCRUD = $(this).attr('modalCRUD');
        const rowData = $(this).closest('tr').find('td');
        const cotizacionId = rowData.eq(0).text().trim();
        const cotizacionName = rowData.eq(1).text().trim();

        currentCotizacionId = cotizacionId;
        currentCotizacionName = cotizacionName;
        window.currentCotizacionName = cotizacionName;

        $('#kid_cotizacion_compra').val(cotizacionName);

        $(`#modalCRUD${modalCRUD}-View`).modal('show');
        // Limpia y muestra cargando
        const table = $(`#modalCRUD${modalCRUD}-View table tbody`);
        table.empty();
        table.append('<tr><td colspan="10" class="text-center"><i class="bi bi-hourglass-split"></i> Cargando detalles...</td></tr>');

        // AJAX para detalles
        $.ajax({
            type: "POST",
            url: "../../../vistas/compras/bd/crudEndpoint.php",
            data: { 
                modalCRUD: "detalles_cotizaciones_compras", 
                firstColumnValue: cotizacionId,
                opcion: "getDetails"
            },
            dataType: "json",
            success: function(response) {
                table.empty();
                let detalles = [];
                if (response.status === "success") {
                    if (Array.isArray(response.data)) {
                        detalles = response.data;
                    } else if (response.data && Array.isArray(response.data.data)) {
                        detalles = response.data.data;
                    }
                }
                if (detalles.length > 0) {
                    detalles.forEach(function(row) {
                        let newRow = $("<tr></tr>");
                        if(Array.isArray(row)) {
                            row.forEach(function(cell) {
                                newRow.append($("<td></td>").text(cell));
                            });
                            table.append(newRow);
                        }
                    });
                } else {
                    table.append('<tr><td colspan="10" class="text-center">No se encontraron detalles para esta cotización</td></tr>');
                }
            },
            error: function(xhr, status, error) {
                table.empty();
                table.append('<tr><td colspan="10" class="text-center text-danger">Error al cargar los detalles. Por favor intente de nuevo.</td></tr>');
            }
        });
        return false;
    });

    // Botón "Nuevo Detalle de Cotización" dentro del modal de detalles
    $(document).on('click', '#modalCRUDdetalles_cotizaciones_compras-View .btn-primary', function(e) {
        sessionStorage.setItem('currentCotizacionName', currentCotizacionName);
        $(document).one('shown.bs.modal', '#modalCRUDdetalles_cotizaciones_compras', function() {
            if (currentCotizacionName) {
                $('#kid_cotizacion_compra').val(currentCotizacionName);
            }
        });
    });

    // Cuando se abre el modal de "Nuevo Detalle"
    $(document).on('show.bs.modal', '#modalCRUDdetalles_cotizaciones_compras', function() {
        const savedCotizacionName = sessionStorage.getItem('currentCotizacionName');
        if (savedCotizacionName) {
            setTimeout(function() {
                $('#kid_cotizacion_compra').val(savedCotizacionName);
            }, 300);
        }
    });

});
</script>