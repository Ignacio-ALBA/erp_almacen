<?php
$nonce_value = isset($nonce) ? htmlspecialchars($nonce) : '';
?>

<script nonce="<?php echo $nonce_value; ?>">
document.addEventListener('DOMContentLoaded', function() {
    // Form validation function for detalles_ordenes
    function validateDetallesOrden(form) {
        const articuloSelect = form.querySelector('#kid_articulo');
        if (!articuloSelect.value) {
            alert('Por favor seleccione un artículo');
            return false;
        }
        return true;
    }

    // Make the validation function available globally
    window.validateDetallesOrden = validateDetallesOrden;

    function setupCalculations(form) {
        const cantidad = form.querySelector('input[name="cantidad"], #cantidad');
        const costoUnitarioTotal = form.querySelector('input[name="costo_unitario_total"], #costo_unitario_total');
        const costoUnitarioNeto = form.querySelector('input[name="costo_unitario_neto"], #costo_unitario_neto');
        const montoTotal = form.querySelector('input[name="monto_total"], #monto_total');
        const montoNeto = form.querySelector('input[name="monto_neto"], #monto_neto');
        const porcentajeDescuento = form.querySelector('input[name="porcentaje_descuento"], #porcentaje_descuento');
        const kidArticulo = form.querySelector('select[name="kid_articulo"], #kid_articulo');

        const camposRequeridos = {
            cantidad,
            costoUnitarioTotal,
            costoUnitarioNeto,
            montoTotal,
            montoNeto
        };

        const camposFaltantes = Object.entries(camposRequeridos)
            .filter(([_, elemento]) => !elemento)
            .map(([nombre]) => nombre);

        if (camposFaltantes.length > 0) {
            return;
        }

        // Configurar campos de solo lectura
        costoUnitarioNeto.readOnly = true;
        montoTotal.readOnly = true;
        montoNeto.readOnly = true;

        function calcularMontos() {
            try {
                const cantidadVal = parseFloat(cantidad.value) || 0;
                const costoUnitarioTotalVal = parseFloat(costoUnitarioTotal.value) || 0;
                const descuentoVal = porcentajeDescuento ? (parseFloat(porcentajeDescuento.value) || 0) : 0;

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
                // Silently handle errors
            }
        }

        // Ensure article ID is properly handled when form is submitted
        form.addEventListener('submit', function(e) {
            // Make sure kid_articulo is included in the form data
            if (kidArticulo && !kidArticulo.disabled) {
                const articuloInput = document.createElement('input');
                articuloInput.type = 'hidden';
                articuloInput.name = 'kid_articulo';
                articuloInput.value = kidArticulo.value;
                form.appendChild(articuloInput);
            }
        });

        // Configurar event listeners para los campos que desencadenan cálculos
        [cantidad, costoUnitarioTotal].forEach(campo => {
            ['input', 'change'].forEach(evento => {
                campo.addEventListener(evento, calcularMontos);
            });
        });

        if (porcentajeDescuento) {
            ['input', 'change'].forEach(evento => {
                porcentajeDescuento.addEventListener(evento, calcularMontos);
            });
        }

        // Realizar cálculo inicial
        calcularMontos();
    }
    
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
                    setupCalculations(node.querySelector('form'));
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
        const modal = event.target;
        const forms = modal.querySelectorAll('form');
        forms.forEach(setupCalculations);
    });

    // Inicialización inicial
    initializeModal();
});
</script>