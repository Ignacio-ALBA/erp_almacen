<?php

function GenerateCustomsButtons($CustomButtons, $id = '') {
    
    $bloques_de_botones = array_chunk($CustomButtons, 3);
    $html ='<div style="width:100%; height:100%; display:grid;">';
    foreach ($bloques_de_botones as $index => $bloque) {
        $paddingStyle = ($index < count($bloques_de_botones) - 1) ? 'style="padding-bottom:10px;"' : '';
        $html .= '<div class="btn-group" role="group" '.$paddingStyle.'>';
        foreach($bloque as $boton) {
            $boton = str_replace('${modalCRUD}', htmlspecialchars($id), $boton);
            $html .= $boton;
        }
        $html .= '</div>';
    }
    $html .= '</div>';
    
    return $html;
}

function CreateInput($atributos = []) {
    // Convertir el array de atributos en un string
    $atributosString = '';
    foreach ($atributos as $key => $value) {
    if($key != 'etiqueta' && $key != 'class' && $key != 'div_clases'){
        $atributosString .= $key . '="' . htmlspecialchars($value) . '" ';
    }
    }

    // Obtener el id y la etiqueta de los atributos
    $id = isset($atributos['id']) ? $atributos['id'] : null;
    $etiqueta = isset($atributos['etiqueta']) ? $atributos['etiqueta'] : '';
    $tipoInput = isset($atributos['type']) ? $atributos['type'] : 'text'; // Tipo por defecto
    $clases = isset($atributos['class']) ? $atributos['class'] :'';
    $div_style = isset($atributos['div_style']) ? $atributos['div_style'] :'';
    $div_clases = isset($atributos['div_clases']) ? $atributos['div_clases'] :'';
    $styleinput= '';
    if($tipoInput == 'color'){
        $styleinput = 'style="max-width:90px; min-height:80px;"';
    }
    return '
    <div class="form-group '.$div_clases.'" style="' . $div_style . '">
        <label for="' . htmlspecialchars($id) . '" class="col-sm-8 col-form-label">' . htmlspecialchars($etiqueta) . ':</label>
        <input class="form-control '.$clases.'" id="' . htmlspecialchars($id) . '" ' . trim($atributosString) .' '.$styleinput.'>
        <div id="error_' . htmlspecialchars($id) . '" class="invalid-feedback"></div>
        <div id="valid_' . htmlspecialchars($id) . '" class="valid-feedback">Luce bien!</div>
        <div id="validate_' . htmlspecialchars($id) . '" class="warning-text"></div>
    </div>';
}

function CreateTextArea($atributos = []) {
    // Convertir el array de atributos en un string
    $atributosString = '';
    foreach ($atributos as $key => $value) {
    if($key != 'etiqueta' && $key != 'class'){
        $atributosString .= $key . '="' . htmlspecialchars($value) . '" ';
    }
    }

    // Obtener el id y la etiqueta de los atributos
    $id = isset($atributos['id']) ? $atributos['id'] : null;
    $etiqueta = isset($atributos['etiqueta']) ? $atributos['etiqueta'] : null;
    $tipoInput = isset($atributos['tipo']) ? $atributos['tipo'] : 'text'; // Tipo por defecto
    $clases = isset($atributos['class']) ? $atributos['class'] :'';
    $div_style = isset($atributos['div_style']) ? $atributos['div_style'] :'';
    $div_clases = isset($atributos['div_clases']) ? $atributos['div_clases'] :'';

    return '
    <div class="form-group '.$div_clases.'" style="' . $div_style . '">
        <label for="' . htmlspecialchars($id) . '" class="col-sm-8 col-form-label">' . htmlspecialchars($etiqueta) . ':</label>
        <textarea class="form-control '.$clases.'" id="' . htmlspecialchars($id) . '" ' . trim($atributosString) . '></textarea>
        <div id="error_' . htmlspecialchars($id) . '" class="invalid-feedback"></div>
        <div id="valid_' . htmlspecialchars($id) . '" class="valid-feedback">Luce bien!</div>
        <div id="validate_' . htmlspecialchars($id) . '" class="warning-text"></div>
    </div>';
}

