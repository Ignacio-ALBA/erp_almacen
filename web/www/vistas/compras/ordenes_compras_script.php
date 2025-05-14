<?php
$nonce_value = isset($nonce) ? htmlspecialchars($nonce) : '';
?>

<script nonce="<?php echo $nonce_value; ?>">
document.addEventListener('DOMContentLoaded', function() {
    // Función para configurar cálculos automáticos en formularios
    function setupCalculations(form) {
        // Para formularios de órdenes de compra
        const cantidad = form.querySelector('input[name="cantidad"], #cantidad');
        const costoUnitarioTotal = form.querySelector('input[name="costo_unitario_total"], #costo_unitario_total');
        const costoUnitarioNeto = form.querySelector('input[name="costo_unitario_neto"], #costo_unitario_neto');
        const montoTotal = form.querySelector('input[name="monto_total"], #monto_total');
        const montoNeto = form.querySelector('input[name="monto_neto"], #monto_neto');
        const porcentajeDescuento = form.querySelector('input[name="porcentaje_descuento"], #porcentaje_descuento');

        // Para el formulario principal de órdenes de compra
        const montoTotalPrincipal = form.querySelector('#modalCRUDordenes_compras #monto_total, #monto_total');
        const montoNetoPrincipal = form.querySelector('#modalCRUDordenes_compras #monto_neto, #monto_neto');

        // Si estamos en el formulario principal de órdenes de compra
        if (montoTotalPrincipal && montoNetoPrincipal) {
            montoTotalPrincipal.addEventListener('input', function() {
                const valor = parseFloat(this.value) || 0;
                montoNetoPrincipal.value = (valor * 1.16).toFixed(2);
            });
        }

        // Si estamos en el formulario de detalles
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
                // Gestionar errores silenciosamente
            }
        }

        [cantidad, costoUnitarioTotal, porcentajeDescuento].forEach(campo => {
            ['input', 'change'].forEach(evento => {
                campo.addEventListener(evento, calcularMontos);
            });
        });

        calcularMontos();
    }

    // Inicializar cálculos en todos los modales
    function initializeModal() {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            const forms = modal.querySelectorAll('form');
            forms.forEach(setupCalculations);
        });
    }

    // Observer para detectar nuevos modales
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

    // Aplicar cálculos cuando se muestra un modal
    document.body.addEventListener('shown.bs.modal', function(event) {
        const modal = event.target;
        const forms = modal.querySelectorAll('form');
        forms.forEach(setupCalculations);
    });

    initializeModal();

    // Variables para rastrear la orden actual
    let currentOrdenId = '';
    let currentOrdenName = '';

    // Función para actualizar el título del modal con el nombre de la orden
    function updateModalTitle(ordenName) {
        const modalTitle = document.querySelector('#modalCRUDdetalles_ordenes_compras-View .modal-title');
        if (modalTitle && ordenName) {
            modalTitle.innerHTML = `Detalle de Orden de Compra: <strong>${ordenName}</strong>`;
        }
    }

    // Manejar el clic en el botón "Ver Detalles"
    $(document).on('click', '.ModalNewAdd3', function(e) {
        // Prevenir comportamientos por defecto y propagación del evento
        e.preventDefault();
        e.stopImmediatePropagation();
        
        const modalCRUD = $(this).attr('modalCRUD');
        const rowData = $(this).closest('tr').find('td');
        const ordenId = rowData.eq(0).text().trim();
        const ordenName = rowData.eq(2).text().trim();  // La columna 3 contiene el nombre de la orden
        
        // Almacenar esta información para usarla más tarde
        currentOrdenId = ordenId;
        currentOrdenName = ordenName;
        
        console.log("Botón Ver Detalles clickeado");
        console.log("Valor del ID de orden:", ordenId);
        console.log("Nombre de orden:", ordenName);
        
        // Actualizar el título del modal antes de mostrarlo
        updateModalTitle(ordenName);
        
        // Mostrar el modal de pantalla completa
        $(`#modalCRUD${modalCRUD}-View`).modal('show');
        
        // Limpiar datos previos y mostrar mensaje de carga
        const table = $(`#modalCRUD${modalCRUD}-View table tbody`);
        table.empty();
        table.append('<tr><td colspan="10" class="text-center"><i class="bi bi-hourglass-split"></i> Cargando detalles...</td></tr>');
        
        // Obtener y cargar todos los detalles relacionados con esta orden
        $.ajax({
            type: "POST",
            url: "../../../vistas/compras/bd/crudEndpoint.php",
            data: { 
                modalCRUD: "detalles_ordenes_compras", 
                firstColumnValue: ordenId,
                opcion: "getDetails"
            },
            dataType: "json",
            success: function(response) {
                table.empty(); // Limpiar mensaje de carga
                
                console.log("Response:", response);
                
                try {
                    // Verificar si tenemos datos en el formato correcto
                    if(response.status === "success" && response.data && response.data.data) {
                        console.log("Datos de detalles recibidos:", response.data.data.length);
                        
                        // Verificar si data.data es un array y tiene elementos
                        if(Array.isArray(response.data.data) && response.data.data.length > 0) {
                            // Poblar tabla con todos los detalles
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
                            // No se encontraron detalles
                            table.append('<tr><td colspan="10" class="text-center">No se encontraron detalles para esta orden de compra</td></tr>');
                        }
                    } else {
                        // No se encontraron detalles o el formato es inesperado
                        table.append('<tr><td colspan="10" class="text-center">No se encontraron detalles para esta orden de compra</td></tr>');
                    }
                } catch(err) {
                    console.error("Error procesando respuesta:", err);
                    table.append('<tr><td colspan="10" class="text-center text-danger">Error al procesar los datos: ' + err.message + '</td></tr>');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error obteniendo detalles:", error);
                console.error("Código de estado:", xhr.status);
                
                // Limpiar carga y mostrar mensaje de error
                table.empty();
                table.append('<tr><td colspan="10" class="text-center text-danger">Error al cargar los detalles. Por favor intente de nuevo.</td></tr>');
            }
        });
        
        // Evitar que el evento se procese más de una vez
        return false;
    });

    // Manejar el clic en el botón "Nuevo Detalle" dentro del modal de detalles
    $(document).on('click', '#modalCRUDdetalles_ordenes_compras-View .btn-primary', function(e) {
        console.log("Botón Nuevo Detalle clickeado");
        console.log("Orden de compra actual:", currentOrdenName);
        
        // Guardar estos valores en variables de sesión para usarlos cuando se abra el nuevo modal
        sessionStorage.setItem('currentOrdenId', currentOrdenId);
        sessionStorage.setItem('currentOrdenName', currentOrdenName);
        
        // Establecer un evento para cuando se abra el modal de nuevo detalle
        $(document).one('shown.bs.modal', '#modalCRUDdetalles_ordenes_compras', function() {
            console.log("Modal nuevo detalle abierto");
            // Establecer el valor del campo kid_orden_compra con el nombre de la orden actual
            if (currentOrdenName) {
                $('#kid_orden_compra').val(currentOrdenName);
            }
        });
    });
    
    // Este evento se dispara cuando se está abriendo el modal para nuevo detalle
    $(document).on('show.bs.modal', '#modalCRUDdetalles_ordenes_compras', function() {
        console.log("Abriendo modal para nuevo detalle");
        // Recuperar los valores guardados en el sessionStorage
        const savedOrdenName = sessionStorage.getItem('currentOrdenName');
        
        if (savedOrdenName) {
            // Asignar el valor al campo después de un breve retraso para asegurar que el DOM esté listo
            setTimeout(function() {
                $('#kid_orden_compra').val(savedOrdenName);
            }, 300);
        }
    });

    // Capturar el valor actual de la orden cuando se abra el modal de ver detalles
    $(document).on('shown.bs.modal', '#modalCRUDdetalles_ordenes_compras-View', function(e) {
        // Almacenar el nombre de la orden actual en una variable global
        currentOrdenName = $('#kid_orden_compra').val() || '';
        
        // También almacenarlo en sessionStorage para mayor seguridad
        if (currentOrdenName) {
            sessionStorage.setItem('currentOrdenName', currentOrdenName);
        }
        
        console.log("Modal de detalles abierto, orden actual:", currentOrdenName);
    });

    // Modificar directamente el botón en el modal de detalles
    $(document).on('shown.bs.modal', '#modalCRUDdetalles_ordenes_compras-View', function() {
        // Obtener el nombre de la orden actual
        if (currentOrdenName) {
            console.log("Modal de detalles abierto, orden actual:", currentOrdenName);
            
            // Encontrar el botón "Nuevo Detalle" dentro del modal
            const addButton = $('#modalCRUDdetalles_ordenes_compras-View .btn-primary');
            
            // Remover cualquier evento click previo para evitar duplicaciones
            addButton.off('click.setOrden');
            
            // Agregar un nuevo evento al botón
            addButton.on('click.setOrden', function() {
                console.log("Botón Nuevo Detalle clickeado, estableciendo valor:", currentOrdenName);
                
                // Usar MutationObserver para detectar cuando el modal de nuevo detalle se abra
                const observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.addedNodes && mutation.addedNodes.length > 0) {
                            for (let i = 0; i < mutation.addedNodes.length; i++) {
                                const node = mutation.addedNodes[i];
                                // Verificar si el nodo añadido es el modal o contiene el modal
                                if (node.nodeType === 1 && 
                                    (node.id === 'modalCRUDdetalles_ordenes_compras' || 
                                     node.querySelector && node.querySelector('#modalCRUDdetalles_ordenes_compras'))) {
                                    console.log("Modal de nuevo detalle detectado en el DOM");
                                    
                                    // Encontrar el campo y establecer el valor
                                    const inputField = document.querySelector('#kid_orden_compra');
                                    if (inputField) {
                                        console.log("Campo encontrado, estableciendo valor:", currentOrdenName);
                                        inputField.value = currentOrdenName;
                                        
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
});
</script>