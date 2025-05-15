<?php
    ob_start(); // Inicia la captura del buffer de salida

    $PageSection = "Contenido de Ordenes de Compras";
    $AllowADDButton = true; // Explicitly set to true to ensure the button is displayed
?>

  <div class="pagetitle">
    <h1><?php echo $PageSection; ?></h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Home</a></li>
        <li class="breadcrumb-item"><?php echo $nombre_modulo; ?></li>
        <?php 
          if($breadcrumb){
            echo $breadcrumb;
          } else{
            echo '<li class="breadcrumb-item active">'.$PageSection.'</li>';
          }
        ?>
      </ol>
    </nav>
  </div><!-- End Page Title -->
<?php 
  $id = 'detalles_ordenes_compras';
  $ButtonAddLabel = "Nuevo Detalle de Orden";
  $titulos = ['ID', 'Orden de Compra', 'Materia Prima', 'Cantidad De Super Sacos', 'Costo Unitario Total', 'Costo Unitario Neto', 'Monto Total', 'Monto Neto', 'Fecha de creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data, $AllowADDButton);
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
      CreateSelect(['id'=>'kid_orden_compras','etiqueta'=>'Orden de Compras','required' => '','class'=>'OnEditReadOnly'],$ordenes),
      CreateSelect(['id'=>'kid_articulo','etiqueta'=>'Materia Prima','required' => ''],$articulos),
      CreateInput(['type'=>'number','id'=>'cantidad','etiqueta'=>'Cantidad De Super Sacos','required' => '','class'=>'MUL-1 MUL-2']),
      CreateInput(['type'=>'number','id'=>'costo_unitario_total','etiqueta'=>'Costo Unitario Total','required' => '','class'=>'MUL-1']),
      CreateInput(['type'=>'number','id'=>'costo_unitario_neto','etiqueta'=>'Costo Unitario Neto','required' => '','readonly' => '','class'=>'MUL-2']),
      CreateInput(['type'=>'number','id'=>'monto_total','etiqueta'=>'Monto Total','required' => '','readonly' => '']),
      CreateInput(['type'=>'number','id'=>'monto_neto','etiqueta'=>'Monto Neto','required' => '','readonly' => '']),
    ]);

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer

  include 'wrapper.php'; // Incluye el wrapper
?>