function CreateCardIMG($atributos = []) {
    // Convertir el array de atributos en un string
    $atributosString = '';
    foreach ($atributos as $key => $value) {
    if($key != 'etiqueta' && $key != 'class'){
        $atributosString .= $key . '="' . htmlspecialchars($value) . '" ';
    }
    }

    // Obtener el id y la etiqueta de los atributos
    $id = isset($atributos['id']) ? $atributos['id'] : null;
    $etiqueta = isset($atributos['etiqueta']) ? $atributos['etiqueta'] : null;
    $tipoInput = isset($atributos['tipo']) ? $atributos['tipo'] : 'text'; // Tipo por defecto
    $clases = isset($atributos['class']) ? $atributos['class'] :'';
    $div_style = isset($atributos['div_style']) ? $atributos['div_style'] :'';
    $div_clases = isset($atributos['div_clases']) ? $atributos['div_clases'] :'';

    return '
    <div class="card" id="div'.$id.'" class="'.$div_clases.'" style="margin-bottom:0px; display:none; '.$div_style.'">
        <img id="'.$id.'" class="'.$clases.'" alt="'.$id.'" '.$atributosString.'>
    </div>';
}

function CreateCardTab($contenido,$atributos = []){

    $atributosString = '';
    foreach ($atributos as $key => $value) {
        if(!in_array($key,['etiqueta','class','div_clases','div_style','title','id'])){
            $atributosString .= $key . '="' . htmlspecialchars($value) . '" ';
        }
    }

    // Obtener el id y la etiqueta de los atributos
    $id = isset($atributos['id']) ? $atributos['id'] : null;
    $title = isset($atributos['title']) ? $atributos['title'] : '';
    $div_style = isset($atributos['div_style']) ? $atributos['div_style'] : '';
    $tabs = '';
    $tabscontent = '';

    $selected = "true";
    $tabindex='';
    foreach ($contenido as $value) {
        $id_aleatorio = rand(1, 100);
        $tabs .='<li class="nav-item" role="presentation">
                    <button class="nav-link '.($selected == "true"?'active':'').'" id="'.str_replace(' ', '', $value['titulo']).$id_aleatorio.'-tab" data-bs-toggle="tab" data-bs-target="#bordered-'.str_replace(' ', '', $value['titulo']).$id_aleatorio.'" type="button" role="tab" aria-controls="'.str_replace(' ', '', $value['titulo']).'" aria-selected="'.$selected.'" '.$tabindex.'>'.$value['titulo'].'</button>
                </li>';

        $tabscontent .= '<div class="tab-pane fade '.($selected == "true"?'show active':'').'" id="bordered-'.str_replace(' ', '', $value['titulo']).$id_aleatorio.'" role="tabpanel" aria-labelledby="'.str_replace(' ', '', $value['titulo']).$id_aleatorio.'-tab">';
        foreach ($value['contenido'] as $index => $elemento) {
            $tabscontent .= $elemento; // Agrega el elemento al contenido final
            
            // Solo agrega <br> si no es el último elemento
            if ($index < count($value['contenido']) - 1) {
                // Solo agrega un <br> visible si el elemento actual no está oculto
                if (str_contains($elemento, 'display:none;') && str_contains($elemento, '<button')) {
                    $tabscontent .= '<br style="display: none;">';
                } else {
                    // Si el elemento está oculto, agrega un <br> oculto
                    $tabscontent .= '<br>'; // Agrega un <br> visible
                }
            }
        }
        $tabscontent .= '</div>';
        $selected = "false";
        $tabindex='tabindex="-1"';
    }
    

    return '
    <div class="card" style="'.$div_style.'">
        <div class="card-body" '.$atributosString.'>
            '.($title != '' ? '<h5 class="card-title">'.$title.'</h5>':'').'

            <!-- Bordered Tabs -->
            <ul class="nav nav-tabs nav-tabs-bordered" id="borderedTab" role="tablist">
            '.$tabs.'
            </ul>

            <div class="tab-content pt-2" id="borderedTabContent">
            '.$tabscontent.'
            </div><!-- End Bordered Tabs -->

        </div>
    </div>
    ';
}

