<?php
//$path = $SERVERURL.'/assets/img/logot2.JPG'; // Ruta de la imagen
//$imageData = base64_encode(file_get_contents($path)); // Leer la imagen y codificarla en base64
//$imageType = pathinfo($path, PATHINFO_EXTENSION); // Obtener el tipo de imagen
//$dataUrl2 = 'data:image/' . $imageType . ';base64,' . $imageData; // Crear el Data URL
//$path = $SERVERURL.'/assets/img/logoi.JPG'; // Ruta de la imagen
//$imageData = base64_encode(file_get_contents($path)); // Leer la imagen y codificarla en base64
//$imageType = pathinfo($path, PATHINFO_EXTENSION); // Obtener el tipo de imagen
//$dataUrl1 = 'data:image/' . $imageType . ';base64,' . $imageData; // Crear el Data URL

// 1. Definir rutas base y rutas de imágenes 
if (!defined('SERVERURL')) {
    define('SERVERURL', 'http://' . $_SERVER['HTTP_HOST']);
}

// Intentar obtener las imágenes desde URL primero
try {
    // Procesar logo superior (logot2.JPG)
    $path = SERVERURL.'/assets/img/logot2.JPG';
    $imageData = @file_get_contents($path);
    if ($imageData !== false) {
        $imageType = pathinfo($path, PATHINFO_EXTENSION);
        $dataUrl2 = 'data:image/' . $imageType . ';base64,' . base64_encode($imageData);
    } else {
        // Si falla la URL, intentar con ruta del sistema de archivos
        $logo_top_path = $_SERVER['DOCUMENT_ROOT'] . '/assets/img/logot2.JPG';
        if (file_exists($logo_top_path)) {
            $imageData = file_get_contents($logo_top_path);
            $imageType = pathinfo($logo_top_path, PATHINFO_EXTENSION);
            $dataUrl2 = 'data:image/' . $imageType . ';base64,' . base64_encode($imageData);
        } else {
            error_log("No se pudo cargar logot2.JPG ni por URL ni por sistema de archivos");
            $dataUrl2 = ''; // O asignar una imagen por defecto en base64
        }
    }

    // Procesar logo izquierdo (logoi.JPG)
    $path = SERVERURL.'/assets/img/logoi.JPG';
    $imageData = @file_get_contents($path);
    if ($imageData !== false) {
        $imageType = pathinfo($path, PATHINFO_EXTENSION);
        $dataUrl1 = 'data:image/' . $imageType . ';base64,' . base64_encode($imageData);
    } else {
        // Si falla la URL, intentar con ruta del sistema de archivos
        $logo_izq_path = $_SERVER['DOCUMENT_ROOT'] . '/assets/img/logoi.JPG';
        if (file_exists($logo_izq_path)) {
            $imageData = file_get_contents($logo_izq_path);
            $imageType = pathinfo($logo_izq_path, PATHINFO_EXTENSION);
            $dataUrl1 = 'data:image/' . $imageType . ';base64,' . base64_encode($imageData);
        } else {
            error_log("No se pudo cargar logoi.JPG ni por URL ni por sistema de archivos");
            $dataUrl1 = ''; // O asignar una imagen por defecto en base64
        }
    }
} catch (Exception $e) {
    error_log("Error al procesar las imágenes: " . $e->getMessage());
    // Imágenes por defecto en caso de error
    $dataUrl1 = 'data:image/jpeg;base64,/9j/4AAQSkZJRg...';
    $dataUrl2 = 'data:image/jpeg;base64,/9j/4AAQSkZJRg...';
}
?>
<script nonce="<?php echo $nonce; ?>">
    now_editing = null;

    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        if(calendarEl){
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth'
            });
            calendar.render();
        }
        
      });
    
