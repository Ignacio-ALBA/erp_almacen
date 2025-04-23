<script nonce="<?php echo $nonce; ?>">
// Función auxiliar para crear un select de insumos
function createSelectInsumoHTML(options) {
    const { id, etiqueta, required, className } = options;
    return `
        <div class="form-group">
            <label for="${id}" class="col-form-label">${etiqueta}:</label>
            <select class="form-select ${className || ''}" id="${id}" name="${id}" ${required ? 'required' : ''}>
                <option selected disabled value="">Seleccione...</option>
                ${window.insumosOptions || ''}
            </select>
            <div id="error_${id}" class="invalid-feedback"></div>
        </div>
    `;
}

// Función auxiliar para crear un input
function createInputHTML(options) {
    const { type, id, etiqueta, required, className, readonly, value } = options;
    return `
        <div class="form-group">
            <label for="${id}" class="col-sm-8 col-form-label">${etiqueta}:</label>
            <input class="form-control ${className || ''}" 
                   id="${id}" 
                   type="${type || 'text'}"
                   ${required ? 'required' : ''}
                   ${readonly ? 'readonly' : ''}
                   ${value !== undefined ? `value="${value}"` : ''}>
            <div id="error_${id}" class="invalid-feedback"></div>
            <div id="valid_${id}" class="valid-feedback">Luce bien!</div>
            <div id="validate_${id}" class="warning-text"></div>
        </div>
    `;
}

