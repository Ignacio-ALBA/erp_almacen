<?php
$nonce_value = isset($nonce) ? htmlspecialchars($nonce) : '';
?>

<script nonce="<?php echo $nonce_value; ?>">
document.addEventListener('DOMContentLoaded', function() {
    // --- 1. Forzar envío de campos requeridos (readonly o no) ---
    function ensureRequiredFieldsAreSent(form) {
        ['kid_lista_compras','kid_articulo'].forEach(function(fieldId){
            var el = form.querySelector('[name="' + fieldId + '"], #' + fieldId);
            if (el) {
                // Si no está visible o está deshabilitado, o simplemente para forzar que siempre se envíe
                // Borra input hidden anterior si existe
                var prevHidden = form.querySelector('input[type="hidden"][name="' + fieldId + '"]');
                if (prevHidden) prevHidden.remove();
                // Si el input no está en el form (o está deshabilitado/readOnly), añade hidden
                if (el.disabled || el.readOnly || el.type === "hidden") {
                    var hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = fieldId;
                    hidden.value = el.value;
                    form.appendChild(hidden);
                }
                // Si el select/input está habilitado, pero por seguridad forzamos también el hidden (esto evita problemas con serialización AJAX)
                else if (!form.querySelector('input[type="hidden"][name="' + fieldId + '"]')) {
                    var hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = fieldId;
                    hidden.value = el.value;
                    form.appendChild(hidden);
                }
            }
        });
    }

    // Aplica a todos los formularios de modales antes de enviar
    document.body.addEventListener('submit', function(e) {
        var form = e.target;
        if (form.closest('.modal')) {
            ensureRequiredFieldsAreSent(form);
        }
    }, true);

    // --- 2. Cálculos automáticos para el formulario ---
    function setupCalculations(form) {
        const cantidad = form.querySelector('input[name="cantidad"], #cantidad');
        const costoUnitarioTotal = form.querySelector('input[name="costo_unitario_total"], #costo_unitario_total');
        const costoUnitarioNeto = form.querySelector('input[name="costo_unitario_neto"], #costo_unitario_neto');
        const montoTotal = form.querySelector('input[name="monto_total"], #monto_total');
        const montoNeto = form.querySelector('input[name="monto_neto"], #monto_neto');
        const porcentajeDescuento = form.querySelector('input[name="porcentaje_descuento"], #porcentaje_descuento');
        const total = form.querySelector('input[name="total"], #total');
        const camposRequeridos = {
            cantidad,
            costoUnitarioTotal,
            costoUnitarioNeto,
            montoTotal,
            montoNeto,
            porcentajeDescuento,
            total
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
                const descuentoVal = parseFloat(porcentajeDescuento.value) || 0;

                // MUL-1: cantidad * costo_unitario_total
                const resultMul1 = cantidadVal * costoUnitarioTotalVal;
                
                // MUL-2: cantidad * costo_unitario_neto
                const costoUnitarioNetoVal = costoUnitarioTotalVal * 1.16;
                costoUnitarioNeto.value = costoUnitarioNetoVal.toFixed(2);
                const resultMul2 = cantidadVal * costoUnitarioNetoVal;

                // RESULT-1 y RESULT-3: monto_total sin y con descuento
                // RESULT-1: monto_total sin descuento
                montoTotal.value = resultMul1.toFixed(2);

                // RESULT-2: monto_neto sin descuento
                montoNeto.value = resultMul2.toFixed(2);

                // TOTAL: monto_neto - descuento (valor directo)
                const totalFinal = resultMul2 - descuentoVal;
                total.value = totalFinal.toFixed(2);

            } catch (error) {
                // Silently handle errors
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
document.body.addEventListener('submit', function(e) {
    var form = e.target;
    if (form.closest('.modal')) {
        ensureRequiredFieldsAreSent(form);
        // DEBUG: Muestra los valores que realmente se van a enviar
        console.log('-- DEBUG FORM DATA --');
        ['kid_lista_compras','kid_articulo'].forEach(function(fieldId){
            var el = form.querySelector('[name="' + fieldId + '"]');
            if (el) {
                console.log(fieldId, el.value);
            } else {
                console.error('NO SE ENCUENTRA el campo:', fieldId);
            }
        });
    }
}, true);
    // Inicialización inicial
    initializeModal();
});
</script>