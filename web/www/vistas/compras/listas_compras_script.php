<?php
$nonce_value = isset($nonce) ? htmlspecialchars($nonce) : '';
?>

<script nonce="<?php echo $nonce_value; ?>">
document.addEventListener('DOMContentLoaded', function() {
    function setupCalculations(form) {
        const cantidad = form.querySelector('input[name="cantidad"], #cantidad');
        const costoUnitarioTotal = form.querySelector('input[name="costo_unitario_total"], #costo_unitario_total');
        const costoUnitarioNeto = form.querySelector('input[name="costo_unitario_neto"], #costo_unitario_neto');
        const montoTotal = form.querySelector('input[name="monto_total"], #monto_total');
        const montoNeto = form.querySelector('input[name="monto_neto"], #monto_neto');
        const porcentajeDescuento = form.querySelector('input[name="porcentaje_descuento"], #porcentaje_descuento');

        const camposRequeridos = {
            cantidad,
            costoUnitarioTotal,
            costoUnitarioNeto,
            montoTotal,
            montoNeto,
            porcentajeDescuento
        };

        // Verificar que todos los campos necesarios existen
        const todosCamposExisten = Object.values(camposRequeridos).every(campo => campo !== null);

        if (!todosCamposExisten) return; // Si falta algún campo, no configurar los cálculos

        function updateCalculations() {
            if (cantidad && costoUnitarioTotal && montoTotal) {
                const cantidadValue = parseFloat(cantidad.value) || 0;
                const costoUnitarioTotalValue = parseFloat(costoUnitarioTotal.value) || 0;
                montoTotal.value = (cantidadValue * costoUnitarioTotalValue).toFixed(2);
            }

            if (cantidad && costoUnitarioNeto && montoNeto) {
                const cantidadValue = parseFloat(cantidad.value) || 0;
                const costoUnitarioNetoValue = parseFloat(costoUnitarioNeto.value) || 0;
                montoNeto.value = (cantidadValue * costoUnitarioNetoValue).toFixed(2);
            }

            if (costoUnitarioTotal && costoUnitarioNeto && porcentajeDescuento) {
                const porcentajeDescuentoValue = parseFloat(porcentajeDescuento.value) || 0;
                const costoUnitarioTotalValue = parseFloat(costoUnitarioTotal.value) || 0;
                costoUnitarioNeto.value = (costoUnitarioTotalValue * (1 + porcentajeDescuentoValue / 100)).toFixed(2);
            }

            if (montoTotal && montoNeto && porcentajeDescuento) {
                const porcentajeDescuentoValue = parseFloat(porcentajeDescuento.value) || 0;
                const montoTotalValue = parseFloat(montoTotal.value) || 0;
                montoNeto.value = (montoTotalValue * (1 + porcentajeDescuentoValue / 100)).toFixed(2);
            }
        }

        [cantidad, costoUnitarioTotal, costoUnitarioNeto, porcentajeDescuento].forEach(campo => {
            if (campo) {
                campo.addEventListener('input', updateCalculations);
            }
        });
    }

    function initializeModal() {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            const forms = modal.querySelectorAll('form');
            forms.forEach(setupCalculations);
        });
    }

    // Observer para detectar cuando se agrega un nuevo modal al DOM
    const observer = new MutationObserver(mutations => {
        mutations.forEach(mutation => {
            mutation.addedNodes.forEach(node => {
                if (node.nodeType === 1 && node.classList?.contains('modal')) {
                    const forms = node.querySelectorAll('form');
                    forms.forEach(setupCalculations);
                }
            });
        });
    });

    // Configurar el observer
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });

    document.body.addEventListener('shown.bs.modal', function(event) {
        const modal = event.target;
        const forms = modal.querySelectorAll('form');
        forms.forEach(setupCalculations);
    });

    // ===================== BLOQUE PARA LLENAR kid_lista_compras AUTOMÁTICAMENTE =====================

    // Variable global para la lista seleccionada
    let currentListaName = '';

    // Cuando das clic en "Ver Detalles" guarda el nombre de la lista actual
    $(document).on('click', '.ModalNewAdd3', function(e) {
        const row = $(this).closest('tr');
        currentListaName = row.find('td').eq(2).text().trim(); // Cambia el índice si tu columna nombre no es la 2
        sessionStorage.setItem('currentListaName', currentListaName);
    });

    // Cuando das clic en "Nuevo Detalle" (dentro del modal de detalles)
    $(document).on('click', '#modalCRUDdetalles_listas_compras-View .btn-primary', function(e) {
        sessionStorage.setItem('currentListaName', currentListaName);
        $(document).one('shown.bs.modal', '#modalCRUDdetalles_listas_compras', function() {
            if (currentListaName) {
                $('#kid_lista_compras').val(currentListaName);
            }
        });
    });

    // Cuando se abre el modal de "Nuevo Detalle" (seguro)
    $(document).on('show.bs.modal', '#modalCRUDdetalles_listas_compras', function() {
        const savedListaName = sessionStorage.getItem('currentListaName');
        if (savedListaName) {
            setTimeout(function() {
                $('#kid_lista_compras').val(savedListaName);
            }, 300);
        }
    });

    // ================================================================================================

    // Variable para controlar si estamos en el modal de detalles
    let isViewingDetails = false;

    // Setup click handler for Ver Detalles button (ya incluido arriba)
    $(document).on('click', '.ModalNewAdd3', function(e) {
        e.preventDefault();
        isViewingDetails = true; // Indicar que estamos viendo detalles
        const modalCRUD = $(this).attr('modalCRUD');
        const rowData = $(this).closest('tr').find('td');
        const listaId = rowData.eq(0).text().trim();
        const listaName = rowData.eq(2).text().trim();

        // Show the fullscreen modal
        $(`#modalCRUD${modalCRUD}-View`).modal('show');

        // Clear previous data and show loading message
        const table = $(`#modalCRUD${modalCRUD}-View table tbody`);
        table.empty();
        table.append('<tr><td colspan="10" class="text-center"><i class="bi bi-hourglass-split"></i> Cargando detalles...</td></tr>');

        // Fetch and load all details
        $.ajax({
            type: "POST",
            url: "../../../vistas/compras/bd/crudEndpoint.php",
            data: {
                modalCRUD: modalCRUD,
                firstColumnValue: listaId,
                opcion: "getDetails"
            },
            dataType: "json",
            success: function(response) {
                table.empty();
                console.log("Response:", response);

                try {
                    if(response.status === "success") {
                        if(response.data && Array.isArray(response.data) && response.data.length > 0) {
                            response.data.forEach(function(row) {
                                let newRow = $("<tr></tr>");
                                if(Array.isArray(row)) {
                                    row.forEach(function(cell) {
                                        newRow.append($("<td></td>").text(cell));
                                    });
                                }
                                table.append(newRow);
                            });
                        } else {
                            table.append('<tr><td colspan="10" class="text-center">No se encontraron detalles para esta lista</td></tr>');
                        }
                    } else {
                        table.append('<tr><td colspan="10" class="text-center">No se encontraron detalles para esta lista</td></tr>');
                    }
                } catch(err) {
                    console.error("Error procesando respuesta:", err);
                    table.append('<tr><td colspan="10" class="text-center text-danger">Error al procesar los datos</td></tr>');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error obteniendo detalles:", error);
                table.empty();
                table.append('<tr><td colspan="10" class="text-center text-danger">Error al cargar los detalles</td></tr>');
            }
        });
    });

    // Prevenir la llamada automática cuando se está viendo detalles
    $(document).on('show.bs.modal', '[id^=modalCRUD]', function(e) {
        if (isViewingDetails) {
            isViewingDetails = false; // Resetear el flag
            if ($(this).attr('id').includes('-View')) {
                // Si es el modal de detalles, prevenir la llamada automática
                e.stopImmediatePropagation();
            }
        }
    });

    initializeModal();
});
</script>