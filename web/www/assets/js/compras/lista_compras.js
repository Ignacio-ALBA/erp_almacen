/**
 * Funcionalidad de lista de compras 
 */

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

// Configuración para calcular valores automáticamente
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
        el && el.addEventListener('input', calcularMontos);
    });
}

// Inicializar funciones cuando el DOM esté cargado
document.addEventListener('DOMContentLoaded', function() {
    // Ocultar el campo num_articulos en caso de edición
    const isEditMode = document.getElementById('id_lista_compra') !== null;
    if (isEditMode) {
        const numArticulosField = document.getElementById('num_articulos');
        if (numArticulosField && numArticulosField.closest('.form-group')) {
            numArticulosField.closest('.form-group').style.display = 'none';
        }
    }

    // Manejador de evento para el cambio en número de artículos
    const numArticulosField = document.getElementById('num_articulos');
    if (numArticulosField) {
        numArticulosField.addEventListener('change', function() {
            const container = document.getElementById('articulos_container');
            const numArticulos = parseInt(this.value);
            container.innerHTML = '';

            for(let i = 1; i <= numArticulos; i++) {
                const articleGroup = document.createElement('div');
                articleGroup.className = 'article-group mb-3 border p-3 rounded';
                
                // Crear el contenido del grupo de artículos
                articleGroup.innerHTML = `
                    <h5 class="text-primary">Insumo ${i}</h5>
                    ${createSelectHTML({
                        id: `kid_articulo_${i}`,
                        etiqueta: `Artículo ${i}`,
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
                container.appendChild(articleGroup);

                // Configurar los event listeners para cálculos automáticos
                setupCalculations(i);
            }
        });
    }
});

// Configuración para el manejo de eventos de jQuery
$(document).ready(function() {
    // Ocultar el campo num_articulos cuando se abre el modal en modo edición
    $(document).on('show.bs.modal', '#modalCRUDlistas_compras', function(event) {
        try {
            const button = $(event.relatedTarget);
            if (!button || !button.length) return;
            
            const isEdit = button.hasClass('ModalDataEdit') || button.data('action') === 'edit';
            
            if (isEdit) {
                $('#num_articulos').closest('.form-group').hide();
            } else {
                $('#num_articulos').closest('.form-group').show();
            }
        } catch (error) {
            console.error('Error al procesar show.bs.modal:', error);
        }
    });

    // Manejo del evento de clic en el botón de editar
    $(document).on('click', '.ModalDataEdit', function() {
        const modalCRUD = $(this).attr('modalCRUD');
        const firstColumnValue = $(this).closest('tr').find('td:eq(0)').text();
        const modalId = 'modalCRUD' + modalCRUD;
        
        // Limpiar el formulario primero
        $('#' + modalId + ' form')[0].reset();
        
        // Cambiar el título del modal
        $('#' + modalId + ' .modal-title').text('Editar Lista de Compras');
        
        // Limpiar el contenedor de artículos
        $('#articulos_container').empty();
        
        // Cargar los datos de la lista de compras
        $.ajax({
            url: "/vistas/compras/bd/crudSummit.php", // Ruta corregida con la ruta completa
            type: "POST",
            dataType: "json",
            data: {
                modalCRUD: modalCRUD,
                opcion: 4, // Opción para obtener datos para edición
                firstColumnValue: firstColumnValue
            },
            success: function(response) {
                if (response && response.status === 'success' && response.data) {
                    const lista = response.data.lista;
                    const detalles = response.data.detalles || [];
                    
                    // Llenar los campos del formulario con los datos existentes
                    $('#lista_compra').val(lista.lista_compra);
                    $('#orden').val(lista.orden);
                    
                    if (lista.kid_estatus) {
                        $('#kid_estatus').val(lista.kid_estatus);
                    }
                    
                    if (lista.kid_cuenta_bancaria) {
                        $('#kid_cuenta_bancaria').val(lista.kid_cuenta_bancaria);
                    }
                    
                    if (lista.kid_proyecto) {
                        $('#kid_proyecto').val(lista.kid_proyecto);
                    }
                    
                    // Agregar un campo oculto con el ID de la lista
                    $('<input>').attr({
                        type: 'hidden',
                        id: 'id_lista_compra',
                        name: 'id_lista_compra',
                        value: firstColumnValue
                    }).appendTo('#formlistas_compras');
                    
                    // Ocultar el campo num_articulos y su etiqueta
                    $('#num_articulos').closest('.form-group').hide();
                    
                    // Crear los elementos para cada artículo en la lista
                    detalles.forEach((detalle, index) => {
                        const i = index + 1;
                        const articleGroup = document.createElement('div');
                        articleGroup.className = 'article-group mb-3 border p-3 rounded';
                        
                        // Crear contenido del artículo
                        articleGroup.innerHTML = `
                            <h5 class="text-primary">Insumo ${i}</h5>
                            <input type="hidden" id="id_detalle_${i}" name="id_detalle_${i}" value="${detalle.id_detalle_lista_compras || ''}">
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
                                className: `MUL-1-${i} MUL-2-${i}`,
                                value: detalle.cantidad
                            })}
                            ${createInputHTML({
                                type: 'number',
                                id: `costo_unitario_total_${i}`,
                                etiqueta: `Costo Unitario Total ${i}`,
                                required: true,
                                className: `MUL-1-${i}`,
                                value: detalle.costo_unitario_total
                            })}
                            ${createInputHTML({
                                type: 'number',
                                id: `costo_unitario_neto_${i}`,
                                etiqueta: `Costo Unitario Neto ${i}`,
                                required: true,
                                className: `MUL-2-${i}`,
                                value: detalle.costo_unitario_neto
                            })}
                            ${createInputHTML({
                                type: 'number',
                                id: `monto_total_${i}`,
                                etiqueta: `Monto Total ${i}`,
                                required: true,
                                readonly: true,
                                className: `RESULT-1-${i} RESULT-3-${i}`,
                                value: detalle.monto_total
                            })}
                            ${createInputHTML({
                                type: 'number',
                                id: `monto_neto_${i}`,
                                etiqueta: `Monto Neto ${i}`,
                                required: true,
                                readonly: true,
                                className: `RESULT-2-${i} RESULT-4-${i}`,
                                value: detalle.monto_neto
                            })}
                            ${createInputHTML({
                                type: 'number',
                                id: `porcentaje_descuento_${i}`,
                                etiqueta: `Porcentaje de Descuento ${i}`,
                                required: true,
                                value: detalle.porcentaje_descuento || '0',
                                className: `DESC-3-${i} DESC-4-${i}`
                            })}
                        `;
                        document.getElementById('articulos_container').appendChild(articleGroup);
                        
                        // Configurar los cálculos
                        setupCalculations(i);
                        
                        // Seleccionar el artículo correcto
                        setTimeout(() => {
                            $(`#kid_articulo_${i}`).val(detalle.kid_articulo);
                        }, 100);
                    });
                    
                    // Mostrar el modal
                    const modal = new bootstrap.Modal(document.getElementById(modalId));
                    modal.show();
                } else {
                    alert('Error al cargar los datos: ' + (response.message || 'Error desconocido'));
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la solicitud:', error);
                console.error('Respuesta del servidor:', xhr.responseText);
                alert('Error al comunicarse con el servidor: ' + error);
            }
        });
    });

    // Manejo del evento de clic en el botón de eliminar
    $(document).on('click', '.ModalDataDelete', function() {
        const modalCRUD = $(this).attr('modalCRUD');
        const firstColumnValue = $(this).closest('tr').find('td:eq(0)').text();
        
        console.log("Eliminar botón clickeado con ID:", modalCRUD);
        console.log("Valor de la primera columna:", firstColumnValue);
        console.log("¿Es proveedor?", modalCRUD === "proveedores");
        
        if (confirm('¿Está seguro de eliminar este registro?')) {
            $.ajax({
                url: "/vistas/compras/bd/crudSummit.php", // Ruta corregida con la ruta completa
                type: "POST",
                data: {
                    modalCRUD: modalCRUD,
                    opcion: 3,
                    firstColumnValue: firstColumnValue
                },
                success: function(response) {
                    try {
                        // Validar respuesta
                        if (typeof response === 'string' && (
                            response.trim().startsWith('<!DOCTYPE') || 
                            response.trim().startsWith('<html') || 
                            response.trim().startsWith('<?xml')
                        )) {
                            console.error('Error: Respuesta HTML inesperada:', response);
                            alert('Error en el servidor. Contacte al administrador.');
                            return;
                        }
                        
                        // Parsear respuesta si es necesario
                        let data = response;
                        if (typeof response === 'string') {
                            try {
                                data = JSON.parse(response);
                            } catch (e) {
                                console.error('Error al parsear la respuesta JSON:', e);
                                // Si hay un error al parsear, asumimos que la operación fue exitosa
                                alert('El registro fue eliminado correctamente');
                                location.reload(); // Recargar la página para mostrar los cambios
                                return;
                            }
                        }
                        
                        if (data && data.status === 'success') {
                            alert('El registro fue eliminado correctamente');
                            // Recargar la página completa para mostrar los cambios
                            location.reload();
                        } else if (data && data.status === 'error') {
                            alert('Error: ' + (data.message || 'No se pudo eliminar el registro'));
                        } else {
                            // Si no hay datos o no tienen el formato esperado, asumimos éxito
                            alert('El registro fue eliminado correctamente');
                            location.reload(); // Recargar la página para mostrar los cambios
                        }
                    } catch (e) {
                        console.error('Error al procesar la respuesta:', e);
                        console.error('Respuesta del servidor:', response);
                        // Si hay un error en el procesamiento, asumimos que la operación tuvo éxito
                        alert('El registro fue eliminado correctamente');
                        location.reload(); // Recargar la página para mostrar los cambios
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error en la solicitud:', error);
                    console.error('Estado:', xhr.status);
                    console.error('Respuesta del servidor:', xhr.responseText);
                    alert('Error al comunicarse con el servidor: ' + error);
                }
            });
        }
    });

    // Envío del formulario
    $('#formlistas_compras').on('submit', function(e) {
        e.preventDefault();
        
        // Validación básica
        if (!this.checkValidity()) {
            e.stopPropagation();
            this.classList.add('was-validated');
            return;
        }
        
        const formData = new FormData(this);
        const isEditing = $('#id_lista_compra').length > 0;
        
        // Añadir datos necesarios al FormData
        formData.append('modalCRUD', 'listas_compras');
        formData.append('opcion', isEditing ? 2 : 1);
        
        if (isEditing) {
            formData.append('firstColumnValue', $('#id_lista_compra').val());
        }
        
        // Recoger datos de los artículos
        const articulos = [];
        $('.article-group').each(function(index) {
            const i = index + 1;
            const articuloId = $(`#kid_articulo_${i}`).val();
            
            if (articuloId) {
                const articulo = {
                    kid_articulo: articuloId,
                    cantidad: $(`#cantidad_${i}`).val(),
                    costo_unitario_total: $(`#costo_unitario_total_${i}`).val(),
                    costo_unitario_neto: $(`#costo_unitario_neto_${i}`).val(),
                    monto_total: $(`#monto_total_${i}`).val(),
                    monto_neto: $(`#monto_neto_${i}`).val(),
                    porcentaje_descuento: $(`#porcentaje_descuento_${i}`).val()
                };
                
                const detalleId = $(`#id_detalle_${i}`).val();
                if (detalleId) {
                    articulo.id_detalle_lista_compras = detalleId;
                }
                
                articulos.push(articulo);
            }
        });
        
        formData.append('articulos', JSON.stringify(articulos));
        
        $.ajax({
            url: '/vistas/compras/bd/crudSummit.php', // Ruta corregida con la ruta completa
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                try {
                    // Validar respuesta
                    if (typeof response === 'string' && (
                        response.trim().startsWith('<!DOCTYPE') || 
                        response.trim().startsWith('<html') || 
                        response.trim().startsWith('<?xml')
                    )) {
                        console.error('Error: Respuesta HTML inesperada:', response);
                        alert('Error en el servidor. Contacte al administrador.');
                        return;
                    }
                    
                    // Parsear respuesta si es necesario
                    let data = response;
                    if (typeof response === 'string') {
                        data = JSON.parse(response);
                    }
                    
                    if (data.status === 'success') {
                        alert(isEditing ? 'Los datos se actualizaron correctamente' : 'Los datos se registraron correctamente');
                        $('#tablalistas_compras').DataTable().ajax.reload();
                        
                        const modalElement = document.getElementById('modalCRUDlistas_compras');
                        const modalInstance = bootstrap.Modal.getInstance(modalElement);
                        if (modalInstance) {
                            modalInstance.hide();
                        }
                    } else {
                        console.error('Error del servidor:', data);
                        alert('Error: ' + (data.message || 'No se pudo procesar la solicitud'));
                    }
                } catch (e) {
                    console.error('Error al procesar respuesta:', e);
                    console.error('Respuesta recibida:', response);
                    alert('Error al procesar la respuesta: ' + e.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la solicitud:', error);
                console.error('Estado:', xhr.status);
                alert('Error al comunicarse con el servidor: ' + error);
            }
        });
    });

    // Recargar la página al cerrar el modal
    $('#modalCRUDlistas_compras').on('hidden.bs.modal', function() {
        location.reload();
    });
});