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
    
    // Find all forms that might need calculations
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        // Only setup calculations for order detail forms
        if (form.id && (form.id.includes('detalles_ordenes_compras') || 
                        form.getAttribute('action') && form.getAttribute('action').includes('detalles_ordenes_compras'))) {
            setupOrderDetailCalculations(form);
        }
    });

    // Setup calculation logic for modals that are opened dynamically
    document.addEventListener('shown.bs.modal', function(event) {
        const modal = event.target;
        if (modal.querySelector('form') && 
            (modal.id.includes('detalles_ordenes_compras') || 
             modal.querySelector('form').getAttribute('action') && 
             modal.querySelector('form').getAttribute('action').includes('detalles_ordenes_compras'))) {
            setupOrderDetailCalculations(modal.querySelector('form'));
        }
    });

    // Function to set up calculations in a form
    function setupOrderDetailCalculations(form) {
        console.log('Setting up calculations for form', form.id || 'unknown');
        
        // Find form inputs
        const cantidad = form.querySelector('input[name="cantidad"], #cantidad');
        const costoUnitarioTotal = form.querySelector('input[name="costo_unitario_total"], #costo_unitario_total');
        const costoUnitarioNeto = form.querySelector('input[name="costo_unitario_neto"], #costo_unitario_neto');
        const montoTotal = form.querySelector('input[name="monto_total"], #monto_total');
        const montoNeto = form.querySelector('input[name="monto_neto"], #monto_neto');
        const porcentajeDescuento = form.querySelector('input[name="porcentaje_descuento"], #porcentaje_descuento');

        // Verify that we found all necessary fields
        if (!cantidad || !costoUnitarioTotal || !costoUnitarioNeto || !montoTotal || !montoNeto) {
            console.warn('Missing required fields for calculations');
            return;
        }

        // Set read-only fields
        if (costoUnitarioNeto) costoUnitarioNeto.readOnly = true;
        if (montoTotal) montoTotal.readOnly = true;
        if (montoNeto) montoNeto.readOnly = true;

        // Add event listeners to input fields
        if (cantidad) {
            cantidad.addEventListener('input', calculateAmounts);
        }
        
        if (costoUnitarioTotal) {
            costoUnitarioTotal.addEventListener('input', calculateAmounts);
        }
        
        if (porcentajeDescuento) {
            porcentajeDescuento.addEventListener('input', calculateAmounts);
        }

        // Initial calculation
        calculateAmounts();

        // Function to calculate all amounts
        function calculateAmounts() {
            try {
                // Parse input values, default to 0 if empty or NaN
                const cantidadVal = parseFloat(cantidad.value.replace(/,/g, '')) || 0;
                const costoUnitarioTotalVal = parseFloat(costoUnitarioTotal.value.replace(/,/g, '')) || 0;
                const descuentoVal = porcentajeDescuento ? (parseFloat(porcentajeDescuento.value.replace(/,/g, '')) || 0) : 0;
                
                // Calculate costo unitario neto (with VAT)
                const costoUnitarioNetoVal = costoUnitarioTotalVal * 1.16;
                
                // Calculate monto total (without discount)
                const montoTotalVal = cantidadVal * costoUnitarioTotalVal;
                
                // Calculate monto neto (with discount and VAT)
                const montoConDescuento = montoTotalVal * (1 - descuentoVal / 100);
                const montoNetoVal = montoConDescuento * 1.16;
                
                // Update field values with formatting
                if (costoUnitarioNeto) costoUnitarioNeto.value = formatNumber(costoUnitarioNetoVal);
                if (montoTotal) montoTotal.value = formatNumber(montoTotalVal);
                if (montoNeto) montoNeto.value = formatNumber(montoNetoVal);
                
                console.log('Calculation complete:', {
                    cantidad: cantidadVal,
                    costoUnitarioTotal: costoUnitarioTotalVal,
                    costoUnitarioNeto: costoUnitarioNetoVal,
                    montoTotal: montoTotalVal,
                    montoNeto: montoNetoVal,
                    descuento: descuentoVal
                });
            } catch (error) {
                console.error('Error in calculation:', error);
            }
        }
        
        // Format number with 2 decimal places and thousands separator
        function formatNumber(number) {
            return number.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
    }
});
</script>