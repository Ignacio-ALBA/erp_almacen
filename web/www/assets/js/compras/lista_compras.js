/**
 * Funcionalidad de lista de compras 
 */

// Función auxiliar para crear un select
function createSelectHTML(options) {
    const { id, etiqueta, required, className, selectedValue } = options;
    let optionsHTML = '';
    if (window.articulosOptions) {
        // Si window.articulosOptions es un string de opciones, usarlo directamente
        optionsHTML = window.articulosOptions;
    }
    return `
        <div class="form-group">
            <label for="${id}" class="col-form-label">${etiqueta}:</label>
            <select class="form-select ${className || ''}" id="${id}" name="${id}" ${required ? 'required' : ''}>
                <option disabled value="">Seleccione...</option>
                ${optionsHTML}
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

    // Refuerzo: ocultar num_articulos en cualquier caso de edición
    function hideNumArticulosField() {
        const numArticulosField = document.getElementById('num_articulos');
        if (numArticulosField && numArticulosField.closest('.form-group')) {
            numArticulosField.closest('.form-group').style.display = 'none';
        }
        $('#num_articulos').closest('.form-group').hide(); // jQuery por si acaso
    }

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
        
        // Cargar los datos de la lista de compras y sus artículos
        $.ajax({
            url: "/vistas/compras/bd/crudEndpoint.php",
            type: "POST",
            dataType: "json",
            data: {
                modalCRUD: 'lista_compras_detalles',
                firstColumnValue: firstColumnValue
            },
            success: function(response) {
                if(response.status === 'success') {
                    const lista = response.lista;
                    const articulos = response.articulos;
                    
                    // Llenar los campos de la lista
                    $('#lista_compra').val(lista.lista_compra);
                    $('#orden').val(lista.orden);
                    if(lista.kid_estatus) $('#kid_estatus').val(lista.kid_estatus);
                    if(lista.kid_cuenta_bancaria) $('#kid_cuenta_bancaria').val(lista.kid_cuenta_bancaria);
                    if(lista.kid_proyecto) $('#kid_proyecto').val(lista.kid_proyecto);
                    
                    // Set number of articles based on details
                    $('#num_articulos').val(articulos.length);
                    $('#num_articulos').trigger('change');

                    // Wait for article inputs to be created
                    setTimeout(() => {
                        articulos.forEach((articulo, index) => {
                            const i = index + 1;
                            $(`#kid_articulo_${i}`).val(articulo.kid_articulo);
                            $(`#cantidad_${i}`).val(articulo.cantidad);
                            $(`#costo_unitario_total_${i}`).val(articulo.costo_unitario_total);
                            $(`#costo_unitario_neto_${i}`).val(articulo.costo_unitario_neto);
                            $(`#monto_total_${i}`).val(articulo.monto_total);
                            $(`#monto_neto_${i}`).val(articulo.monto_neto);
                            $(`#porcentaje_descuento_${i}`).val(articulo.porcentaje_descuento);
                            
                            // Store the detail ID for updating - CORREGIDO: Nombre consistente con columna en DB
                            $(`<input type="hidden" id="id_detalle_${i}" name="id_detalle_${i}" value="${articulo.id_detalle_lista_compras}">`).appendTo('#formlistas_compras');
                        });
                    }, 100);

                    // Store the lista_compra ID
                    $('<input>').attr({
                        type: 'hidden',
                        id: 'id_lista_compra',
                        name: 'id_lista_compra',
                        value: firstColumnValue
                    }).appendTo('#formlistas_compras');
                    
                    $('#modalCRUDlistas_compras').modal('show');
                } else {
                    console.error('Error loading data:', response.message || 'Unknown error');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
                console.error('Server response:', xhr.responseText);
            }
        });
    });

    // Refuerzo: ocultar campo al abrir el modal en modo edición (por si el evento show.bs.modal no se dispara)
    $(document).on('shown.bs.modal', '#modalCRUDlistas_compras', function(event) {
        const isEdit = $(this).find('.modal-title').text().toLowerCase().includes('editar');
        if (isEdit) {
            hideNumArticulosField();
        }
    });

    // Manejo del evento de clic en el botón de eliminar
    // Usar off().on() para evitar listeners duplicados
    // $(document).off('click', '.ModalDataDelete').on('click', '.ModalDataDelete', function(e) {
    //     ... código de eliminación personalizado ...
    // });

    // Envío del formulario
    $('#formlistas_compras').on('submit', function(e) {
        e.preventDefault();
        
        // Validación básica
        if (!this.checkValidity()) {
            e.stopPropagation();
            this.classList.add('was-validated');
            return;
        }
        
        // Crear un objeto formData para enviar los datos
        const formData = new FormData();
        const isEditing = $('#id_lista_compra').length > 0;
        
        // Añadir datos necesarios al FormData
        formData.append('modalCRUD', 'listas_compras');
        formData.append('opcion', isEditing ? 2 : 1);
        
        if (isEditing) {
            formData.append('firstColumnValue', $('#id_lista_compra').val());
        }
        
        // Recopilar los datos básicos de la lista de compras
        const listaCompraData = {
            lista_compra: $('#lista_compra').val(),
            orden: $('#orden').val(),
            kid_estatus: $('#kid_estatus').val() || 1,
            kid_cuenta_bancaria: $('#kid_cuenta_bancaria').val(),
            kid_proyecto: $('#kid_proyecto').val()
        };
        formData.append('formDataJson', JSON.stringify(listaCompraData));
        
        // Recopilar los datos de los artículos
        const articulos = [];
        $('.article-group').each(function(index) {
            const i = index + 1;
            const articuloId = $(`#kid_articulo_${i}`).val();
            
            if (articuloId) {
                const detalle = {
                    kid_articulo: articuloId,
                    cantidad: $(`#cantidad_${i}`).val(),
                    costo_unitario_total: $(`#costo_unitario_total_${i}`).val(),
                    costo_unitario_neto: $(`#costo_unitario_neto_${i}`).val(),
                    monto_total: $(`#monto_total_${i}`).val(),
                    monto_neto: $(`#monto_neto_${i}`).val(),
                    porcentaje_descuento: $(`#porcentaje_descuento_${i}`).val() || 0
                };
                
                // Si estamos editando y existe un id_detalle, añadirlo
                const detalleId = $(`#id_detalle_${i}`).val();
                if (detalleId) {
                    detalle.id_detalle_lista_compras = detalleId;
                }
                
                articulos.push(detalle);
            }
        });
        
        // Añadir los artículos como JSON
        formData.append('articulos', JSON.stringify(articulos));
        formData.append('AlertDataSimilar', 'true'); // Use true to allow similar items
        
        // Mostrar datos enviados por AJAX para depuración
        console.log('[AJAX submit] Enviando datos de lista de compras:', listaCompraData);
        console.log('[AJAX submit] Enviando artículos:', articulos);
        
        // Enviar los datos mediante AJAX
        $.ajax({
            url: '/vistas/compras/bd/crudSummit.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                try {
                    // Validar respuesta
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }
                    
                    if (response.status === 'success') {
                        alert(isEditing ? 'Los datos se actualizaron correctamente' : 'Los datos se registraron correctamente');
                        $('#tablalistas_compras').DataTable().ajax.reload();
                        const modalElement = document.getElementById('modalCRUDlistas_compras');
                        const modalInstance = bootstrap.Modal.getInstance(modalElement);
                        if (modalInstance) {
                            modalInstance.hide();
                        }
                    } else if (response.status === 'error') {
                        console.error('Error del servidor:', response);
                        alert('Error: ' + (response.message || 'No se pudo procesar la solicitud'));
                    }
                } catch (e) {
                    console.error('Error al procesar respuesta:', e);
                    alert('Error al procesar la respuesta: ' + e.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la solicitud:', error);
                console.error('Estado:', xhr.status);
                console.error('Respuesta:', xhr.responseText);
                alert('Error al comunicarse con el servidor: ' + error);
            }
        });
    });
    // Recargar la página al cerrar el modal
    $('#modalCRUDlistas_compras').on('hidden.bs.modal', function() {
        location.reload();
    });
    // Integración para abrir el modal de Ver y mostrar artículos en solo lectura
    $(document).on('click', '.ModalDataView', function() {
        // Obtén el id_lista_compra desde la fila de la tabla (ajusta el selector si es necesario)
        const id_lista_compra = $(this).closest('tr').find('td').eq(0).text().trim();
        cargarListaYArticulos(id_lista_compra, 'ver');
    });
    // Integración para abrir el modal de Editar y mostrar artículos en modo editable
    $(document).on('click', '.ModalDataEdit', function() {
        // Obtén el id_lista_compra desde la fila de la tabla (ajusta el selector si es necesario)
        const id_lista_compra = $(this).closest('tr').find('td').eq(0).text().trim();
        cargarListaYArticulos(id_lista_compra, 'editar');
    });
});
// =====================
// =====================
// Cargar lista y artículos en modal (Ver o Editar) - Inputs dinámicos
// =====================
function cargarListaYArticulos(id_lista_compra, modo) {
    // First, clear any existing hidden fields to avoid duplicates
    $('input[id^="id_detalle_"]').remove();
    
    $.post('/vistas/compras/bd/crudEndpoint.php', {
        modalCRUD: 'lista_compras_detalles',
        firstColumnValue: id_lista_compra
    }, function(response) {
        if(response.status === 'success') {
            // Llena los campos de la lista
            $('#lista_compra').val(response.lista.lista_compra);
            $('#orden').val(response.lista.orden);
            if(response.lista.kid_estatus) $('#kid_estatus').val(response.lista.kid_estatus);
            if(response.lista.kid_cuenta_bancaria) $('#kid_cuenta_bancaria').val(response.lista.kid_cuenta_bancaria);
            if(response.lista.kid_proyecto) $('#kid_proyecto').val(response.lista.kid_proyecto);

            // Store the lista_compra ID in a hidden field that we'll use for the form submission
            $('#id_lista_compra').remove(); // Remove if exists
            $('<input>').attr({
                type: 'hidden',
                id: 'id_lista_compra',
                name: 'id_lista_compra',
                value: id_lista_compra
            }).appendTo('#formlistas_compras');
            
            // Preparar las opciones para los selects de artículos
            let opcionesArticulos = '';
            if (response.opciones_articulos && response.opciones_articulos.length > 0) {
                opcionesArticulos = response.opciones_articulos.map(art => 
                    `<option value="${art.id}">${art.text}</option>`
                ).join('');
            } else if (window.articulosOptions) {
                opcionesArticulos = window.articulosOptions;
            }
            
            // Renderizar los artículos como inputs dinámicos
            let container = document.getElementById('articulos_container');
            container.innerHTML = '';
            response.articulos.forEach(function(art, idx) {
                const i = idx + 1;
                const articleGroup = document.createElement('div');
                articleGroup.className = 'article-group mb-3 border p-3 rounded';
                
                // Crear el HTML del grupo de artículos - store ID as data attribute instead of hidden input
                articleGroup.innerHTML = `
                    <h5 class="text-primary">Insumo ${i}</h5>
                    <div class="form-group">
                        <label for="kid_articulo_${i}" class="col-form-label">Insumo ${i}:</label>
                        <select class="form-select OnEditReadOnly" id="kid_articulo_${i}" name="kid_articulo_${i}" data-id-detalle="${art.id_detalle_lista_compras || ''}" required>
                            <option disabled value="">Seleccione...</option>
                            ${opcionesArticulos}
                        </select>
                        <div id="error_kid_articulo_${i}" class="invalid-feedback"></div>
                    </div>
                    ${createInputHTML({
                        type: 'number',
                        id: `cantidad_${i}`,
                        etiqueta: `Cantidad ${i}`,
                        required: true,
                        className: `MUL-1-${i} MUL-2-${i}`,
                        value: art.cantidad
                    })}
                    ${createInputHTML({
                        type: 'number',
                        id: `costo_unitario_total_${i}`,
                        etiqueta: `Costo Unitario Total ${i}`,
                        required: true,
                        className: `MUL-1-${i}`,
                        value: art.costo_unitario_total
                    })}
                    ${createInputHTML({
                        type: 'number',
                        id: `costo_unitario_neto_${i}`,
                        etiqueta: `Costo Unitario Neto ${i}`,
                        required: true,
                        className: `MUL-2-${i}`,
                        value: art.costo_unitario_neto
                    })}
                    ${createInputHTML({
                        type: 'number',
                        id: `monto_total_${i}`,
                        etiqueta: `Monto Total ${i}`,
                        required: true,
                        readonly: true,
                        className: `RESULT-1-${i} RESULT-3-${i}`,
                        value: art.monto_total
                    })}
                    ${createInputHTML({
                        type: 'number',
                        id: `monto_neto_${i}`,
                        etiqueta: `Monto Neto ${i}`,
                        required: true,
                        readonly: true,
                        className: `RESULT-2-${i} RESULT-4-${i}`,
                        value: art.monto_neto
                    })}
                    ${createInputHTML({
                        type: 'number',
                        id: `porcentaje_descuento_${i}`,
                        etiqueta: `Porcentaje de Descuento ${i}`,
                        required: true,
                        value: art.porcentaje_descuento || '0',
                        className: `DESC-3-${i} DESC-4-${i}`
                    })}
                `;
                container.appendChild(articleGroup);
                setupCalculations(i);
                
                // Establecer el valor seleccionado después de agregar las opciones
                setTimeout(() => {
                    $(`#kid_articulo_${i}`).val(art.kid_articulo);
                }, 100);
            });

            // Solo lectura si es modo ver
            if(modo === 'ver') {
                $('#modalCRUDlistas_compras input, #modalCRUDlistas_compras select').prop('disabled', true);
            } else {
                $('#modalCRUDlistas_compras input, #modalCRUDlistas_compras select').prop('disabled', false);
            }

            // Mostrar el modal
            const modal = new bootstrap.Modal(document.getElementById('modalCRUDlistas_compras'));
            modal.show();
        } else {
            alert('No se encontraron datos');
        }
    }, 'json');
}