function CreateCarousel($atributos = [],$fotos = []){
    $atributosString = '';
    foreach ($atributos as $key => $value) {
        if(!in_array($key,['etiqueta','class','div_clases','div_style','title','id'])){
            $atributosString .= $key . '="' . htmlspecialchars($value) . '" ';
        }
    }

    // Obtener el id y la etiqueta de los atributos
    $id = isset($atributos['id']) ? $atributos['id'] : '';
    $title = isset($atributos['title']) ? $atributos['title'] : '';
    $div_style = isset($atributos['div_style']) ? $atributos['div_style'] : '';
    $class = isset($atributos['class']) ? $atributos['class'] : '';

    $content_fotos = '';
    $content_indicators = '';
    $id_aleatorio = rand(1, 100);

    foreach ($fotos as $key => $foto) {
        debug($key);
        $content_fotos .= ' 
        <div class="carousel-item">
            <img src="'.getServerUrl().$foto.'" class="d-block w-100" alt="...">
        </div>';

        $content_indicators = '<button type="button" data-bs-target="#carousel'.$id.$id_aleatorio.'" data-bs-slide-to="'.$key.$id_aleatorio.'" class="" aria-label="Slide '.$key.$id_aleatorio.'"></button>';
    }

    return '<div class="card" style="'.$div_style.'">
            <div class="card-body" '.$atributosString.'>
              '.($title != '' ? '<h5 class="card-title">'.$title.'</h5>':'').'
              <!-- Slides with indicators -->
              <div id="carousel'.$id.$id_aleatorio.'" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-indicators">
                '.$content_indicators.'
                </div>
                <div class="carousel-inner '.$class.'">
                '.($content_fotos ? $content_fotos : '<h5><i class="bi bi-image"></i> Sin Fotos Subidas</h5>').'
                </div>

                <button class="carousel-control-prev" type="button" data-bs-target="#carousel'.$id.$id_aleatorio.'" data-bs-slide="prev">
                  <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                  <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carousel'.$id.$id_aleatorio.'" data-bs-slide="next">
                  <span class="carousel-control-next-icon" aria-hidden="true"></span>
                  <span class="visually-hidden">Next</span>
                </button>

              </div><!-- End Slides with indicators -->

            </div>
          </div>';
}

function CreateButton($atributos = [],$form = true) {
    // Convertir el array de atributos en un string
    $atributosString = '';
    foreach ($atributos as $key => $value) {
    if($key != 'etiqueta'){
        $atributosString .= $key . '="' . htmlspecialchars($value) . '" ';
    }
    }

    // Obtener el id y la etiqueta de los atributos
    $id = isset($atributos['id']) ? $atributos['id'] : null;
    $etiqueta = isset($atributos['etiqueta']) ? $atributos['etiqueta'] : null;
    if($form){
        $element = '<div class="form-group">
                        <button id="' . htmlspecialchars($id) . '" ' . trim($atributosString) . '>'.$etiqueta.'</button>
                    </div>';
    }else{
        $element = '<button id="' . htmlspecialchars($id) . '" ' . trim($atributosString) . '>'.$etiqueta.'</button>';
    }
    return $element;
    
}
function CreateButtonP($atributos = [], $form = true) {
    // Convertir el array de atributos en un string
    $atributosString = '';
    foreach ($atributos as $key => $value) {
        if($key != 'etiqueta' && $key != 'text' && $key != 'html') {
            $atributosString .= $key . '="' . htmlspecialchars($value) . '" ';
        }
    }

    // Obtener el id y el texto del botón
    $id = isset($atributos['id']) ? $atributos['id'] : null;
    $text = isset($atributos['text']) ? $atributos['text'] : '';
    $html = isset($atributos['html']) && $atributos['html'] === true;

    // Crear el botón
    if($form) {
        $element = '<div class="form-group">
                        <button id="' . htmlspecialchars($id) . '" ' . trim($atributosString) . '>' . 
                        ($html ? $text : htmlspecialchars($text)) . 
                        '</button>
                    </div>';
    } else {
        $element = '<button id="' . htmlspecialchars($id) . '" ' . trim($atributosString) . '>' . 
                   ($html ? $text : htmlspecialchars($text)) . 
                   '</button>';
    }
    
    return $element;
}

function CreatSwitchCheck($atributos = []) {
    // Convertir el array de atributos en un string
    $atributosString = '';
    foreach ($atributos as $key => $value) {
    if($key != 'etiqueta' && $key != 'class' && $key != 'div_clases'){
        $atributosString .= $key . '="' . htmlspecialchars($value) . '" ';
    }
    }

    // Obtener el id y la etiqueta de los atributos
    $id = isset($atributos['id']) ? $atributos['id'] : null;
    $etiqueta = isset($atributos['etiqueta']) ? $atributos['etiqueta'] : '';
    $etiqueta2 = isset($atributos['etiqueta2']) ? $atributos['etiqueta2'] : '';
    $class = isset($atributos['class']) ? $atributos['class'] : '';

    return '
    <div class="form-group">
        <label for="' . htmlspecialchars($id) . '" class="col-sm-6 col-form-label">' . htmlspecialchars($etiqueta) . ':</label>
        <div class="col-sm-9" style="display:flex; justify-content: start; align-items: center; padding-left:10px;">
            <div class="form-check form-switch">
                <input class="form-check-input '.$class.'" type="checkbox" id="' . htmlspecialchars($id) . '" ' . trim($atributosString) . '>
                <label class="form-check-label" for="' . htmlspecialchars($id) . '">' . htmlspecialchars($etiqueta2) . '</label>
            </div>
        </div>
        <div id="error_' . htmlspecialchars($id) . '" class="invalid-feedback"></div>
    </div>';
    
}

