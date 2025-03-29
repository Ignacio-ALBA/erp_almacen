<?php
    ob_start(); // Inicia la captura del buffer de salida
    
    $lo_lo = CreateBadgeIcon('danger',['etiqueta'=>'Muy Baja', 'class'=>'danger']);
    $hi_hi = CreateBadgeIcon('danger',['etiqueta'=>'Muy Alta', 'class'=>'danger']);
    $lo = CreateBadgeIcon('warning',['etiqueta'=>'Baja', 'class'=>'warning']);
    $hi = CreateBadgeIcon('warning',['etiqueta'=>'Alta', 'class'=>'warning']);
    $alarmasok = CreateBadgeIcon('success',['etiqueta'=>'Normal', 'class'=>'success']);

    $consultaselect = "SELECT ad.id_detalle_almacen, 
                          a.almacen AS kid_almacen, 
                          ar.articulo AS kid_articulo,
                          ad.cantidad,  
                          CASE 
                              WHEN ad.cantidad < ad.lo_lo THEN '$lo_lo' 
                              WHEN ad.cantidad < ad.lo AND ad.cantidad > ad.lo_lo THEN '$lo' 
                              WHEN ad.cantidad > ad.high AND  ad.cantidad < ad.high_high THEN '$hi' 
                              WHEN ad.cantidad > ad.high_high THEN '$hi_hi' 
                              ELSE '$alarmasok' 
                          END AS alarma,
                          ad.fecha_creacion
                      FROM 
                          detalles_almacenes ad
                      LEFT JOIN 
                          articulos ar ON ad.kid_articulo = ar.id_articulo
                      LEFT JOIN 
                          almacenes a ON ad.kid_almacen = a.id_almacen 
                      WHERE 
                          ad.kid_estatus = 1";

    $resultado = $conexion->prepare($consultaselect);
    $resultado->execute();
    $data=$resultado->fetchAll(PDO::FETCH_ASSOC);

    //var_dump($data);

    $consult = "SELECT almacen FROM almacenes WHERE kid_estatus = 1";
    $resultado = $conexion->prepare($consult);
    $resultado->execute();
    $almacenes = $resultado->fetchAll(PDO::FETCH_ASSOC);

    $almacenes = array_map(fn($item) => [
      'valor'=> $item['almacen'],
      'pordefecto' => 0
    ], $almacenes);


    $consult = "SELECT articulo FROM articulos WHERE kid_estatus = 1";
    $resultado = $conexion->prepare($consult);
    $resultado->execute();
    $articulos = $resultado->fetchAll(PDO::FETCH_ASSOC);

    $articulos = array_map(fn($item) => [
      'valor'=> $item['articulo'],
      'pordefecto' => 0
    ], $articulos);

    $tipo_comentario = GetTiposComentariosListForSelect();


    


    /*$consulta = "SELECT id_estatus,estatus FROM estatus";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $estatuss=$resultado->fetchAll(PDO::FETCH_ASSOC);*/
    $PageSection = "Contenido de Almacén";
?>


  <div class="pagetitle">
    <h1><?php echo $PageSection; ?></h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Home</a></li>
        <li class="breadcrumb-item">Catálogo</li>
        <li class="breadcrumb-item active" ><?php echo $PageSection; ?></li>
      </ol>
    </nav>
  </div><!-- End Page Title -->
<?php 
  $id = 'detalles_almacenes';
  $ButtonAddLabel = "Nuevo Detalle";
  $titulos = ['ID','Almacén','Artículo','Cantidad','Existencia','Fecha de creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data,true,$botones_acciones);
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Detalle',
      'Title3'=>'Ver Detalle',
      'ModalType'=>'modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'catalogo'
    ],
    [
      CreateSelect(['id'=>'kid_almacen','etiqueta'=>'Almacén','required' => ''],$almacenes),
      CreateSelect(['id'=>'kid_articulo','etiqueta'=>'Artículo','required' => ''],$articulos),
      CreateInput(['type'=>'text','id'=>'cantidad','etiqueta'=>'Cantidad','required' => '']),
      CreateInput(['type'=>'text','id'=>'lo_lo','etiqueta'=>'Existencia Muy Baja','required' => '','placeholder'=>'Número de existencia']),
      CreateInput(['type'=>'text','id'=>'lo','etiqueta'=>'Existencia Baja','required' => '','placeholder'=>'Número de existencia']),
      CreateInput(['type'=>'text','id'=>'high','etiqueta'=>'Existencia Alta','required' => '','placeholder'=>'Número de existencia']),
      CreateInput(['type'=>'text','id'=>'high_high','etiqueta'=>'Existencia Muy Alta','required' => '','placeholder'=>'Número de existencia']),

      //CreatSwitchCheck(['id'=>'pordefecto','etiqueta'=>'Por defecto'])
      
    ]);

    CreateModalForm(
      [
        'id'=> 'comentarios_almacenes', 
        'Title'=>'Agregar Comentario',
        'Title2'=>'Editar Comentario',
        'Title3'=>'Ver Comentario',
        'ModalType'=>'modal-dialog-scrollable', 
        'method'=>'POST',
        'action'=>'bd/crudSummit.php',
        'bloque'=>'catalogo'
      ],
      [
        CreateInput(['type'=>'text','id'=>'almacen','etiqueta'=>'Almacén','readonly' => '']),
        CreateInput(['type'=>'text','id'=>'kid_detalle_almacen','etiqueta'=>'Detalle Almacén','readonly' => '']),
        CreateInput(['type'=>'text','id'=>'comentario_almacen','etiqueta'=>'Comentario','required' => '']),
        CreateSelect(['id'=>'kid_tipo_comentario','etiqueta'=>'Tipo Comentario','required' => ''],$tipo_comentario)
  
        //CreatSwitchCheck(['id'=>'pordefecto','etiqueta'=>'Por defecto'])
        
      ]);

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
