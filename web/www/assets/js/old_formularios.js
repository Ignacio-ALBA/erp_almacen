$(document).ready(function() {

    $('.VerificarCambioMostrar').on('change', function() {
        // Obtener el ID del elemento que disparó el evento
        var id = $(this).attr('id');
    
     
            // Si todos los campos tienen valor, mostrar los elementos con la clase igual al ID
            $('.' + id).slideDown(); // O puedes usar .fadeIn() o .slideDown() si deseas un efecto
            $('.' + id).next('br').show();
    });

    $('.DataGET').on('change', function() {
        // Obtener la primera clase que contiene "Data-"
        var claseData = $(this).attr('class').split(' ').find(function(className) {
            return className.includes('Data-');
        });
    
        // Si se encontró una clase que contiene "Data-", obtener la parte después de "Data-"
        var modalCRUD = claseData ? claseData.split('Data-')[1] : null; // Obtiene la parte después de "Data-"
    
        console.log(modalCRUD); // Ahora 'resultado' es un solo elemento o null si no se encontró
    
        // Obtener el ID del elemento que disparó el evento
        var id = $(this).attr('id');
        var formbloque = ($(this).attr('bloque') || "") + "/";

        var inputfill = $('.' + id).first();
        console.log(inputfill);
        
    
        // Si todos los campos tienen valor, mostrar los elementos con la clase igual al ID
        //$('.' + id).slideDown(); // O puedes usar .fadeIn() o .slideDown() si deseas un efecto
        //$('.' + id).next('br').show();

        var data = {
            modalCRUD: modalCRUD,
            firstColumnValue: $(this).val(),
        };

        

        $.ajax({
            url: `../${formbloque}bd/crudEndpoint.php`,
            type: "POST",
            dataType: "json",
            data: data,
            success: function(response){  
                console.log(response);
                if(response.data){
                    if (inputfill.length && inputfill.is('select')) { // Verifica que el elemento exista y sea un select
                        var opcionesArray = response.data; // Este es tu array con las opciones
                    
                        inputfill.empty(); // Limpia las opciones existentes
                    
                        // Agrega las nuevas opciones al select
                        $.each(opcionesArray, function(index, value) {
                            var texto = '';

                            if('text'in value){
                                texto = value['text'];
                            }else{
                                texto = value['valor'];
                            }

                            var isSelected = value['pordefecto'] ? true : false;
                            
                            console.log(value);
                            
                            if (inputfill.children('option').length === 0) {
                                // Si no hay opciones, agrega la opción por defecto
                                inputfill.append($('<option>', {
                                    value: '',
                                    text: 'Seleccione...',
                                    selected: true,
                                    disabled: true
                                }));
                            } else {
                                // Si hay opciones, puedes agregar las nuevas opciones aquí
                                $.each(opcionesArray, function(index, value) {
                                    var texto = value['text'] || value['valor'];
                                    var isSelected = value['pordefecto'] ? true : false;
                            
                                    inputfill.append($('<option>', {
                                        value: value['valor'],
                                        text: texto,
                                        selected: isSelected
                                    }));
                                });
                            }
                        });
                    
                        // Opcional: Seleccionar la primera opción por defecto
                        //inputfill.val(opcionesArray[0]['valor']);
                    }else{
                        idinputfill = inputfill.attr('id');
                        inputfill.val(response.data[idinputfill])
                    }
                }
            }
        });
        
    });

    function ClearForm(modalCRUD) {
        $('.VerificarCambioMostrar').each(function() {
            var id = $(this).attr('id'); // Obtener el ID del elemento actual
            if (id) { // Verificar que el ID no sea null o undefined
                // Ocultar todos los elementos que tengan una clase igual al ID
                $('.' + id).hide();
                $('.' + id).next('br').hide();
            }
        });

        $('.ModalinModal').hide();
        $(`#form${modalCRUD}`).removeClass('was-validated');
        $(`#form${modalCRUD} input, #form${modalCRUD} select`).each(function() {
            var id = $(this).attr('id');
            $(`#form${modalCRUD}`).removeAttr('alertdatasimilar');
            $(`#alert_${modalCRUD}`).hide();
    
            if (id) {
                $(this).removeClass('is-invalid');
                if ($(this).is(':checkbox')) {
                    $(this).prop('checked', false);
                    $(`#error_${id}`).text("");
                    $(`#validate_${id}`).text("");
                    $(this).removeClass('is-invalid');
                } else {
                    if ($(this).is('select')) {
                        $(this).val($(this).find('option:first').val());
                    } else {
                        $(this).val("");
                    }
                    $(`#error_${id}`).text("");
                    $(`#validate_${id}`).text("");
                    
    
                    // Asegúrate de que el valor no sea null antes de llamar a trim()
                    var value = $(this).val();
                    if ($(this).is(':required') && (value === null || value.trim() === "")) {
                        $(this).removeClass('is-invalid');
                    }
                }
            }
        });
    }


    const select = (el, all = false) => {
        el = el.trim();
        if (all) {
            return [...document.querySelectorAll(el)];
        } else {
            return document.querySelector(el);
        }
    }
    
    const dataTableInstances = {};
    const datatables = select('.datatable', true);
    datatables.forEach(datatable => {
        const tableId = datatable.id;
        console.log(tableId);
        
        dataTableInstances[tableId] = $(datatable).DataTable({
        "language": {
                "lengthMenu": "Mostrar _MENU_ registros por página",
                "zeroRecords": `<div style="display:flex; justify-content:center; align-items:center;">No se encontraron resultados</div>`,
                "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sSearch": "Buscar:",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast":"Último",
                    "sNext":"Siguiente",
                    "sPrevious": "Anterior"
                },
                "sProcessing":"Procesando...",
            },
            "dom": '<"top"lf>rt<"bottom"ip><"clear">', // Personaliza la disposición de los elementos
            "initComplete": function() {
                // Agregar clase personalizada al contenedor de longitud
                $(`select[name="${tableId}_length"]`).addClass('form-select');
                // Centrar todas las columnas
                $(this.api().table().header()).find('th').addClass('text-center');
                this.api().rows().every(function() {
                    $(this.node()).find('td').addClass('text-center');
                });

                // Alinear a la derecha la última columna
                /*var lastColumnIndex = this.api().columns().count() - 1;
                this.api().column(lastColumnIndex).nodes().each(function(cell) {
                    $(cell).addClass('text-end');
                    $(cell).removeClass('text-center');
                });*/
            }
        }); 
    });

    function AddRow(modalCRUD,data) {
        data.push(`<div class="btn-group" role="group" aria-label="Basic mixed styles example">
            <button class="ModalDataEdit btn btn-warning warning" modalCRUD="${modalCRUD}">Editar</button>
            <button class="ModalDataDelete btn btn-danger danger" modalCRUD="${modalCRUD}">Eliminar</button>
        </div>`);
        tableId =`tabla${modalCRUD}`
        tabla = dataTableInstances[tableId]
        tabla.row.add(data).draw();
        tabla.rows().every(function() {
            $(this.node()).find('td').addClass('text-center');
        });
    

    }
    
    // Función para actualizar una fila
    function UpdateRow(modalCRUD,rowIndex, data) {
        
        data.push(`<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                            <button class="ModalDataEdit btn btn-warning warning" modalCRUD="${modalCRUD}">Editar</button>
                            <button class="ModalDataDelete btn btn-danger danger" modalCRUD="${modalCRUD}">Eliminar</button>
                        </div>`);
        tableId =`tabla${modalCRUD}`

        tabla = dataTableInstances[tableId]
        rowIndex = tabla.row(rowIndex).index();
        console.log("Actualizando fila en índice absoluto:", rowIndex);
        tabla.row(rowIndex).data(data).draw();
    }
    
    // Función para eliminar una fila
    function DeleteRow(modalCRUD,rowIndex) {
        tableId =`tabla${modalCRUD}`
        tabla = dataTableInstances[tableId]
        rowIndex = tabla.row(rowIndex).index();
        tabla.row(rowIndex).remove().draw();
    }


    // Función para manejar el clic en el botón "Editar"
    $(document).on('click', '.ModalDataEdit', function(){
        var modalCRUD = $(this).attr('modalCRUD');
        
        ClearForm(modalCRUD);
        $(`#modalTitle2${modalCRUD}`).show();
        $(`#modalTitle1${modalCRUD}`).hide();

        var formbloque = ($(`#form${modalCRUD}`).attr('bloque') || "") + "/";

        var row = $(this).closest('tr');
        var firstColumnValue = row.find('td:first').text();
        console.log('Editar botón clickeado con ID:', modalCRUD);
        console.log('Valor de la primera columna:', firstColumnValue);
        var data = {
            modalCRUD: modalCRUD,
            firstColumnValue: firstColumnValue,
        };

        $.ajax({
            url: `../${formbloque}bd/crudEndpoint.php`,
            type: "POST",
            dataType: "json",
            data: data,
            success: function(response){  
                console.log(response);
                if(response.data){
                    for (var key in response.data) {
                        if (response.data.hasOwnProperty(key)) {
                            // Selecciona el input o select usando el key
                            var inputElement = $('[id="' + key + '"]'); // Asegúrate de que los inputs/selects tengan el atributo name correspondiente
                            
                            if (inputElement.length) {
                                // Verifica si el input es un checkbox
                                if (inputElement.is(':checkbox')) {
                                    // Establece el estado del checkbox basado en el valor de la respuesta
                                    inputElement.prop('checked', response.data[key] === 1);
                                } else if (inputElement.is('select')) {
                                    console.log(inputElement.find('option:selected'));
                                    
                                    // Deselecciona todos los options del select
                                    inputElement.find('option:selected').prop('selected', false);
                                    // Establece el valor del select
                                    inputElement.val(response.data[key]);
                                } else {
                                    console.log("entro al else");
                                    console.log(inputElement);
                                    
                                    
                                    // Establece el valor del input
                                    inputElement.val(response.data[key]);
                                }
                            }
                        }
                    }
                    $(`#modalCRUD${modalCRUD}`).modal('show');
                }
            }
        });
        

        $(`#form${modalCRUD}`).off('submit').on('submit', function(event) {
            event.preventDefault();
            var formDataJson = {};
            var form_error = false;
            $(`#form${modalCRUD} input, #form${modalCRUD} select`).each(function() {
                var id = $(this).attr('id');
                var value;
            
                // Para checkboxes, obtenemos el estado (checked) en lugar del valor
                if ($(this).is(':checkbox')) {
                    value = $(this).is(':checked') ? 'on' : ''; // o el valor que desees almacenar
                } else {
                    value = $(this).val();
                }
            
                if (id) { 
                    if ($(this).is(':required') && (value === "" || value === null || value === undefined || ($(this).is(':checkbox') && !$(this).is(':checked')))) { 
                        // Manejo de inputs de texto, selects y checkboxes
                        form_error = true;
                        $(`#error_${id}`).text("Campo Obligatorio");
                    } else {
                        $(`#error_${id}`).text("");
                       // $(this).removeClass('is-invalid');
                        if ($(this).is(':checkbox')) {
                            formDataJson[id] = $(this).is(':checked') ? 1 : 0; // Almacena 1 si está activado, 0 si no
                        } else {
                            formDataJson[id] = value; // Almacena el valor de otros inputs
                        }
                    }
                }
            });
        
            if (form_error) {
                console.log('Por favor, complete todos los campos requeridos.');
                return;
            }
        
            console.log(formDataJson);
            var AlertDataSimilar = $(`#form${modalCRUD}`).attr('alertdatasimilar') === 'true';
            var additionalData = { opcion: 2, formDataJson: formDataJson, modalCRUD: modalCRUD,firstColumnValue:firstColumnValue, AlertDataSimilar:AlertDataSimilar};
        
            console.log('Formulario enviado para el modal:', modalCRUD);
            console.log('Datos adicionales:', additionalData);
            
            
            $.ajax({
                url: `../${formbloque}bd/crudSummit.php`, 
                type: "POST",
                dataType: "json",
                data: additionalData,
                success: function(response) {
                    if(response.status == "error"){
                        if(response.checkdata){
                            checkdata = response.checkdata;
                            console.log(checkdata);
                            
                            if(checkdata.DataExist){
                                DataExist = checkdata.DataExist
                                for (const id of DataExist) {
                                    
                                    valor = $(`#${id}`).val();
                                    $(`#${id}`).val("");
                                    $(`#error_${id}`).text(`El valor "${valor}" ya existe.`);
                                    $(`#form${modalCRUD}`).removeAttr('alertdatasimilar');
                                }
                            }

                            if(checkdata.DataSimilar){
                                DataSimilar = checkdata.DataSimilar
                                for (const id in DataSimilar) {
                                    data = DataSimilar[id];

                                    
                                    valor = $(`#${id}`).val();
                                    //$(`#${id}`).val("");
                                    $(`#validate_${id}`).text(`Elementos similares: ${data.join(", ")}`);
                                    $(`#alertmsg_${modalCRUD}`).text(`Ya existen elementos similares registrados, si deseas agregar el elemento actual vuelve a guardarlo.`);
                                    $(`#alert_${modalCRUD}`).show();
                                    $(`#form${modalCRUD}`).attr('alertdatasimilar',true);
                                }
                            }
                        
                        }
                    }else{
                        console.log('Respuesta 1 del servidor:', response);
                        data = Object.values(response.data);
                        UpdateRow(modalCRUD,row,data)
                        $(`#modalCRUD${modalCRUD}`).modal('hide'); // Cerrar el modal después de enviar
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error en la solicitud:', error);
                }
            });
            console.log(" ");
        });
    
    });

    $('.ModalDataAdd').on('click', function() {
        var modalCRUD = $(this).attr('modalCRUD')
        ClearForm(modalCRUD);
        $(`#modalTitle1${modalCRUD}`).show();
        $(`#modalTitle2${modalCRUD}`).hide();
        var formbloque = ($(`#form${modalCRUD}`).attr('bloque') || "") + "/";

        $(`#modalCRUD${modalCRUD}`).modal('show');
        

        $(`#form${modalCRUD}`).off('submit').on('submit', function(event) {
            event.preventDefault();
            var formDataJson = {};
            var form_error = false;
            $(`#form${modalCRUD} input, #form${modalCRUD} select`).each(function() {
                var id = $(this).attr('id');
                var value;
            
                // Para checkboxes, obtenemos el estado (checked) en lugar del valor
                if ($(this).is(':checkbox')) {
                    value = $(this).is(':checked') ? 1 : 0; // o el valor que desees almacenar
                } else {
                    value = $(this).val();
                }
            
                if (id) { 
                    if ($(this).is(':required') && (value === "" || value === " " || value === null || value === undefined || ($(this).is(':checkbox') && !$(this).is(':checked')))) { 
                        // Manejo de inputs de texto, selects y checkboxes
                        form_error = true;
                        $(`#error_${id}`).text("Campo Obligatorio");
                    } else {
                        $(`#error_${id}`).text("");
                        //$(this).removeClass('is-invalid');
                        if ($(this).is(':checkbox')) {
                            formDataJson[id] = $(this).is(':checked') ? 1 : 0; // Almacena 1 si está activado, 0 si no
                        } else {
                            formDataJson[id] = value; // Almacena el valor de otros inputs
                        }
                    }
                }
            });
        
            if (form_error) {
                console.log('Por favor, complete todos los campos requeridos.');
                return;
            }
        
            console.log(formDataJson);
            var AlertDataSimilar = $(`#form${modalCRUD}`).attr('alertdatasimilar') === 'true';
        
            var additionalData = { opcion: 1, formDataJson: formDataJson, modalCRUD: modalCRUD, AlertDataSimilar: AlertDataSimilar };
            console.log('AlertDataSimilar');
            console.log(AlertDataSimilar);
            
        
            console.log('Formulario enviado para el modal:', modalCRUD);
            console.log('Datos adicionales:', additionalData);
        
            $.ajax({
                url: `../${formbloque}bd/crudSummit.php`, 
                type: "POST",
                data: additionalData,
                dataType: "json",
                success: function(response) {
                    if(response.status == "error"){
                        if(response.checkdata){
                            checkdata = response.checkdata;
                            console.log(checkdata);
                            
                            if(checkdata.DataExist){
                                DataExist = checkdata.DataExist
                                for (const id of DataExist) {
                                    
                                    valor = $(`#${id}`).val();
                                    $(`#${id}`).val("");
                                    $(`#error_${id}`).text(`El valor "${valor}" ya existe.`);
                                    $(`#form${modalCRUD}`).removeAttr('alertdatasimilar');
                                }
                            }

                            if(checkdata.DataSimilar){
                                DataSimilar = checkdata.DataSimilar
                                for (const id in DataSimilar) {
                                    data = DataSimilar[id];

                                    
                                    valor = $(`#${id}`).val();
                                    //$(`#${id}`).val("");
                                    $(`#validate_${id}`).text(`Elementos similares: ${data.join(", ")}`);
                                    $(`#alertmsg_${modalCRUD}`).text(`Ya existen elementos similares registrados, si deseas agregar el elemento actual vuelve a guardarlo.`);
                                    $(`#alert_${modalCRUD}`).show();
                                    $(`#form${modalCRUD}`).attr('alertdatasimilar',true);
                                }
                            }
                        
                        }
                    }else{
                        data = Object.values(response.data);
                        AddRow(modalCRUD,data)
                        $(`#modalCRUD${modalCRUD}`).modal('hide'); // Cerrar el modal después de enviar
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error en la solicitud:', error);
                }
            });
        });
    
    });

    $('.DataAdd').on('click', function() {
        var modalCRUD = $(this).attr('modalCRUD')
        ClearForm(modalCRUD);
        $(`#modalTitle1${modalCRUD}`).show();
        $(`#modalTitle2${modalCRUD}`).hide();
        var formbloque = ($(`#form${modalCRUD}`).attr('bloque') || "") + "/";

        $(`#modalCRUD${modalCRUD}`).show()
        

        $(`#form${modalCRUD}`).off('submit').on('submit', function(event) {
            event.preventDefault();
            var formDataJson = {};
            var form_error = false;
            $(`#form${modalCRUD} input, #form${modalCRUD} select`).each(function() {
                var id = $(this).attr('id');
                var value;
            
                // Para checkboxes, obtenemos el estado (checked) en lugar del valor
                if ($(this).is(':checkbox')) {
                    value = $(this).is(':checked') ? 1 : 0; // o el valor que desees almacenar
                } else {
                    value = $(this).val();
                }
            
                if (id) { 
                    if ($(this).is(':required') && (value.trim() === "" || ( $(this).is(':checkbox') && !$(this).is(':checked')))) { 
                        // Manejo de inputs de texto, selects y checkboxes
                        form_error = true;
                        $(`#error_${id}`).text("Campo Obligatorio");
                        //$(this).addClass('is-invalid');
                    } else {
                        $(`#error_${id}`).text("");
                        //$(this).removeClass('is-invalid');
                        if ($(this).is(':checkbox')) {
                            formDataJson[id] = $(this).is(':checked') ? 1 : 0; // Almacena 1 si está activado, 0 si no
                        } else {
                            formDataJson[id] = value; // Almacena el valor de otros inputs
                        }
                    }
                }
            });
        
            if (form_error) {
                console.log('Por favor, complete todos los campos requeridos.');
                return;
            }
        
            console.log(formDataJson);
        
            var additionalData = { opcion: 1, formDataJson: formDataJson, modalCRUD: modalCRUD, };
        
            console.log('Formulario enviado para el modal:', modalCRUD);
            console.log('Datos adicionales:', additionalData);
        
            $.ajax({
                url: `../${formbloque}bd/crudSummit.php`, 
                type: "POST",
                data: additionalData,
                dataType: "json",
                success: function(response) {
                    
                    data = Object.values(response.data);
                    AddRow(modalCRUD,data)
                    $(`#modalCRUD${modalCRUD}`).modal('hide'); // Cerrar el modal después de enviar
                },
                error: function(xhr, status, error) {
                    console.error('Error en la solicitud:', error);
                }
            });
        });
    
    });


    // Función para manejar el clic en el botón "Eliminar"
    $(document).on('click', '.ModalDataDelete', function()  {
        var modalCRUD = $(this).attr('modalCRUD');

        var formbloque = ($(`#form${modalCRUD}`).attr('bloque') || "") + "/";
        var row = $(this).closest('tr');
        var firstColumnValue = row.find('td:first').text();
        console.log('Eliminar botón clickeado con ID:', modalCRUD);
        console.log('Valor de la primera columna:', firstColumnValue);
        var additionalData = { opcion: 3, formDataJson: [1,1], modalCRUD: modalCRUD,firstColumnValue:firstColumnValue, AlertDataSimilar:false};
        
        console.log('Formulario enviado para el modal:', modalCRUD);
        console.log('Datos adicionales:', additionalData);

        if (!row) {
            console.error('Fila no válida.');
            return;
        }

    
        const confirmar = confirm('¿Estás seguro de que deseas eliminar esta fila?');
        if (!confirmar) {
            return;
        }
        
    
        $.ajax({
            url: `../${formbloque}bd/crudSummit.php`, 
            type: "POST",
            dataType: "json",
            data: additionalData,
            success: function(response) {
                console.log('Respuesta del servidor:', response);
                if(response.data){
                    DeleteRow(modalCRUD,row)
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la solicitud:', error);
            }
        });
    });

    /************************************** Validaciones de inputs **************************************/
    $('.ValidateCorreo').on('change', function() {
        const email = $(this).val().trim(); // Obtiene el valor y elimina espacios en blanco
        var id = $(this).attr('id');
        // Expresión regular para validar el correo electrónico
        const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

        if (email !== "") { // Solo valida si el valor no está vacío
            if (!emailPattern.test(email)) {
                $(`#error_${id}`).text('El formato del correo electrónico es inválido.'); // Asegúrate de que el selector sea correcto
                $(`#error_${id}`).show(); // Muestra el mensaje de error
                $(this).addClass('is-invalid'); // Agrega una clase para mostrar el error
            } else {
                $(`#error_${id}`).hide(); // Oculta el mensaje de error si es válido
                $(this).removeClass('is-invalid'); // Elimina la clase de error si es válido
            }
        } else {
            $(`#error_${id}`).hide(); // Oculta el mensaje de error si el campo está vacío
            $(this).removeClass('is-invalid'); // Elimina la clase de error si el campo está vacío
        }
    });

    $('.ValidateTelefono').on('change', function() {
        const telefono = $(this).val().trim(); // Obtiene el valor y elimina espacios en blanco
        var id = $(this).attr('id');
        // Expresión regular para validar números de teléfono (10 dígitos)
        const telefonoPattern = /^\d{10}$/; // Cambia esta expresión regular según el formato que necesites

        if (telefono !== "") { // Solo valida si el valor no está vacío
            if (!telefonoPattern.test(telefono)) {
                $(`#error_${id}`).text('El número de teléfono debe tener 10 dígitos.'); // Asegúrate de que el selector sea correcto
                $(`#error_${id}`).show(); // Muestra el mensaje de error
                $(this).addClass('is-invalid'); // Agrega una clase para mostrar el error
            } else {
                $(`#error_${id}`).hide(); // Oculta el mensaje de error si es válido
                $(this).removeClass('is-invalid'); // Elimina la clase de error si es válido
            }
        } else {
            $(`#error_${id}`).hide(); // Oculta el mensaje de error si el campo está vacío
            $(this).removeClass('is-invalid'); // Elimina la clase de error si el campo está vacío
        }
    });

    $('.ValidateRFC').on('change', function() {
        const rfc = $(this).val().trim(); // Obtiene el valor y elimina espacios en blanco
        var id = $(this).attr('id');

        // Expresión regular para validar el RFC de persona física y moral
        const rfcPattern = /^([A-Z&]{3}|[A-Z]{4})(\d{6})([A-Z0-9]{3})$/;

        if (rfc !== "") { // Solo valida si el valor no está vacío
            if (!rfcPattern.test(rfc)) {
                $(`#error_${id}`).text('El RFC es inválido.'); // Asegúrate de que el selector sea correcto
                $(`#error_${id}`).show(); // Muestra el mensaje de error
                $(this).addClass('is-invalid'); // Agrega una clase para mostrar el error
            } else {
                $(`#error_${id}`).hide(); // Oculta el mensaje de error si es válido
                $(this).removeClass('is-invalid'); // Elimina la clase de error si es válido
            }
        } else {
            $(`#error_${id}`).hide(); // Oculta el mensaje de error si el campo está vacío
            $(this).removeClass('is-invalid'); // Elimina la clase de error si el campo está vacío
        }
    });

    /************************************* Validaciones de inputs *************************************/
});