// Convertir los insumos PHP a opciones HTML
window.insumosOptions = `<?php 
    if(isset($articulos)) {
        foreach($articulos as $insumo) {
            echo '<option value="'.htmlspecialchars($insumo['valor']).'">'.htmlspecialchars($insumo['valor']).'</option>';
        }
    }
?>`;

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('numero_insumos').addEventListener('change', function() {
        const container = document.getElementById('insumos_container');
        const numInsumos = parseInt(this.value);
        container.innerHTML = '';

        for(let i = 1; i <= numInsumos; i++) {
            const insumoGroup = document.createElement('div');
            insumoGroup.className = 'insumo-group mb-3 border p-3 rounded';
            
            // Crear el contenido del grupo de insumos
            insumoGroup.innerHTML = `
                <h5 class="text-primary">Insumo ${i}</h5>
                ${createSelectInsumoHTML({
                    id: `kid_insumo_${i}`,
                    etiqueta: `Insumo ${i}`,
                    required: true,
                    className: 'OnEditReadOnly'
                })}
                ${createInputHTML({
                    type: 'number',
                    id: `cantidad_${i}`,
                    etiqueta: `Cantidad ${i}`,
                    required: true,
                    className: `MUL-1-${i} MUL-2-${i}`
                })}
                ${createInputHTML({
                    type: 'number',
                    id: `costo_unitario_total_${i}`,
                    etiqueta: `Costo Unitario Total ${i}`,
                    required: true,
                    className: `MUL-1-${i}`
                })}
                ${createInputHTML({
                    type: 'number',
                    id: `costo_unitario_neto_${i}`,
                    etiqueta: `Costo Unitario Neto ${i}`,
                    required: true,
                    className: `MUL-2-${i}`
                })}
                ${createInputHTML({
                    type: 'number',
                    id: `monto_total_${i}`,
                    etiqueta: `Monto Total ${i}`,
                    required: true,
                    readonly: true,
                    className: `RESULT-1-${i} RESULT-3-${i}`
                })}
                ${createInputHTML({
                    type: 'number',
                    id: `monto_neto_${i}`,
                    etiqueta: `Monto Neto ${i}`,
                    required: true,
                    readonly: true,
                    className: `RESULT-2-${i} RESULT-4-${i}`
                })}
                ${createInputHTML({
                    type: 'number',
                    id: `porcentaje_descuento_${i}`,
                    etiqueta: `Porcentaje de Descuento ${i}`,
                    required: true,
                    value: '0',
                    className: `DESC-3-${i} DESC-4-${i}`
                })}
            `;
            container.appendChild(insumoGroup);
            setupCalculations(i);
        }
    });

    // Validación y envío del formulario
    const form = document.querySelector('form[id="cotizaciones_compras"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const numInsumos = parseInt(document.getElementById('numero_insumos').value);
            const formData = new FormData(this);
            const formDataObj = {};
            for (let [key, value] of formData.entries()) {
                formDataObj[key] = value;
            }
            delete formDataObj.numero_insumos;
            formDataObj.insumos = [];
            for (let i = 1; i <= numInsumos; i++) {
                const insumo = document.getElementById(`kid_insumo_${i}`);
                if (insumo && insumo.value) {
                    formDataObj.insumos.push({
                        kid_insumo: insumo.value,
                        cantidad: document.getElementById(`cantidad_${i}`).value,
                        costo_unitario_total: document.getElementById(`costo_unitario_total_${i}`).value,
                        costo_unitario_neto: document.getElementById(`costo_unitario_neto_${i}`).value,
                        monto_total: document.getElementById(`monto_total_${i}`).value,
                        monto_neto: document.getElementById(`monto_neto_${i}`).value,
                        porcentaje_descuento: document.getElementById(`porcentaje_descuento_${i}`).value
                    });
                }
            }
            if (formDataObj.insumos.length === 0 && numInsumos > 0) {
                alert('Debe completar al menos un insumo con todos sus datos');
                return;
            }
            $.ajax({
                url: 'bd/crudSummit.php',
                type: 'POST',
                data: formData,
                success: function(response) {
                    let jsonResponse;
                    try {
                        if (response.trim().startsWith('<')) {
                            alert('El servidor respondió con un error. Contacte al administrador.');
                            return;
                        }
                        jsonResponse = typeof response === 'object' ? response : JSON.parse(response);
                        if (jsonResponse.status === 'success') {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('modalCRUDcotizaciones_compras'));
                            if (modal) modal.hide();
                            alert('¡Cotización creada con éxito!');
                            $('#tablacotizaciones_compras').DataTable().ajax.reload();
                        } else {
                            alert(jsonResponse.message || 'Error al crear la cotización');
                        }
                    } catch (e) {
                        alert('Error al procesar la respuesta del servidor: ' + e.message);
                        return;
                    }
                },
                error: function(xhr, status, error) {
                    alert('Error al comunicarse con el servidor: ' + error);
                }
            });
        });
    }

    function setupCalculations(index) {
        const cantidad = document.getElementById(`cantidad_${index}`);
        const costoUnitarioTotal = document.getElementById(`costo_unitario_total_${index}`);
        const costoUnitarioNeto = document.getElementById(`costo_unitario_neto_${index}`);
        const montoTotal = document.getElementById(`monto_total_${index}`);
        const montoNeto = document.getElementById(`monto_neto_${index}`);
        const porcentajeDescuento = document.getElementById(`porcentaje_descuento_${index}`);
        if (!cantidad || !costoUnitarioTotal || !costoUnitarioNeto || !montoTotal || !montoNeto || !porcentajeDescuento) {
            return;
        }
        function calcularMontos() {
            const cantidadVal = parseFloat(cantidad.value) || 0;
            const costoUnitarioTotalVal = parseFloat(costoUnitarioTotal.value) || 0;
            const descuentoVal = parseFloat(porcentajeDescuento.value) || 0;
            // Calcular monto total
            const montoTotalVal = cantidadVal * costoUnitarioTotalVal;
            montoTotal.value = montoTotalVal.toFixed(2);
            // Calcular costo unitario neto con IVA y descuento
            let costoUnitarioNetoVal = costoUnitarioTotalVal * 1.16;
            if (descuentoVal > 0) {
                costoUnitarioNetoVal = costoUnitarioNetoVal * (1 - descuentoVal / 100);
            }
            costoUnitarioNeto.value = costoUnitarioNetoVal.toFixed(2);
            // Calcular monto neto con IVA y descuento
            let montoNetoVal = montoTotalVal * 1.16;
            if (descuentoVal > 0) {
                montoNetoVal = montoNetoVal * (1 - descuentoVal / 100);
            }
            montoNeto.value = montoNetoVal.toFixed(2);
        }
        [cantidad, costoUnitarioTotal, porcentajeDescuento].forEach(el => {
            el.addEventListener('input', calcularMontos);
        });
    }
});
</script>