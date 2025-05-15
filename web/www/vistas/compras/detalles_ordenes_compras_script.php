<?php
$nonce_value = isset($nonce) ? htmlspecialchars($nonce) : '';
?>

<script nonce="<?php echo $nonce_value; ?>">
document.addEventListener('DOMContentLoaded', function() {
    // Form validation function for detalles_ordenes_compras
    function validateDetallesOrdenCompra(form) {
        const articuloSelect = form.querySelector('#kid_articulo');
        const ordenCompraSelect = form.querySelector('#kid_orden_compras');
        
        if (!articuloSelect.value) {
            alert('Por favor seleccione un artículo');
            return false;
        }
        
        if (!ordenCompraSelect.value) {
            alert('Por favor seleccione una orden de compra');
            return false;
        }
        
        return true;
    }

    // Make the validation function available globally
    window.validateDetallesOrdenCompra = validateDetallesOrdenCompra;

    // Función para manejar la apertura del modal y configurar correctamente los selects
    function handleModalOpen(event) {
        const modal = event.target;
        const actionType = modal.dataset.action; // 'edit' o 'view'
        
        if (modal.id.includes('detalles_ordenes_compras')) {
            // Solo para modales de detalles de órdenes de compras
            setTimeout(() => {
                // Verificar si tenemos los datos del endpoint
                const dataContainer = modal.querySelector('.modal-data-container');
                if (dataContainer) {
                    try {
                        const modalData = JSON.parse(dataContainer.dataset.modalData);
                        console.log("Datos del modal:", modalData);
                        
                        // Configurar selects con los valores correctos
                        if (modalData.options) {
                            // Configurar select de artículo
                            if (modalData.options.kid_articulo && modalData.options.kid_articulo.length > 0) {
                                const articuloData = modalData.options.kid_articulo[0];
                                const articuloSelect = modal.querySelector('#kid_articulo');
                                if (articuloSelect) {
                                    setSelectOptionByValue(articuloSelect, articuloData.valor);
                                }
                            }
                            
                            // Configurar select de orden de compra
                            if (modalData.options.kid_orden_compras && modalData.options.kid_orden_compras.length > 0) {
                                const ordenCompraData = modalData.options.kid_orden_compras[0];
                                const ordenCompraSelect = modal.querySelector('#kid_orden_compras');
                                if (ordenCompraSelect) {
                                    setSelectOptionByValue(ordenCompraSelect, ordenCompraData.valor);
                                }
                            }
                        }
                    } catch (error) {
                        console.error("Error al procesar datos del modal:", error);
                    }
                }
            }, 300); // Pequeño retraso para asegurar que todos los elementos están cargados
        }
    }

    // Función para establecer un valor en un select
    function setSelectOptionByValue(selectElement, value) {
        if (!selectElement || !value) return;
        
        // Intentar encontrar la opción por valor
        const option = Array.from(selectElement.options).find(opt => opt.value == value);
        if (option) {
            selectElement.value = value;
            console.log(`Opción seleccionada en ${selectElement.id}:`, option.text);
        } else {
            console.warn(`No se encontró opción con valor ${value} en ${selectElement.id}`);
        }
    }

    // Configurar cálculos para el formulario
    function setupCalculations(form) {
        const cantidad = form.querySelector('input[name="cantidad"], #cantidad');
        const costoUnitarioTotal = form.querySelector('input[name="costo_unitario_total"], #costo_unitario_total');
        const costoUnitarioNeto = form.querySelector('input[name="costo_unitario_neto"], #costo_unitario_neto');
        const montoTotal = form.querySelector('input[name="monto_total"], #monto_total');
        const montoNeto = form.querySelector('input[name="monto_neto"], #monto_neto');
        const porcentajeDescuento = form.querySelector('input[name="porcentaje_descuento"], #porcentaje_descuento');

        if (!cantidad || !costoUnitarioTotal || !costoUnitarioNeto || 
            !montoTotal || !montoNeto || !porcentajeDescuento) {
            return; // Salir si falta algún campo
        }

        // Configurar campos de solo lectura
        costoUnitarioNeto.readOnly = true;
        montoTotal.readOnly = true;
        montoNeto.readOnly = true;

        function calcularMontos() {
            try {
                const cantidadVal = parseFloat(cantidad.value) || 0;
                const costoUnitarioTotalVal = parseFloat(costoUnitarioTotal.value) || 0;
                const descuentoVal = parseFloat(porcentajeDescuento.value) || 0;

                // Cálculo de costo unitario neto (con IVA)
                const costoUnitarioNetoVal = costoUnitarioTotalVal * 1.16;
                costoUnitarioNeto.value = costoUnitarioNetoVal.toFixed(2);

                // Cálculo de monto total (sin IVA)
                const montoTotalSinDescuento = cantidadVal * costoUnitarioTotalVal;
                const montoTotalConDescuento = montoTotalSinDescuento * (1 - (descuentoVal/100));
                montoTotal.value = montoTotalConDescuento.toFixed(2);

                // Cálculo de monto neto (con IVA)
                const montoNetoVal = cantidadVal * costoUnitarioNetoVal * (1 - (descuentoVal/100));
                montoNeto.value = montoNetoVal.toFixed(2);
            } catch (error) {
                console.error("Error en cálculos:", error);
            }
        }

        // Configurar event listeners para los campos que desencadenan cálculos
        [cantidad, costoUnitarioTotal, porcentajeDescuento].forEach(campo => {
            ['input', 'change'].forEach(evento => {
                campo.addEventListener(evento, calcularMontos);
            });
        });

        // Realizar cálculo inicial
        calcularMontos();
    }
    
    // Inicializar modales existentes
    function initializeModal() {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            const forms = modal.querySelectorAll('form');
            forms.forEach(setupCalculations);
        });
    }

    // Observar cambios en el DOM para nuevos modales
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (node.nodeType === 1 && node.classList.contains('modal')) {
                    const form = node.querySelector('form');
                    if (form) setupCalculations(form);
                }
            });
        });
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });

    // Manejar la apertura de modales
    document.body.addEventListener('shown.bs.modal', function(event) {
        handleModalOpen(event);
        const form = event.target.querySelector('form');
        if (form) setupCalculations(form);
    });

    // Inicialización inicial
    initializeModal();
});
</script>