function CreateSelect($atributos = [], $opciones = [], $valorSeleccionado = null) {
    // Convertir el array de atributos en un string
    $atributosString = '';
    foreach ($atributos as $key => $value) {
        // Excluir 'etiqueta' y 'placeholder' del string de atributos
        if ($key != 'etiqueta' && $key != 'placeholder' && $key != 'class' && $key != 'div_style') {
            $atributosString .= $key . '="' . htmlspecialchars($value) . '" ';
        }
    }

    // Obtener el id y la etiqueta de los atributos
    $id = isset($atributos['id']) ? $atributos['id'] : null;
    $etiqueta = isset($atributos['etiqueta']) ? $atributos['etiqueta'] : null;
    $placeholder = isset($atributos['placeholder']) ? $atributos['placeholder'] : null;
    $clases = isset($atributos['class']) ? $atributos['class'] :'';
    $div_style = isset($atributos['div_style']) ? $atributos['div_style'] :'';
    $select_input = '
    <div class="form-group" style="' . $div_style . '">
    <label for="' . htmlspecialchars($id) . '" class="col-form-label">' . htmlspecialchars($etiqueta) . ':</label>
    <select class="form-select '.$clases.'" id="' . htmlspecialchars($id) . '" name="' . htmlspecialchars($id) . '" ' . trim($atributosString) . '>
        '; // Opción por defecto

    // Si se proporciona un placeholder, mostrarlo como opción deshabilitada
    $options = '';
    
    if ($placeholder != null) {
        $options.= '<option value="null" disabled>' . htmlspecialchars($placeholder) . '</option>';
    }
    $primerElementoSeleccionado = false;
    
    // Generar las opciones del select
    foreach ($opciones as $valor) {
        // Verifica si el elemento es el que debe ser seleccionado por defecto
        if ($valor['pordefecto'] == 1 && !$primerElementoSeleccionado) {
            $selected = 'selected';
            $primerElementoSeleccionado = true; // Marca que ya se ha seleccionado un elemento por defecto
        } else {
            $selected = ($valor['valor'] == $valorSeleccionado) ? 'selected' : '';
        }

        $elementopadre = '';
        if(isset($valor['elementopadre']) && $valor['elementopadre']){
            $elementopadre = 'elementopadre="'.$valor['elementopadre'].'"';
        }

        
        if(isset($valor['text']) && $valor['text']){
            $options .= '<option value="' . htmlspecialchars($valor['valor']) . '" ' . $selected. ' ' .$elementopadre.'>' . htmlspecialchars($valor['text']) . '</option>';
        }else{
            $options .= '<option value="' . htmlspecialchars($valor['valor']) . '" ' . $selected. ' ' .$elementopadre.'>' . htmlspecialchars($valor['valor']) . '</option>';
        }
    
        
    }

    if($primerElementoSeleccionado === false){
        $select_input .= '<option selected="" disabled="" value="">Seleccione...</option>';
    }//else{$select_input .= '<option disabled="" value="">Seleccione...</option>';}

    $select_input .= $options;

    $select_input .= '
    </select>
    <div id="error_' . htmlspecialchars($id) . '" class="invalid-feedback"></div>
    </div>';


    return $select_input;
}

