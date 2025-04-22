<script nonce="<?php echo $nonce; ?>">
// Función auxiliar para crear un select
function createSelectHTML(options) {
    const { id, etiqueta, required, className } = options;
    return `
        <div class="form-group">
            <label for="${id}" class="col-form-label">${etiqueta}:</label>
            <select class="form-select ${className || ''}" id="${id}" name="${id}" ${required ? 'required' : ''}>
                <option selected disabled value="">Seleccione...</option>
                ${window.articulosOptions || ''}
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
            const articleGroup = document.createElement('div');
            articleGroup.className = 'article-group mb-3 border p-3 rounded';
            
            // Crear el contenido del grupo de artículos
            articleGroup.innerHTML = `
                <h5 class="text-primary">Artículo ${i}</h5>
                ${createSelectHTML({
                    id: `kid_articulo_${i}`,
                    etiqueta: `Artículo ${i}`,
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
            container.appendChild(articleGroup);

            // Configurar los event listeners para cálculos automáticos
            setupCalculations(i);
        }
    });

    // Agregar validación al formulario
    const form = document.querySelector('form[id="listas_compras"]');
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
                Swal.fire({
                    title: 'Error',
                    text: 'Debe completar al menos un artículo con todos sus datos',
                    icon: 'error'
                });
                return;
            }
            $.ajax({
    url: 'bd/crudSummit.php',
    type: 'POST',
    data: formData,
    success: function(response) {
        try {
            // Verificar si la respuesta parece HTML
            if (response.trim().startsWith('<')) {
                console.error('Error: Respuesta HTML inesperada:', response);
                Swal.fire('Error', 'El servidor respondió con un error. Contacte al administrador.', 'error');
                return;
            }

            // Intentar parsear la respuesta como JSON
            const jsonResponse = JSON.parse(response);

            if (jsonResponse.status === 'success') {
                // Procesar la respuesta exitosa
                console.log('Respuesta exitosa:', jsonResponse);
            } else {
                Swal.fire('Error', jsonResponse.message || 'Error al procesar la solicitud', 'error');
            }
        } catch (e) {
            console.error('Error al parsear respuesta:', e);
            console.error('Respuesta del servidor:', response);
            Swal.fire('Error', 'Error al procesar la respuesta del servidor: ' + e.message, 'error');
        }
    },
    error: function(xhr, status, error) {
        console.error('Error en la solicitud:', error);
        console.error('Detalles:', xhr.responseText);
    }
});
            // Enviar datos al servidor
            $.ajax({
                url: 'bd/crudSummit.php',
                type: 'POST',
                dataType: 'text',
                data: {
                    modalCRUD: 'listas_compras',
                    opcion: '1',
                    formDataJson: JSON.stringify(formDataObj)
                },
                success: function(response) {
                    try {
                        // Verificar si la respuesta parece HTML
                        if (response.trim().startsWith('<')) {
                            console.error('Error: Respuesta HTML inesperada:', response);
                            Swal.fire('Error', 'El servidor respondió con un error. Contacte al administrador.', 'error');
                            return;
                        }
                        
                        // Intentar parsear la respuesta como JSON
                        const jsonResponse = JSON.parse(response);
                        
                        if (jsonResponse.status === 'success') {
                            // Cerrar el modal
                            const modal = bootstrap.Modal.getInstance(document.getElementById('modalCRUDlistas_compras'));
                            modal.hide();

                            // Mostrar mensaje de éxito
                            Swal.fire({
                                title: '¡Lista creada con éxito!',
                                text: 'La lista y sus artículos han sido guardados correctamente.',
                                icon: 'success',
                                showCancelButton: true,
                                confirmButtonText: 'Ver detalles',
                                cancelButtonText: 'Cerrar'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Abrir modal de detalles
                                    const detailsModal = new bootstrap.Modal(document.getElementById('detalles_listas_compras-View'));
                                    
        

                                    // Cargar los detalles
                                    $.ajax({
                                        url: 'bd/crudEndpoint.php',
                                        type: 'POST',
                                        dataType: 'text', // Cambiado a text para ver la respuesta completa
                                        data: {
                                            modalCRUD: 'detalles_listas_compras',
                                            firstColumnValue: jsonResponse.id_lista_compra,
                                            opcion: '4'
                                        },
                                        success: function(detailsResponse) {
                                            try {
                                                const detailsJsonResponse = JSON.parse(detailsResponse);
                                                if (detailsJsonResponse.status === 'success' && detailsJsonResponse.data) {
                                                    const tablaDetalles = $('#detalles_listas_compras').DataTable();
                                                    tablaDetalles.clear();
                                                    
                                                    if (detailsJsonResponse.data.length > 0) {
                                                        tablaDetalles.rows.add(detailsJsonResponse.data).draw();
                                                    }
                                                    
                                                    detailsModal.show();
                                                }
                                            } catch (e) {
                                                console.error('Error al parsear respuesta de detalles:', e);
                                                console.error('Respuesta del servidor:', detailsResponse);
                                                Swal.fire('Error', 'Error al procesar la respuesta del servidor: ' + e.message, 'error');
                                            }
                                        },
                                        error: function(xhr, status, error) {
                                            console.error('Error al cargar detalles:', error);
                                            console.error('Respuesta del servidor:', xhr.responseText);
                                            Swal.fire('Error', 'No se pudieron cargar los detalles: ' + error, 'error');
                                        }
                                    });
                                }
                            });

                            // Recargar la tabla principal
                            $('#tablalistas_compras').DataTable().ajax.reload();
                        } else {
                            Swal.fire('Error', jsonResponse.message || 'Error al crear la lista', 'error');
                        }
                    } catch (e) {
                        console.error('Error al parsear respuesta:', e);
                        console.error('Respuesta del servidor:', response);
                        Swal.fire('Error', 'Error al procesar la respuesta del servidor: ' + e.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error en la solicitud:', error);
                    console.error('Respuesta del servidor:', xhr.responseText);
                    Swal.fire('Error', 'Error al comunicarse con el servidor: ' + error, 'error');
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

    function calcularMontos() {
        if (cantidad.value && costoUnitarioTotal.value) {
            const cantidadVal = parseFloat(cantidad.value);
            const costoUnitarioTotalVal = parseFloat(costoUnitarioTotal.value);

            // Calcular monto total
            const montoTotalVal = cantidadVal * costoUnitarioTotalVal;
            montoTotal.value = montoTotalVal.toFixed(2);

            // Calcular costo unitario neto (automático, sin descuento)
            const costoUnitarioNetoVal = costoUnitarioTotalVal * 1.16;
            costoUnitarioNeto.value = costoUnitarioNetoVal.toFixed(2);

            // Calcular monto neto (automático, sin descuento)
            const montoNetoVal = montoTotalVal * 1.16;
            montoNeto.value = montoNetoVal.toFixed(2);
        }
    }

    [cantidad, costoUnitarioTotal].forEach(el => {
        el.addEventListener('input', calcularMontos);
    });
}
});

$(document).ready(function() {
    $('#formlistas_compras').on('submit', function(e) {
        e.preventDefault();
        
        // Validación básica
        if (!this.checkValidity()) {
            e.stopPropagation();
            this.classList.add('was-validated');
            return;
        }
        
        var formData = new FormData(this);
        formData.append('modalCRUD', 'listas_compras');
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
                        if ($.fn.DataTable.isDataTable('#tablalistas_compras')) {
                            $('#tablalistas_compras').DataTable().ajax.reload();
                        }
                        // Cerrar el modal usando Bootstrap 5
                        var modalElement = document.getElementById('modalCRUDlistas_compras');
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
});
</script>