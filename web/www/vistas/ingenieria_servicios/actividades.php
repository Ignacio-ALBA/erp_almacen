<?php
    ob_start(); // Inicia la captura del buffer de salida
    $PageSection = "Actividades";
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
$botones_acciones = [
  '<button class="ModalDataView btn btn-primary primary" modalCRUD="cronograma"><i class="bi bi-eye"></i> Ver Cronograma</button>',
  '<button class="ModalNewAdd3 btn btn-info info" modalCRUD="asignacion_viaticos"><i class="bi bi-wallet2"></i> Viáticos</button>',
];

  $id = 'actividades';
  $ButtonAddLabel = "Nuevo Proveedor";
  $titulos = ['ID', 'Bolsa de Proyecto','Proyecto','Cliente','Actividades','Actividades Terminadas','Actividades Pendientes','Actividades Retrasadas',
    'Fecha de Inicio','Fecha de Fin','Días Totales', 'Horas Totales','Estado'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data,false,$botones_acciones);

  CreateModalForm([
      'id'=> 'cronograma', 
      'Title'=>"Ver Evidencias de  Actividad",
      'Title2'=>'Ver Evidencias de  Actividad',
      'Title3'=>'Ver Evidencias de actividad',
      'ModalType'=>'modal-dialog modal-fullscreen', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'ingenieria_servicios',
      'modalCRUD'=>'cronograma'
    ],["<div id='calendar'></div>"]);

  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Proveedor',
      'Title3'=>'Ver Proveedor',
      'ModalType'=>'modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'ingenieria_servicios'
    ],
    [
      CreateInput(['type'=>'text','maxlength'=>'16','id'=>'codigo','etiqueta'=>'Código','required' => '']),
      CreateInput(['type'=>'number','id'=>'orden','etiqueta'=>'Orden','required' => '']),
      CreateInput(['type'=>'text','maxlength'=>'100','id'=>'proveedor','etiqueta'=>'Categoría','required' => '']),
      CreateInput(['type'=>'text','maxlength'=>'100','id'=>'razon_social','etiqueta'=>'Razón Social','required' => '']),
      CreateInput(['type'=>'text','maxlength'=>'100','id'=>'nombre_comercial','etiqueta'=>'Nombre Comercial','required' => '']),
      CreateInput(['type'=>'text','maxlength'=>'13','id'=>'rfc','etiqueta'=>'RFC','class'=>'ValidateRFC','required' => '']),
      CreateInput(['type'=>'text','maxlength'=>'50','id'=>'nombre','etiqueta'=>'Nombre','required' => '']),
      CreateInput(['type'=>'text','maxlength'=>'50','id'=>'apellido_paterno','etiqueta'=>'Apellido Paterno','required' => '']),
      CreateInput(['type'=>'text','maxlength'=>'50','id'=>'apellido_materno','etiqueta'=>'Apellido Materno','required' => '']),
      CreateInput(['type'=>'number','min'=>'1'.str_repeat('0',9), 'max'=>str_repeat('9', 10),'id'=>'telefono1','etiqueta'=>'Teléfono 1','class'=>'ValidateTelefono']),
      CreateInput(['type'=>'number','min'=>'1'.str_repeat('0',9), 'max'=>str_repeat('9', 10),'id'=>'telefono2','etiqueta'=>'Teléfono 2','class'=>'ValidateTelefono']),
      CreateInput(['type'=>'number','min'=>'1'.str_repeat('0',9), 'max'=>str_repeat('9', 10),'id'=>'celular1','etiqueta'=>'Celular 1','class'=>'ValidateTelefono']),
      CreateInput(['type'=>'number','min'=>'1'.str_repeat('0',9), 'max'=>str_repeat('9', 10),'id'=>'celular2','etiqueta'=>'Celular 2','class'=>'ValidateTelefono']),
      CreateInput(['type'=>'email','id'=>'email1','etiqueta'=>'Correo 1','class'=>'ValidateCorreo']),
      CreateInput(['type'=>'email','id'=>'email2','etiqueta'=>'Correo 2','class'=>'ValidateCorreo']),
      CreateSelect(['id'=>'nombre_municipio','etiqueta'=>'Municipio', 'class'=>'kid_estado'],[]),
      CreateInput(['type'=>'number','maxlength'=>'6','id'=>'cp','etiqueta'=>'CP', 'class'=>'DataGET Data-GETColonia','bloque'=>'catalogo']),
      CreateInput(['type'=>'text','maxlength'=>'200','id'=>'nombre_colonia','etiqueta'=>'Colonia', 'class'=>'cp']),
      CreateInput(['type'=>'text','maxlength'=>'200','id'=>'nombre_localidad','etiqueta'=>'Localidad']),
      CreateInput(['type'=>'text','maxlength'=>'200','id'=>'nombre_vialidad','etiqueta'=>'Vialidad']),
      CreateInput(['type'=>'number','maxlength'=>'5','id'=>'numero_exterior','etiqueta'=>'Número Exterior']),
      CreateInput(['type'=>'number','maxlength'=>'5','id'=>'numero_interior','etiqueta'=>'Número Interior']),
      CreateSelect(['id'=>'calificacion','etiqueta'=>'Calificación'],[['valor'=>6,'pordefecto'=>1],['valor'=>5,'pordefecto'=>1],['valor'=>4,'pordefecto'=>0],['valor'=>3,'pordefecto'=>0],['valor'=>2,'pordefecto'=>0],['valor'=>1,'pordefecto'=>0]]),
      //CreateSelect(['id'=>'pais','etiqueta'=>'País','readonly' => '','disabled' => ''],$paises),
      CreatSwitchCheck(['id'=>'pordefecto','etiqueta'=>'Por defecto'])
      
    ]);

  $id = 'asignacion_viaticos';
  $ButtonAddLabel = "Nueva Asignación";
  $titulos = ['ID', 'Tipo de Viático','Justificación','Monto','Monto Real','Responsable','Proyecto','Actividad','Fecha de Creación'];
  

    ob_start();
    CreateTable($id, $ButtonAddLabel, $titulos, [],true,[],'',$atributos = ['data-select-column'=>0]);
    $detailsTableOutput = ob_get_clean();

    CreateModal( [
      'id'=> $id.'-View', 
      'Title'=>'Contenido de Planeación de RRHH',
      'Title2'=>'Editar Planeación de RRHH',
      'Title3'=>'Ver Planeación de RRHH',
      'ModalType'=>'modal-fullscreen modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'compras'
    ],
    [
      $detailsTableOutput
    ],
    ['<button type="button" class="btn btn-secondary secondary" data-bs-dismiss="modal">Cancelar</button>']);


    CreateModalForm(
      [
        'id'=> $id, 
        'Title'=>$ButtonAddLabel,
        'Title2'=>'Editar Asignación',
        'Title3'=>'Ver Asignación',
        'ModalType'=>'modal-dialog-scrollable', 
        'method'=>'POST',
        'action'=>'bd/crudSummit.php',
        'bloque'=>'compras'
      ],
      [
        CreateSelect(['id'=>'kid_tipo_viatico','etiqueta'=>'Tipo de Viatico'],$tipos_viaticos),
        CreateInput(['type'=>'text','maxlength'=>'100','id'=>'justificacion','etiqueta'=>'Justificación']),
        CreateInput(['type'=>'number','id'=>'monto_asignado','etiqueta'=>'Monto Asignado']),
        CreateInput(['type'=>'number','id'=>'monto_real','etiqueta'=>'Monto Real']),
        CreateSelect(['id'=>'kid_responsable','etiqueta'=>'Responsable'],$colaboradores),
        CreateSelect(['id'=>'kid_proyecto','etiqueta'=>'Proyecto'],[]),
        CreateInput(['type'=>'number','id'=>'grupo','etiqueta'=>'Grupo']),
        CreateSelect(['id'=>'kid_detalle_actividad','etiqueta'=>'Actividad'],[]),
        CreateSelect(['id'=>'kid_actividad','etiqueta'=>'No. Actividad'],[]),
        //CreateSelect(['id'=>'pais','etiqueta'=>'País','readonly' => '','disabled' => ''],$paises),
       // CreatSwitchCheck(['id'=>'pordefecto','etiqueta'=>'Por defecto'])
        
      ]);


  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