function CreateTable($id, $buttonlabel, $titulos, $consulta, $buttonallow = true,$CustomButtons = [],$class = '',$atributos = []) {
    $atributosString = '';
    foreach ($atributos as $key => $value) {
        // Excluir 'etiqueta' y 'placeholder' del string de atributos
        if ($key != 'etiqueta' && $key != 'placeholder' && $key != 'class') {
            $atributosString .= $key . '="' . htmlspecialchars($value) . '" ';
        }
    }
    $ButtonsInRow ='';
    if($CustomButtons == 'ButtonsInRow'){
        $ButtonsInRow = 'ButtonsInRow';
    }
    // Iniciar la sección
    $html = '<section class="section">
                <div class="row">
                  <div class="col-lg-12">
                    <div class="card">
                      <div class="card-body table-responsive">';
                    if($buttonallow === true){
                        $html.='
                        <br>
                        <button type="button" class="ModalDataAdd btn btn-primary primary" modalCRUD=' . htmlspecialchars($id) . '>'. htmlspecialchars($buttonlabel) .'</button>
                        <br>';
                    }
                    
                    $html.='<br>
                        
                        <!-- Table with stripped rows -->
                        <table style="border-radius:10px;" class="table table-sm table-striped datatable table-bordered '.$class.'" id="tabla' . htmlspecialchars($id) . '"'.$atributosString.' '.$ButtonsInRow.' modalCRUD=' . htmlspecialchars($id) .'>
                            <thead class="text-center">
                            <tr>';
    $firts = true;                        
    foreach ($titulos as $titulo) {
        $ancho = '';
        if($firts === true){
            $ancho = ' min-width:40px;';
            $firts = false;
        }
        $html .= '<th style="white-space: nowrap; height:auto;'.$ancho.'">' . $titulo. '</th>';
    }
    
    $html .= '
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>';
    
    // Crear las filas de la tabla
    foreach ($consulta as $fila) {
        //debug($fila);
        $html .= '<tr>';
        $ancho_columna = '50px';
        foreach ($fila as $columna) {
            $html .= '<td style="white-space: nowrap; vertical-align: middle; text-align:center; min-width:'. $ancho_columna.';">' . ($columna !== null ? $columna : '') . '</td>';
            $ancho_columna = '100px';
        }
        

        if($CustomButtons === []){
            $html .= '<td style="white-space: nowrap;">
                        <div class="btn-group" role="group" style="width:100%;">
                            <button type="button" class="ModalDataView btn btn-primary primary" modalCRUD=' . htmlspecialchars($id) . '><i class="bi bi-eye"></i> Ver</button>
                            <button type="button" class="ModalDataEdit btn btn-warning warning" modalCRUD=' . htmlspecialchars($id) . '><i class="bi bi-pencil"></i> Editar</button>
                            <button type="button" class="ModalDataDelete btn btn-danger danger" modalCRUD=' . htmlspecialchars($id) . '><i class="bi bi-trash"></i> Eliminar</button>
                        </div>
                    </td>
                    ';
        }else if($ButtonsInRow == '') {
            $html .= '<td style="white-space: nowrap; display:flex; flex-direction:column;">';
            $bloques_de_botones = array_chunk($CustomButtons, 3);
            foreach ($bloques_de_botones as $index => $bloque) {
                $paddingStyle = ($index < count($bloques_de_botones) - 1) ? 'style="padding-bottom:10px;"' : '';
                $html .= '<div class="btn-group" role="group" '. $paddingStyle .'>';
                foreach($bloque as $boton) {
                    $boton = str_replace('${modalCRUD}', htmlspecialchars($id), $boton);
                    $html .= $boton;
                }
                $html .= '</div>';
            }
            $html .= '</td>';
        }
        
        
                    
        $html .= '</tr>';
    }

    // Cerrar el cuerpo y la tabla
    $html .= '      </tbody>       
                    </table>
                    <!-- End Table with stripped rows -->
                  </div>
                </div>
              </div>
            </div>
          </section>';
    
    echo $html;
}

function CreateTableNotSection($id, $buttonlabel, $titulos, $consulta, $buttonallow = true,$CustomButtons = [],$class = '',$atributos = []) {
    $atributosString = '';
    foreach ($atributos as $key => $value) {
        // Excluir 'etiqueta' y 'placeholder' del string de atributos
        if ($key != 'etiqueta' && $key != 'placeholder' && $key != 'class') {
            $atributosString .= $key . '="' . htmlspecialchars($value) . '" ';
        }
    }
    $ButtonsInRow ='';
    if($CustomButtons == 'ButtonsInRow'){
        $ButtonsInRow = 'ButtonsInRow';
    }
    // Iniciar la sección
    $html = '<table style="border-radius:10px;" class="table table-sm table-striped datatable table-bordered '.$class.'" id="tabla' . htmlspecialchars($id) . '"'.$atributosString.' '.$ButtonsInRow.' modalCRUD=' . htmlspecialchars($id) .'>
                            <thead class="text-center">
                            <tr>';
    $firts = true;                        
    foreach ($titulos as $titulo) {
        $ancho = '';
        if($firts === true){
            $ancho = ' min-width:40px;';
            $firts = false;
        }
        $html .= '<th style="white-space: nowrap; height:auto;'.$ancho.'">' . $titulo. '</th>';
    }
    
    $html .= '
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>';
    
    // Crear las filas de la tabla
    foreach ($consulta as $fila) {
        //debug($fila);
        $html .= '<tr>';
        $ancho_columna = '50px';
        foreach ($fila as $columna) {
            $html .= '<td style="white-space: nowrap; vertical-align: middle; text-align:center; min-width:'. $ancho_columna.';">' . ($columna !== null ? $columna : '') . '</td>';
            $ancho_columna = '100px';
        }
        

        if($CustomButtons === []){
            $html .= '<td style="white-space: nowrap;">
                        <div class="btn-group" role="group">
                            <button type="button" class="ModalDataView btn btn-primary primary" modalCRUD=' . htmlspecialchars($id) . '><i class="bi bi-eye"></i> Ver</button>
                            <button type="button" class="ModalDataEdit btn btn-warning warning" modalCRUD=' . htmlspecialchars($id) . '><i class="bi bi-pencil"></i> Editar</button>
                            <button type="button" class="ModalDataDelete btn btn-danger danger" modalCRUD=' . htmlspecialchars($id) . '><i class="bi bi-trash"></i> Eliminar</button>
                        </div>
                    </td>
                    ';
        }else if($ButtonsInRow == '') {
            $html .= '<td style="white-space: nowrap; display:flex; flex-direction:column;">';
            $bloques_de_botones = array_chunk($CustomButtons, 3);
            foreach ($bloques_de_botones as $index => $bloque) {
                $paddingStyle = ($index < count($bloques_de_botones) - 1) ? 'style="padding-bottom:10px;"' : '';
                $html .= '<div class="btn-group" role="group" '. $paddingStyle .'>';
                foreach($bloque as $boton) {
                    $boton = str_replace('${modalCRUD}', htmlspecialchars($id), $boton);
                    $html .= $boton;
                }
                $html .= '</div>';
            }
            $html .= '</td>';
        }
        
        
                    
        $html .= '</tr>';
    }

    // Cerrar el cuerpo y la tabla
    $html .= '      </tbody>       
                    </table>';
    
    echo $html;
}

