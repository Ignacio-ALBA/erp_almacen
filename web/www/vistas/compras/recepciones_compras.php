<?php
    ob_start(); // Inicia la captura del buffer de salida


    

    $PageSection = "Recepciones";
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

  $id = 'recepciones_compras';
  $ButtonAddLabel = "Nuevo recepción";
  $titulos = ['ID', 'Recepción','Código Externo','Proyecto','Proveedor','Almacén','Orden de Compra','Estado','Fecha de creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data, false, $botones_acciones);
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar recepción',
      'Title3'=>'Ver Lista',
      'ModalType'=>'modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'compras'
    ],
    [
      CreateInput(['type'=>'text','maxlength'=>'100','id'=>'recepcion_compras','etiqueta'=>'Recepción','required' => '']),
      CreateInput(['type'=>'text','maxlength'=>'100','id'=>'codigo_externo','etiqueta'=>'Código Externo','required' => '']),
      CreateInput(['type'=>'text','id'=>'kid_proyecto','etiqueta'=>'Proyecto','readonly' => '']),
      CreateInput(['type'=>'text','id'=>'kid_proveedor','etiqueta'=>'Proveedor','readonly' => '']),
      CreateInput(['type'=>'text','id'=>'kid_orden_compras','etiqueta'=>'Orden de Compra','readonly' => '']),
      CreateSelect(['type'=>'text','id'=>'kid_almacen','etiqueta'=>'Almacén','readonly' => ''],$almacenes),
      CreateSelect(['id'=>'kid_recibe','etiqueta'=>'Recibió','required' => ''],$colaboradores),
      CreateSelect(['id'=>'kid_reclama','etiqueta'=>'Reclamo','required' => ''],$colaboradores),
      CreateSelect(['id'=>'kid_regresa','etiqueta'=>'Regreso ','required' => ''],$colaboradores),
      CreateInput(['type'=>'number','id'=>'monto_total','etiqueta'=>'Monto Total']),
      CreateInput(['type'=>'number','id'=>'monto_neto','etiqueta'=>'Monto Neto']),
    ]);

    $id='detalles_recepciones_compras';
    $ButtonAddLabel = "Nuevo Detalle";
    $titulos = ['ID','Artículos', 'Recepción','Cantidad','Costo Unitario Total','Costo Unitario Neto','Monto Total','Monto Neto','Fecha de creación'];
  

    ob_start();
    CreateTable($id, $ButtonAddLabel, $titulos, [],false,[],'',$atributos = ['data-select-column'=>2]);
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
    CreateInput(['id'=>'kid_recepcion_compras','etiqueta'=>'Recepción','required' => '','class'=>'OnEditReadOnly']),
      CreateInput(['id'=>'kid_articulo','etiqueta'=>'Articulo','required' => '','class'=>'OnEditReadOnly']),
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
