<?php
    ob_start(); // Inicia la captura del buffer de salida


    

    $PageSection = "Detalles de Listas de Compras";
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

  $id = 'detalles_listas_compras';
  $ButtonAddLabel = "Nuevo Detalle";
  $titulos = ['ID', 'Lista de Compras','Insumo','Cantidad','Costo Unitario Total','Costo Unitario Neto','Descuento','Monto Total','Monto Neto','Fecha de creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data);
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
      'data-input-fill'=>'[kid_articulo]'
    ],
    [
      CreateSelect(['id'=>'kid_lista_compras','etiqueta'=>'Lista de Compras','required' => '','class'=>'OnEditReadOnly'],$listas_compras),
      CreateSelect(['id'=>'kid_articulo','etiqueta'=>'Insumo','required' => '','class'=>'OnEditReadOnly'],$articulos),
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
