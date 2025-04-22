<?php
    ob_start(); 
    $lo_lo = CreateBadgeIcon('danger',['etiqueta'=>'Muy Baja', 'class'=>'danger']);
    $hi_hi = CreateBadgeIcon('danger',['etiqueta'=>'Muy Alta', 'class'=>'danger']);
    $lo = CreateBadgeIcon('warning',['etiqueta'=>'Baja', 'class'=>'warning']);
    $hi = CreateBadgeIcon('warning',['etiqueta'=>'Alta', 'class'=>'warning']);
    $alarmasok = CreateBadgeIcon('success',['etiqueta'=>'Normal', 'class'=>'success']);

    $consultaselect = "SELECT ad.id_detalle_almacen, 
                          a.almacen AS kid_almacen, 
                          ar.articulo AS kid_articulo,
                          c.categoria AS kid_categoria,
                          ad.cantidad_megabultos,  
                          ad.peso, 
                          d.dimension AS kid_dimension, -- Trae el nombre de la dimensión
                          CASE 
                              WHEN ad.cantidad_megabultos < ad.lo_lo THEN '$lo_lo' 
                              WHEN ad.cantidad_megabultos < ad.lo AND ad.cantidad_megabultos > ad.lo_lo THEN '$lo' 
                              WHEN ad.cantidad_megabultos > ad.high AND  ad.cantidad_megabultos < ad.high_high THEN '$hi' 
                              WHEN ad.cantidad_megabultos > ad.high_high THEN '$hi_hi' 
                              ELSE '$alarmasok' 
                          END AS alarma,
                          ad.fecha_creacion
                      FROM 
                          detalles_almacenes ad
                      LEFT JOIN 
                          articulos ar ON ad.kid_articulo = ar.id_articulo
                      LEFT JOIN 
                          almacenes a ON ad.kid_almacen = a.id_almacen 
                          LEFT JOIN
                          dimensiones d ON ad.kid_dimension = d.id_dimension
                          LEFT JOIN
                          categorias c ON ad.kid_categoria = c.id_categoria
                      WHERE 
                          ad.kid_estatus = 1 AND ad.kid_categoria = 21";

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

    //var_dump($almacenes);
    //var_dump($articulos);

    $tipo_comentario = GetTiposComentariosListForSelect();

    $PageSection = "Central de materia prima";
?>


  <div class="pagetitle">
    <h1><?php echo $PageSection; ?></h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Home</a></li>
        <li class="breadcrumb-item">Central de materia prima</li>
        <li class="breadcrumb-item active" ><?php echo $PageSection; ?></li>
      </ol>
    </nav>
  </div><!-- End Page Title -->
<?php 

  $id = 'central_mp';
  $ButtonAddLabel = "";
  $titulos = ['ID','Almacén','Artículo','Categoria','Cantidad','Peso','Medida de peso','Existencia','Fecha de creación'];

  CreateTable($id, $ButtonAddLabel, $titulos, $data,false, []);

   CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Materia Prima',
      'Title3'=>'Ver Materia Prima',
      'ModalType'=>'modal-dialog-centered', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'central_servicios'
    ],
    [
      CreateSelect(['id'=>'kid_almacen','etiqueta'=>'Almacén','required' => ''], $almacenes),
      CreateSelect(['id'=>'kid_articulo','etiqueta'=>'Artículo','required' => ''], $articulos),
      CreateInput(['type'=>'number','id'=>'cantidad','etiqueta'=>'Cantidad','required' => '']),
      CreateInput(['type'=>'number','id'=>'peso','etiqueta'=>'Peso en KG','required' => '']),
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
        'bloque'=>'central_servicios'
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
