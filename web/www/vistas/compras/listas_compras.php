<?php
    ob_start(); // Inicia la captura del buffer de salida
    $PageSection = "Programa de Compras";
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
  $ButtonAddLabel = "Nuevo Programa";
  $titulos = ['ID', 'Orden','Programa','Estado','La Creo','La Autorizo','Fecha de creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data, true, $botones_acciones,'StaticButtons');
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Programa',
      'Title3'=>'Ver Programa',
      'ModalType'=>'modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'compras'
    ],
    [
      CreateInput(['type'=>'text','maxlength'=>'200','id'=>'lista_compra','etiqueta'=>'Programa de Compra','required' => '']),
      CreateInput(['type'=>'number','id'=>'orden','etiqueta'=>'Orden','required' => ''])
    ]);

    $id='detalles_listas_compras';
    $ButtonAddLabel = "Nuevo Detalle";
$titulos = ['ID', 'Programa de Compras','Insumo','Cantidad (KG)','Precio por Kg','SubTotal','Precio por Kg (IVA)','Monto IVA','Retención de IVA','Total','Fecha de creación'];
  
    ob_start();
    CreateTable($id, $ButtonAddLabel, $titulos, [], true, [], '', $atributos = ['data-select-column'=>2]);
    $detailsTableOutput = ob_get_clean();
  CreateModal( [
    'id'=> $id.'-View', 
    'Title'=>'Detalle de Lista de Compras',
    'Title2'=>'Editar Lista',
    'Title3'=>'Ver Lista',
    'ModalType'=>'modal-fullscreen modal-dialog-scrollable', 
    'method'=>'POST',
    'action'=>'bd/crudSummit.php',
    'bloque'=>'compras',
     'data-select-column'=>'[1]',
    'data-input-fill'=>'[kid_lista_compras]'
  ],
  [
    $detailsTableOutput
  ],
  ['<button type="button" class="btn btn-secondary secondary" data-bs-dismiss="modal">Cancelar</button>'
]);

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
    'data-select-column'=>'[1]',
    'data-input-fill'=>'[kid_lista_compras]'
  ],
  [
    CreateInput(['id'=>'kid_lista_compras','etiqueta'=>'Programa de Compras','required' => '','readonly' => 'readonly','class'=>'OnEditReadOnly']),
    CreateSelect(['id'=>'kid_articulo','etiqueta'=>'Insumo','required' => '','class'=>'OnEditReadOnly','data-validation'=>'required'],$articulos),
    CreateInput(['type'=>'number','id'=>'cantidad','etiqueta'=>'Cantidad (KG)','required' => '','class'=>'MUL-1 MUL-2']),
    CreateInput(['type'=>'number','id'=>'costo_unitario_total','etiqueta'=>'Precio por Kg (MXN)','required' => '','class'=>'MUL-1']),
    CreateInput(['type'=>'number','id'=>'monto_total','etiqueta'=>'Subtotal','required' => '','readonly' => '','class'=>'RESULT-1']),
    CreateInput(['type'=>'number','id'=>'costo_unitario_neto','etiqueta'=>'Precio Por Kg (IVA)','required' => '','readonly' => '','class'=>'MUL-2']),
    CreateInput(['type'=>'number','id'=>'monto_neto','etiqueta'=>'Monto IVA','required' => '','readonly' => '','class'=>'RESULT-2']),
    CreateInput(['type'=>'number','value'=>'0','id'=>'porcentaje_descuento','etiqueta'=>'Retencion IVA','required' => '','class'=>'DESC']),
    CreateInput(['type'=>'number','id'=>'total','etiqueta'=>'Total','required' => '','readonly' => '','class'=>'RESULT-TOTAL'])
  ]);



  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