$(document).ready(function() {

    function addAlert(mensaje, tipo = 'danger', tiempo = 5000) {
        const alerta = $('<div class="alert alert-' + tipo + ' bg-' + tipo + ' text-light border-0 alert-dismissible fade show" role="alert">' +
            mensaje +
            '<button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>' +
            '</div>');
        let contenedorAlertas = $('#contenedor-alertas');
        if (!contenedorAlertas.length) {
            contenedorAlertas = $('<div id="contenedor-alertas" style="position: fixed; top: 70px; right: 10px;"></div>');
            $('body').append(contenedorAlertas);
        }
        if (contenedorAlertas.children().length >= 5) {
            contenedorAlertas.children().first().remove();
        }
        contenedorAlertas.append(alerta);
        alerta.css({
            position: 'relative',
            display: 'none'
        });
        alerta.slideDown(300);
        setTimeout(function() {
            alerta.slideUp(300, function() {
            alerta.remove();
            });
        }, tiempo);
    }


    let fotos = [];
    const viewFotos = $('.ViewFotos');

    function updateCarousel(fotoadd) {
        try {
            const carouselIndicators = $('.carousel-indicators');
            fotos.push(fotoadd);
            //$('.FotoInput').val(JSON.stringify(fotos));
            //viewFotos.html('');
            viewFotos.empty();

            // Verificación de errores
            if (!carouselIndicators.length) {
                throw new Error('No se encontró el elemento con clase "carousel-indicators"');
            }

            if (!viewFotos.length) {
                throw new Error('No se encontró el elemento con clase "viewFotos"');
            }

            //carouselIndicators.html('');
            carouselIndicators.empty();

            const idPadre = viewFotos.parent().attr('id');
            console.log(idPadre);
            
            
            if (!idPadre) {
                throw new Error('No se encontró el atributo "id" en el elemento padre de "viewFotos"');
            }

            let active = 'active';
            fotos.forEach((foto, index) => {
                const carouselItem = $(`<div class="carousel-item ${active}"></div>`);
                const img = $(`<img src="${foto}" class="d-block w-100" alt="...">`);
                const carouselIndicatorItem = $(`<button type="button" data-bs-target="#${idPadre}" data-bs-slide-to="${index}" class=" ${index === 0 ? 'active' : ''}" aria-label="Slide ${index}"></button>`);
                carouselItem.append(img);
                viewFotos.append(carouselItem);
                carouselIndicators.append(carouselIndicatorItem);
                active = '';
            });
            return true
        } catch (error) {
            console.error('Error en la función updateCarousel:', error.message);
            return error.message;
        }
    }


    let imagenesGlobal = [];

    function VerificarCambioMostrar(){
        var id = $(this).attr('id'); 
        if ($(this).is(':checkbox')) {
            var inputs = $('.' + id).find('input');
            if ($(this).is(':checked')) {
                $('.' + id).slideDown();
                inputs.removeAttr('disabled');
                inputs.prop('required', true);
                $('.' + id).next('br').show();
            } else {
                $('.' + id).slideUp();
                inputs.removeAttr('required');
                inputs.prop('disabled', true);
                $('.' + id).next('br').hide();
                
            }
            console.log();
        } else {
            $('.' + id).slideDown();
            $('.' + id).next('br').show();
        }
    }

    $('.VerificarCambioMostrar').on('change', VerificarCambioMostrar);

    $('.ViewFile').on('change', function() {
        // Obtener la primera clase que contiene "Data-"
        var claseData = $(this).attr('class').split(' ').find(function(className) {
            return className.includes('Data-');
        });

        // Si se encontró una clase que contiene "Data-", obtener la parte después de "Data-"
        var modalCRUD = claseData ? claseData.split('Data-')[1] : null; // Obtiene la parte después de "Data-"

        console.log(modalCRUD); // Ahora 'resultado' es un solo elemento o null si no se encontró

        // Obtener el ID del elemento que disparó el evento
        var id = $(this).attr('id');
        var formbloque = $(this).closest('form').attr('bloque') || "";
        formbloque += "/";

        var inputfill = $('.' + id);
        console.log(inputfill);
        

        // Si todos los campos tienen valor, mostrar los elementos con la clase igual al ID
        //$('.' + id).slideDown(); // O puedes usar .fadeIn() o .slideDown() si deseas un efecto
        //$('.' + id).next('br').show();

        var data = {
            modalCRUD: modalCRUD,
            firstColumnValue: $(this).val(),
        };

        

        $.ajax({
            url: `/vistas/${formbloque}bd/crudEndpoint.php`,
            type: "POST",
            dataType: "json",
            data: data,
            success: function(response){  
                console.log(response);
                if(response.data){
                    if (inputfill.length && inputfill.is('select')) { // Verifica que el elemento exista y sea un select
                        var opcionesArray = response.data; // Este es tu array con las opciones
                    
                        inputfill.empty(); // Limpia las opciones existentes
                    
                        if (inputfill.children('option').length === 0) {
                            // Si no hay opciones, agrega la opción por defecto
                            inputfill.append($('<option>', {
                                value: '',
                                text: 'Seleccione...',
                                selected: true,
                                disabled: true
                            }));
                        }

                        // Agrega las nuevas opciones al select
                        $.each(opcionesArray, function(index, value) {
                            var texto = value['text'] || value['valor'];
                            var isSelected = value['pordefecto'] ? true : false;

                            inputfill.append($('<option>', {
                                value: value['valor'],
                                text: texto,
                                selected: isSelected
                            }));
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



    $('.DownloadFile').on('change', function() {
        // Obtener la primera clase que contiene "Data-"
        var claseData = $(this).attr('class').split(' ').find(function(className) {
            return className.includes('Data-');
        });

        // Si se encontró una clase que contiene "Data-", obtener la parte después de "Data-"
        var modalCRUD = claseData ? claseData.split('Data-')[1] : null; // Obtiene la parte después de "Data-"

        console.log(modalCRUD); // Ahora 'resultado' es un solo elemento o null si no se encontró

        // Obtener el ID del elemento que disparó el evento
        var id = $(this).attr('id');
        var formbloque = $(this).closest('form').attr('bloque') || "";
        formbloque += "/";

        var inputfill = $('.' + id);
        console.log(inputfill);
        

        // Si todos los campos tienen valor, mostrar los elementos con la clase igual al ID
        //$('.' + id).slideDown(); // O puedes usar .fadeIn() o .slideDown() si deseas un efecto
        //$('.' + id).next('br').show();

        var data = {
            modalCRUD: modalCRUD,
            firstColumnValue: $(this).val(),
        };

        

        $.ajax({
            url: `/vistas/${formbloque}bd/crudEndpoint.php`,
            type: "POST",
            dataType: "json",
            data: data,
            success: function(response){  
                console.log(response);
                if(response.data){
                    if (inputfill.length && inputfill.is('select')) { // Verifica que el elemento exista y sea un select
                        var opcionesArray = response.data; // Este es tu array con las opciones
                    
                        inputfill.empty(); // Limpia las opciones existentes
                    
                        if (inputfill.children('option').length === 0) {
                            // Si no hay opciones, agrega la opción por defecto
                            inputfill.append($('<option>', {
                                value: '',
                                text: 'Seleccione...',
                                selected: true,
                                disabled: true
                            }));
                        }

                        // Agrega las nuevas opciones al select
                        $.each(opcionesArray, function(index, value) {
                            var texto = value['text'] || value['valor'];
                            var isSelected = value['pordefecto'] ? true : false;

                            inputfill.append($('<option>', {
                                value: value['valor'],
                                text: texto,
                                selected: isSelected
                            }));
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
        var formbloque = $(this).closest('form').attr('bloque') || "";
        formbloque += "/";

        var inputfill = $('.' + id);
        console.log(inputfill);
        

        // Si todos los campos tienen valor, mostrar los elementos con la clase igual al ID
        //$('.' + id).slideDown(); // O puedes usar .fadeIn() o .slideDown() si deseas un efecto
        //$('.' + id).next('br').show();

        var data = {
            modalCRUD: modalCRUD,
            firstColumnValue: $(this).val(),
        };

        

        $.ajax({
            url: `/vistas/${formbloque}bd/crudEndpoint.php`,
            type: "POST",
            dataType: "json",
            data: data,
            success: function(response){  
                console.log(response);
                if(response.data){
                    if (inputfill.length && inputfill.is('select')) { // Verifica que el elemento exista y sea un select
                        var opcionesArray = response.data; // Este es tu array con las opciones
                    
                        inputfill.empty(); // Limpia las opciones existentes
                    
                        if (inputfill.children('option').length === 0) {
                            // Si no hay opciones, agrega la opción por defecto
                            inputfill.append($('<option>', {
                                value: '',
                                text: 'Seleccione...',
                                selected: true,
                                disabled: true
                            }));
                        }

                        // Agrega las nuevas opciones al select
                        $.each(opcionesArray, function(index, value) {
                            var texto = value['text'] || value['valor'];
                            var isSelected = value['pordefecto'] ? true : false;

                            inputfill.append($('<option>', {
                                value: value['valor'],
                                text: texto,
                                selected: isSelected
                            }));
                        });
                    
                        // Opcional: Seleccionar la primera opción por defecto
                        //inputfill.val(opcionesArray[0]['valor']);
                    }else{
                        console.log(inputfill);
                        
                        idinputfill = inputfill.attr('id');
                        inputfill.val(response.data[idinputfill])
                    }
                }
            }
        });
        
    });

    function ClearForm(modalCRUD) {
        imagenesGlobal = [];
        fotos = [];
        // Verificar cambios en los elementos que tienen la clase 'VerificarCambioMostrar'
        $('.VerificarCambioMostrar').each(function() {
            VerificarCambioMostrar.call(this); // Asegúrate de que se llame con el contexto correcto
        });
        $('textarea').each(function() {
            $(this).css('height', 'auto'); // Restablece la altura al valor predeterminado
        });

        $('.ViewFotos').each(function() {
            $(this).empty();
            $(this).append('<h5><i class="bi bi-image"></i> Sin Fotos Subidas</h5>');
        });

        $('.ModalinModal').hide();
        //$('.OnOpenClear').find('option').remove();
        $(`#form${modalCRUD}`).removeClass('was-validated');
        $(`#form${modalCRUD} input, #form${modalCRUD} select, #form${modalCRUD} textarea`).each(function() {
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
                    } else if (!$(this).attr('value')){
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

        if($('.IMGInputUpdateContainer')){
            $('.IMGInputUpdateContainer').val('');
            const contenedorFotos = $('.IMGViewerContainer');
            contenedorFotos.attr('src',''); // Agrega la imagen al contenedor
            contenedorFotos.parent().hide(); // Muestra el div padre del contenedor
        }  
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
        
        
        // Inicializa DataTable
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
                    "sLast": "Último",
                    "sNext": "Siguiente",
                    "sPrevious": "Anterior"
                },
                "sProcessing": "Procesando...",
            },
            "searchPanes": {
                cascadePanes: true,
                viewTotal: true
            },
            "dom": '<"top"lf><"table-buttons"B><"clear">rt<"bottom"ip><"clear">',
            "buttons": [
                {
                    extend: 'excelHtml5', // Tipo de exportación
                    text: '<i class="bi bi-filetype-xlsx"></i> Excel', // Texto del botón
                    filename: `${tableId}_${new Date().toISOString().replace(/[-:T.]/g, '').slice(0, 14)}`, // Título del archivo exportado
                    className: 'btn btn-outline-secondary', // Clase del botón
                    exportOptions: {
                        columns: ':visible:not(:last-child)'
                    }
                },
                {
                    extend: 'pdfHtml5', // Tipo de exportación
                    text: '<i class="bi bi-filetype-pdf"></i> PDF', // Texto del botón
                    orientation: 'landscape',
                    filename: `${tableId}_${new Date().toISOString().replace(/[-:T.]/g, '').slice(0, 14)}`, // Título del archivo exportado
                    className: 'btn btn-outline-secondary', // Clase del botón
                    exportOptions: {
                        columns: ':visible:not(:last-child)'
                    },
                    customize: function (doc) {
                        // Obtener la fecha actual
                        const formattedDate = new Date().toLocaleDateString('es-MX', { day: '2-digit', month: '2-digit', year: 'numeric' }).replace(/(\d{2})\/(\d{2})\/(\d{4})/, '$1/$2/$3');


                        // Definir el ancho de la segunda imagen
                        const secondImageWidth = 100; // Ancho de la segunda imagen

                        // Calcular la altura de la segunda imagen proporcionalmente
                        const aspectRatio = 2/9; // Relación de aspecto (ejemplo: 1.5 significa que la altura es 1.5 veces el ancho)
                        const secondImageHeight = secondImageWidth * aspectRatio; // Altura proporcional de la segunda imagen

                        // Crear un contenedor para la fila
                        const row = {
                            columns: [
                                {
                                    image: "<?php echo $dataUrl1 ?>", // Ruta de la primera imagen
                                    width: secondImageHeight, // Ancho de la primera imagen
                                    //height: 20, // Altura de la primera imagen
                                    margin: [0, 0, 10, 0] // Margen derecho para espacio entre imágenes y fecha
                                },
                                {
                                    image: "<?php echo $dataUrl2 ?>", // Ruta de la segunda imagen
                                    width: secondImageWidth, // Ancho de la segunda imagen
                                    height: secondImageHeight, // Altura proporcional de la segunda imagen
                                },
                                {
                                    text: 'Elaborado: '+formattedDate, // Texto de la fecha
                                    italics: true,
                                    alignment: 'right',
                                    margin: [10, 0, 0, 20]
                                }
                            ],
                            columnGap: 10 // Espacio entre columnas
                        };

                        const tableLayout = {
                            hLineWidth: function(i) {
                                return 0.5; // Ancho de la línea horizontal
                            },
                            vLineWidth: function(i) {
                                return 0.5; // Ancho de la línea vertical
                            },
                            hLineColor: function(i) {
                                return 'black'; // Color de la línea horizontal
                            },
                            vLineColor: function(i) {
                                return 'black'; // Color de la línea vertical
                            },
                            paddingLeft: function(i) {
                                return 4; // Espaciado a la izquierda
                            },
                            paddingRight: function(i) {
                                return 4; // Espaciado a la derecha
                            },
                            paddingTop: function(i) {
                                return 4; // Espaciado en la parte superior
                            },
                            paddingBottom: function(i) {
                                return 4; // Espaciado en la parte inferior
                            }
                        };

                        const anchoTotal = 297*2.84; // mm
                        const margen = 25*2.84; // mm
                        
                        // Calcular el ancho útil
                        const anchoUtil = anchoTotal - (2*margen);
                        
                        // Centrar el contenido de las celdas
                        doc.content.forEach(function(item) {
                            if (item.table) {
                                item.layout = tableLayout;
                                if(item.table && item.table.body[0].length < 8) {
                                    const numColumnas = item.table.body[0].length;
                                    const anchoColumna = anchoUtil / numColumnas; // Ancho de cada columna

                                    // Asignar el ancho proporcional a las columnas
                                    item.table.widths = Array(numColumnas).fill(anchoColumna); // Ajustar el ancho de las columnas
                                }
                                item.table.body.forEach(function(row) {
                                    row.forEach(function(cell) {
                                        cell.alignment = 'center'; // Centrar el contenido de la celda
                                    });
                                });
                            }
                        });

                        // Agregar la fila al contenido del documento
                        doc.content.splice(0, 0, row);
                    }
                },
                // Aquí puedes agregar más botones personalizados si lo deseas
            ],
            "initComplete": function() {
                // Agregar clase personalizada al contenedor de longitud
                $(`select[name="${tableId}_length"]`).addClass('form-select');
                const searchInput = $(`input[type="search"]`);
                searchInput.addClass('form-control')
                // Centrar todas las columnas
                $(this.api().table().header()).find('th').addClass('text-center');
                this.api().rows().every(function() {
                    $(this.node()).find('td').addClass('text-center');
                });

                // Crear campos de búsqueda para cada columna
                const api = this.api();
                api.columns().every(function() {
                    const column = this;
                    const header = $(column.header());
                    
                    // Obtener el título de la columna para usar como placeholder
                    const columnTitle = header.text().trim();

                    // Crear un input de búsqueda
                    const input = $('<input class="form-control" style="border-style:none; max-width:100%; min-width:100%; " type="text" placeholder="' + columnTitle + '" />');

                    const div = $(`
                    <div class="form-floating">
                        <label style="max-width:100%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${columnTitle}</label>
                    </div>
                    `);
                    
                    // Agregar el input al div
                    div.prepend(input);

                    // Agregar el div al encabezado de la columna
                    div.appendTo($(header).empty());

                    // Manejar eventos de búsqueda
                    input.on('keyup change clear', function() {
                        if (column.search() !== this.value) {
                            column.search(this.value).draw();
                        }
                    });
                });

                //console.log(tableId);
                

                if (tableId === 'tablaproveedores' && api.columns().indexes().length > 4) {
                    console.log(`Ordenando por la columna 4 en la tabla ${tableId}`);
                    api.order([4, 'desc']).draw(); // Ordena por la columna 4 en orden descendente
                }
            }
        }); 
        console.log(tableId);
    });

    function AddRow(modalCRUD,data) {
        tableId =`tabla${modalCRUD}`
        tabla = dataTableInstances[tableId]
        console.log($(tabla).attributes);

        var numColumnas = tabla.columns().count();
        
        if(data.length < numColumnas && !$(`#${tableId}`).hasClass('PrimaryTable') && !$(`#${tableId}`).hasClass('StaticButtons')){
            data.push(`<div class="btn-group" role="group" style="width:100%;">
            <?php
                if (checkPerms("ver_".$id,true)) echo '<button type="button" class="ModalDataView btn btn-primary primary" modalCRUD=\'${modalCRUD}\'><i class="bi bi-eye"></i> Ver</button>';
                if (checkPerms("editar_".$id,true)) echo '<button type="button" class="ModalDataEdit btn btn-warning warning" modalCRUD=\'${modalCRUD}\'><i class="bi bi-pencil"></i> Editar</button>';
                if (checkPerms("eliminar_".$id,true)) echo '<button type="button" class="ModalDataDelete btn btn-danger danger" modalCRUD=\'${modalCRUD}\'><i class="bi bi-trash"></i> Eliminar</button>';
            ?>
        </div>`)
        console.log("Botones 1");
        
        }else if(data[data.length - 1].length > 1 && data[data.length - 1][0] == true && $(`#${tableId}`).hasClass('PrimaryTable')){
            data[data.length - 1] = data[data.length - 1][1];
            console.log("Botones 2");
        }else if(data[data.length - 1].length > 1 && $(`#${tableId}`).hasClass('StaticButtons')){
            console.log("Botones 3");
            data.push(`
            <?php 
                $bloques_de_botones = array_chunk($botones_acciones, 3);
                foreach ($bloques_de_botones as $index => $bloque) {
                    $paddingStyle = ($index < count($bloques_de_botones) - 1) ? 'padding-bottom:10px;' : '';
                    echo'<div class="btn-group" role="group" style="width:100%; '.$paddingStyle.'">';
                    foreach($bloque as $boton){
                        echo $boton;
                    }
                    echo ' </div>';
                }
            ?>
        `);
        }
        
        tabla.row.add(data).draw();
        tabla.rows().every(function() {
            $(this.node()).find('td').addClass('text-center');
        });


    }
    
    // Función para actualizar una fila
    function UpdateRow(modalCRUD,rowIndex, data) {
        console.log('data');
        console.log(data);
        console.log(data[data.length - 1].length);
        
        
        //modalCRUD = modalCRUD.replace('editar_', '');
        tableId =`tabla${modalCRUD}`
        tableId = tableId.replace('editar_', '');
        
        

        tabla = dataTableInstances[tableId]

        var numColumnas = tabla.columns().count();
        console.log(`columnas: ${numColumnas}`);
        console.log(`numero de columnas data: ${data.length}`);

        
        let botones
        if(data.length < numColumnas && !$(`#${tableId}`).hasClass('PrimaryTable') && !$(`#${tableId}`).hasClass('StaticButtons')){
            data.push(`<div class="btn-group" role="group" style="width:100%;">
            <?php
                if (checkPerms("ver_".$id,true)) echo '<button type="button" class="ModalDataView btn btn-primary primary" modalCRUD=\'${modalCRUD}\'><i class="bi bi-eye"></i> Ver</button>';
                if (checkPerms("editar_".$id,true)) echo '<button type="button" class="ModalDataEdit btn btn-warning warning" modalCRUD=\'${modalCRUD}\'><i class="bi bi-pencil"></i> Editar</button>';
                if (checkPerms("eliminar_".$id,true)) echo '<button type="button" class="ModalDataDelete btn btn-danger danger" modalCRUD=\'${modalCRUD}\'><i class="bi bi-trash"></i> Eliminar</button>';
            ?>
        </div>`)
        console.log("Botones 1");
        
        }else if(data[data.length - 1].length > 1 && data[data.length - 1][0] == true && $(`#${tableId}`).hasClass('PrimaryTable')){
            data[data.length - 1] = data[data.length - 1][1];
            console.log("Botones 2");
        }else if(data[data.length - 1].length > 1 && $(`#${tableId}`).hasClass('StaticButtons')){
            console.log("Botones 3");
            data.push(`
            <?php 
                $bloques_de_botones = array_chunk($botones_acciones, 3);
                foreach ($bloques_de_botones as $index => $bloque) {
                    $paddingStyle = ($index < count($bloques_de_botones) - 1) ? 'padding-bottom:10px;' : '';
                    echo'<div class="btn-group" role="group" style="width:100%; '.$paddingStyle.'">';
                    foreach($bloque as $boton){
                        echo $boton;
                    }
                    echo ' </div>';
                }
            ?>
        `);
        }
        
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
    function ClearTable(modalCRUD) {
        tableId =`tabla${modalCRUD}`
        tabla = dataTableInstances[tableId]
        tabla.clear().draw();
    }

    $(document).on('click', '.ModalSetData', function() {
        var modalCRUD = $(this).attr('modalCRUD')
        ClearForm(modalCRUD);
        if($(this).attr('title')){
            title = $(this).attr('title');
            console.log(`#modal${title}`);
            $(`#modalTitle1${modalCRUD}`).hide();
            $(`#modalTitle2${modalCRUD}`).hide();
            $(`#modalTitle3${modalCRUD}`).hide();
            $(`#modal${title + modalCRUD}`).show();
        }else{
            $(`#modalTitle1${modalCRUD}`).show();
            $(`#modalTitle2${modalCRUD}`).hide();
            $(`#modalTitle3${modalCRUD}`).hide();
        }
        
        var formbloque = ($(`#form${modalCRUD}`).attr('bloque') || "") + "/";

        $(`#form${modalCRUD} button[type="submit"]`).show();
        $(`#form${modalCRUD} input`).prop('disabled', false);
        $(`#form${modalCRUD} select`).prop('disabled', false);
        $(`#form${modalCRUD} textarea`).prop('disabled', false);
        $(`#form${modalCRUD} input[type="checkbox"]`).prop('disabled', false);

        $(`#form${modalCRUD} .OnEditReadOnly`).prop('disabled', false);
        $(`#form${modalCRUD} .OnEditReadOnly`).prop('required', true);
        $(`#form${modalCRUD} .OnAddReadOnly`).prop('disabled', true);
        $(`#form${modalCRUD} .OnAddReadOnly`).prop('required', false);
        //$(`#form${modalCRUD} .OnEditReadOnly`).removeClass('readonly-input');
        $(`#modalCRUD${modalCRUD}`).modal('show');

        const form = $(`#form${modalCRUD}`);
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition((position) => {
                form.find('.GETLatitud').val(position.coords.latitude);
                form.find('.GETLongitud').val(position.coords.longitude);
            });
        }

        var row = $(this).closest('tr');
        var firstColumnValue = row.find('td:first').text();


        $(`#form${modalCRUD}`).off('submit').on('submit', function(event) {
            event.preventDefault();
            var formDataJson = {};
            //var formDataJson = new FormData();
            var form_error = false;
            $(`#form${modalCRUD} input, #form${modalCRUD} select, #form${modalCRUD} textarea`).each(function() {
                var id = $(this).attr('id');
                var value;

                // Quitar "-SetData" del ID si existe
                if (id && id.includes("-SetData")) {
                    id = id.replace("-SetData", ""); // Elimina "-SetData" del ID
                }

            
                // Para checkboxes, obtenemos el estado (checked) en lugar del valor
                if ($(this).is(':checkbox')) {
                    value = $(this).is(':checked') ? 1 : 0; // o el valor que desees almacenar
                } else {
                    value = $(this).val();
                }
            
                if (id) { 
                    if ($(this).is(':required') && !$(this).is(':disabled') && $(this).is(':visible') && (value === "" || value === null || value === undefined || ($(this).is(':checkbox') && !$(this).is(':checked')))) { 
                        // Manejo de inputs de texto, selects y checkboxes
                        form_error = true;
                        $(`#error_${id}`).text("Campo Obligatorio");
                    } else {
                        $(`#error_${id}`).text("");
                        //$(this).removeClass('is-invalid');
                        if ($(this).is(':checkbox')) {
                            formDataJson[id] = $(this).is(':checked') ? 1 : 0; // Almacena 1 si está activado, 0 si no
                            //formDataJson.append(id, $(this).is(':checked') ? 1 : 0);
                        } else {
                            //formDataJson.append(id, value);
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
            
            var additionalData = new FormData();
            additionalData.append('opcion', 1);
            additionalData.append('formDataJson', JSON.stringify(formDataJson));
            additionalData.append('modalCRUD', modalCRUD);
            additionalData.append('AlertDataSimilar', AlertDataSimilar);
            additionalData.append('firstColumnValue', firstColumnValue);

            // Adjuntar cada imagen individualmente
            if (imagenesGlobal && imagenesGlobal.length > 0) {
                imagenesGlobal.forEach((imagen, index) => {
                    additionalData.append(`imgs[${index}]`, imagen); // Adjuntar cada archivo con una clave única
                });
                console.log('formDataJson:', formDataJson);
            }

            console.log('Datos adicionales:', additionalData);
            //var additionalData = { opcion: 1, formDataJson: formDataJson, modalCRUD: modalCRUD, AlertDataSimilar: AlertDataSimilar, firstColumnValue:firstColumnValue };
            console.log('AlertDataSimilar');
            console.log(AlertDataSimilar);
            
        
            console.log('Formulario enviado para el modal:', modalCRUD);
            console.log('Datos adicionales:', additionalData);
        
            $.ajax({
                url: `/vistas/${formbloque}bd/crudSummit.php`, 
                type: "POST",
                data: additionalData,
                processData: false,   // No procesar los datos
                contentType: false,
                dataType: "json",
                success: function(response) {
                    if(response.status == "error"){
                        if(response.checkdata){
                            checkdata = response.checkdata;
                            
                            
                            if(checkdata.DataExist){
                                DataExist = checkdata.DataExist
                                console.log(DataExist);
                                for (const id of DataExist) {
                                    
                                    subfix = '-SetData';
                                    if(!$(`#${id}`)){
                                        subfix = '-SetData';
                                    }
                                    console.log($(`#${id+subfix}`));
                                    
                                    valor = $(`#${id+subfix}`).val();
                                    $(`#${id+subfix}`).val("");
                                    $(`#error_${id+subfix}`).text(`El valor "${valor}" ya existe.`);
                                    $(`#form${modalCRUD}`).removeAttr('alertdatasimilar');
                                }
                            }

                            if(checkdata.DataSimilar){
                                DataSimilar = checkdata.DataSimilar
                                for (const id in DataSimilar) {
                                    data = DataSimilar[id];

                                    subfix = '-SetData';
                                    if(!$(`#${id}`)){
                                        subfix = '-SetData';
                                    }
                                    valor = $(`#${id+subfix}`).val();
                                    //$(`#${id}`).val("");
                                    $(`#validate_${id+subfix}`).text(`Elementos similares: ${data.join(", ")}`);
                                    $(`#alertmsg_${modalCRUD}`).text(`Ya existen elementos similares registrados, si deseas agregar el elemento actual vuelve a guardarlo.`);
                                    $(`#alert_${modalCRUD}`).show();
                                    $(`#form${modalCRUD}`).attr('alertdatasimilar',true);
                                }
                            }
                        
                        }
                    }else{
                        $(`#modalCRUD${modalCRUD}`).modal('hide'); // Cerrar el modal después de enviar
                        //window.location.reload();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error en la solicitud:', error);
                }
            });
        });
    });

    $(document).on('click', '.UpdateEstatus', function() {
        var modalCRUD = $(this).attr('modalCRUD');
        tablemodalCRUD = $(this).closest('table').attr('id').replace('tabla', '');
        console.log(tablemodalCRUD);
        
        var formbloque = ($(this).attr('bloque') || "") + "/";
        var row = $(this).closest('tr');
        var firstColumnValue = row.find('td:first').text();
        var name = $(this).attr('name')
        var formDataJson = {};
        formDataJson['UpdateEstatus'] = name;
        var additionalData = { opcion: 2, formDataJson: formDataJson, modalCRUD: modalCRUD,firstColumnValue:firstColumnValue };

    
        console.log('Formulario enviado para el modal:', modalCRUD);
        console.log('Datos adicionales:', additionalData);
    
        $.ajax({
            url: `/vistas/${formbloque}bd/crudSummit.php`, 
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
                    
                    }else{
                        console.log(response);
                        
                        addAlert(response.message);
                    }
                }else{
                    if(response.data != 'NoChanges'){
                        data = Object.values(response.data || {});    
                        UpdateRow(tablemodalCRUD,row,data)
                    }
                    
                    //window.location.reload();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la solicitud:', error);
            }
        });
    });
    
    

    $(document).on('click', '.ModalDataView', function(){
        var modalCRUD = $(this).attr('modalCRUD');
        
        ClearForm(modalCRUD);
        $(`#modalTitle2${modalCRUD}`).hide();
        $(`#modalTitle1${modalCRUD}`).hide();
        $(`#modalTitle3${modalCRUD}`).show();
        $('.OnlyInEdit').parent().hide();
        $('.OnlyInEdit').next('br').hide();
        

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
            url: `/vistas/${formbloque}bd/crudEndpoint.php`,
            type: "POST",
            dataType: "json",
            data: data,
            success: function(response){  
                console.log(response);
                if(response.data){
                    for (var key in response.data) { 
                        //console.log(response.data[key]);
                        //updateCarousel(fotoadd)
                        if(key == 'imgs'){
                            let fotos = response.data[key]
                            for (var fotokey in fotos) { 
                                //console.log(fotos[fotokey]);
                                updateCarousel(fotos[fotokey])
                                
                            }

                        }else if (response.data.hasOwnProperty(key)) {
                            // Selecciona el input o select usando el key
                            var inputElement = $('[id="' + key + '"]'); // Asegúrate de que los inputs/selects tengan el atributo name correspondiente
                            
                            if (inputElement.length) {
                                // Agregar el atributo disabled
                                inputElement.prop('disabled', true);
                                
                                // Verifica si el input es un checkbox
                                if (inputElement.is(':checkbox')) {
                                    // Establece el estado del checkbox basado en el valor de la respuesta
                                    inputElement.prop('checked', response.data[key] === 1);
                                } else if (inputElement.is('select')) {
                                    if (inputElement.find('option').length / inputElement.length === 1) {
                                        // Si no hay opciones, crea una nueva opción
                                        inputElement.find('option').remove();
                                        inputElement.append($('<option>', {
                                            value: '', // No tiene valor seleccionable
                                            text: response.data[key], // Muestra el texto que deseas
                                            disabled: true, // Deshabilita la opción
                                            selected: true // Selecciona esta opción por defecto
                                        }));
                                    } else {
                                        // Deselecciona todos los options del select
                                        inputElement.find('option:selected').prop('selected', false);
                                        inputElement.val(response.data[key]);
                                    }
                                } else if (inputElement.is('input[type="date"]')) {
                                    // Si el input es de tipo date, convierte el valor a un formato aceptable
                                    if (response.data[key] != null){
                                        const dateValue = new Date(response.data[key]);
                                        const formattedDate = dateValue.toISOString().split('T')[0];
                                        inputElement.val(formattedDate);
                                    }
                                    
                                }else {
                                    console.log("entro al else");
                                    console.log(inputElement);
                                    
                                    // Establece el valor del input
                                    inputElement.val(response.data[key]);
                                }
                            }else{
                                console.log('key');
                                console.log(key);
                                if ($(`[id="${key}_date"]`).is('input[type="date"]')) {
                                    // Si el input es de tipo date, convierte el valor a un formato aceptable
                                    if (response.data[key] != null){
                                        const dateValue = new Date(response.data[key]);

                                        // Formateo de fecha y asignación al campo de fecha
                                        const formattedDate = dateValue.toISOString().split('T')[0];
                                        $(`[id="${key}_date"]`).val(formattedDate);

                                        // Formateo de hora y asignación al campo de hora
                                        const formattedTime = dateValue.toISOString().split('T')[1].substring(0, 8);
                                        console.log(formattedTime);
                                        
                                        $(`[id="${key}_time"]`).val(formattedTime);
                                    }
                                    
                                }else if ($(`[id="${key}_time"]`).is('input[type="time"]')) {
                                    // Si el input es de tipo date, convierte el valor a un formato aceptable
                                    if (response.data[key] != null){
                                        const dateValue = new Date(response.data[key]);
                                        const formattedTime = dateValue.toISOString().split('T')[1].substring(0, 8);;
                                        $(`[id="${key}_time"]`).val(formattedTime);
                                    }
                                    
                                }
                            }
                        }
                    }
                    // Deshabilitar el botón de submit
                    $(`#form${modalCRUD} button[type="submit"]`).hide();
                    
                    $(`#form${modalCRUD} .OnEditReadOnly`).prop('disabled', true);
                    $(`#form${modalCRUD} .OnEditReadOnly`).prop('required', false);
                    $(`#form${modalCRUD} .OnAddReadOnly`).prop('disabled', true);
                    $(`#form${modalCRUD} .OnAddReadOnly`).prop('required', false);
                    //$(`#form${modalCRUD} .OnEditReadOnly`).addClass('readonly-input');
                    $(`#modalCRUD${modalCRUD}`).modal('show');
                }
            }
        });
        $('.VerificarCambioMostrar').each(function() {
            VerificarCambioMostrar.call(this); // Asegúrate de que se llame con el contexto correcto
        });

        $(`#form${modalCRUD}`).on('submit', function(event) {
            event.preventDefault(); // Previene el envío del formulario
        });
    });

    // Función para manejar el clic en el botón "Editar"
    $(document).on('click', '.ModalDataEdit', function(){
        var modalCRUD = $(this).attr('modalCRUD');
        var data_actual = {};
        
        ClearForm(modalCRUD);
        $(`#modalTitle2${modalCRUD}`).show();
        $(`#modalTitle1${modalCRUD}`).hide();
        $(`#modalTitle3${modalCRUD}`).hide();

        var formbloque = ($(`#form${modalCRUD}`).attr('bloque') || "") + "/";

        $(`#form${modalCRUD} button[type="submit"]`).show();

        $(`#form${modalCRUD} input`).prop('disabled', false);
        $(`#form${modalCRUD} select`).prop('disabled', false);
        $(`#form${modalCRUD} textarea`).prop('disabled', false);
        $(`#form${modalCRUD} input[type="checkbox"]`).prop('disabled', false);

        var row = $(this).closest('tr');
        var firstColumnValue = row.find('td:first').text();
        console.log('Editar botón clickeado con ID:', modalCRUD);
        console.log('Valor de la primera columna:', firstColumnValue);
        now_editing = firstColumnValue;

        $('.OnlyInEdit').parent().show();
        $('.OnlyInEdit').next('br').show();
        var data = {
            modalCRUD: modalCRUD.split('-')[0],
            firstColumnValue: firstColumnValue,
        };

        $.ajax({
            url: `/vistas/${formbloque}bd/crudEndpoint.php`,
            type: "POST",
            dataType: "json",
            data: data,
            success: function(response){  
                console.log(response);
                if(response.data){
                    data_actual = response.data
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
                                    if (Array.isArray(response.data[key])) {
                                        // Si el valor es un arreglo, agregar opciones al select
                                        inputElement.empty(); // Limpia las opciones existentes
                                        response.data[key].forEach(option => {
                                            console.log(option);
                                            
                                            inputElement.append(new Option(option.text, option.valor));
                                        });
                                    } else if (inputElement.find('option').length / inputElement.length === 1) {
                                        inputElement.find('option').remove();
                                        // Si no hay opciones, crea una nueva opción
                                        inputElement.append($('<option>', {
                                            value: response.data[key], // No tiene valor seleccionable
                                            text: response.data[key], // Muestra el texto que deseas
                                            disabled: false, // Deshabilita la opción
                                            selected: true // Selecciona esta opción por defecto
                                        }));
                                    } else {
                                        // Deselecciona todos los options del select
                                        inputElement.find('option:selected').prop('selected', false);
                                        inputElement.val(response.data[key]);
                                    }
                                } else if (inputElement.is('input[type="date"]')) {
                                    // Si el input es de tipo date, convierte el valor a un formato aceptable
                                    if (response.data[key] != null){
                                        const dateValue = new Date(response.data[key]);
                                        const formattedDate = dateValue.toISOString().split('T')[0];
                                        inputElement.val(formattedDate);
                                    }
                                    
                                }else {
                                    console.log("entro al else");
                                    console.log(inputElement);
                                    
                                    
                                    // Establece el valor del input
                                    inputElement.val(response.data[key]);
                                }
                            }else{
                                console.log('key');
                                console.log(key);
                                if ($(`[id="${key}_date"]`).is('input[type="date"]')) {
                                    // Si el input es de tipo date, convierte el valor a un formato aceptable
                                    if (response.data[key] != null){
                                        const dateValue = new Date(response.data[key]);

                                        // Formateo de fecha y asignación al campo de fecha
                                        const formattedDate = dateValue.toISOString().split('T')[0];
                                        $(`[id="${key}_date"]`).val(formattedDate);

                                        // Formateo de hora y asignación al campo de hora
                                        const formattedTime = dateValue.toISOString().split('T')[1].substring(0, 8);
                                        console.log(formattedTime);
                                        
                                        $(`[id="${key}_time"]`).val(formattedTime);
                                    }
                                    
                                }else if ($(`[id="${key}_time"]`).is('input[type="time"]')) {
                                    // Si el input es de tipo date, convierte el valor a un formato aceptable
                                    if (response.data[key] != null){
                                        const dateValue = new Date(response.data[key]);
                                        const formattedTime = dateValue.toISOString().split('T')[1].substring(0, 8);;
                                        $(`[id="${key}_time"]`).val(formattedTime);
                                    }
                                    
                                }
                            }
                        }
                    }
                    $(`#form${modalCRUD} .OnEditReadOnly`).prop('readonly', true);
                    $(`#form${modalCRUD} .OnEditReadOnly`).prop('readonly', false);
                    //$(`#form${modalCRUD} .OnEditReadOnly`).addClass('readonly-input');
                    $(`#modalCRUD${modalCRUD}`).modal('show');
                }
            }
        });
        
        $('.VerificarCambioMostrar').each(function() {
            VerificarCambioMostrar.call(this); // Asegúrate de que se llame con el contexto correcto
        });

        $(`#form${modalCRUD}`).off('submit').on('submit', function(event) {
            event.preventDefault();
            var formDataJson = {};
            var form_error = false;
            $(`#form${modalCRUD} input, #form${modalCRUD} select, #form${modalCRUD} textarea`).each(function() {
                var id = $(this).attr('id');
                var value;
            
                // Para checkboxes, obtenemos el estado (checked) en lugar del valor
                if ($(this).is(':checkbox')) {
                    value = $(this).is(':checked') ? 'on' : ''; // o el valor que desees almacenar
                } else {
                    value = $(this).val();
                }
            
                if (id) { 
                    if ($(this).is(':required') && !$(this).is(':disabled') && $(this).is(':visible') && (value === "" || value === null || value === undefined || ($(this).is(':checkbox') && !$(this).is(':checked')))) { 
                        // Manejo de inputs de texto, selects y checkboxes
                        form_error = true;
                        $(`#error_${id}`).text("Campo Obligatorio");
                        console.log(id);
                        console.log(id);
                        
                    } else if(!$(this).prop('disabled')) {
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
            data_actual
            var AlertDataSimilar = $(`#form${modalCRUD}`).attr('alertdatasimilar') === 'true';
            var additionalData = { opcion: 2, formDataJson: formDataJson,formDataOldJson:data_actual, modalCRUD:  modalCRUD.split('-')[0],firstColumnValue:firstColumnValue, AlertDataSimilar:AlertDataSimilar};
        
            console.log('Formulario enviado para el modal:', modalCRUD);
            console.log('Datos adicionales:', additionalData);
            
            
            $.ajax({
                url: `/vistas/${formbloque}bd/crudSummit.php`, 
                type: "POST",
                dataType: "json",
                data: additionalData,
                success: function(response) {
                    if(response.status == "error"){
                        console.log('Respuesta error 1 del servidor:', response);
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
                                    $(`#alert_${modalCRUD}`).removeClass(function(index, className) {
                                        return (className.match(/(bg-\S+|alert-(?!dismissible)\S+)/g) || []).join(' ');
                                    });
                                    var tipoalerta = 'warning';
                                    $(`#alert_${modalCRUD}`).addClass(`alert-${tipoalerta}`);
                                    $(`#alert_${modalCRUD}`).addClass(`bg-${tipoalerta}`);
                                    $(`#alertmsg_${modalCRUD}`).text(`Ya existen elementos similares registrados, si deseas agregar el elemento actual vuelve a guardarlo.`);
                                    $(`#alert_${modalCRUD}`).show();
                                    $(`#form${modalCRUD}`).attr('alertdatasimilar',true);
                                }
                            }
                        
                        }else{
                            $(`#alert_${modalCRUD}`).removeClass(function(index, className) {
                                return (className.match(/(bg-\S+|alert-(?!dismissible)\S+)/g) || []).join(' ');
                            });
                            var tipoalerta = 'danger';
                            $(`#alert_${modalCRUD}`).addClass(`alert-${tipoalerta}`);
                            $(`#alert_${modalCRUD}`).addClass(`bg-${tipoalerta}`);
                            $(`#alertmsg_${modalCRUD}`).text(response.message);
                            $(`#alert_${modalCRUD}`).show();
                        }
                        return;
                    }if(response.status == "nocambios"){
                        $(`#alert_${modalCRUD}`).removeClass(function(index, className) {
                            return (className.match(/(bg-\S+|alert-(?!dismissible)\S+)/g) || []).join(' ');
                        });
                        var tipoalerta = 'info';
                        $(`#alert_${modalCRUD}`).addClass(`alert-${tipoalerta}`);
                        $(`#alert_${modalCRUD}`).addClass(`bg-${tipoalerta}`);
                        $(`#alertmsg_${modalCRUD}`).text(response.message);
                        $(`#alert_${modalCRUD}`).show();
                        return;
                    }else{
                        console.log('Respuesta 1 del servidor:', response);
                        if(response.data != 'NoChanges'){
                            data = Object.values(response.data || {});
                            UpdateRow(modalCRUD.split('-')[0],row,data)
                        }
                        
                        $(`#modalCRUD${modalCRUD}`).modal('hide'); // Cerrar el modal después de enviar
                    }
                },
                error: function(xhr, status, error) {
                    $(`#alert_${modalCRUD}`).removeClass(function(index, className) {
                        return (className.match(/(bg-\S+|alert-(?!dismissible)\S+)/g) || []).join(' ');
                    });
                    var tipoalerta = 'danger';
                    $(`#alert_${modalCRUD}`).addClass(`alert-${tipoalerta}`);
                    $(`#alert_${modalCRUD}`).addClass(`bg-${tipoalerta}`);
                    $(`#alertmsg_${modalCRUD}`).text(`No se pudo editar el elemento seleciconado. Error: ${error}`);
                    $(`#alert_${modalCRUD}`).show();
                    console.error('Error en la solicitud:', error);
                }
            });
            console.log(" ");
        });

    });

    $(document).on('click', '.ModalDataAdd', function() {
        var modalCRUD = $(this).attr('modalCRUD')
        ClearForm(modalCRUD);
        $(`#modalTitle1${modalCRUD}`).show();
        $(`#modalTitle2${modalCRUD}`).hide();
        $(`#modalTitle3${modalCRUD}`).hide();
        var formbloque = ($(`#form${modalCRUD}`).attr('bloque') || "") + "/";

        $(`#form${modalCRUD} button[type="submit"]`).show();
        $(`#form${modalCRUD} input`).prop('disabled', false);
        $(`#form${modalCRUD} select`).prop('disabled', false);
        $(`#form${modalCRUD} textarea`).prop('disabled', false);
        $(`#form${modalCRUD} input[type="checkbox"]`).prop('disabled', false);

        $(`#form${modalCRUD} .OnEditReadOnly`).prop('disabled', false);
        $(`#form${modalCRUD} .OnEditReadOnly`).prop('required', true);
        $(`#form${modalCRUD} .OnAddReadOnly`).prop('disabled', true);
        $(`#form${modalCRUD} .OnAddReadOnly`).prop('required', false);
        $('.OnlyInEdit').parent().hide();
        $('.OnlyInEdit').next('br').hide();

        const form = $(`#form${modalCRUD}`);
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition((position) => {
                form.find('.GETLatitud').val(position.coords.latitude);
                form.find('.GETLongitud').val(position.coords.longitude);
            });
        }



        
        //$(`#form${modalCRUD} .OnEditReadOnly`).removeClass('readonly-input');

        const data_select_column = $(`#form${modalCRUD}`).attr('data-select-column') || false;
        if (data_select_column != false) {
            const tabla_id = `#tabla${modalCRUD}`;
            const row_data = $(tabla_id).attr('data-input-fill') || false;
            var data_get_list = data_select_column.replace(/[\[\]']+/g, '').split(',').map(function(item) {
                return item.trim(); // Elimina espacios en blanco alrededor de cada elemento
            });
            const data_input_fill = $(`#form${modalCRUD}`).attr('data-input-fill') || false;

            if (data_input_fill != false && row_data != false) {
                var data_inputs_get_list = data_input_fill.replace(/[\[\]']+/g, '').split(',').map(function(item) {
                    return item.trim(); // Elimina espacios en blanco alrededor de cada elemento
                });
                var row_data_list = row_data.replace(/[\[\]']+/g, '').split(',').map(function(item) {
                    return item.trim(); // Elimina espacios en blanco alrededor de cada elemento
                });
                
                for(i=0;i<data_get_list.length;i++){
                    $(`[id='${data_inputs_get_list[i]}']`).val(row_data_list[data_get_list[i]])
                    console.log(data_inputs_get_list[i]);
                    console.log(data_get_list[i]);
                    
                }
            }
            
        }

        $(`#modalCRUD${modalCRUD}`).modal('show');
        $('.VerificarCambioMostrar').each(function() {
            VerificarCambioMostrar.call(this); // Asegúrate de que se llame con el contexto correcto
        });
        
        

        $(`#form${modalCRUD}`).off('submit').on('submit', function(event) {
            event.preventDefault();
            var formDataJson = {};
            var form_error = false;
            var additionalData = new FormData();
            $(`#form${modalCRUD} input, #form${modalCRUD} select, #form${modalCRUD} textarea`).each(function() {
                var id = $(this).attr('id');
                var value;
            
                // Para checkboxes, obtenemos el estado (checked) en lugar del valor
                if ($(this).is(':checkbox')) {
                    value = $(this).is(':checked') ? 1 : 0; // o el valor que desees almacenar
                } else {
                    value = $(this).val();
                }
            
                if (id) { 
                    if ($(this).is(':required') && !$(this).is(':disabled') && $(this).is(':visible') && (value === "" || value === " " || value === null || value === undefined || ($(this).is(':checkbox') && !$(this).is(':checked')))) { 
                        // Manejo de inputs de texto, selects y checkboxes
                        form_error = true;
                        $(`#error_${id}`).text("Campo Obligatorio");
                    } else {
                        $(`#error_${id}`).text("");
                        //$(this).removeClass('is-invalid');
                        if ($(this).is(':checkbox')) {
                            formDataJson[id] = $(this).is(':checked') ? 1 : 0; // Almacena 1 si está activado, 0 si no
                        } else {
                            if($(this).is('input[type="file"]')){
                                const archivo = $(this)[0].files[0];
                                additionalData.append(id, archivo);
                            }else{
                                formDataJson[id] = value; // Almacena el valor de otros inputs
                            }
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
        
            //var additionalData = { opcion: 1, formDataJson: formDataJson, modalCRUD: modalCRUD, AlertDataSimilar: AlertDataSimilar };
            
            additionalData.append('opcion', 1);
            additionalData.append('formDataJson', JSON.stringify(formDataJson));
            additionalData.append('modalCRUD', modalCRUD);
            additionalData.append('AlertDataSimilar', AlertDataSimilar);

            console.log('AlertDataSimilar');
            console.log(AlertDataSimilar);
            
        
            console.log('Formulario enviado para el modal:', modalCRUD);
            console.log('Datos adicionales:', additionalData);
        
            $.ajax({
                url: `/vistas/${formbloque}bd/crudSummit.php`, 
                type: "POST",
                data: additionalData,
                processData: false,   // No procesar los datos
                contentType: false,
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
                                    $(`#alert_${modalCRUD}`).removeClass(function(index, className) {
                                        return (className.match(/(bg-\S+|alert-(?!dismissible)\S+)/g) || []).join(' ');
                                    });
                                    var tipoalerta = 'warning';
                                    $(`#alert_${modalCRUD}`).addClass(`alert-${tipoalerta}`);
                                    $(`#alert_${modalCRUD}`).addClass(`bg-${tipoalerta}`);
                                    $(`#alertmsg_${modalCRUD}`).text(`Ya existen elementos similares registrados, si deseas agregar el elemento actual vuelve a guardarlo.`);
                                    $(`#alert_${modalCRUD}`).show();
                                    $(`#form${modalCRUD}`).attr('alertdatasimilar',true);
                                }
                            }
                        
                        }else{
                            $(`#alert_${modalCRUD}`).removeClass(function(index, className) {
                                return (className.match(/(bg-\S+|alert-(?!dismissible)\S+)/g) || []).join(' ');
                            });
                            var tipoalerta = 'danger';
                            $(`#alert_${modalCRUD}`).addClass(`alert-${tipoalerta}`);
                            $(`#alert_${modalCRUD}`).addClass(`bg-${tipoalerta}`);
                            $(`#alertmsg_${modalCRUD}`).text(response.message);
                            $(`#alert_${modalCRUD}`).show();
                        }
                    }else{
                        data = Object.values(response.data || {});
                        try {
                            AddRow(modalCRUD, data);
                        } catch (e) {
                            console.error('Error en AddRow:', e);
                            // Cerrar el modal aunque AddRow falle
                            $(`#modalCRUD${modalCRUD}`).modal('hide');
                        }
                        $(`#modalCRUD${modalCRUD}`).modal('hide'); // Cerrar el modal después de enviar
                    }
                },
                error: function(xhr, status, error) {
                    $(`#alert_${modalCRUD}`).removeClass(function(index, className) {
                        return (className.match(/(bg-\S+|alert-(?!dismissible)\S+)/g) || []).join(' ');
                    });
                    var tipoalerta = 'danger';
                    $(`#alert_${modalCRUD}`).addClass(`alert-${tipoalerta}`);
                    $(`#alert_${modalCRUD}`).addClass(`bg-${tipoalerta}`);
                    $(`#alertmsg_${modalCRUD}`).text(`No se pudo guardar el nuevo elemento. Error: ${error}`);
                    $(`#alert_${modalCRUD}`).show();
                    console.error('Error en la solicitud:', error);
                }
            });
        });

    });

    $(document).on('click', '.CloseAlertBox', function() {
        var alertBoxId = $(this).closest('.alert').attr('id');
        console.log(alertBoxId);
        $('#' + alertBoxId).hide();
    });

    <?php if(isset($NewAdd1) && $NewAdd1):?>
    $(document).on('click', '.ModalNewAdd1', function() {
        var modalCRUD = $(this).attr('modalCRUD')
        ClearForm(modalCRUD);
        $(`#modalTitle1${modalCRUD}`).show();
        $(`#modalTitle2${modalCRUD}`).hide();
        $(`#modalTitle3${modalCRUD}`).hide();
        var formbloque = ($(`#form${modalCRUD}`).attr('bloque') || "") + "/";

        $(`#form${modalCRUD} button[type="submit"]`).show();
        $(`#form${modalCRUD} input`).prop('disabled', false);
        $(`#form${modalCRUD} select`).prop('disabled', false);
        $(`#form${modalCRUD} input[type="checkbox"]`).prop('disabled', false);

        $(`#form${modalCRUD} .OnEditReadOnly`).prop('disabled', false);
        $(`#form${modalCRUD} .OnEditReadOnly`).prop('required', true);
        $(`#form${modalCRUD} .OnAddReadOnly`).prop('disabled', true);
        $(`#form${modalCRUD} .OnAddReadOnly`).prop('required', false);
        //$(`#form${modalCRUD} .OnEditReadOnly`).removeClass('readonly-input');
        $(`#modalCRUD${modalCRUD}`).modal('show');

        var row = $(this).closest('tr');
        var firstColumnValue = row.find('td:first').text();
        <?php 
        // Supongamos que $NewAdd1['data_list_column'] es un array que contiene los IDs de los inputs y los índices de las columnas de la tabla.
        foreach($NewAdd1['data_list_column'] as $data_list_column => $value) {
            // Asegúrate de que $data_list_column sea un ID válido y que $value sea un índice de columna válido
            echo '$("#' . htmlspecialchars($data_list_column) . '").val(row.find("td").eq(' . intval($value) . ').text());' . "\n";
            echo 'console.log("Filling input with ID: ' . htmlspecialchars($data_list_column) . ' with value: " + row.find("td").eq(' . intval($value) . ').text());' . "\n";
        }
        ?>

        
        

        $(`#form${modalCRUD}`).off('submit').on('submit', function(event) {
            event.preventDefault();
            var formDataJson = {};
            var form_error = false;
            $(`#form${modalCRUD} input, #form${modalCRUD} select, #form${modalCRUD} textarea`).each(function() {
                var id = $(this).attr('id');
                var value;

                // Quitar "-NewAdd1" del ID si existe
                if (id && id.includes("-NewAdd1")) {
                    id = id.replace("-NewAdd1", ""); // Elimina "-NewAdd1" del ID
                }

            
                // Para checkboxes, obtenemos el estado (checked) en lugar del valor
                if ($(this).is(':checkbox')) {
                    value = $(this).is(':checked') ? 1 : 0; // o el valor que desees almacenar
                } else {
                    value = $(this).val();
                }
            
                if (id) { 
                    if ($(this).is(':required') && !$(this).is(':disabled') && $(this).is(':visible') && (value === "" || value === " " || value === null || value === undefined || ($(this).is(':checkbox') && !$(this).is(':checked')))) { 
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
                url: `/vistas/${formbloque}bd/crudSummit.php`, 
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
                        $(`#modalCRUD${modalCRUD}`).modal('hide'); // Cerrar el modal después de enviar
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error en la solicitud:', error);
                }
            });
        });
    });
    <?php endif ?>

    <?php if(isset($NewAdd2) && $NewAdd2):?>
    $(document).on('click', '.ModalNewAdd2', function() {
        var modalCRUD = $(this).attr('modalCRUD');
        
        ClearForm(modalCRUD);
        $(`#modalTitle2${modalCRUD}`).hide();
        $(`#modalTitle1${modalCRUD}`).show();
        $(`#modalTitle3${modalCRUD}`).hide();

        var formbloque = ($(`#form${modalCRUD}`).attr('bloque') || "") + "/";

        $(`#form${modalCRUD} button[type="submit"]`).show();

        $(`#form${modalCRUD} input`).prop('disabled', false);
        $(`#form${modalCRUD} select`).prop('disabled', false);
        $(`#form${modalCRUD} input[type="checkbox"]`).prop('disabled', false);
        var row = $(this).closest('tr');
        var firstColumnValue = row.find('td:first').text();

        <?php 
        // Supongamos que $NewAdd2['data_list_column'] es un array que contiene los IDs de los inputs y los índices de las columnas de la tabla.
        foreach($NewAdd2['data_list_column'] as $data_list_column => $value) {
            // Asegúrate de que $data_list_column sea un ID válido y que $value sea un índice de columna válido
            echo '$("#' . htmlspecialchars($data_list_column) . '").val(row.find("td").eq(' . intval($value) . ').text());' . "\n";
            echo 'console.log("Filling input with ID: ' . htmlspecialchars($data_list_column) . ' with value: " + row.find("td").eq(' . intval($value) . ').text());' . "\n";
        }
        ?>

        
        console.log('Editar botón clickeado con ID:', modalCRUD);
        console.log('Valor de la primera columna:', firstColumnValue);
        var data = {
            modalCRUD: modalCRUD+"Complement",
            firstColumnValue: firstColumnValue,
        };

        $.ajax({
            url: `/vistas/${formbloque}bd/crudEndpoint.php`,
            type: "POST",
            dataType: "json",
            data: data,
            success: function(response){  
                console.log(response);
                if(response.data){
                    for (var key in response.data) {
                        if (response.data.hasOwnProperty(key)) {
                            // Selecciona el input o select usando el key
                            var inputElement = $('[id="' + key + '-NewAdd2"]'); // Asegúrate de que los inputs/selects tengan el atributo name correspondiente
                            
                            if (inputElement.length) {
                                // Verifica si el input es un checkbox
                                if (inputElement.is(':checkbox')) {
                                    // Establece el estado del checkbox basado en el valor de la respuesta
                                    inputElement.prop('checked', response.data[key] === 1);
                                } else if (inputElement.is('select')) {
                                    if (inputElement.find('option').length === 1) {
                                        // Si no hay opciones, crea una nueva opción
                                        inputElement.append($('<option>', {
                                            value: response.data[key], // No tiene valor seleccionable
                                            text: response.data[key], // Muestra el texto que deseas
                                            disabled: false, // Deshabilita la opción
                                            selected: true // Selecciona esta opción por defecto
                                        }));
                                    } else {
                                        // Deselecciona todos los options del select
                                        inputElement.find('option:selected').prop('selected', false);
                                        inputElement.val(response.data[key]);
                                    }
                                } else {
                                    console.log("entro al else");
                                    console.log(inputElement);
                                    
                                    
                                    // Establece el valor del input
                                    inputElement.val(response.data[key]);
                                }
                            }
                        }
                    }
                    $(`#form${modalCRUD} .OnEditReadOnly`).prop('disabled', true);
                    $(`#form${modalCRUD} .OnEditReadOnly`).prop('required', false);
                    $(`#form${modalCRUD} .OnAddReadOnly`).prop('disabled', true);
        $(`#form${modalCRUD} .OnAddReadOnly`).prop('required', false);
                    //$(`#form${modalCRUD} .OnEditReadOnly`).addClass('readonly-input');
                    $(`#modalCRUD${modalCRUD}`).modal('show');
                }
            }
        });
        
        $('.VerificarCambioMostrar').each(function() {
            VerificarCambioMostrar.call(this); // Asegúrate de que se llame con el contexto correcto
        });

        $(`#form${modalCRUD}`).off('submit').on('submit', function(event) {
            event.preventDefault();
            var formDataJson = {};
            var form_error = false;
            $(`#form${modalCRUD} input, #form${modalCRUD} select, #form${modalCRUD} textarea`).each(function() {
                var id = $(this).attr('id');
                var value;
                // Quitar "-NewAdd2" del ID si existe
                if (id && id.includes("-NewAdd2")) {
                    id = id.replace("-NewAdd2", ""); // Elimina "-NewAdd2" del ID
                }

            
                // Para checkboxes, obtenemos el estado (checked) en lugar del valor
                if ($(this).is(':checkbox')) {
                    value = $(this).is(':checked') ? 'on' : ''; // o el valor que desees almacenar
                } else {
                    value = $(this).val();
                }
            
                if (id) { 
                    if ($(this).is(':required') && !$(this).is(':disabled') && $(this).is(':visible') && (value === "" || value === null || value === undefined || ($(this).is(':checkbox') && !$(this).is(':checked')))) { 
                        // Manejo de inputs de texto, selects y checkboxes
                        form_error = true;
                        $(`#error_${id}`).text("Campo Obligatorio");
                        console.log(id);
                        console.log(id);
                        
                    } else if(!$(this).prop('disabled')) {
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
                url: `/vistas/${formbloque}bd/crudSummit.php`, 
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
                        data = Object.values(response.data || {});
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
    <?php endif ?>

    <?php if(isset($NewAdd3) && $NewAdd3):?>
    $(document).on('click', '.ModalNewAdd3', function() {
        var modalCRUD = $(this).attr('modalCRUD');
        
        console.log(modalCRUD);
        
        
        ClearForm(modalCRUD);
        $(`#modalTitle2${modalCRUD}-View`).hide();
        $(`#modalTitle1${modalCRUD}-View`).show();
        $(`#modalTitle3${modalCRUD}-View`).hide();

        var formbloque = ($(`#modalCRUD${modalCRUD}-View`).attr('bloque') || "") + "/";

        $(`#form${modalCRUD}-View button[type="submit"]`).show();

        $(`#form${modalCRUD}-View input`).prop('disabled', false);
        $(`#form${modalCRUD}-View select`).prop('disabled', false);
        $(`#form${modalCRUD}-View input[type="checkbox"]`).prop('disabled', false);
        const row = $(this).closest('tr');
        var firstColumnValue = row.find('td:first').text();

        <?php 
        // Supongamos que $NewAdd3['data_list_column'] es un array que contiene los IDs de los inputs y los índices de las columnas de la tabla.
        foreach($NewAdd3['data_list_column'] as $data_list_column => $value) {
            // Asegúrate de que $data_list_column sea un ID válido y que $value sea un índice de columna válido
            echo '$("#' . htmlspecialchars($data_list_column) . '").val(row.find("td").eq(' . intval($value) . ').text());' . "\n";
            echo 'console.log("Filling input with ID: ' . htmlspecialchars($data_list_column) . ' with value: " + row.find("td").eq(' . intval($value) . ').text());' . "\n";
        }
        ?>

        

        
        console.log('Editar botón clickeado con ID:', modalCRUD);
        console.log('Valor de la primera columna:', firstColumnValue);
        console.log('formbloque:', formbloque);
        
        var data = {
            modalCRUD: modalCRUD,
            firstColumnValue: firstColumnValue,
            opcion:2
        };

        $.ajax({
            url: `/vistas/${formbloque}bd/crudEndpoint.php`,
            type: "POST",
            dataType: "json",
            data: data,
            success: function(response) {  
                const tableId1 = $(row).closest("table").attr("id");
                tabla1 = dataTableInstances[tableId1]
                const rowIndex1 = tabla1.row(row).index();
                console.log(tableId1);
                console.log(rowIndex1);
                const tabla1_data_row = tabla1.row(rowIndex1).data().slice(0, -1);

                console.log(response);
                const tabla_id = `#tabla${modalCRUD}`;
                ClearTable(modalCRUD);
                var data = response.data;
                if(data && !data.options) {
                    data.forEach(fila => {
                        console.log(fila);
                        AddRow(modalCRUD, fila);
                    });
                }else{
                    if(data){
                        console.log(data);
                    
                    data.data.forEach(fila => {
                        console.log(fila);
                        AddRow(modalCRUD, fila);
                    });

                    Object.keys(data.options).forEach(key => {
                        console.log(key);
                        data_key = data.options[key]
                        const $select = $(`#${key}`);
                        data_key.forEach(option => {
                            const value = option.valor;
                            
                            console.log(value);
                            

                            // Establecer el valor del select
                            if ($select.length) {
                                if ($select.find(`option[value="${value}"]`).length) {
                                    // Si el valor existe, establecerlo
                                    $select.val(value);
                                } else {
                                    // Si el valor no existe, agregar la opción
                                    $select.append(new Option(value, value));
                                    $select.val(value); // Establecer el valor después de agregar la opción
                                }
                            }
                        });

                        
                    });
                    }
                }

                
                const column_id = $(tabla_id).attr('data-select-column') || 0; // Obtiene el índice de la columna o usa 0 por defecto
                console.log(column_id);
                
                const text_value = tabla1_data_row[column_id]; // Usa eq() para seleccionar la celda

                const text = $(`#modalTitle1${modalCRUD}-View`).text().split(' (')[0];
                $(`#modalTitle1${modalCRUD}-View`).text(`${text} (${text_value})`); // Concatenación simplificada
                $(`#modalCRUD${modalCRUD}-View`).modal('show');
                
                $(tabla_id).attr('data-input-fill', `${tabla1_data_row}`);
            }
        });
        
    });
    <?php endif ?>

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
                    if ($(this).is(':required') && !$(this).is(':disabled') && $(this).is(':visible') && (value.trim() === "" || ( $(this).is(':checkbox') && !$(this).is(':checked')))) { 
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
                url: `/vistas/${formbloque}bd/crudSummit.php`, 
                type: "POST",
                data: additionalData,
                dataType: "json",
                success: function(response) {
                    
                    data = Object.values(response.data || {});
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
            url: `/vistas/${formbloque}bd/crudSummit.php`, 
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
    //Validar Correo
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

    //Validar Telefono
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

    //Validar RFC
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
    
    //Validar CURP
    $('.ValidateCURP').on('change', function() {
        const curp = $(this).val().trim(); // Obtiene el valor y elimina espacios en blanco
        var id = $(this).attr('id');

        // Expresión regular para validar la CURP
        const curpPattern = /^[A-Z][AEIOU][A-Z]{2}[0-9]{2}(0[1-9]|1[0-2])(0[1-9]|[12][0-9]|3[01])[HM][A-Z]{2}[B-DF-HJ-NP-TV-Z]{3}([0-9]|[A-Z])[0-9]$/;

        if (curp !== "") { // Solo valida si el valor no está vacío
            if (!curpPattern.test(curp)) {
                $(`#error_${id}`).text('La CURP es inválida.'); // Asegúrate de que el selector sea correcto
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

    //Validar PWS
    $('.ValidatePWS').on('change', function() {
        const password = $(this).val().trim(); // Obtiene el valor y elimina espacios en blanco
        var id = $(this).attr('id');
        
        // Inicializa un array para almacenar los requisitos que faltan
        let missingRequirements = [];

        // Verifica los requisitos de la contraseña
        if (password.length < 7) {
            missingRequirements.push('Al menos 8 caracteres');
        }
        if (!/[a-z]/.test(password)) {
            missingRequirements.push('Una letra minúscula');
        }
        if (!/[A-Z]/.test(password)) {
            missingRequirements.push('Una letra mayúscula');
        }
        if (!/\d/.test(password)) {
            missingRequirements.push('Un número');
        }
        if (!/[@$!%*?&#]/.test(password)) {
            missingRequirements.push('Un carácter especial (por ejemplo, @$!%*?&)');
        }

        if (missingRequirements.length > 0) {
            // Si hay requisitos que faltan, muestra el mensaje de error
            $(`#error_${id}`).html('La contraseña no es segura. Faltan:<br>' + missingRequirements.join('<br>'));
            $(`#error_${id}`).show(); // Muestra el mensaje de error
            $(this).addClass('is-invalid'); // Agrega una clase para mostrar el error
        } else {
            $(`#error_${id}`).hide(); // Oculta el mensaje de error si es válido
            $(this).removeClass('is-invalid'); // Elimina la clase de error si es válido
            const password1 = $('#password1');
            const password2 = $('#password2');
            if (password1.val().trim() !== "" && password2.val().trim() !== "") { // Solo valida si ambos campos no están vacíos
                if (password1.val().trim() !== password2.val().trim()) { // Corregido para comparar los valores de las contraseñas
                    $(`#error_${password1.attr('id')}`).text('Las contraseñas no coinciden.'); // Mensaje de error
                    $(`#error_${password1.attr('id')}`).show(); // Muestra el mensaje de error
                    password1.addClass('is-invalid'); // Agrega una clase para mostrar el error

                    $(`#error_${password2.attr('id')}`).text('Las contraseñas no coinciden.'); // Mensaje de error
                    $(`#error_${password2.attr('id')}`).show(); // Muestra el mensaje de error
                    password2.addClass('is-invalid'); // Agrega una clase para mostrar el error
                } else {
                    $(`#error_${password1.attr('id')}`).hide(); // Oculta el mensaje de error si son iguales
                    password1.removeClass('is-invalid'); // Elimina la clase de error si son iguales
                    $(`#error_${password2.attr('id')}`).hide(); // Oculta el mensaje de error si son iguales
                    password2.removeClass('is-invalid'); // Elimina la clase de error si son iguales
                }
            } else {
                $(`#error_${id}`).hide(); // Oculta el mensaje de error si alguno de los campos está vacío
                $(this).removeClass('is-invalid'); // Elimina la clase de error si alguno de los campos está vacío
            }
        }
    });

    // Validar número de cuenta
    $('.ValidateAccountNumber').on('change', function() {
        const accountNumber = $(this).val().trim();
        var id = $(this).attr('id');
        
        // Verificar si el campo no está vacío
        if (accountNumber === '') {
            $(`#error_${id}`).hide(); // Ocultar el mensaje de error si está vacío
            $(this).removeClass('is-invalid');
            return; // Salir de la función
        }

        // Expresión regular para validar un número de cuenta (ejemplo: 10 dígitos)
        const accountNumberRegex = /^\d{10}$/;

        if (!accountNumberRegex.test(accountNumber)) {
            $(`#error_${id}`).html('Número de cuenta inválido. Debe tener 10 dígitos.');
            $(`#error_${id}`).show();
            $(this).addClass('is-invalid');
        } else {
            $(`#error_${id}`).hide();
            $(this).removeClass('is-invalid');
        }
    });

    // Validar CLABE
    $('.ValidateCLABE').on('change', function() {
        const clabe = $(this).val().trim();
        var id = $(this).attr('id');
        
        // Verificar si el campo no está vacío
        if (clabe === '') {
            $(`#error_${id}`).hide(); // Ocultar el mensaje de error si está vacío
            $(this).removeClass('is-invalid');
            return; // Salir de la función
        }

        // Expresión regular para validar una CLABE (18 dígitos)
        const clabeRegex = /^\d{18}$/;

        if (!clabeRegex.test(clabe)) {
            $(`#error_${id}`).html('CLABE inválida. Debe tener 18 dígitos.');
            $(`#error_${id}`).show();
            $(this).addClass('is-invalid');
        } else {
            $(`#error_${id}`).hide();
            $(this).removeClass('is-invalid');
        }
    });

    // Validar número de tarjeta
    $('.ValidateCardNumber').on('change', function() {
        const cardNumber = $(this).val().trim();
        var id = $(this).attr('id');
        
        // Verificar si el campo no está vacío
        if (cardNumber === '') {
            $(`#error_${id}`).hide(); // Ocultar el mensaje de error si está vacío
            $(this).removeClass('is-invalid');
            return; // Salir de la función
        }

        // Expresión regular para validar un número de tarjeta (16 dígitos)
        const cardNumberRegex = /^\d{16}$/;

        if (!cardNumberRegex.test(cardNumber)) {
            $(`#error_${id}`).html('Número de tarjeta inválido. Debe tener 16 dígitos.');
            $(`#error_${id}`).show();
            $(this).addClass('is-invalid');
        } else {
            $(`#error_${id}`).hide();
            $(this).removeClass('is-invalid');
        }
    });


    /************************************* Fin Validaciones de inputs *************************************/

    $('#changepass').on('submit', function(event) {
        var pass1 = $('#password1').val();
        var pass2 = $('#password2').val();
        var passw = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z0-9])(?!.*\s).{8,15}$/;

        if (!pass1.match(passw)) {
            alert('¡Error en contraseña! Revisar las reglas');
            event.preventDefault(); // Prevenir el envío del formulario
        } else if (pass1 !== pass2) {
            alert("Las contraseñas son diferentes");
            event.preventDefault(); // Prevenir el envío del formulario
        }
    });
    
    $(".filter-input").on("keyup", function() {
        const index = $(this).data("index");
        const filter = $(this).val().toLowerCase();
        const rows = $("#tabla' . htmlspecialchars($id) . ' tbody tr");
        
        rows.each(function() {
            const cell = $(this).find("td[data-columns=\'[" + index + "]\']");
            if (cell.length) {
                const text = cell.text().toLowerCase();
                $(this).toggle(text.indexOf(filter) > -1);
            }
        });
    });

    function TomarFotos() {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = 'image/*';
        input.capture = 'camera'; // Esto abre la aplicación de cámara por defecto en dispositivos móviles

        input.addEventListener('change', (e) => {
            const file = e.target.files[0];
            const reader = new FileReader();
            reader.onload = (event) => {
                const imagen = document.createElement('img');
                imagen.src = event.target.result;
                const contenedorFotos = $('.IMGViewerContainer');
                console.log(contenedorFotos);
                
                contenedorFotos.attr('src', event.target.result); // Agrega la imagen al contenedor
                contenedorFotos.parent().show(); // Muestra el div padre del contenedor
                console.log(contenedorFotos.parent());
            };
            reader.readAsDataURL(file);
        });

        input.click(); // Esto abre la aplicación de cámara por defecto
    }

    $('.TomarFotosBT').on('click', function() {
        console.log('TomarFotosBT');
        
        TomarFotos();
    });


    $('.DeleteFotoDiv').on('click', function() {
        $('.IMGInputUpdateContainer').val('');
        const contenedorFotos = $('.IMGViewerContainer');
        contenedorFotos.attr('src',''); // Agrega la imagen al contenedor
        contenedorFotos.parent().hide(); // Muestra el div padre del contenedor
    });

    
    // Variable global para almacenar las imágenes


    $('.AddFotoDiv').on('click', function() {
        //const contenedorFotos = $('.IMGViewerContainer');
        //const inputImagen = $('.IMGInputUpdateContainer');
        const contenedorFotos = $(this).closest('.tab-pane').find('.IMGViewerContainer');
        const inputImagen = $(this).closest('.tab-pane').find('.IMGInputUpdateContainer');
        console.log('inputImagen');
        console.log(inputImagen);

        var modalCRUD = $(this).closest('form').attr('modalCRUD');
        var form = $(`#form${modalCRUD}`);
        $(`#alert_${modalCRUD}`).removeClass(function(index, className) {
            return (className.match(/(bg-\S+|alert-(?!dismissible)\S+)/g) || []).join(' ');
        });
        let tipoalerta;
        let alerttext;

        if (inputImagen[0].files.length > 0) {
            // Crear FormData para las imágenes si no existe
            const formData = form.data('imgfiles') || new FormData(form[0]);

            // Agregar las imágenes a formData y a la variable global
            for (let i = 0; i < inputImagen[0].files.length; i++) {
                formData.append('imagenes[]', inputImagen[0].files[i]);
                imagenesGlobal.push(inputImagen[0].files[i]); // Guardar imagen en la variable global
            }

            form.data('imgfiles', formData);
            tipoalerta = 'success';
            alerttext = 'Se añadieron correctamente las fotografías a la lista.';

            // Mostrar la primera imagen en el visor
            const reader = new FileReader();
            reader.onload = (event) => {
                updateCarousel(event.target.result); // Función para actualizar el carrusel de imágenes
            };
            reader.readAsDataURL(inputImagen[0].files[0]);

            console.log('formData:', formData);
            console.log('imagenesGlobal:', imagenesGlobal); // Mostrar imágenes guardadas en la variable global
        } else {
            tipoalerta = 'warning';
            alerttext = 'Seleccione o tome una foto antes de tratar de añadirla a la lista.';
        }
        console.log(modalCRUD);
        

        // Actualizar la alerta
        $(`#alert_${modalCRUD}`).addClass(`alert-${tipoalerta}`);
        $(`#alert_${modalCRUD}`).addClass(`bg-${tipoalerta}`);
        $(`#alertmsg_${modalCRUD}`).text(alerttext);
        $(`#alert_${modalCRUD}`).slideDown();

        setTimeout(() => {
            $(`#alert_${modalCRUD}`).slideUp();
        }, 5000);
    });

    $(document).on('click', '.ViewDocument', function(evento) {
        evento.preventDefault();
        var href = $(this).attr('href');
        window.open(href, '_blank');
    });

    $(document).on('click', '.DownloadDocument', function(evento) {
        evento.preventDefault();
        var href = $(this).attr('href');
        var filename = href.split(/\/|\\/).pop();
        
        var a = document.createElement('a');
        a.href = href;
        a.download = filename; // Establece el nombre del archivo
        a.click();
    });

    

    $('.IMGInputUpdateContainer').on('change', function() {
        const file = this.files[0];
        const reader = new FileReader();
        reader.onload = (event) => {
            const contenedorFotos = $('.IMGViewerContainer');
            contenedorFotos.attr('src', event.target.result); // Cambia la imagen del contenedor
            contenedorFotos.parent().show(); // Muestra el div padre del contenedor
        };
        reader.readAsDataURL(file);
    });

    (function() {
    var originalVal = $.fn.val;
    
    $.fn.val = function(value) {
        if (arguments.length === 0) {
            return originalVal.call(this);
        }
        
        // Marcar el input como cambiado por código
        this.each(function() {
            $(this).data("cambio-por-codigo", true);
        });

        var resultado = originalVal.call(this, value);

        // Actualizar los RESULT si fue un cambio por código
        this.each(function() {
            var clases = $(this).attr('class').split(' ');
            actualizarResultadosPorClases(clases);
        });

        return resultado;
    };
})();

// Detectar cambios manuales
$(document).on("input keydown", '[class*="SUM-"], [class*="RES-"], [class*="MUL-"], [class*="DIV-"], [class*="DESC-"]', function() {
    $(this).data("cambio-por-codigo", false);
});

$(document).on("change", '[class*="SUM-"], [class*="RES-"], [class*="MUL-"], [class*="DIV-"], [class*="DESC-"]', function() {
    var clases = $(this).attr('class').split(' ');

    if ($(this).data("cambio-por-codigo")) {
        console.log("Cambio por código:", $(this).val());
    } else {
        console.log("Cambio manual por usuario:", $(this).val());
    }

    actualizarResultadosPorClases(clases);
});

function actualizarResultadosPorClases(clases) {
    clases.forEach(function(clase) {
        if (clase.includes('SUM-')) {
            var numero = clase.split('SUM-')[1];
            var suma = 0;
            $(`.SUM-${numero}`).each(function() {
                suma += parseFloat($(this).val()) || 0;
            });

            $(`.RESULT-${numero}`).data('suma-original', suma);
            var sumaOriginal = $(`.RESULT-${numero}`).data('suma-original');

            var resta = 0;
            $(`.RES-${numero}`).each(function() {
                resta += parseFloat($(this).val()) || 0;
            });

            if (sumaOriginal !== undefined) {
                var resultado = sumaOriginal - resta;
                $(`.RESULT-${numero}`).val(resultado > 0 ? resultado : 0);
            }
        } else if (clase.includes('DESC-')) {
            var numero = clase.split('DESC-')[1];
            var porcentaje = parseFloat($(`.DESC-${numero}`).val()) || 0;

            $(`.RESULT-${numero}`).each(function() {
                var totalOriginal = $(this).data('total-original');
                var descuentoAnterior = $(this).data('descuento-anterior');

                if (totalOriginal === undefined || (descuentoAnterior !== undefined && descuentoAnterior != parseFloat($(this).val()))) {
                    totalOriginal = parseFloat($(this).val()) || 0;
                    $(this).data('total-original', totalOriginal);
                }

                var descuento = totalOriginal * (porcentaje / 100);
                var resultado = totalOriginal - descuento;
                $(this).data('descuento-anterior', resultado);
                $(this).val(resultado > 0 ? resultado : totalOriginal);
            });
        } else if (clase.includes('MUL-')) {
            var numero = clase.split('MUL-')[1];
            var multiplicacion = 1;
            $(`.MUL-${numero}`).each(function() {
                multiplicacion *= parseFloat($(this).val()) || 0;
            });
            $(`.RESULT-${numero}`).val(multiplicacion);
        } else if (clase.includes('DIV-')) {
            var numero = clase.split('DIV-')[1];
            var division = null;
            $(`.DIV-${numero}`).each(function() {
                var valor = parseFloat($(this).val()) || 0;
                if (division === null) {
                    division = valor;
                } else if (valor !== 0) {
                    division /= valor;
                }
            });
            $(`.RESULT-${numero}`).val(division !== null ? division : 0);
        }
    });
}


    $(document).on('change', '[class*="DateStartCal-"], [class*="DateEndCal-"]', function() {
        var clase = $(this).attr('class');
        var numero = clase.match(/DateStartCal-(\d+)/) || clase.match(/DateEndCal-(\d+)/);
        if (numero) {
            numero = numero[1];
            var startDate = $('.DateStartCal-' + numero).val();
            var endDate = $('.DateEndCal-' + numero).val();

            if (startDate !== "" && endDate !== "") {
                var start = new Date(Date.parse(startDate + 'T00:00:00'));
                var end = new Date(Date.parse(endDate + 'T00:00:00'));
                console.log('Fechas:');
                console.log(startDate);
                console.log(endDate);
                console.log(start);
                console.log(end);
                if (start <= end) {
                    var diffTime = Math.abs(end - start);
                    var diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24)) + 1;

                    // Buscar elementos con clases OnHolidaysAllow, OnSaturdayAllow y OnSundayAllow más cercanos
                    var container = $(this).closest('.modal-body');
                    var onHolidaysAllow = container.find('.OnHolidaysAllow');
                    var onSaturdayAllow = container.find('.OnSaturdayAllow');
                    var onSundayAllow = container.find('.OnSundayAllow');

                    // Restar días festivos, sábados y domingos si los elementos no están activos
                    var holidays = 0;
                    var saturdays = 0;
                    var sundays = 0;
                    console.log(onHolidaysAllow);
                    console.log(onSaturdayAllow);
                    console.log(onSundayAllow);
                    
                    for (var i = start; i <= end; i.setDate(i.getDate() + 1)) {
                        var date = i.getDate() + '/' + (i.getMonth() + 1);
                        var month = i.getMonth();
                        var year = i.getFullYear();

                        if (i.getDay() === 0) {
                            if (!onSundayAllow.is(':checked')) {
                            sundays++;
                            }
                        } else if (i.getDay() === 6) {
                            if (!onSaturdayAllow.is(':checked')) {
                            saturdays++;
                            }
                        }
                        }
                    console.log('holidays: ' + holidays);
                    console.log('saturdays: ' + saturdays);
                    console.log('sundays: ' + sundays);
                    diffDays -= holidays + saturdays + sundays;

                    $('.DateResultCal-' + numero).val(diffDays);
                } else {
                    alert("La fecha de inicio debe ser antes de la fecha de fin");
                    $('.DateResultCal-' + numero).val("");
                }
            } else {
                $('.DateResultCal-' + numero).val("");
            }
        }
    });

    $(document).on('click', '.GenerarReporte', function() {
        var url = '/rutas/reportes.php';
        console.log($(this));
        
        var reporte = $(this).attr('reporte');
        var formId = $(this).attr('form');
        var parametros = {};

        // Tomar atributos del botón para formar los argumentos de la URL
        $.each($(this).data(), function(key, value) {
            if (key !== 'reporte') {
            parametros[key] = value;
            }
        });

        // Construir la URL con los parámetros
        var urlCompleta = url + '/' + reporte + '?' + $.param(parametros);

        // Abrir el formulario si existe el atributo form
        if (formId) {
            ClearForm(formId)
            var modalCRUD = $(`#modalCRUD${formId}`);
            modalCRUD.modal('show'); // Abrir el formulario con el id especificado
            var formulario = $(`#form${formId}`);
            // Esperar a que el usuario envíe el formulario
            formulario.on('submit', function(event) {
                event.preventDefault(); // Prevenir el envío del formulario por defecto
                var camposllenos = true;
                var datareport = [];
                formulario.find('input, select, textarea').each(function() {
                    datareport[$(this).attr('id')] = $(this).val();
                    if ($(this).is('select')) {
                        if ($(this).val() === '' || $(this).find('option:selected').text() === 'Seleccione...') {
                            camposllenos = false;
                        }
                    } else {
                        if ($(this).val() === '') {
                            camposllenos = false;
                        }
                    }
                });

                if (camposllenos) {
                    var form = document.createElement('form');
                    form.action = urlCompleta;
                    form.method = 'post';
                    form.target = '_blank';

                    for (var key in datareport) {
                        var input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = key;
                        input.value = datareport[key];
                        form.appendChild(input);
                    }

                    document.body.appendChild(form);
                    setTimeout(function() { form.submit(); }, 0);
                }
            });
        } else {
            // Abrir la URL directamente si no existe el formulario
            window.open(urlCompleta, '_blank');
        }

    });
    
    // Recargar la página al cerrar cualquier modal de formulario que empiece con 'modalCRUD'
    $("[id^='modalCRUD']").on('hidden.bs.modal', function () {
        location.reload();
    });


    });


    $(document).ready(function() {
        $('input[type="number"][maxlength]').on('input', function() {
            
            var maxLength = parseInt($(this).attr('maxlength'));
            var valorActual = $(this).val();
            if (valorActual.length > maxLength) {
            $(this).val(valorActual.slice(0, -1));
            }
            console.log($(this).val());
        });
    });
    
</script>