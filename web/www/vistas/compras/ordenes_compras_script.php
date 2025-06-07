<?php
$nonce_value = isset($nonce) ? htmlspecialchars($nonce) : '';
?>

<script nonce="<?php echo $nonce_value; ?>">
    // Eliminar required de proyecto al abrir modal
    $(document).on('show.bs.modal', '#modalCRUDordenes_compras', function() {
        $('#kid_proyecto').removeAttr('required');
    });

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

        // Handle Ver Detalles button click
        $(document).on('click', '.ModalNewAdd3', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            
            const modalCRUD = $(this).attr('modalCRUD');
            const rowData = $(this).closest('tr').find('td');
            const ordenId = rowData.eq(0).text().trim();
            const ordenName = rowData.eq(2).text().trim();
            
            console.log("Botón Ver Detalles clickeado");
            console.log("Valor del ID de orden:", ordenId);
            console.log("Nombre de orden:", ordenName);
            
            // Store for later use
            sessionStorage.setItem('currentOrdenId', ordenId);
            sessionStorage.setItem('currentOrdenName', ordenName);
            
            // Update modal title
            updateModalTitle(ordenName);
            
            // Show fullscreen modal
            $(`#modalCRUD${modalCRUD}-View`).modal('show');
            
            // Clear previous data and show loading
            const table = $(`#modalCRUD${modalCRUD}-View table tbody`);
            table.empty();
            table.append('<tr><td colspan="9" class="text-center"><i class="bi bi-hourglass-split"></i> Cargando detalles...</td></tr>');
            
            // Fetch details
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
                    table.empty();
                    
                    try {
                        if(response.status === "success" && response.data) {
                            if(Array.isArray(response.data) && response.data.length > 0) {
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
                                table.append('<tr><td colspan="9" class="text-center">No se encontraron detalles para esta orden de compra</td></tr>');
                            }
                        } else {
                            table.append('<tr><td colspan="9" class="text-center">No se encontraron detalles para esta orden de compra</td></tr>');
                        }
                    } catch(err) {
                        console.error("Error procesando respuesta:", err);
                        table.append('<tr><td colspan="9" class="text-center text-danger">Error al procesar los datos</td></tr>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error obteniendo detalles:", error);
                    table.empty();
                    table.append('<tr><td colspan="9" class="text-center text-danger">Error al cargar los detalles</td></tr>');
                }
            });
        });

        // Handle new detail button click
        $(document).on('click', '#modalCRUDdetalles_ordenes_compras-View .btn-primary', function(e) {
            // Get current orden name from sessionStorage
            const ordenName = sessionStorage.getItem('currentOrdenName');
            if (ordenName) {
                setTimeout(() => {
                    $('#kid_orden_compras').val(ordenName);
                }, 300);
            }
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
            const savedOrdenName = sessionStorage.getItem('currentOrdenName');
            
            console.log("Orden recuperada de sessionStorage:", savedOrdenName);
            
            if (savedOrdenName) {
                // Asignar el valor al campo después de un breve retraso para asegurar que el DOM esté listo
                setTimeout(function() {
                    $('#kid_orden_compras').val(savedOrdenName);
                    console.log("Valor establecido en kid_orden_compras:", savedOrdenName);
                    
                    // Disparar evento de cambio para notificar a otros controladores
                    const event = new Event('change', { bubbles: true });
                    document.getElementById('kid_orden_compras').dispatchEvent(event);
                }, 300);
            }
        });
        
        // También asegurar que el valor se establece cuando el modal ya está completamente visible
        $(document).on('shown.bs.modal', '#modalCRUDdetalles_ordenes_compras', function() {
            console.log("Modal para nuevo detalle completamente visible");
            
            // Recuperar los valores guardados en el sessionStorage
            const savedOrdenName = sessionStorage.getItem('currentOrdenName');
            
            console.log("Orden recuperada de sessionStorage (en shown):", savedOrdenName);
            
            if (savedOrdenName) {
                $('#kid_orden_compras').val(savedOrdenName);
                console.log("Valor establecido en kid_orden_compras (en shown):", savedOrdenName);
                
                // Disparar evento de cambio para notificar a otros controladores
                const event = new Event('change', { bubbles: true });
                document.getElementById('kid_orden_compras').dispatchEvent(event);
            }
        });

        // Capturar el valor actual de la orden cuando se abra el modal de ver detalles
        $(document).on('shown.bs.modal', '#modalCRUDdetalles_ordenes_compras-View', function(e) {
            // Almacenar el nombre de la orden actual en una variable global
            if ($('#kid_orden_compras').length > 0 && $('#kid_orden_compras').val()) {
                currentOrdenName = $('#kid_orden_compras').val();
            } else {
                // Si no está en el DOM, intentar recuperarlo del sessionStorage
                currentOrdenName = sessionStorage.getItem('currentOrdenName') || '';
            }
            
            // También almacenarlo en sessionStorage para mayor seguridad
            if (currentOrdenName) {
                sessionStorage.setItem('currentOrdenName', currentOrdenName);
            }
            
            console.log("Modal de detalles abierto, orden actual:", currentOrdenName);
        });

        // === NUEVA LÓGICA: Iniciar Pesaje (incluye proveedor) ===
        $(document).on('click', '.IniciarPesaje', function(e) {
            e.preventDefault();
            const row = $(this).closest('tr');
            const idOrdenCompra = row.find('td').eq(0).text().trim();      // ID de la orden
            const nombreOrdenCompra = row.find('td').eq(2).text().trim();  // Orden de Compra
            const proveedorOrdenCompra = row.find('td').eq(3).text().trim(); // Proveedor

            // Guarda en LocalStorage
            localStorage.setItem('selected_orden_compra_id', idOrdenCompra);
            localStorage.setItem('selected_orden_compra', nombreOrdenCompra);
            localStorage.setItem('selected_orden_compra_proveedor', proveedorOrdenCompra);

            // Redirige
            window.location.href = '/rutas/almacen_mp.php/recepciones_materia_prima';
        });
    });
</script>