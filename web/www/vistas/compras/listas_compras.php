<?php
    ob_start(); // Inicia la captura del buffer de salida
    $PageSection = "Listas de Compras";
?>

  <div class="pagetitle">
    <h1><?php echo $PageSection; ?></h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.html">Home</a></li>
        <li class="breadcrumb-item">Compras</li>
        <li class="breadcrumb-item active" ><?php echo $PageSection; ?></li>
      </ol>
    </nav>
  </div><!-- End Page Title -->
<?php 

  $id = 'listas_compras';
  $ButtonAddLabel = "Nuevo Lista";
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
      CreateSelect(['id'=>'kid_cuenta_bancaria','etiqueta'=>'Cuenta Bancaria','div_style'=>'display:none;','class'=>'OnEditReadOnly'],$cuentas_bancarias),
      CreateSelect(['id'=>'kid_proyecto','etiqueta'=>'Proyecto','div_style'=>'display:none;','required'=>'','class'=>'OnEditReadOnly'],$proyectos),
      CreateSelect(['id'=>'kid_estatus','etiqueta'=>'Estado','div_style'=>'display:none;','class'=>'OnlyInEdit'],$estatus)
    ]);

    $id='detalles_listas_compras';
    $ButtonAddLabel = "Nuevo Detalle";
    $titulos = ['ID', 'Lista de Compras','Insumos','Cantidad','Costo Unitario Total','Costo Unitario Neto','Monto Total','Monto Neto','Fecha de creación'];
  
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
    CreateSelect(['id'=>'kid_articulo','etiqueta'=>'Insumo','required' => '','class'=>'OnEditReadOnly','data-validation'=>'required'],$articulos),
    CreateInput(['type'=>'number','id'=>'cantidad','etiqueta'=>'Cantidad De Super Sacos','required' => '','class'=>'MUL-1 MUL-2']),
    CreateInput(['type'=>'number','id'=>'costo_unitario_total','etiqueta'=>'Costo Unitario Total','required' => '','class'=>'MUL-1']),
    CreateInput(['type'=>'number','id'=>'costo_unitario_neto','etiqueta'=>'Costo Unitario Neto','required' => '','readonly' => '','class'=>'MUL-2']),
    CreateInput(['type'=>'number','id'=>'monto_total','etiqueta'=>'Monto Total','required' => '','readonly' => '','class'=>'RESULT-1 RESULT-3']),
    CreateInput(['type'=>'number','id'=>'monto_neto','etiqueta'=>'Monto Neto','required' => '','readonly' => '','class'=>'RESULT-2 RESULT-4']),
    CreateInput(['type'=>'number','value'=>'0','id'=>'porcentaje_descuento','etiqueta'=>'Porcentaje de Descuento','required' => '','class'=>'DESC-3 DESC-4']),
  ]);



  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
