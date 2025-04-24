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

// Convertir los artículos PHP a opciones HTML
window.articulosOptions = `<?php 
    if(isset($articulos)) {
        foreach($articulos as $articulo) {
            echo '<option value="'.htmlspecialchars($articulo['valor']).'">'.htmlspecialchars($articulo['valor']).'</option>';
        }
    }
?>`;

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('num_articulos').addEventListener('change', function() {
        const container = document.getElementById('articulos_container');
        const numArticulos = parseInt(this.value);
        container.innerHTML = '';

        for(let i = 1; i <= numArticulos; i++) {
            const articuloGroup = document.createElement('div');
            articuloGroup.className = 'articulo-group mb-3 border p-3 rounded';
            
            // Crear el contenido del grupo de artículos
            articuloGroup.innerHTML = `
                <h5 class="text-primary">Insumo ${i}</h5>
                ${createSelectInsumoHTML({
                    id: `kid_articulo_${i}`,
                    etiqueta: `Insumo ${i}`,
                    required: true,
                    className: 'OnEditReadOnly'
                })}
                ${createInputHTML({
                    type: 'number',
                    id: `cantidad_${i}`,
                    etiqueta: `Cantidad De Supersacos ${i}`,
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
            container.appendChild(articuloGroup);
            setupCalculations(i);
        }
    });

    // Validación y envío del formulario
    const form = document.querySelector('form[id="cotizaciones_compras"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const numArticulos = parseInt(document.getElementById('num_articulos').value);
            const formData = new FormData(this);
            const formDataObj = {};

             // Convertir FormData a objeto
             for (let [key, value] of formData.entries()) {
                formDataObj[key] = value;
            }
            
            // Excluir 'num_articulos' y cualquier clave numerada relacionada con artículos
            delete formDataObj.num_articulos;
  // Agregar los datos de los artículos
  formDataObj.articulos = [];
            for (let i = 1; i <= numArticulos; i++) {
                const articulo = document.getElementById(`kid_articulo_${i}`);
                if (articulo && articulo.value) {
                    formDataObj.articulos.push({
                        kid_articulo: articulo.value,
                        cantidad: document.getElementById(`cantidad_${i}`).value,
                        costo_unitario_total: document.getElementById(`costo_unitario_total_${i}`).value,
                        costo_unitario_neto: document.getElementById(`costo_unitario_neto_${i}`).value,
                        monto_total: document.getElementById(`monto_total_${i}`).value,
                        monto_neto: document.getElementById(`monto_neto_${i}`).value,
                        porcentaje_descuento: document.getElementById(`porcentaje_descuento_${i}`).value
                    });
                }
            }
            console.log('Datos enviados:', formData);
            // Validar que haya al menos un artículo
            if (formDataObj.articulos.length === 0 && numArticulos > 0) {
                alert('Debe completar al menos un artículo con todos sus datos');
                return;
            }
            $.ajax({
    url: 'bd/crudSummit.php',
    type: 'POST',
    data: formData,
    success: function(response) {
        let jsonResponse;
        try {
            // Verificar si la respuesta parece HTML
            if (response.trim().startsWith('<')) {
                console.error('Error: Respuesta HTML inesperada:', response);
                alert('El servidor respondió con un error. Contacte al administrador.');
                return;
            }
            jsonResponse = typeof response === 'object' ? response : JSON.parse(response);
            if (jsonResponse.status === 'success') {
                // Cerrar el modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalCRUDcotizaciones_compras'));
                if (modal) modal.hide();
                alert('¡Cotiozación creada con éxito! La cotización y sus artículos han sido guardados correctamente.');
                // Recargar la tabla principal
                $('#tablacotizaciones_compras').DataTable().ajax.reload();
            } else {
                alert(jsonResponse.message || 'Error al crear la cotizacion');
            }
        } catch (e) {
            console.error('Error al parsear respuesta:', e);
            console.error('Respuesta del servidor:', response);
            alert('Error al procesar la respuesta del servidor: ' + e.message);
            return;
        }
        // Si la respuesta fue exitosa pero hay un error concurrente en AddRow, lo ignoramos y cerramos el modal
        if (jsonResponse && jsonResponse.status === 'success') {
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalCRUDcotizaciones_compras'));
            if (modal) modal.hide();
            setTimeout(function() {
                $('#tablacotizaciones_compras').DataTable().ajax.reload(null, false);
            }, 500);
        }
    },
    error: function(xhr, status, error) {
        console.error('Error en la solicitud:', error);
        console.error('Respuesta del servidor:', xhr.responseText);
        alert('Error al comunicarse con el servidor: ' + error);
    }
});
            // Enviar datos al servidor
            $.ajax({
                url: 'bd/crudSummit.php',
                type: 'POST',
                dataType: 'text',
                data: {
                    modalCRUD: 'cotizaciones_compras',
                    opcion: '1',
                    formDataJson: JSON.stringify(formDataObj)
                },
                success: function(response) {
                    let jsonResponse;
                    try {
                        jsonResponse = typeof response === 'object' ? response : JSON.parse(response);
                    } catch (e) {
                        return;
                    }
                    if (jsonResponse && jsonResponse.status === 'success') {
                        // Cerrar el modal correctamente
                        const modal = bootstrap.Modal.getInstance(document.getElementById('modalCRUDcotizaciones_compras'));
                        if (modal) modal.hide();
                        // No recargar la tabla aquí
                    } else {
                    }
                },
                error: function(xhr, status, error) {
                }
            });
        });
    }

    // Validar solo campos visibles y habilitados
    let valid = true;
    $(this).find(':input[required]').each(function() {
        if ($(this).is(':hidden') || $(this).is(':disabled') || $(this).css('display') === 'none') return;
        if (!$(this).val()) valid = false;
    });
    if (!valid) {
        alert('Por favor, complete todos los campos requeridos visibles.');
        return;
    }

function setupCalculations(index) {
    const cantidad = document.getElementById(`cantidad_${index}`);
    const costoUnitarioTotal = document.getElementById(`costo_unitario_total_${index}`);
    const costoUnitarioNeto = document.getElementById(`costo_unitario_neto_${index}`);
    const montoTotal = document.getElementById(`monto_total_${index}`);
    const montoNeto = document.getElementById(`monto_neto_${index}`);
    const porcentajeDescuento = document.getElementById(`porcentaje_descuento_${index}`);
    if (!cantidad || !costoUnitarioTotal || !costoUnitarioNeto || !montoTotal || !montoNeto || !porcentajeDescuento) {
        console.warn(`Algún input no existe para el índice ${index}`);
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

$(document).ready(function() {
    $.fn.dataTable.ext.errMode = 'none';
    $('#tablacotizaciones_compras').on('error.dt', function(e, settings, techNote, message) {
  // Evita mostrar el error en pantalla
  return false;
});
    $('#formcotizaciones_compras').on('submit', function(e) {
        e.preventDefault();
        
        // Validación básica
        if (!this.checkValidity()) {
            e.stopPropagation();
            this.classList.add('was-validated');
            return;
        }
        
        var formData = new FormData(this);
        formData.append('modalCRUD', 'cotizaciones_compras');
        formData.append('opcion', 1);
        
        // Recoger los datos de los artículos
        var articulos = [];
        $('.articulo-row').each(function(index) {
            var articuloId = $(this).find('.kid_articulo').val();
            if (articuloId) {
                var articulo = {
                    kid_articulo: articuloId,
                    cantidad: $(this).find('.cantidad').val(),
                    costo_unitario_total: $(this).find('.costo_unitario_total').val(),
                    costo_unitario_neto: $(this).find('.costo_unitario_neto').val(),
                    monto_total: $(this).find('.monto_total').val(),
                    monto_neto: $(this).find('.monto_neto').val(),
                    porcentaje_descuento: $(this).find('.porcentaje_descuento').val()
                };
                articulos.push(articulo);
            }
        });

        // Añadir los artículos al formData
        formData.append('articulos', JSON.stringify(articulos));

        $.ajax({
            url: 'bd/crudSummit.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                try {
                    // Si la respuesta no es un objeto JSON, intentar parsearla
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }
                    
                    if (response && response.status === 'success') {
                        alert('Los datos se registraron correctamente');
                        // Actualizar la tabla si existe
                        if ($.fn.DataTable.isDataTable('#tablacotizaciones_compras')) {
                            $('#tablacotizaciones_compras').DataTable().ajax.reload();
                        }
                        // Cerrar el modal usando Bootstrap 5
                        var modalElement = document.getElementById('modalCRUDcotizaciones_compras');
                        var modalInstance = bootstrap.Modal.getInstance(modalElement);
                        if (modalInstance) {
                            modalInstance.hide();
                        } else {
                            // Si no se encuentra la instancia, crear una nueva y cerrarla
                            new bootstrap.Modal(modalElement).hide();
                        }
                    } else {
                        alert('Error: ' + ((response && response.message) || 'Ocurrió un error al procesar la solicitud'));
                    }
                } catch (e) {
                    console.error('Error al procesar la respuesta:', e);
                    alert('Error: Ocurrió un error al procesar la respuesta del servidor');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la solicitud:', error);
                alert('Error: Ocurrió un error en la comunicación con el servidor');
            }
        });
    });

    // Recargar la página al cerrar el modal de nueva cotizacion
    $('#modalCRUDcotizaciones_compras').on('hidden.bs.modal', function () {
        location.reload();
    });
});
</script>