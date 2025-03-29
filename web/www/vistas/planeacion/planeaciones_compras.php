<?php
    ob_start(); // Inicia la captura del buffer de salida


    

    $PageSection = "Planeación de Compras";
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

  $id = 'planeaciones_compras';
  $ButtonAddLabel = "Nueva Planeación de Compras";
  $titulos = ['ID', 'Bolsa de Proyectos','Proyecto','Cliente','Estado','La Creo','Fecha de creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data, $AllowADDButton, 'ButtonsInRow');
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Planeación de Compras',
      'Title3'=>'Ver Planeación de Compras',
      'ModalType'=>'modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'planeacion'
    ],
    [
      CreateSelect(['id'=>'kid_bolsa_proyecto','etiqueta'=>'Bolsa de Proyectos','required' => '','class'=>'DataGET Data-GETProyectoByBolsa'],$bolsas_proyectos),
      CreateSelect(['id'=>'kid_proyecto','etiqueta'=>'Proyecto','required' => '','class'=>'kid_bolsa_proyecto'],[]),
      CreateInput(['type'=>'text','id'=>'kid_cliente','etiqueta'=>'Cliente','readonly' => '','value'=>isset($valor_id)?$valor_id:'']),
      CreateInput(['type'=>'number','id'=>'costo_total_almacen','etiqueta'=>'Costo Total en Almacén','required' => '']),
      CreateInput(['type'=>'number','id'=>'costo_total_a_comprar','etiqueta'=>'Costo a Comprar','required' => '']),
      CreateInput(['type'=>'number','id'=>'monto_total','etiqueta'=>'Monto Total','required' => '']),
      CreateInput(['type'=>'number','id'=>'registros_almacen','etiqueta'=>'Registros en Almacén','required' => '']),
      CreateInput(['type'=>'number','id'=>'registros_a_comprar','etiqueta'=>'Registros a Comprar','required' => '']),
      CreateInput(['type'=>'number','id'=>'registros_total','etiqueta'=>'Registros Totales','required' => '']),
    ]);



  $id = 'detalles_planeaciones_compras';
  $ButtonAddLabel = "Nuevo Contenido de Planeación";
  $titulos = ['ID', 'Planeación','Articulo','Cantidad Solicitada','Cantidad en Almacén', 'Cantidad a Comprar','Fecha de creación'];
  

    ob_start();
    CreateTable($id, $ButtonAddLabel, $titulos, [],true,[],'',$atributos = ['data-select-column'=>0]);
    $detailsTableOutput = ob_get_clean();

    CreateModal( [
      'id'=> $id.'-View', 
      'Title'=>'Detalle de Planeación de RRHH',
      'Title2'=>'Editar Planeación de RRHH',
      'Title3'=>'Ver Planeación de RRHH',
      'ModalType'=>'modal-fullscreen modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'planeacion'
    ],
    [
      $detailsTableOutput
    ],
    ['<button type="button" class="btn btn-secondary secondary" data-bs-dismiss="modal">Cancelar</button>']);


    CreateModalForm(
      [
        'id'=> $id, 
        'Title'=>$ButtonAddLabel,
        'Title2'=>'Editar Contenido de Planeación',
        'Title3'=>'Ver Contenido de Planeación',
        'ModalType'=>'modal-dialog-scrollable', 
        'method'=>'POST',
        'action'=>'bd/crudSummit.php',
        'bloque'=>'planeacion',
        'data-select-column'=>'[0]',
        'data-input-fill'=>'[kid_planeacion_compras]'
      ],
      [
        CreateInput(['id'=>'kid_planeacion_compras','etiqueta'=>'Planeación de Compra','readonly' => '']),
        CreateSelect(['id'=>'kid_articulo','etiqueta'=>'Articulo','required' => ''],$articulos),
        CreateInput(['type'=>'number','id'=>'cantidad_solicitada','etiqueta'=>'Cantidad Solicitada','required' => '','class'=>'SUM-1']),
        CreateInput(['type'=>'number','id'=>'cantidad_en_almacen','etiqueta'=>'Cantidad en Almacén','required' => '','class'=>'RES-1 MUL-2']),
        CreateInput(['type'=>'number','id'=>'cantidad_a_comprar','etiqueta'=>'Cantidad a Comprar','required' => '','readonly' => '','class'=>'RESULT-1 MUL-3']),
        CreateInput(['type'=>'number','id'=>'costo_unitario_a_comprar','etiqueta'=>'Costo Unitario','required' => '','class'=>'MUL-2 MUL-3']),
        CreateInput(['type'=>'number','id'=>'costo_total_almacen','etiqueta'=>'Costo Total en Almacén','required' => '','readonly' => '','class'=>'RESULT-2 SUM-4']),
        CreateInput(['type'=>'number','id'=>'costo_total_a_comprar','etiqueta'=>'Costo Total a Comprar','required' => '','readonly' => '','class'=>'RESULT-3 SUM-4']),
        CreateInput(['type'=>'number','id'=>'monto_total','etiqueta'=>'Monto Total','required' => '','readonly' => '','class'=>'RESULT-4']),
      ]);

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
