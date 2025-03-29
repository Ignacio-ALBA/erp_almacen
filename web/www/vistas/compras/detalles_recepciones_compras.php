<?php
    ob_start(); // Inicia la captura del buffer de salida


    

    $PageSection = "Detalles de Recepciones";
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
  $id = 'detalles_recepciones_compras';
  $ButtonAddLabel = "Nuevo Detalle";
  $titulos = ['ID','Artículos', 'Recepción','Cantidad','Costo Unitario Total','Costo Unitario Neto','Monto Total','Monto Neto','Fecha de creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data,false,$botones_acciones);
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Detalle',
      'Title3'=>'Ver Detalle',
      'ModalType'=>'modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'compras'
    ],
    [
      CreateInput(['id'=>'kid_recepcion_compras','etiqueta'=>'Recepción','required' => '','class'=>'OnEditReadOnly']),
      CreateInput(['id'=>'kid_articulo','etiqueta'=>'Articulo','required' => '','class'=>'OnEditReadOnly']),
      CreateInput(['type'=>'number','id'=>'cantidad','etiqueta'=>'Cantidad','required' => '','class'=>'MUL-1 MUL-2']),
      CreateInput(['type'=>'number','id'=>'costo_unitario_total','etiqueta'=>'Costo Unitario Total','required' => '','class'=>'MUL-1']),
      CreateInput(['type'=>'number','id'=>'costo_unitario_neto','etiqueta'=>'Costo Unitario Neto','required' => '','class'=>'MUL-2']),
      CreateInput(['type'=>'number','id'=>'monto_total','etiqueta'=>'Monto Total','required' => '','readonly' => '', 'class'=>'RESULT-1 RESULT-3']),
      CreateInput(['type'=>'number','id'=>'monto_neto','etiqueta'=>'Monto Neto','required' => '','readonly' => '', 'class'=>'RESULT-2 RESULT-4']),
      CreateInput(['type'=>'number','value'=>'0','id'=>'porcentaje_descuento','etiqueta'=>'Porcentaje de Descuento','required' => '','class'=>'DESC-3 DESC-4']),
    ]);


    $id = 'comentarios_recepciones';
    $ButtonAddLabel = "Nuevo Comentario";
    CreateModalForm(
      [
        'id'=> $id, 
        'Title'=>$ButtonAddLabel,
        'Title2'=>'Editar Comentario',
        'Title3'=>'Ver Comentario',
        'ModalType'=>'modal-dialog-scrollable', 
        'method'=>'POST',
        'action'=>'bd/crudSummit.php',
        'bloque'=>'compras'
      ],
      [
        CreateInput(['id'=>'kid_recepcion_compras-NewAdd1','etiqueta'=>'Recepción','readonly' => '']),
        CreateInput(['id'=>'kid_detalle_recepcion_compras','etiqueta'=>'Detalle Recepción','readonly' => '']),
        CreateInput(['type'=>'text','id'=>'comentario_recepcion_compras','etiqueta'=>'Comentario','required' => '']),
        CreateSelect(['id'=>'kid_tipo_comentario','etiqueta'=>'Detalle Almacén','disabled' => ''],$tipo_comentario)
  
        //CreatSwitchCheck(['id'=>'pordefecto','etiqueta'=>'Por defecto'])
        
      ]);

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
