<?php
    ob_start(); // Inicia la captura del buffer de salida
    $PageSection = "Ordenes de Compras";
?>


  <div class="pagetitle">
    <h1><?php echo $PageSection; ?></h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Home</a></li>
        <li class="breadcrumb-item">Compras</li>
        <li class="breadcrumb-item active" ><?php echo $PageSection; ?></li>
      </ol>
    </nav>
  </div><!-- End Page Title -->
<?php 

  $id = 'ordenes_compras';
  $ButtonAddLabel = "Nueva Orden de Compra";
  $titulos = ['ID', 'Código Externo', 'Orden de Compra', 'Proveedor', 'Monto Total', 'Monto Neto', 'Fecha de Creación'];
  
  // Define custom buttons for the table
  $botones_acciones = [
    '<button type="button" class="ModalDataView btn btn-primary primary" modalCRUD=${modalCRUD}><i class="bi bi-eye"></i> Ver</button>',
    '<button type="button" class="ModalDataEdit btn btn-warning warning" modalCRUD=${modalCRUD}><i class="bi bi-pencil"></i> Editar</button>',
    '<button type="button" class="ModalDataDelete btn btn-danger danger" modalCRUD=${modalCRUD}><i class="bi bi-trash"></i> Eliminar</button>',
    '<button type="button" class="ModalNewAdd3 btn btn-info info" modalCRUD=${modalCRUD}><i class="bi bi-list-check"></i> Ver Detalles</button>',
    '<button type="button" class="IniciarPesaje btn btn-success success" modalCRUD=${modalCRUD}><i class="bi bi-box-seam"></i> Iniciar Pesaje</button>'
  ];
  CreateTable($id, $ButtonAddLabel, $titulos, $data,true,'ButtonsInRow');
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Orden de Compra',
      'Title3'=>'Ver Orden de Compra',
      'ModalType'=>'modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'compras'
    ],
    [
      CreateInput(['type'=>'text','maxlength'=>'100','id'=>'orden_compras','etiqueta'=>'Orden de Compras','required' => '']),
      CreateInput(['type'=>'text','maxlength'=>'80','id'=>'codigo_externo','etiqueta'=>'Código Externo','required' => '']),
      CreateInput(['type'=>'text','id'=>'kid_proyecto','etiqueta'=>'Proyecto','required' => '','readonly' => '','div_style'=>'display:none;','class'=>'OnEditReadOnly']),
      CreateSelect(['id'=>'kid_proveedor','type'=>'text','id'=>'kid_proveedor','etiqueta'=>'Proveedor','required' => '','class'=>'OnEditReadOnly'],$proveedores),
      CreateInput(['type'=>'number','id'=>'monto_total','etiqueta'=>'Monto Total','required' => '']),
      CreateInput(['type'=>'number','id'=>'monto_neto','etiqueta'=>'Monto Neto','required' => '', 'readonly' => '']),
      //CreateSelect(['id'=>'kid_estatus','etiqueta'=>'Estado','div_style'=>'display:none;','class'=>'OnlyInEdit'],$estatus),
    ]);

  $id='detalles_ordenes_compras';
  $ButtonAddLabel = "Nuevo Detalle";
  $titulos = ['ID', 'Orden de Compra','Materia Prima','Cantidad','Costo Unitario Total','Costo Unitario Neto','Monto Total','Monto Neto','Fecha de creación'];

  ob_start();
  CreateTable($id, $ButtonAddLabel, $titulos, [], true, [], '', ['data-select-column'=>'[1]']);
  $detailsTableOutput = ob_get_clean();

  CreateModal([
    'id'=> $id.'-View', 
    'Title'=>'Detalles de Orden de Compra',
    'Title2'=>'',
    'Title3'=>'',
    'ModalType'=>'modal-fullscreen modal-dialog-scrollable', 
    'method'=>'POST',
    'action'=>'bd/crudSummit.php',
    'bloque'=>'compras'
  ],
  [
    $detailsTableOutput
  ],
  ['<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCRUDdetalles_ordenes_compras">Nuevo Detalle</button>',
   '<button type="button" class="btn btn-secondary secondary" data-bs-dismiss="modal">Cancelar</button>']);

  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Detalle de Orden',
      'Title3'=>'Ver Detalle de Orden',
      'ModalType'=>'modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'compras',
      'data-select-column'=>'[1]',
      'data-input-fill'=>'[kid_orden_compras]'
    ],
    [
      CreateInput(['id'=>'kid_orden_compras','etiqueta'=>'Orden de Compras','required' => '','readonly' => '','class'=>'OnEditReadOnly']),
      CreateSelect(['id'=>'kid_articulo','etiqueta'=>'Materia Prima','required' => ''],$articulos),
      CreateInput(['type'=>'number','id'=>'cantidad','etiqueta'=>'Cantidad De Super Sacos','required' => '','class'=>'MUL-1 MUL-2']),
      CreateInput(['type'=>'number','id'=>'costo_unitario_total','etiqueta'=>'Costo Unitario Total','required' => '','class'=>'MUL-1']),
      CreateInput(['type'=>'number','id'=>'costo_unitario_neto','etiqueta'=>'Costo Unitario Neto','required' => '','readonly' => '','class'=>'MUL-2']),
      CreateInput(['type'=>'number','id'=>'monto_total','etiqueta'=>'Monto Total','required' => '','readonly' => '','class'=>'RESULT-1 RESULT-3']),
      CreateInput(['type'=>'number','id'=>'monto_neto','etiqueta'=>'Monto Neto','required' => '','readonly' => '','class'=>'RESULT-2 RESULT-4']),
      CreateInput(['type'=>'number','value'=>'0','id'=>'porcentaje_descuento','etiqueta'=>'Porcentaje de Descuento','required' => '','class'=>'DESC-3 DESC-4']),
    ]);

  $id='recepciones_compras';
  $ButtonAddLabel = "Nueva Recepción";
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Recepción',
      'Title3'=>'Ver Recepción',
      'ModalType'=>'modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'compras'
    ],
    [
      CreateInput(['type'=>'text','maxlength'=>'100','id'=>'recepcion_compras-SetData','etiqueta'=>'Recepción','required' => '']),
      CreateInput(['type'=>'text','maxlength'=>'100','id'=>'codigo_externo-SetData','etiqueta'=>'Código Externo','required' => '']),
      CreateSelect(['type'=>'text','id'=>'kid_almacen','etiqueta'=>'Almacén','readonly' => ''],$almacenes),
      CreateSelect(['id'=>'kid_recibe','etiqueta'=>'Recibió'],$colaboradores),
      CreateSelect(['id'=>'kid_reclama','etiqueta'=>'Reclamo'],$colaboradores),
      CreateSelect(['id'=>'kid_regresa','etiqueta'=>'Regreso '],$colaboradores),
    ]);

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer

  // Incluir el script específico para órdenes de compra

  include 'wrapper.php'; // Incluye el wrapper
?>