function CreateModalForm($formattribute, $contenidoModal) {
    
    // Inicializa una variable para almacenar el contenido final
    $contenidoFinal = '';

    $idModal = isset($formattribute['id']) ? $formattribute['id'] : null;
    $titulo = isset($formattribute['Title']) ? $formattribute['Title'] : '';
    $titulo2 = isset($formattribute['Title2']) ? $formattribute['Title2'] : '';
    $titulo3 = isset($formattribute['Title3']) ? $formattribute['Title3'] : '';
    $ModalType = isset($formattribute['ModalType']) ? $formattribute['ModalType'] : null;
    $PrimaryButttonName = isset($formattribute['PrimaryButttonName']) ? $formattribute['PrimaryButttonName'] :'Guardar';
    $atributosString = '';
    foreach ($formattribute as $key => $value) {
        // Excluir 'etiqueta' y 'placeholder' del string de atributos
        if (!in_array($key,['id','title','ModalType','PrimaryButttonName'])) {
            $atributosString .= $key . '="' . htmlspecialchars($value) . '" ';
        }
    }
    
    // Recorre el array de contenido
    foreach ($contenidoModal as $index => $elemento) {
        $contenidoFinal .= $elemento; // Agrega el elemento al contenido final
        
        // Solo agrega <br> si no es el último elemento
        if ($index < count($contenidoModal) - 1) {
            // Solo agrega un <br> visible si el elemento actual no está oculto
            if (str_contains($elemento, 'display:none;') && str_contains($elemento, '<button')) {
                $contenidoFinal .= '<br style="display: none;">';
            } else {
                // Si el elemento está oculto, agrega un <br> oculto
                $contenidoFinal .= '<br>'; // Agrega un <br> visible
            }
        }
    }
    
    echo '
    <div class="modal fade" id="modalCRUD' . $idModal . '" tabindex="-1" style="display: none;">
        <form id="form' . $idModal . '" style="width:100%; height: 100%;"' . trim($atributosString) . 'class="needs-validation" novalidate>
            <div class="modal-dialog '.$ModalType.'">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle1' . $idModal . '">' . $titulo . '</h5>
                        <h5 class="modal-title" id="modalTitle2' . $idModal . '">' . $titulo2 . '</h5>
                        <h5 class="modal-title" id="modalTitle3' . $idModal . '">' . $titulo3 . '</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    <div style="display:none;" class="alert alert-warning bg-warning border-0 alert-dismissible fade show" role="alert" id="alert_' . htmlspecialchars($idModal) . '">
                        <div id="alertmsg_' . htmlspecialchars($idModal) . '"></div>
                        <button type="button" class="btn-close CloseAlertBox"  aria-label="Close" alertid="'.htmlspecialchars($idModal).'"></button>
                    </div>
                        ' . $contenidoFinal . '
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary primary">'.$PrimaryButttonName.'</button>
                    </div>
                </div>
            </div>
        </form>
    </div>';
}

