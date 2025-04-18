<?php
    ob_start(); // Inicia la captura del buffer de salida

    include '../helpers/vistas_funciones.php';

    // Validar permisos para acceder a esta vista
    checkPerms(['ver_listas_compras']);

    // Validar permisos adicionales para acciones específicas
    $canCreate = checkPerms(['crear_listas_compras'], true);
    $canEdit = checkPerms(['editar_listas_compras'], true);
    $canDelete = checkPerms(['eliminar_listas_compras'], true);

    $PageSection = "Listas de Compras";
?>


<!-- Begin Page Title -->
<div class="pagetitle">
    <h1><?php echo $PageSection; ?></h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Home</a></li>
        <li class="breadcrumb-item">Compras</li>
        <li class="breadcrumb-item active" ><?php echo $PageSection; ?></li>
      </ol>
    </nav>
</div>
<!-- End Page Title -->

<?php 
  $id = 'listas_compras';
  $ButtonAddLabel = "Nueva lista de compras";
  $titulos = ['ID', 'Orden','Lista','Estado','La Creo','La Autorizo','Fecha de creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data, true, $botones_acciones,'StaticButtons');
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Lista',
      'Title3'=>'Ver Lista',
      'ModalType'=>'modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'compras'
    ],
    [
      CreateInput(['type'=>'text','maxlength'=>'200','id'=>'lista_compra','etiqueta'=>'Lista de Compra','required' => '']),
      CreateInput(['type'=>'number','id'=>'orden','etiqueta'=>'Orden','required' => '']),
      CreateInput(['type'=>'number','id'=>'num_articulos','etiqueta'=>'Número de Artículos','required' => '','min'=>'1']),
      CreateSelect(['id'=>'kid_estatus','etiqueta'=>'Estado','div_style'=>'display:none;','class'=>'OnlyInEdit'],$estatus),
      CreateSelect(['id'=>'kid_cuenta_bancaria','etiqueta'=>'Cuenta Bancaria','class'=>'OnEditReadOnly'],$cuentas_bancarias),
      CreateSelect(['id'=>'kid_proyecto','etiqueta'=>'Proyecto','required'=>'','class'=>'OnEditReadOnly'],$proyectos),
    '<div id="articulos_container"></div>' // Container for dynamic articles
    ]);


    $id='detalles_listas_compras';
    $ButtonAddLabel = "Nuevo Detalle";
    $titulos = ['ID', 'Lista de Compras','Articulos','Cantidad','Costo Unitario Total','Costo Unitario Neto','Monto Total','Monto Neto','Fecha de creación'];
  

    ob_start();
    CreateTable($id, $ButtonAddLabel, $titulos, [],true,[],'',$atributos = ['data-select-column'=>2]);
    $detailsTableOutput = ob_get_clean();

  CreateModal( [
    'id'=> $id.'-View', 
    'Title'=>'Detalle de Lista de Compras',
    'Title2'=>'Editar Lista',
    'Title3'=>'Ver Lista',
    'ModalType'=>'modal-fullscreen modal-dialog-scrollable', 
    'method'=>'POST',
    'action'=>'bd/crudSummit.php',
    'bloque'=>'compras'
  ],
  [
    $detailsTableOutput
  ],
  ['<button type="button" class="btn btn-secondary secondary" data-bs-dismiss="modal">Cancelar</button>']);
  
  // Agregar botón para ver detalles en la tabla principal
  $modalCRUD = 'detalles_listas_compras';
  $nuevo_boton = '
      <button class="ModalNewAdd3 btn btn-info info" modalCRUD="'.$modalCRUD.'"><i class="bi bi-file-spreadsheet"></i> Ver Detalles</button>
  ';
  $data_script['botones_acciones'] = array();
if(!isset($data_script['botones_acciones']) || !is_array($data_script['botones_acciones'])) {
    $data_script['botones_acciones'] = array();
}
if(!isset($data_script['botones_acciones']) || !is_array($data_script['botones_acciones'])) {
    $data_script['botones_acciones'] = array();
}
if(!isset($data_script['botones_acciones']) || !is_array($data_script['botones_acciones'])) {
    $data_script['botones_acciones'] = array();
}
if(!isset($data_script['botones_acciones']) || !is_array($data_script['botones_acciones'])) {
    $data_script['botones_acciones'] = array();
}
if(!isset($data_script['botones_acciones']) || !is_array($data_script['botones_acciones'])) {
    $data_script['botones_acciones'] = array();
}
if(!isset($data_script['botones_acciones']) || !is_array($data_script['botones_acciones'])) {
    $data_script['botones_acciones'] = array();
}
if(!isset($data_script['botones_acciones']) || !is_array($data_script['botones_acciones'])) {
    $data_script['botones_acciones'] = array();
}
array_push($data_script['botones_acciones'], $nuevo_boton);
  $data['data_show']['botones_acciones'] = $data_script['botones_acciones'];
  $optionkey = 'NewAdd3';
  $data_script[$optionkey] =['data_list_column'=>[]];

  // Ajustar botones de acción según los permisos
  $data_script['botones_acciones'] = [];
  if ($canEdit) {
      $data_script['botones_acciones'][] = '<button class="ModalDataEdit btn btn-warning warning" modalCRUD="${modalCRUD}"><i class="bi bi-pencil"></i> Editar</button>';
  }
  if ($canDelete) {
      $data_script['botones_acciones'][] = '<button class="ModalDataDelete btn btn-danger danger" modalCRUD="${modalCRUD}"><i class="bi bi-trash"></i> Eliminar</button>';
  }

  CreateModalForm(
  [
    'id'=> $id, 
    'Title'=>$ButtonAddLabel,
    'Title2'=>'Editar Detalle',
    'Title3'=>'Ver Detalle',
    'ModalType'=>'modal-dialog-scrollable', 
    'method'=>'POST',
    'action'=>'bd/crudSummit.php',
    'bloque'=>'compras',
    'data-select-column'=>'[2]',
    'data-input-fill'=>'[kid_lista_compras, orden]'
  ],
  [
    CreateInput(['id'=>'kid_lista_compras','etiqueta'=>'Lista de Compras','required' => '','readonly' => '','class'=>'OnEditReadOnly']),
    CreateSelect(['id'=>'kid_articulo','etiqueta'=>'Articulo','required' => '','class'=>'OnEditReadOnly'],$articulos),
    CreateInput(['type'=>'number','id'=>'cantidad','etiqueta'=>'Cantidad','required' => '','class'=>'MUL-1 MUL-2']),
    CreateInput(['type'=>'number','id'=>'costo_unitario_total','etiqueta'=>'Costo Unitario Total','required' => '','class'=>'MUL-1']),
    CreateInput(['type'=>'number','id'=>'costo_unitario_neto','etiqueta'=>'Costo Unitario Neto','required' => '','class'=>'MUL-2']),
    CreateInput(['type'=>'number','id'=>'monto_total','etiqueta'=>'Monto Total','required' => '','readonly' => '','class'=>'RESULT-1 RESULT-3']),
    CreateInput(['type'=>'number','id'=>'monto_neto','etiqueta'=>'Monto Neto','required' => '','readonly' => '','class'=>'RESULT-2 RESULT-4']),
    CreateInput(['type'=>'number','value'=>'0','id'=>'porcentaje_descuento','etiqueta'=>'Porcentaje de Descuento','required' => '','class'=>'DESC-3 DESC-4']),
  ]);

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
