<?php
    ob_start(); // Inicia la captura del buffer de salida
    $PageSection = "Recibir orden de compra";
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
  $ButtonAddLabel = "Nueva Recepción";
  $titulos = ['ID', 'Orden de Compra','Código Externo','Grupo de Cotización','Proyecto','Proveedor','Monto Total','Monto Neto','Estado','Fecha de creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data,true);
  // Add centered button
echo '<div class="row mt-3">
<div class="col-12 text-center">
    <button type="button" id="btn_finalizar_recepcion" class="btn btn-primary">
        <i class="bi bi-check-circle"></i> Finalizar recepción
    </button>
</div>
</div>';

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
      CreateInput(['type'=>'text','id'=>'kid_proyecto','etiqueta'=>'Proyecto','required' => '','readonly' => '','class'=>'OnEditReadOnly']),
      CreateInput(['type'=>'text','id'=>'kid_proveedor','etiqueta'=>'Proveedor','required' => '','readonly' => '','class'=>'OnEditReadOnly']),
      CreateInput(['type'=>'number','id'=>'monto_total','etiqueta'=>'Monto Total','required' => '','readonly' => '']),
      CreateInput(['type'=>'number','id'=>'monto_neto','etiqueta'=>'Monto Neto','required' => '','readonly' => '']),
      CreateSelect(['id'=>'kid_estatus','etiqueta'=>'Estado','div_style'=>'display:none;','class'=>'OnlyInEdit'],$estatus),
      //CreateButton(['id'=>'button_aceptar_recepcion','etiqueta'=>'Confirmar Recepción','modalCRUD'=>'ordenes_compras','op'=>1,'class'=>'OnlyInEdit btn btn-primary primary GhangeEstatus'])
    ]);

  
    $id='detalles_ordenes_compras';
    $ButtonAddLabel = "Nuevo Detalle";
    $titulos = ['ID', 'Orden de Compra','Grupo Cotización','Articulos','Cantidad','Costo Unitario Total','Costo Unitario Neto','Monto Total','Monto Neto','Fecha de creación'];
  

    ob_start();
    CreateTable($id, $ButtonAddLabel, $titulos, [],false,[],'',$atributos = ['data-select-column'=>2]);
    $detailsTableOutput = ob_get_clean();

    CreateModal( [
      'id'=> $id.'-View', 
      'Title'=>'Contenido de Ordenes de Compras',
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
        'Title2'=>'Editar Detalle de Orden',
        'Title3'=>'Ver Detalle de Orden',
        'ModalType'=>'modal-dialog-scrollable', 
        'method'=>'POST',
        'action'=>'bd/crudSummit.php',
        'bloque'=>'compras'
      ],
      [
        CreateInput(['id'=>'kid_orden_compra','etiqueta'=>'Orden de Compras','required' => '','class'=>'OnEditReadOnly']),
        CreateSelect(['id'=>'kid_articulo','etiqueta'=>'Articulo','required' => '','class'=>'OnEditReadOnly'],[]),
        CreateInput(['type'=>'number','id'=>'cantidad','etiqueta'=>'Cantidad','required' => '','class'=>'MUL-1 MUL-2']),
        CreateInput(['type'=>'number','id'=>'costo_unitario_total','etiqueta'=>'Costo Unitario Total','required' => '','class'=>'MUL-1']),
        CreateInput(['type'=>'number','id'=>'costo_unitario_neto','etiqueta'=>'Costo Unitario Neto','required' => '','class'=>'MUL-2']),
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

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
