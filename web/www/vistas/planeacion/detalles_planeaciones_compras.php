<?php
    ob_start(); // Inicia la captura del buffer de salida


    

    $PageSection = "Contenido de Planeación de Compras";
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
  $id = 'detalles_planeaciones_compras';
  $ButtonAddLabel = "Nuevo Contenido de Planeación de Compras";
  $titulos = ['ID', 'Planeación','Articulo','Cantidad Solicitada','Cantidad en Almacén', 'Cantidad a Comprar','Fecha de creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data,$AllowADDButton,[]);
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Contenido de Planeación de Compras',
      'Title3'=>'Ver Contenido de Planeación',
      'ModalType'=>'modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'planeacion'
    ],
    [
      CreateInput(['id'=>'kid_planeacion_compras','etiqueta'=>'Planeación de Compra','required' => '','readonly' => '','value'=>isset($valor_id)?$valor_id:'']),
      CreateSelect(['id'=>'kid_articulo','etiqueta'=>'Articulo','required' => '','class'=>'DataGET Data-GETCantidadArticulos'],$articulos),
      CreateInput(['type'=>'number','id'=>'cantidad_solicitada','etiqueta'=>'Cantidad Solicitada','required' => '','class'=>'SUM-1']),
        CreateInput(['type'=>'number','id'=>'cantidad_en_almacen','etiqueta'=>'Cantidad en Almacén','required' => '','readonly' => '','class'=>'RES-1 MUL-2 kid_articulo']),
        CreateInput(['type'=>'number','id'=>'cantidad_a_comprar','etiqueta'=>'Cantidad a Comprar','required' => '','readonly' => '','class'=>'RESULT-1 MUL-3']),
        CreateInput(['type'=>'number','id'=>'costo_unitario_a_comprar','etiqueta'=>'Costo Unitario','required' => '','class'=>'MUL-2 MUL-3']),
        CreateInput(['type'=>'number','id'=>'costo_total_almacen','etiqueta'=>'Costo Total en Almacén','required' => '','readonly' => '','class'=>'RESULT-2 SUM-4']),
        CreateInput(['type'=>'number','id'=>'costo_total_a_comprar','etiqueta'=>'Costo Total a Comprar','required' => '','readonly' => '','class'=>'RESULT-3 SUM-4']),
        CreateInput(['type'=>'number','id'=>'monto_total','etiqueta'=>'Monto Total','required' => '','readonly' => '','class'=>'RESULT-4']),
        CreateTextArea(['maxlength'=>'300','id'=>'comentarios','etiqueta'=>'Comentario','required'=>''])
        
    ]);

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