function CreateModalinModal($formattribute, $contenidoModal) {
    // Inicializa una variable para almacenar el contenido final
    $contenidoFinal = '';

    $idModal = isset($formattribute['id']) ? $formattribute['id'] : null;
    $titulo = isset($formattribute['Title']) ? $formattribute['Title'] : '';
    $titulo2 = isset($formattribute['Title2']) ? $formattribute['Title2'] : '';

    $atributosString = '';
    foreach ($formattribute as $key => $value) {
        // Excluir 'etiqueta' y 'placeholder' del string de atributos
        if ($key != 'id' && $key != 'title' && $key != 'ModalType') {
            $atributosString .= $key . '="' . htmlspecialchars($value) . '" ';
        }
    }
    
    // Recorre el array de contenido
    foreach ($contenidoModal as $index => $elemento) {
        $contenidoFinal .= $elemento; // Agrega el elemento al contenido final
        // Si no es el último elemento, agrega un <br>
        if ($index < count($contenidoModal) - 1) {
            $contenidoFinal .= '<br>';
        }
    }
    
    return '
    <div id="modalCRUD' . $idModal . '" style="display: none;" class="ModalinModal">
        <form id="form' . $idModal . '" style="width:100%; height: 100%;"' . trim($atributosString) . 'class="needs-validation" novalidate>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle1' . $idModal . '">' . $titulo . '</h5>
                    </div>
                    <div class="modal-body">
                        <div style="display:none;" class="alert alert-warning bg-warning border-0 alert-dismissible fade show" role="alert" id="alert_' . htmlspecialchars($idModal) . '">
                            <div id="alertmsg_' . htmlspecialchars($idModal) . '"></div>
                            <button type="button" class="btn-close"  aria-label="Close" onclick="$(`#alert_' . htmlspecialchars($idModal) . '`).hide()"></button>
                        </div>
                        ' . $contenidoFinal . '
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary secondary CancelModalinModal">Cancelar</button>
                        <button type="submit" class="btn btn-primary primary">Guardar</button>
                    </div>
                </div>
        </form>
    </div>';

}

function CreateBadgeIcon($type, $atributos = []) {

    $atributosString = '';
    foreach ($atributos as $key => $value) {
        if($key != 'etiqueta' && $key != 'class' && $key != 'style'){
            $atributosString .= $key . '="' . htmlspecialchars($value) . '" ';
        }
    }

    $etiqueta = isset($atributos['etiqueta']) ? $atributos['etiqueta'] : null;
    $clases = isset($atributos['class']) ? $atributos['class'] :'';
    $style = isset($atributos['style']) ? $atributos['class'] :'';
    // Definir los tipos de badge y sus respectivas clases y iconos
    $badges = [
        'primary' => ['class' => 'bg-primary', 'icon' => 'bi-star'],
        'secondary' => ['class' => 'bg-secondary', 'icon' => 'bi-collection'],
        'success' => ['class' => 'bg-success', 'icon' => 'bi-check-circle'],
        'danger' => ['class' => 'bg-danger', 'icon' => 'bi-exclamation-octagon'],
        'warning' => ['class' => 'bg-warning text-dark', 'icon' => 'bi-exclamation-triangle'],
        'info' => ['class' => 'bg-info text-dark', 'icon' => 'bi-info-circle'],
        'light' => ['class' => 'bg-light text-dark', 'icon' => 'bi-star'],
        'dark' => ['class' => 'bg-dark', 'icon' => 'bi-folder']
    ];

    // Verificar si el tipo de badge existe en el array
    if (array_key_exists($type, $badges)) {
        $badgeClass = $badges[$type]['class'];
        $badgeIcon = $badges[$type]['icon'];

        // Si no se proporciona un label, usar el label por defecto
        //$badgeLabel = $label !== null ? htmlspecialchars($label) : ucfirst($type);

        // Devolver el HTML del badge
        return '<span class="badge ' . $badgeClass .' '.$clases.' " ' . trim($atributosString) . ($style ? 'style="'.$style.'"' : '') . '><i class="bi ' . $badgeIcon . ' me-1"></i>' . $etiqueta . '</span>';
    } else {
        // Si el tipo no existe, devolver un badge por defecto o un mensaje de error
        return '<span class="badge bg-light text-dark">Badge no válido</span>';
    }
}

function CreateBadge($atributos = []) {

    $atributosString = '';
    foreach ($atributos as $key => $value) {
        if($key != 'etiqueta' && $key != 'class' && $key != 'style'){
            $atributosString .= $key . '="' . htmlspecialchars($value) . '" ';
        }
    }

    $etiqueta = isset($atributos['etiqueta']) ? $atributos['etiqueta'] : null;
    $clases = isset($atributos['class']) ? $atributos['class'] : '';
    $style = isset($atributos['style']) ? $atributos['style'] : '';
    
    // Definir los tipos de badge y sus respectivas clases

    // Verificar si el tipo de badge existe en el array

        // Devolver el HTML del badge
    return '<span class="badge '.$clases.' " ' . trim($atributosString) . ($style ? 'style="'.$style.'"' : '') . '>' . $etiqueta . '</span>';
}


