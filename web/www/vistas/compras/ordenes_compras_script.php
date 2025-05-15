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
        
        // Guardar en sessionStorage inmediatamente
        sessionStorage.setItem('currentOrdenId', ordenId);
        sessionStorage.setItem('currentOrdenName', ordenName);
        
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
        
        // Asegurarse de que tenemos el nombre de la orden actualizado
        if (!currentOrdenName) {
            currentOrdenName = sessionStorage.getItem('currentOrdenName') || '';
        }
        
        console.log("Usando nombre de orden:", currentOrdenName);
    });
    
    // Asegurar que el valor se establece cuando se abre el modal para nuevo detalle
    $(document).on('show.bs.modal', '#modalCRUDdetalles_ordenes_compras', function() {
        console.log("Abriendo modal para nuevo detalle");
        
        // Recuperar los valores guardados en el sessionStorage
        const savedOrdenId = sessionStorage.getItem('currentOrdenId');
        
        console.log("ID de orden recuperado de sessionStorage:", savedOrdenId);
        
        if (savedOrdenId) {
            // Asignar el valor al campo después de un breve retraso para asegurar que el DOM esté listo
            setTimeout(function() {
                const selectOrden = document.getElementById('kid_orden_compras');
                if (selectOrden) {
                    // Primero intentamos buscar una opción con el valor exacto
                    const option = Array.from(selectOrden.options).find(opt => opt.value === savedOrdenId);
                    if (option) {
                        selectOrden.value = savedOrdenId;
                        console.log("Valor ID establecido en kid_orden_compras:", savedOrdenId);
                    } else {
                        // Si no se encuentra, intentamos buscar por el texto visible
                        const savedOrdenName = sessionStorage.getItem('currentOrdenName');
                        const optionByText = Array.from(selectOrden.options).find(opt => opt.text === savedOrdenName);
                        if (optionByText) {
                            selectOrden.value = optionByText.value;
                            console.log("Valor establecido en kid_orden_compras por texto:", optionByText.value);
                        } else {
                            console.warn("No se encontró ninguna opción para la orden con ID", savedOrdenId, "o nombre", savedOrdenName);
                        }
                    }
                    
                    // Disparar evento de cambio para notificar a otros controladores
                    const event = new Event('change', { bubbles: true });
                    selectOrden.dispatchEvent(event);
                }
            }, 300);
        }
    });
    
    // También asegurar que el valor se establece cuando el modal ya está completamente visible
    $(document).on('shown.bs.modal', '#modalCRUDdetalles_ordenes_compras', function() {
        console.log("Modal para nuevo detalle completamente visible");
        
        // Recuperar los valores guardados en el sessionStorage
        const savedOrdenId = sessionStorage.getItem('currentOrdenId');
        
        console.log("ID de orden recuperado de sessionStorage (en shown):", savedOrdenId);
        
        if (savedOrdenId) {
            const selectOrden = document.getElementById('kid_orden_compras');
            if (selectOrden) {
                // Primero intentamos buscar una opción con el valor exacto
                const option = Array.from(selectOrden.options).find(opt => opt.value === savedOrdenId);
                if (option) {
                    selectOrden.value = savedOrdenId;
                    console.log("Valor ID establecido en kid_orden_compras (en shown):", savedOrdenId);
                } else {
                    // Si no se encuentra, intentamos buscar por el texto visible
                    const savedOrdenName = sessionStorage.getItem('currentOrdenName');
                    const optionByText = Array.from(selectOrden.options).find(opt => opt.text === savedOrdenName);
                    if (optionByText) {
                        selectOrden.value = optionByText.value;
                        console.log("Valor establecido en kid_orden_compras por texto (en shown):", optionByText.value);
                    } else {
                        console.warn("No se encontró ninguna opción para la orden con ID", savedOrdenId, "o nombre", savedOrdenName);
                    }
                }
                
                // Disparar evento de cambio para notificar a otros controladores
                const event = new Event('change', { bubbles: true });
                selectOrden.dispatchEvent(event);
            }
        }
    });

    // Capturar el valor actual de la orden cuando se abra el modal de ver detalles
    $(document).on('shown.bs.modal', '#modalCRUDdetalles_ordenes_compras-View', function(e) {
        // Almacenar el nombre de la orden actual en una variable global
        const ordenSelect = document.getElementById('kid_orden_compras');
        if (ordenSelect) {
            const selectedOption = ordenSelect.options[ordenSelect.selectedIndex];
            if (selectedOption) {
                currentOrdenId = selectedOption.value;
                currentOrdenName = selectedOption.text;
            }
        } else {
            // Si no está en el DOM, intentar recuperarlo del sessionStorage
            currentOrdenId = sessionStorage.getItem('currentOrdenId') || '';
            currentOrdenName = sessionStorage.getItem('currentOrdenName') || '';
        }
        
        // También almacenarlo en sessionStorage para mayor seguridad
        if (currentOrdenId) {
            sessionStorage.setItem('currentOrdenId', currentOrdenId);
        }
        if (currentOrdenName) {
            sessionStorage.setItem('currentOrdenName', currentOrdenName);
        }
        
        console.log("Modal de detalles abierto, orden actual ID:", currentOrdenId, "Nombre:", currentOrdenName);
    });
    
    // Detectar cuando se abre el modal de editar un detalle de orden
    $(document).on('show.bs.modal', '#modalCRUDdetalles_ordenes_compras-Edit', function(e) {
        console.log("Abriendo modal de edición");
        
        // Verificar si el sistema ya ha configurado el select
        setTimeout(function() {
            const ordenSelect = document.getElementById('kid_orden_compras');
            if (ordenSelect) {
                const selectedValue = ordenSelect.value;
                console.log("En modal de edición, valor actual del select de orden:", selectedValue);
                
                // Si no hay un valor seleccionado, verificar si hay datos de opciones disponibles
                if (!selectedValue || selectedValue === "") {
                    // Verificar si hay datos en el dataJson
                    try {
                        const dataJsonElem = document.querySelector('form input[name="dataJson"]');
                        if (dataJsonElem) {
                            const dataJson = JSON.parse(dataJsonElem.value || "{}");
                            console.log("Datos JSON disponibles:", dataJson);
                            
                            // Verificar si tenemos opciones para kid_orden_compras
                            if (dataJson.options && dataJson.options.kid_orden_compras && 
                                dataJson.options.kid_orden_compras.length > 0) {
                                
                                const ordenData = dataJson.options.kid_orden_compras[0];
                                console.log("Datos de orden encontrados:", ordenData);
                                
                                // Intentar configurar el select
                                if (ordenData.valor) {
                                    const option = Array.from(ordenSelect.options).find(opt => 
                                        opt.value === ordenData.valor || opt.text === ordenData.texto);
                                    
                                    if (option) {
                                        ordenSelect.value = option.value;
                                        console.log("Valor establecido en el select:", option.value);
                                        
                                        // Disparar evento de cambio
                                        const event = new Event('change', { bubbles: true });
                                        ordenSelect.dispatchEvent(event);
                                    }
                                }
                            }
                        }
                    } catch (error) {
                        console.error("Error procesando datos JSON:", error);
                    }
                }
            }
        }, 500);
    });
});
</script>