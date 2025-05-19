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

        const camposFaltantes = Object.entries(camposRequeridos)
            .filter(([_, elemento]) => !elemento)
            .map(([nombre]) => nombre);

        if (camposFaltantes.length > 0) {
            return;
        }

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
            } catch (error) {
                // Silently handle errors
            }
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

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });

    document.body.addEventListener('shown.bs.modal', function(event) {
        const modal = event.target;
        const forms = modal.querySelectorAll('form');
        forms.forEach(setupCalculations);
    });

    initializeModal();

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

    // Variable para rastrear si ya se ha configurado el evento del botón
    let verDetallesConfigured = false;

    // Función que configura el evento "Ver Detalles" solo una vez
    function setupVerDetallesButton() {
        if (verDetallesConfigured) return;
        
        // Handle Ver Detalles button click - Solo configuramos este evento una vez
        $(document).on('click', '.ModalNewAdd3', function(e) {
            // Prevenir comportamientos por defecto y propagación del evento
            e.preventDefault();
            e.stopImmediatePropagation();
            
            const modalCRUD = $(this).attr('modalCRUD');
            const rowData = $(this).closest('tr').find('td');
            const cotizacionId = rowData.eq(0).text().trim();
            const cotizacionName = rowData.eq(1).text().trim();
            
            console.log(modalCRUD);
            console.log("Editar botón clickeado con ID:", modalCRUD);
            console.log("Valor de la primera columna:", cotizacionId);
            console.log("formbloque:", "compras/");
            
            // Set the cotizacion name in the hidden input
            $('#kid_cotizacion_compra').val(cotizacionName);
            
            // Show the fullscreen modal
            $(`#modalCRUD${modalCRUD}-View`).modal('show');
            
            // Clear previous data and show loading message
            const table = $(`#modalCRUD${modalCRUD}-View table tbody`);
            table.empty();
            table.append('<tr><td colspan="10" class="text-center"><i class="bi bi-hourglass-split"></i> Cargando detalles...</td></tr>');
            
            // Fetch and load all details related to this cotizacion
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
                    table.empty(); // Clear loading message
                    console.log("Response:", response); // Debug log
                    
                    try {
                        if(response.status === "success" && response.data) {
                            if(Array.isArray(response.data) && response.data.length > 0) {
                                response.data.forEach(function(row) {
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
                        } else {
                            table.append('<tr><td colspan="10" class="text-center">No se encontraron detalles para esta cotización</td></tr>');
                        }
                    } catch(err) {
                        console.error("Error procesando respuesta:", err);
                        table.append('<tr><td colspan="10" class="text-center text-danger">Error al procesar los datos: ' + err.message + '</td></tr>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching details:", error);
                    if(xhr.responseText) {
                        console.error("Response text:", xhr.responseText.substring(0, 150) + "...");
                    }
                    console.error("Status code:", xhr.status);
                    
                    // Clear loading and show error message
                    table.empty();
                    table.append('<tr><td colspan="10" class="text-center text-danger">Error al cargar los detalles. Por favor intente de nuevo.</td></tr>');
                }
            });
            
            // Evitar que el evento se procese más de una vez
            return false;
        });
        
        verDetallesConfigured = true;
    }
    
    // Configurar el evento del botón "Ver Detalles" una sola vez
    setupVerDetallesButton();

    // Variable para almacenar la cotización actual para el modal de detalles
    let currentCotizacionId = '';
    let currentCotizacionName = '';

    // Función para actualizar el título del modal con el nombre de la cotización
    function updateModalTitle(cotizacionName) {
        const modalTitle = document.querySelector('#modalCRUDdetalles_cotizaciones_compras-View .modal-title');
        if (modalTitle && cotizacionName) {
            modalTitle.innerHTML = `Detalle de Cotización: <strong>${cotizacionName}</strong>`;
        }
    }

    // Handle Ver Detalles button click
    $(document).on('click', '.ModalNewAdd3', function(e) {
        // Prevenir comportamientos por defecto y propagación del evento
        e.preventDefault();
        e.stopImmediatePropagation();
        
        const modalCRUD = $(this).attr('modalCRUD');
        const rowData = $(this).closest('tr').find('td');
        const cotizacionId = rowData.eq(0).text().trim();
        const cotizacionName = rowData.eq(1).text().trim();
        
        // Almacenar esta información para usarla más tarde
        currentCotizacionId = cotizacionId;
        currentCotizacionName = cotizacionName;
        
        console.log(modalCRUD);
        console.log("Editar botón clickeado con ID:", modalCRUD);
        console.log("Valor de la primera columna:", cotizacionId);
        console.log("Nombre de cotización:", cotizacionName);
        console.log("formbloque:", "compras/");
        
        // Set the cotizacion name in the hidden input
        $('#kid_cotizacion_compra').val(cotizacionName);
        
        // Actualizar el título del modal antes de mostrarlo
        updateModalTitle(cotizacionName);
        
        // Show the fullscreen modal
        $(`#modalCRUD${modalCRUD}-View`).modal('show');
        
        // Actualizar el título del modal directamente cuando se muestre
        $(`#modalCRUD${modalCRUD}-View`).on('shown.bs.modal', function() {
            const modalTitle = $(this).find('.modal-title');
            if (modalTitle.length) {
                modalTitle.html(`<span>Detalle de Cotización: </span><strong>${cotizacionName}</strong>`);
            }
        });
        
        // Clear previous data and show loading message
        const table = $(`#modalCRUD${modalCRUD}-View table tbody`);
        table.empty();
        table.append('<tr><td colspan="10" class="text-center"><i class="bi bi-hourglass-split"></i> Cargando detalles...</td></tr>');
        
        // Fetch and load all details related to this cotizacion
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
                table.empty(); // Clear loading message
                
                console.log("Response:", response); // Debug log
                
                try {
                    // Check if we have data in the correct format
                    if(response.status === "success" && response.data && response.data.data) {
                        console.log("tabla" + modalCRUD);
                        console.log(response.data.data.length);
                        console.log(response);
                        
                        // Verificar si data.data es un array y tiene elementos
                        if(Array.isArray(response.data.data) && response.data.data.length > 0) {
                            // Populate table with all details
                            response.data.data.forEach(function(row) {
                                let newRow = $("<tr></tr>");
                                if(Array.isArray(row)) {
                                    row.forEach(function(cell) {
                                        newRow.append($("<td></td>").text(cell));
                                    });
                                } else {
                                    // Si no es un array, mostrar mensaje de error
                                    newRow.append($("<td colspan='10'></td>").text("Formato de datos inesperado"));
                                }
                                
                                table.append(newRow);
                            });
                        } else {
                            // No details found
                            table.append('<tr><td colspan="10" class="text-center">No se encontraron detalles para esta cotización</td></tr>');
                        }
                    } else {
                        // No details found or format is unexpected
                        table.append('<tr><td colspan="10" class="text-center">No se encontraron detalles para esta cotización</td></tr>');
                    }
                } catch(err) {
                    console.error("Error processing response:", err);
                    table.append('<tr><td colspan="10" class="text-center text-danger">Error al procesar los datos: ' + err.message + '</td></tr>');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error fetching details:", error);
                if(xhr.responseText) {
                    console.error("Response text:", xhr.responseText.substring(0, 150) + "...");
                }
                console.error("Status code:", xhr.status);
                
                // Clear loading and show error message
                table.empty();
                table.append('<tr><td colspan="10" class="text-center text-danger">Error al cargar los detalles. Por favor intente de nuevo.</td></tr>');
            }
        });
        
        // Evitar que el evento se procese más de una vez
        return false;
    });

    // Manejar el clic en el botón "Nuevo Detalle de Cotización" dentro del modal de detalles
    $(document).on('click', '#modalCRUDdetalles_cotizaciones_compras-View .btn-primary', function(e) {
        console.log("Botón Nuevo Detalle de Cotización clickeado");
        console.log("Cotización actual:", currentCotizacionName);
        console.log("kid_cotizacion_compra");
        console.log(currentCotizacionId);
        
        // Capturar los valores de los atributos data-*
        const dataSelectColumn = $('#modalCRUDdetalles_cotizaciones_compras-View').data('select-column') || 1;
        const dataInputFill = $('#modalCRUDdetalles_cotizaciones_compras-View').data('input-fill') || ['kid_cotizacion_compra'];
        
        // Guardar estos valores en variables de sesión para usarlos cuando se abra el nuevo modal
        sessionStorage.setItem('currentCotizacionId', currentCotizacionId);
        sessionStorage.setItem('currentCotizacionName', currentCotizacionName);
        
        // Establecer un evento para cuando se abra el modal de nuevo detalle
        $(document).one('shown.bs.modal', '#modalCRUDdetalles_cotizaciones_compras', function() {
            console.log("Modal nuevo detalle abierto");
            // Establecer el valor del campo kid_cotizacion_compra con el nombre de la cotización actual
            if (currentCotizacionName) {
                $('#kid_cotizacion_compra').val(currentCotizacionName);
            }
        });
    });
    
    // Este evento se dispara cuando se está abriendo el modal para nuevo detalle
    $(document).on('show.bs.modal', '#modalCRUDdetalles_cotizaciones_compras', function() {
        console.log("Abriendo modal para nuevo detalle");
        // Recuperar los valores guardados en el sessionStorage
        const savedCotizacionName = sessionStorage.getItem('currentCotizacionName');
        
        if (savedCotizacionName) {
            // Asignar el valor al campo después de un breve retraso para asegurar que el DOM esté listo
            setTimeout(function() {
                $('#kid_cotizacion_compra').val(savedCotizacionName);
            }, 300);
        }
    });

    // Establecer el valor de la cotización cuando se abra el modal de nuevo detalle
    $(document).on('show.bs.modal', '#modalCRUDdetalles_cotizaciones_compras', function(e) {
        console.log("Modal de nuevo detalle abriéndose");
        
        // Comprobar si el modal fue abierto desde el botón en el modal de detalles
        const triggerElement = $(e.relatedTarget);
        if (triggerElement.closest('#modalCRUDdetalles_cotizaciones_compras-View').length) {
            console.log("Modal activado desde Vista de Detalles");
            
            // Establecer un pequeño retraso para asegurarnos de que el modal esté completamente cargado
            setTimeout(function() {
                // Establecer el valor del campo kid_cotizacion_compra con el nombre de la cotización actual
                console.log("Intentando establecer el valor: " + currentCotizacionName);
                $('#kid_cotizacion_compra').val(currentCotizacionName);
            }, 300);
        }
    });

    // Capturar el valor actual de la cotización cuando se abra el modal de ver detalles
    $(document).on('shown.bs.modal', '#modalCRUDdetalles_cotizaciones_compras-View', function(e) {
        // Almacenar el nombre de la cotización actual en una variable global
        currentCotizacionName = $('#kid_cotizacion_compra').val() || '';
        
        // También almacenarlo en sessionStorage para mayor seguridad
        if (currentCotizacionName) {
            sessionStorage.setItem('currentCotizacionName', currentCotizacionName);
        }
        
        console.log("Modal de detalles abierto, cotización actual:", currentCotizacionName);
    });

    // Modificar directamente el botón en el modal de detalles para que establezca el valor cuando se hace clic
    $(document).on('shown.bs.modal', '#modalCRUDdetalles_cotizaciones_compras-View', function() {
        // Obtener el nombre de la cotización actual
        if (currentCotizacionName) {
            console.log("Modal de detalles abierto, cotización actual:", currentCotizacionName);
            
            // Encontrar el botón "Nuevo Detalle de Cotización" dentro del modal
            const addButton = $('#modalCRUDdetalles_cotizaciones_compras-View .btn-primary');
            
            // Remover cualquier evento click previo para evitar duplicaciones
            addButton.off('click.setCotizacion');
            
            // Agregar un nuevo evento al botón
            addButton.on('click.setCotizacion', function() {
                console.log("Botón Nuevo Detalle de Cotización clickeado, estableciendo valor:", currentCotizacionName);
                
                // Uso de MutationObserver para detectar cuando el modal de nuevo detalle se abra
                const observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.addedNodes && mutation.addedNodes.length > 0) {
                            for (let i = 0; i < mutation.addedNodes.length; i++) {
                                const node = mutation.addedNodes[i];
                                // Verificar si el nodo añadido es el modal o contiene el modal
                                if (node.nodeType === 1 && 
                                    (node.id === 'modalCRUDdetalles_cotizaciones_compras' || 
                                     node.querySelector && node.querySelector('#modalCRUDdetalles_cotizaciones_compras'))) {
                                    console.log("Modal de nuevo detalle detectado en el DOM");
                                    
                                    // Encontrar el campo y establecer el valor
                                    const inputField = document.querySelector('#kid_cotizacion_compra');
                                    if (inputField) {
                                        console.log("Campo encontrado, estableciendo valor:", currentCotizacionName);
                                        inputField.value = currentCotizacionName;
                                        
                                        // Disparar evento de cambio para notificar a otros controladores
                                        const event = new Event('change', { bubbles: true });
                                        inputField.dispatchEvent(event);
                                    }
                                    
                                    // Dejar de observar una vez que hemos actualizado el campo
                                    observer.disconnect();
                                }
                            }
                        }
                    });
                });
                
                // Comenzar a observar el documento para detectar cuando se añada el modal al DOM
                observer.observe(document.body, {
                    childList: true,
                    subtree: true
                });
            });
        }
    });

    // Cuando el DOM esté listo
    // Capturar el evento de clic en el botón de ver detalles
    document.querySelectorAll(".ModalNewAdd3").forEach(function(button) {
        button.addEventListener("click", function(e) {
            // Obtener el nombre de la cotización de la fila actual
            const row = this.closest("tr");
            if (row) {
                const cells = row.querySelectorAll("td");
                if (cells.length >= 2) {
                    const cotizacionName = cells[1].textContent.trim();
                    
                    // Actualizar el título inmediatamente
                    const headerSpan = document.getElementById("cotizacion-nombre-header");
                    if (headerSpan) {
                        headerSpan.textContent = cotizacionName;
                    }
                    
                    // También actualizar cuando el modal se muestre completamente
                    $("#modalCRUDdetalles_cotizaciones_compras-View").on("shown.bs.modal", function() {
                        const headerSpan = document.getElementById("cotizacion-nombre-header");
                     
                        
                        // También actualizar directamente el título del modal
                        const modalTitle = $(this).find(".modal-title");
                        if (modalTitle.length && !modalTitle.text().includes(cotizacionName)) {
                            modalTitle.html("Detalle de Cotización: <strong>" + cotizacionName + "</strong>");
                        }
                    });
                }
            }
        });
    });
});
</script>