function CreateModal($formattribute, $contenidoModal,$buttons = []) {
    // Inicializa una variable para almacenar el contenido final
    $contenidoFinal = '';

    $idModal = isset($formattribute['id']) ? $formattribute['id'] : null;
    $titulo = isset($formattribute['Title']) ? $formattribute['Title'] : '';
    $titulo2 = isset($formattribute['Title2']) ? $formattribute['Title2'] : '';
    $titulo3 = isset($formattribute['Title3']) ? $formattribute['Title3'] : '';
    $ModalType = isset($formattribute['ModalType']) ? $formattribute['ModalType'] : null;
    $atributosString = '';
    foreach ($formattribute as $key => $value) {
        // Excluir 'etiqueta' y 'placeholder' del string de atributos
        if ($key != 'id' && $key != 'title' && $key != 'ModalType') {
            $atributosString .= $key . '="' . htmlspecialchars($value) . '" ';
        }
    }
    
    // Recorre el array de contenido
    foreach ($contenidoModal as $index => $elemento) {
        $contenidoFinal .= $elemento; // Agrega el elemento al contenido final
        
        // Solo agrega <br> si no es el último elemento
        if ($index < count($contenidoModal) - 1) {
            // Solo agrega un <br> visible si el elemento actual no está oculto
            if (str_contains($elemento, 'display:none;')) {
                $contenidoFinal .= '<br style="display: none;">';
            } else {
                // Si el elemento está oculto, agrega un <br> oculto
                $contenidoFinal .= '<br>'; // Agrega un <br> visible
            }
        }
    }
    if (empty($buttons)) {
        $buttons = '
        <button type="button" class="btn btn-secondary secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary primary">Guardar</button>
        ';
    } else {
        $buttonslist = '';
        foreach ($buttons as $key => $value) {
            $buttonslist .= $value;
        }
        $buttons = $buttonslist;
    }    
    echo '
    <div class="modal fade" id="modalCRUD' . $idModal . '" tabindex="-1" style="display: none;"'.$atributosString.'>
            <div class="modal-dialog '.$ModalType.'">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle1' . $idModal . '">' . $titulo . '</h5>
                        <h5 class="modal-title" id="modalTitle2' . $idModal . '">' . $titulo2 . '</h5>
                        <h5 class="modal-title" id="modalTitle3' . $idModal . '">' . $titulo3 . '</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="Body' . $idModal . '">
                    <div style="display:none;" class="alert alert-warning bg-warning border-0 alert-dismissible fade show" role="alert" id="alert_' . htmlspecialchars($idModal) . '">
                        <div id="alertmsg_' . htmlspecialchars($idModal) . '"></div>
                        <button type="button" class="btn-close CloseAlertBox"  aria-label="Close"  alertid="'.htmlspecialchars($idModal).'"></button>
                    </div>
                        ' . $contenidoFinal . '
                    </div>
                    <div class="modal-footer">
                        '.$buttons.'
                    </div>
                </div>
            </div>
    </div>';
}

function CreateWeightInput($atributos = []) {
    $atributosString = '';
    foreach ($atributos as $key => $value) {
        if($key != 'etiqueta' && $key != 'class' && $key != 'div_clases'){
            $atributosString .= $key . '="' . htmlspecialchars($value) . '" ';
        }
    }

    $id = isset($atributos['id']) ? $atributos['id'] : null;
    $etiqueta = isset($atributos['etiqueta']) ? $atributos['etiqueta'] : '';
    $value = isset($atributos['value']) ? $atributos['value'] : '0.00';

    return '
    <div class="form-group weight-container" style="margin-top: 15px;">
        <input class="form-control weight-display" 
               id="' . htmlspecialchars($id) . '" 
               value="' . htmlspecialchars($value) . '" 
               ' . trim($atributosString) . '
               style="background-color: #001f3f; 
                      color: #7fdbff; 
                      font-family: \'Digital-7\', monospace;
                      font-size: 2em;
                      text-align: right;
                      padding-right: 10px;
                      border: 2px solid #0074D9;
                      border-radius: 5px;
                      width: calc(100% - 50px);
                      display: inline-block;">
        <span class="weight-unit">kg</span>
        <div id="error_' . htmlspecialchars($id) . '" class="invalid-feedback"></div>
    </div>';
}

function CreateWeightLabel($atributos = []) {
    $id = isset($atributos['id']) ? $atributos['id'] : null;
    $etiqueta = isset($atributos['etiqueta']) ? $atributos['etiqueta'] : '';
    
    return '
    <div class="weight-label" style="margin-bottom: 5px;">
        <label for="' . htmlspecialchars($id) . '" 
               style="font-weight: bold; 
                      color: #001f3f;">' 
        . htmlspecialchars($etiqueta) . 
        '</label>
    </div>';
}
?>
