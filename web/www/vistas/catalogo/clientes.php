<?php
    ob_start(); // Inicia la captura del buffer de salida
    $PageSection = "Clientes";
?>


  <div class="pagetitle">
    <h1><?php echo $PageSection; ?></h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Home</a></li>
        <li class="breadcrumb-item"><?php echo $nombre_modulo; ?></li>
        <li class="breadcrumb-item active" ><?php echo $PageSection; ?></li>
      </ol>
    </nav>
  </div><!-- End Page Title -->
<?php 

  $id = 'clientes';
  $ButtonAddLabel = "Nuevo Cliente";
  $titulos = ['ID', 'Código','Nombre','Razón Social','RFC','Correo','Fecha de creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data,true, 'ButtonsInRow','StaticButtons');
  
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Cliente',
      'Title3'=>'Ver Cliente',
      'ModalType'=>'modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'planeacion'
    ],
    [
      CreateInput(['type'=>'text','id'=>'codigo','etiqueta'=>'Código','required' => '']),
      CreateInput(['type'=>'text','id'=>'nombre','etiqueta'=>'Nombre','required' => '']),
      CreateInput(['type'=>'text','id'=>'razon_social','etiqueta'=>'Razón Social','required' => '']),
      CreateInput(['type'=>'text','id'=>'rfc','etiqueta'=>'RFC','class'=>'ValidateRFC','required' => '']),
      CreateInput(['type'=>'text','id'=>'curp','etiqueta'=>'CURP','class'=>'ValidateCURP']),
      CreateInput(['type'=>'email','id'=>'email','etiqueta'=>'Correo Electrónico','class'=>'ValidateCorreo','required' => '']),
      CreateSelect(['id'=>'nombre_pais','etiqueta'=>'País'],$paises),
      CreateSelect(['id'=>'kid_estado','etiqueta'=>'Estado', 'class'=>'DataGET Data-GETMunicipios','bloque'=>'catalogo'],$estados),
      CreateSelect(['id'=>'nombre_municipio','etiqueta'=>'Municipio', 'class'=>'kid_estado'],[]),
      CreateInput(['type'=>'number','id'=>'cp','etiqueta'=>'CP', 'class'=>'DataGET Data-GETColonia','bloque'=>'catalogo']),
      CreateInput(['type'=>'text','id'=>'nombre_colonia','etiqueta'=>'Colonia', 'class'=>'cp']),
      CreateInput(['type'=>'text','id'=>'nombre_localidad','etiqueta'=>'Localidad']),
      CreateInput(['type'=>'text','id'=>'nombre_vialidad','etiqueta'=>'Vialidad']),
      CreateInput(['type'=>'number','id'=>'numero_exterior','etiqueta'=>'Número Exterior']),
      CreateInput(['type'=>'number','id'=>'numero_interior','etiqueta'=>'Número Interior']),
      CreateInput(['type'=>'number','id'=>'telefono1','etiqueta'=>'Teléfono 1','class'=>'ValidateTelefono']),
      CreateInput(['type'=>'number','id'=>'telefono2','etiqueta'=>'Teléfono 2','class'=>'ValidateTelefono']),
    
      //CreateSelect(['id'=>'pais','etiqueta'=>'País','readonly' => '','disabled' => ''],$paises),
      //CreatSwitchCheck(['id'=>'pordefecto','etiqueta'=>'Por defecto'])
      
    ]);

    /*                   Planeaciones de Compras                   */
    
    $id = 'planeaciones_compras';
    $ButtonAddLabel = "Nueva Planeación de Compra";
    $titulos = ['ID', 'Bolsa de Proyectos','Proyecto','Cliente','Estado','La Creo','Fecha de creación'];
  

    ob_start();
    CreateTable($id, $ButtonAddLabel, $titulos, [],true,[],'',$atributos = ['data-select-column'=>2]);
    $detailsTableOutput = ob_get_clean();

    CreateModal( [
      'id'=> $id.'-View', 
      'Title'=>'Contenido de Planeación de Compra',
      'Title2'=>'Editar Planeación de Compra',
      'Title3'=>'Ver Planeación',
      'ModalType'=>'modal-fullscreen modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'planeacion'
    ],
    [
      $detailsTableOutput
    ],
    ['<button type="button" class="btn btn-secondary secondary" data-bs-dismiss="modal">Cancelar</button>']);


    CreateModalForm(
      [
        'id'=> $id, 
        'Title'=>$ButtonAddLabel,
        'Title2'=>'Editar Planeación',
        'Title3'=>'Ver Planeación',
        'ModalType'=>'modal-dialog-scrollable', 
        'method'=>'POST',
        'action'=>'bd/crudSummit.php',
        'bloque'=>'planeacion',
        'data-select-column'=>'[2]',
        'data-input-fill'=>'[kid_cliente]'
      ],
      [
        CreateSelect(['id'=>'kid_bolsa_proyecto','etiqueta'=>'Bolsa de Proyectos','required' => '','class'=>'DataGET Data-GETProyectoByBolsa'],$bolsas_proyectos),
        CreateSelect(['id'=>'kid_proyecto','etiqueta'=>'Proyecto','required' => '','class'=>'kid_bolsa_proyecto OnOpenClear'],[]),
        CreateInput(['type'=>'text','id'=>'kid_cliente','etiqueta'=>'Cliente','readonly' => '']),
        CreateInput(['type'=>'number','id'=>'costo_total_almacen','etiqueta'=>'Costo Total en Almacén','required' => '']),
        CreateInput(['type'=>'number','id'=>'costo_total_a_comprar','etiqueta'=>'Costo a Comprar','required' => '']),
        CreateInput(['type'=>'number','id'=>'monto_total','etiqueta'=>'Monto Total','required' => '']),
        CreateInput(['type'=>'number','id'=>'registros_almacen','etiqueta'=>'Registros en Almacén','required' => '']),
        CreateInput(['type'=>'number','id'=>'registros_a_comprar','etiqueta'=>'Registros a Comprar','required' => '']),
        CreateInput(['type'=>'number','id'=>'registros_total','etiqueta'=>'Registros Totales','required' => '']),
      ]);


    /*                   Planeaciones de Recursos Humanos                   */
    $id = 'planeaciones_recursos_humanos';
    $ButtonAddLabel = "Nueva Planeación de Recursos Humanos";
    $titulos = ['ID', 'Bolsa de Proyectos','Proyecto','Cliente','No. Internos','No. Externos','Estado','La Creo','Fecha de creación'];
  

    ob_start();
    CreateTable($id, $ButtonAddLabel, $titulos, [],true,[],'',$atributos = ['data-select-column'=>2]);
    $detailsTableOutput = ob_get_clean();

    CreateModal( [
      'id'=> $id.'-View', 
      'Title'=>'Contenido de Planeación de RRHH',
      'Title2'=>'Editar Planeación de RRHH',
      'Title3'=>'Ver Planeación de RRHH',
      'ModalType'=>'modal-fullscreen modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'planeacion'
    ],
    [
      $detailsTableOutput
    ],
    ['<button type="button" class="btn btn-secondary secondary" data-bs-dismiss="modal">Cancelar</button>']);

    CreateModalForm(
      [
        'id'=> $id, 
        'Title'=>$ButtonAddLabel,
        'Title2'=>'Editar Planeación',
        'Title3'=>'Ver Planeación',
        'ModalType'=>'modal-dialog-scrollable', 
        'method'=>'POST',
        'action'=>'bd/crudSummit.php',
        'bloque'=>'planeacion',
        'data-select-column'=>'[2]',
        'data-input-fill'=>'[kid_cliente]'
      ],
      [
        CreateSelect(['id'=>'kid_bolsa_proyecto','etiqueta'=>'Bolsa de Proyectos','required' => '','class'=>'DataGET Data-GETProyectoByBolsa'],$bolsas_proyectos),
        CreateSelect(['id'=>'kid_proyecto','etiqueta'=>'Proyecto','required' => '','class'=>'kid_bolsa_proyecto OnOpenClear'],[]),
        CreateInput(['type'=>'text','id'=>'kid_cliente','etiqueta'=>'Cliente','readonly' => '']),
        CreateInput(['type'=>'number','id'=>'cantidad_internos','etiqueta'=>'Número de Interno','required' => '','class'=>'MUL-1']),
        CreateInput(['type'=>'number','id'=>'costo_cantidad_internos','etiqueta'=>'Costo pot Número de Internos','required' => '','class'=>'MUL-1']),
        CreateInput(['type'=>'number','id'=>'cantidad_externos','etiqueta'=>'Número de Externos','required' => '','class'=>'MUL-2']),
        CreateInput(['type'=>'number','id'=>'costo_cantidad_externos','etiqueta'=>'Costo por Número de Externos','required' => '','class'=>'MUL-2']),
        CreateInput(['type'=>'number','id'=>'monto_total','etiqueta'=>'Monto Total','required' => '','readonly' => '','class'=>'RESULT-3']),
    ]);
    echo CreateInput(['type'=>'number','id'=>'RESULT-1SUM-3','div_style'=>'display:none;','disabled' => '','class'=>'RESULT-1 SUM-3']);
    echo CreateInput(['type'=>'number','id'=>'RESULT-2SUM-3','div_style'=>'display:none;','disabled' => '','class'=>'RESULT-2 SUM-3']);

    /*                   Planeaciones de Actividades                   */

    $id = 'planeaciones_actividades';
    $ButtonAddLabel = "Nueva Planeación de Actividades";
    $titulos = ['ID', 'Bolsa de Proyectos','Proyecto','Cliente','Fecha de Inicio','Fecha de Fin','Total de Días','Estado','La Creo','Fecha de creación'];
  
    ob_start();
    CreateTable($id, $ButtonAddLabel, $titulos, [],true,[],'',$atributos = ['data-select-column'=>2]);
    $detailsTableOutput = ob_get_clean();

    CreateModal( [
      'id'=> $id.'-View', 
      'Title'=>'Planeación de Actividades',
      'Title2'=>'Editar Planeación de RRHH',
      'Title3'=>'Ver Planeación de RRHH',
      'ModalType'=>'modal-fullscreen modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'planeacion'
    ],
    [
      $detailsTableOutput
    ],
    ['<button type="button" class="btn btn-secondary secondary" data-bs-dismiss="modal">Cancelar</button>']);


    CreateModalForm(
      [
        'id'=> $id, 
        'Title'=>$ButtonAddLabel,
        'Title2'=>'Editar Planeación de Actividades',
        'Title3'=>'Ver Planeación de Actividades',
        'ModalType'=>'modal-dialog-scrollable', 
        'method'=>'POST',
        'action'=>'bd/crudSummit.php',
        'bloque'=>'planeacion',
        'data-select-column'=>'[2]',
        'data-input-fill'=>'[kid_cliente]'
      ],
      [
        CreateSelect(['id'=>'kid_bolsa_proyecto','etiqueta'=>'Bolsa de Proyectos','required' => '','class'=>'DataGET Data-GETProyectoByBolsa'],$bolsas_proyectos),
        CreateSelect(['id'=>'kid_proyecto','etiqueta'=>'Proyecto','required' => '','class'=>'kid_bolsa_proyecto OnOpenClear'],[]),
        CreateInput(['type'=>'text','id'=>'kid_cliente','etiqueta'=>'Cliente','readonly' => '']),
        CreateInput(['type'=>'number','id'=>'cantidad_actividades','etiqueta'=>'Número de Actividades','required' => '']),
        CreateInput(['type'=>'date','id'=>'fecha_inicial','etiqueta'=>'Fecha de Inicio','required' => '', 'class'=>'DateStartCal-1']),
        CreateInput(['type'=>'date','id'=>'fecha_final','etiqueta'=>'Fecha de Fin','required' => '', 'class'=>'DateEndCal-1']),
        CreateInput(['type'=>'number','id'=>'dias_totales','etiqueta'=>'Dias Totales','required' => '', 'class'=>'DateResultCal-1']),
        CreateInput(['type'=>'number','id'=>'cantidad_rrhh','etiqueta'=>'Cantidad de Personal','required' => '']),
      ]);


  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
