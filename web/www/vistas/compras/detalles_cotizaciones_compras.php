<?php
    ob_start(); // Inicia la captura del buffer de salida

    $PageSection = "Detalles de Cotizaciones de Compras";
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
  $id = 'detalles_cotizaciones_compras';
  $ButtonAddLabel = "Nuevo Detalle de Cotización";
  $titulos = ['ID', 'Cotización','Insumos','Cantidad','Costo Unitario Total','Costo Unitario Neto','Monto Total','Monto Neto','Fecha de creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data, $AllowADDButton); 
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Detalle de Cotización',
      'Title3'=>'Ver Detalle de Cotización',
      'ModalType'=>'modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'compras',
      'data-select-column'=>'[1,2]',
      'data-input-fill'=>'[kid_cotizacion_compra,kid_articulo]',
      'onSubmit'=>'return validateDetallesCotizacion(this);'
    ],
    [
      CreateSelect(['id'=>'kid_cotizacion_compra','etiqueta'=>'Cotización','required' => '','class'=>'OnEditReadOnly'],$cotizaciones),
      CreateSelect(['id'=>'kid_articulo','etiqueta'=>'Insumo','required' => '','class'=>'OnEditReadOnly'],$articulos),
      CreateInput(['type'=>'number','id'=>'cantidad','etiqueta'=>'Cantidad De Super Sacos','required' => '','class'=>'MUL-1 MUL-2']),
      CreateInput(['type'=>'number','id'=>'costo_unitario_total','etiqueta'=>'Costo Unitario Total','required' => '','class'=>'MUL-1']),
      CreateInput(['type'=>'number','id'=>'costo_unitario_neto','etiqueta'=>'Costo Unitario Neto','required' => '','readonly'=>'readonly','class'=>'MUL-2']),
      CreateInput(['type'=>'number','id'=>'monto_total','etiqueta'=>'Monto Total','required' => '','readonly'=>'readonly']),
      CreateInput(['type'=>'number','id'=>'monto_neto','etiqueta'=>'Monto Neto','required' => '','readonly'=>'readonly']),
      CreateInput(['type'=>'number','value'=>'0','id'=>'porcentaje_descuento','etiqueta'=>'Porcentaje de Descuento','required' => '','class'=>'DESC-3 DESC-4'])
    ]);

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
