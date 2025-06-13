<?php
    ob_start(); // Inicia la captura del buffer de salida


    

    $PageSection = "Detalles de Programa de Compras";
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
  $titulos = ['ID', 'Programa de Compras','Insumo','Cantidad (KG)','Precio por Kg','SubTotal','Precio por Kg (IVA)','Monto IVA','Retención de IVA','Total','Fecha de creación'];
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
      'data-select-column'=>'[1,2]',
      'data-input-fill'=>'[kid_lista_compras,kid_articulo]'
    ],
    [
      CreateSelect(['id'=>'kid_lista_compras','etiqueta'=>'Cotización','required' => '','class'=>'OnEditReadOnly'],$listas_compras),
      CreateSelect(['id'=>'kid_articulo','etiqueta'=>'Insumo','required' => '','class'=>'OnEditReadOnly'],$articulos),
      CreateInput(['type'=>'number','id'=>'cantidad','etiqueta'=>'Cantidad (Kg)','required' => '','class'=>'MUL-1 MUL-2']),
      CreateInput(['type'=>'number','id'=>'costo_unitario_total','etiqueta'=>'Precio Por Kg (MXN)','required' => '','class'=>'MUL-1']),
      CreateInput(['type'=>'number','id'=>'monto_total','etiqueta'=>'Subtotal','required' => '','readonly' => '','class'=>'RESULT-1']),
      CreateInput(['type'=>'number','id'=>'costo_unitario_neto','etiqueta'=>'Precio Por Kg (IVA)','required' => '','div_style'=>'display:none;','class'=>'MUL-2']),
      CreateInput(['type'=>'number','id'=>'monto_neto','etiqueta'=>'Monto IVA','required' => '','readonly' => '','class'=>'RESULT-2']),
      CreateInput(['type'=>'number','value'=>'0','id'=>'porcentaje_descuento','etiqueta'=>'RETENCIÓN DE IVA','required' => '','class'=>'DESC']),
      CreateInput(['type'=>'number','id'=>'total','etiqueta'=>'Total','required' => '','readonly' => '','class'=>'RESULT-TOTAL'])
    ]);

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
