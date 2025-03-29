<?php
    ob_start(); // Inicia la captura del buffer de salida
    

    /*$consultaselect = "SELECT f.id_formato , 
                          f.orden, 
                          f.formato, 
                          CASE 
                              WHEN f.pordefecto = 1 THEN 'Activado' 
                              ELSE 'Desactivado' 
                          END AS formatos,
                          p.presentacion as kid_presentacion,  -- Ahora esta columna está después de pordefecto
                          u.unidad as kid_unidad,
                          d.dimension as kid_dimension,
                          f.fecha_creacion
                    FROM formatos f
                    JOIN presentaciones p ON f.kid_presentacion = p.id_presentacion 
                    JOIN unidades u ON f.kid_unidad = u.id_unidad  -- Suponiendo que tienes una relación con la tabla unidades
                    JOIN dimensiones d ON f.kid_dimension = d.id_dimension  -- Suponiendo que tienes una relación con la tabla dimensiones
                    WHERE f.kid_estatus = 1";*/
    $consultaselect = "SELECT f.id_formato, 
                    f.orden, 
                    f.formato, 
                    CASE 
                        WHEN f.pordefecto = 1 THEN 'Activado' 
                        ELSE 'Desactivado' 
                    END AS formatos,
                    CASE 
                        WHEN f.kid_presentacion = -1 THEN 'Sin asignar' 
                        ELSE COALESCE(p.presentacion, 'Sin asignar') 
                    END AS kid_presentacion,
                    CASE 
                        WHEN f.kid_unidad = -1 THEN 'Sin asignar' 
                        ELSE COALESCE(u.unidad, 'Sin asignar') 
                    END AS kid_unidad,
                    CASE 
                        WHEN f.kid_dimension = -1 THEN 'Sin asignar' 
                        ELSE COALESCE(d.dimension, 'Sin asignar') 
                    END AS kid_dimension,
                    f.fecha_creacion
              FROM formatos f
              LEFT JOIN presentaciones p ON f.kid_presentacion = p.id_presentacion 
              LEFT JOIN unidades u ON f.kid_unidad = u.id_unidad  
              LEFT JOIN dimensiones d ON f.kid_dimension = d.id_dimension  
              WHERE f.kid_estatus = 1";

    $resultado = $conexion->prepare($consultaselect);
    $resultado->execute();
    $data=$resultado->fetchAll(PDO::FETCH_ASSOC);

    /*-------------------- Obtener Tablas Foráneas --------------------*/
    $consult = "SELECT presentacion,pordefecto FROM presentaciones WHERE kid_estatus = 1 ORDER BY pordefecto DESC, orden ASC";
    $resultado = $conexion->prepare($consult);
    $resultado->execute();
    $presentaciones = $resultado->fetchAll(PDO::FETCH_ASSOC);

    $consult = "SELECT unidad,pordefecto FROM unidades WHERE kid_estatus = 1 ORDER BY pordefecto DESC, orden ASC";
    $resultado = $conexion->prepare($consult);
    $resultado->execute();
    $unidad = $resultado->fetchAll(PDO::FETCH_ASSOC);

    $consult = "SELECT dimension,pordefecto FROM dimensiones WHERE kid_estatus = 1 ORDER BY pordefecto DESC, orden ASC";
    $resultado = $conexion->prepare($consult);
    $resultado->execute();
    $dimensiones = $resultado->fetchAll(PDO::FETCH_ASSOC);
    
    /*------------------- Fin Obtener Tablas Foráneas ------------------*/
    $presentaciones = array_map(fn($item) => [
      'valor' => $item['presentacion'],
      'pordefecto' => $item['pordefecto']
    ], $presentaciones);

    $unidad = array_map(fn($item) => [
      'valor' => $item['unidad'],
      'pordefecto' => $item['pordefecto']
    ], $unidad);

    $dimensiones = array_map(fn($item) => [
      'valor' => $item['dimension'],
      'pordefecto' => $item['pordefecto']
    ], $dimensiones);
    


    /*$consulta = "SELECT id_estatus,estatus FROM estatus";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $estatuss=$resultado->fetchAll(PDO::FETCH_ASSOC);*/
    $PageSection = "Formatos";
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

  $id = 'formatos';
  $ButtonAddLabel = "Nuevo Formato";
  $titulos = ['ID', 'Orden','Formato', 'Por Defecto','Presentación','Unidad','Dimensión','Fecha de creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data,true, []);
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Formato',
      'Title3'=>'Ver Formato',
      'ModalType'=>'modal-dialog-centered', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'catalogo'
    ],
    [
      CreateInput(['type'=>'text','id'=>'formato','etiqueta'=>'Formato','required' => '']),
      CreateInput(['type'=>'number','id'=>'orden','etiqueta'=>'Orden','required' => '']),
      CreateSelect(['id'=>'kid_presentacion','etiqueta'=>'Presentación','required' => ''],$presentaciones),
      CreateSelect(['id'=>'kid_unidad','etiqueta'=>'Unidad','required' => ''],$unidad),
      CreateSelect(['id'=>'kid_dimension','etiqueta'=>'Dimensión','required' => ''],$dimensiones),
      CreatSwitchCheck(['id'=>'pordefecto','etiqueta'=>'Por defecto'])
      
    ]);

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