// =====================
// =====================
// Obtener lista de artículos para selects dinámicos
// =====================
// Improve article loading by adding a debugging step
function fetchArticulosOptions(callback) {
    $.ajax({
        url: '/vistas/compras/bd/crudEndpoint.php',
        type: 'POST',
        dataType: 'json',
        data: { modalCRUD: 'GETArticulosProyecto', firstColumnValue: '' },
        success: function(response) {
            console.log('Articles response:', response); // Add debugging
            if (response.status === 'success' && Array.isArray(response.data)) {
                // Construir las opciones del select usando id_articulo como valor y articulo (nombre) como texto
                let options = response.data.map(art => 
                    `<option value="${art.id_articulo}">${art.articulo}</option>`
                ).join('');
                window.articulosOptions = options;
                if (typeof callback === 'function') callback();
            } else {
                console.error('Error loading articles:', response);
                // Provide a fallback to fetch articles from an alternative endpoint
                $.ajax({
                    url: '/vistas/compras/bd/getArticulos.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function(fallbackResponse) {
                        if (Array.isArray(fallbackResponse)) {
                            let options = fallbackResponse.map(art => 
                                `<option value="${art.id_articulo}">${art.articulo}</option>`
                            ).join('');
                            window.articulosOptions = options;
                        } else {
                            window.articulosOptions = '';
                        }
                        if (typeof callback === 'function') callback();
                    },
                    error: function() {
                        window.articulosOptions = '';
                        if (typeof callback === 'function') callback();
                    }
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX error:', error);
            console.error('Server response:', xhr.responseText);
            window.articulosOptions = '';
            if (typeof callback === 'function') callback();
        }
    });
}
// =====================
// =====================
// Sobrescribir ModalDataEdit para poblar selects de artículos
// =====================
$(document).off('click', '.ModalDataEdit').on('click', '.ModalDataEdit', function() {
    // First, clear any existing hidden fields for article details
    $('input[id^="id_detalle_"]').remove();
    
    const modalCRUD = $(this).attr('modalCRUD');
    const firstColumnValue = $(this).closest('tr').find('td:eq(0)').text();
    const modalId = 'modalCRUD' + modalCRUD;
    
    // Limpiar el formulario primero
    $('#' + modalId + ' form')[0].reset();
    
    // Cambiar el título del modal
    $('#' + modalId + ' .modal-title').text('Editar Lista de Compras');
    
    // Limpiar el contenedor de artículos
    $('#articulos_container').empty();
    
    // Ocultar el campo num_articulos y su etiqueta
    $('#num_articulos').closest('.form-group').hide();
    
    // Obtener opciones de artículos y luego cargar los detalles
    fetchArticulosOptions(function() {
        // Cargar los datos de la lista de compras
        const ajaxData = {
            modalCRUD: modalCRUD,
            opcion: 4, // Opción para obtener datos para edición
            firstColumnValue: firstColumnValue
        };
        $.ajax({
            url: "/vistas/compras/bd/crudSummit.php",
            type: "POST",
            dataType: "json",
            data: ajaxData,
            success: function(response) {
                if (response && response.status === 'success' && response.data) {
                    const lista = response.data.lista;
                    const detalles = response.data.detalles || [];
                    $('#lista_compra').val(lista.lista_compra);
                    $('#orden').val(lista.orden);
                    if (lista.kid_estatus) $('#kid_estatus').val(lista.kid_estatus);
                    if (lista.kid_cuenta_bancaria) $('#kid_cuenta_bancaria').val(lista.kid_cuenta_bancaria);
                    if (lista.kid_proyecto) $('#kid_proyecto').val(lista.kid_proyecto);
                    $('<input>').attr({
                        type: 'hidden',
                        id: 'id_lista_compra',
                        name: 'id_lista_compra',
                        value: firstColumnValue
                    }).appendTo('#formlistas_compras');
                    // Crear grupos de artículos para cada detalle
                    detalles.forEach((detalle, index) => {
                        const i = index + 1;
                        const articleGroup = document.createElement('div');
                        articleGroup.className = 'article-group mb-3 border p-3 rounded';
                        articleGroup.innerHTML = `
                            <h5 class="text-primary">Insumo ${i}</h5>
                            <input type="hidden" id="id_detalle_${i}" name="id_detalle_${i}" value="${detalle.id_detalle_lista_compras || ''}">
                            <div class="form-group">
                                <label for="kid_articulo_${i}" class="col-form-label">Artículo ${i}:</label>
                                <select class="form-select OnEditReadOnly" id="kid_articulo_${i}" name="kid_articulo_${i}" required>
                                    <option disabled value="">Seleccione...</option>
                                    ${window.articulosOptions}
                                </select>
                                <div id="error_kid_articulo_${i}" class="invalid-feedback"></div>
                            </div>
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
                        setupCalculations(i);
                    });
                    // Establecer valores de selects después de que el DOM esté listo
                    setTimeout(() => {
                        detalles.forEach((detalle, index) => {
                            const i = index + 1;
                            const selectElement = document.getElementById(`kid_articulo_${i}`);
                            if (selectElement) {
                                // Verificar si el valor existe en el select
                                if ([...selectElement.options].some(option => option.value === detalle.kid_articulo)) {
                                    selectElement.value = detalle.kid_articulo;
                                } else {
                                    // Añadir la opción si no existe
                                    const option = document.createElement('option');
                                    option.value = detalle.kid_articulo;
                                    option.text = `Artículo ID: ${detalle.kid_articulo}`;
                                    selectElement.appendChild(option);
                                    selectElement.value = detalle.kid_articulo;
                                }
                            }
                        });
                    }, 300); // Aumentar tiempo para asegurar que el DOM esté listo
                    // Mostrar el modal
                    const modal = new bootstrap.Modal(document.getElementById(modalId));
                    modal.show();
                } else if (response && response.status === 'error') {
                    console.error('Error al cargar los datos:', response.message || 'Error desconocido');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la solicitud:', error);
                console.error('Respuesta del servidor:', xhr.responseText);
            }
        });
    });
    function hideNumArticulosField() {
        const numArticulosField = document.getElementById('num_articulos');
        if (numArticulosField && numArticulosField.closest('.form-group')) {
            numArticulosField.closest('.form-group').style.display = 'none';
        }
        $('#num_articulos').closest('.form-group').hide();
    }
    hideNumArticulosField();
});
$(document).on('click', '.ModalDataEdit, .ModalDataView', function() {
    const isView = $(this).hasClass('ModalDataView');
    const modalCRUD = $(this).attr('modalCRUD');
    const firstColumnValue = $(this).closest('tr').find('td:eq(0)').text().trim();
    $.ajax({
        url: "/vistas/compras/bd/crudEndpoint.php",
        type: "POST",
        dataType: "json",
        data: {
            modalCRUD: 'lista_compras_detalles',
            firstColumnValue: firstColumnValue
        },
        success: function(response) {
            if(response.status === 'success') {
                const lista = response.lista;
                const articulos = response.articulos;
                const todosArticulos = response.todos_articulos;
                // Mapeo entre ID de artículo y su nombre para búsqueda rápida
                const articuloMap = {};
                if (todosArticulos && todosArticulos.length > 0) {
                    todosArticulos.forEach(art => {
                        articuloMap[art.id_articulo] = art.articulo;
                    });
                    window.articulosOptions = todosArticulos.map(art => 
                        `<option value="${art.id_articulo}">${art.articulo}</option>`
                    ).join('');
                }
                // Llenar campos básicos
                $('#lista_compra').val(lista.lista_compra);
                $('#orden').val(lista.orden);
                if(lista.kid_estatus) $('#kid_estatus').val(lista.kid_estatus);
                if(lista.kid_cuenta_bancaria) $('#kid_cuenta_bancaria').val(lista.kid_cuenta_bancaria);
                if(lista.kid_proyecto) $('#kid_proyecto').val(lista.kid_proyecto);
                // Establecer número de artículos
                $('#num_articulos').val(articulos.length);
                $('#num_articulos').trigger('change');
                // Esperar a que se creen los inputs y establecer valores
                setTimeout(() => {
                    articulos.forEach((articulo, index) => {
                        const i = index + 1;
                        // Asegurarse de que el select se actualice con todas las opciones
                        const selectElement = document.getElementById(`kid_articulo_${i}`);
                        if (selectElement) {
                            // Vaciar el select y agregar las opciones
                            selectElement.innerHTML = '<option disabled value="">Seleccione...</option>' + window.articulosOptions;
                            // Si el artículo seleccionado no está en la lista, verificar si tenemos su nombre
                            if (articulo.kid_articulo && !selectElement.querySelector(`option[value="${articulo.kid_articulo}"]`)) {
                                const articuloText = articulo.nombre_articulo || (articuloMap[articulo.kid_articulo] || `Artículo ${articulo.kid_articulo}`);
                                const option = document.createElement('option');
                                option.value = articulo.kid_articulo;
                                option.text = articuloText;
                                selectElement.appendChild(option);
                                selectElement.value = articulo.kid_articulo;
                            }
                            // Establecer el valor correcto
                            selectElement.value = articulo.kid_articulo;
                        }
                        // Establecer los demás valores
                        $(`#cantidad_${i}`).val(articulo.cantidad);
                        $(`#costo_unitario_total_${i}`).val(articulo.costo_unitario_total);
                        $(`#costo_unitario_neto_${i}`).val(articulo.costo_unitario_neto);
                        $(`#monto_total_${i}`).val(articulo.monto_total);
                        $(`#monto_neto_${i}`).val(articulo.monto_neto);
                        $(`#porcentaje_descuento_${i}`).val(articulo.porcentaje_descuento || '0');
                        // Añadir el ID del detalle si existe
                        if(articulo.id_detalle_lista_compras) {
                            $(`<input type="hidden" id="id_detalle_${i}" name="id_detalle_${i}" value="${articulo.id_detalle_lista_compras}">`).appendTo('#formlistas_compras');
                        }
                    });
                }, 300); // Aumentamos el tiempo para asegurar que los elementos se hayan creado
                // Solo lectura si es modo ver
                if(isView) {
                    $('#modalCRUDlistas_compras input, #modalCRUDlistas_compras select').prop('disabled', true);
                }
                // Mostrar el modal
                const modal = new bootstrap.Modal(document.getElementById('modalCRUDlistas_compras'));
                modal.show();
            } else {
                console.error('Error loading data:', response.message || 'Unknown error');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX error:', error);
            console.error('Server response:', xhr.responseText);
        }
    });
});
// =====================
// =====================
// Buscar artículo por ID para obtener su nombre
// =====================
function getArticuloNameById(articuloId, callback) {
    $.ajax({
        url: '/vistas/compras/bd/crudEndpoint.php',
        type: 'POST',
        dataType: 'json',
        data: { 
            modalCRUD: 'getArticuloById', 
            firstColumnValue: articuloId  
        },
        success: function(response) {
            if (response.status === 'success' && response.data) {
                callback(response.data.articulo);
            } else {
                console.warn('No se pudo encontrar el nombre del artículo con ID:', articuloId);
                callback(`Artículo no encontrado (${articuloId})`);
            }
        },
        error: function() {
            console.error('Error al buscar el artículo con ID:', articuloId);
            callback(`Artículo no encontrado (${articuloId})`);
        }
    });
}
// =====================
// =====================
// Fix form submission to prevent field name conflicts with database columns
// =====================
$('#formlistas_compras').on('submit', function(e) {
    e.preventDefault();
    
    // Validación básica
    if (!this.checkValidity()) {
        e.stopPropagation();
        this.classList.add('was-validated');
        return;
    }
    
    // Crear un objeto formData para enviar los datos
    const formData = new FormData();
    const isEditing = $('#id_lista_compra').length > 0;
    
    // Añadir datos necesarios al FormData
    formData.append('modalCRUD', 'listas_compras');
    formData.append('opcion', isEditing ? 2 : 1);
    
    if (isEditing) {
        formData.append('firstColumnValue', $('#id_lista_compra').val());
    }
    
    // Recopilar los datos básicos de la lista de compras - only include the main form fields
    const listaCompraData = {
        lista_compra: $('#lista_compra').val(),
        orden: $('#orden').val(),
        kid_estatus: $('#kid_estatus').val() || 1,
        kid_cuenta_bancaria: $('#kid_cuenta_bancaria').val(),
        kid_proyecto: $('#kid_proyecto').val()
    };
    formData.append('formDataJson', JSON.stringify(listaCompraData));
    
    // Recopilar los datos de los artículos - use a simple array structure without the numbered fields
    const articulos = [];
    $('.article-group').each(function(index) {
        const i = index + 1;
        const articuloSelect = $(`#kid_articulo_${i}`);
        const articuloId = articuloSelect.val();
        
        if (articuloId) {
            // Create detail object with simple field names that match database columns
            const detalle = {
                kid_articulo: articuloId,
                cantidad: $(`#cantidad_${i}`).val(),
                costo_unitario_total: $(`#costo_unitario_total_${i}`).val(),
                costo_unitario_neto: $(`#costo_unitario_neto_${i}`).val(),
                monto_total: $(`#monto_total_${i}`).val(),
                monto_neto: $(`#monto_neto_${i}`).val(),
                porcentaje_descuento: $(`#porcentaje_descuento_${i}`).val() || 0
            };
            
            // Get the detail ID from data attribute or hidden field
            const detalleId = articuloSelect.data('id-detalle') || $(`#id_detalle_${i}`).val();
            if (detalleId) {
                detalle.id_detalle_lista_compras = detalleId;
            }
            
            articulos.push(detalle);
        }
    });
    
    // Añadir los artículos como JSON - separating them from the main form data
    formData.append('articulos', JSON.stringify(articulos));
    formData.append('AlertDataSimilar', 'true'); // Allow similar items
    
    // Debug info
    console.log('[AJAX submit] Enviando datos de lista de compras:', listaCompraData);
    console.log('[AJAX submit] Enviando artículos:', articulos);
    
    // Enviar los datos mediante AJAX
    $.ajax({
        url: '/vistas/compras/bd/crudSummit.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            try {
                // Validar respuesta
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                
                if (response.status === 'success') {
                    alert(isEditing ? 'Los datos se actualizaron correctamente' : 'Los datos se registraron correctamente');
                    $('#tablalistas_compras').DataTable().ajax.reload();
                    const modalElement = document.getElementById('modalCRUDlistas_compras');
                    const modalInstance = bootstrap.Modal.getInstance(modalElement);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                } else if (response.status === 'error') {
                    console.error('Error del servidor:', response);
                    alert('Error: ' + (response.message || 'No se pudo procesar la solicitud'));
                }
            } catch (e) {
                console.error('Error al procesar respuesta:', e);
                alert('Error al procesar la respuesta: ' + e.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error en la solicitud:', error);
            console.error('Estado:', xhr.status);
            console.error('Respuesta:', xhr.responseText);
            alert('Error al comunicarse con el servidor: ' + error);
        }
    });
});