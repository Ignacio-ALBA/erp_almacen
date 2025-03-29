<?php
    ob_start(); // Inicia la captura del buffer de salida
    

    $consultaselect = "SELECT s.id_presentacion , 
                          s.orden, 
                          s.presentacion, 
                          CASE 
                              WHEN s.pordefecto = 1 THEN 'Activado' 
                              ELSE 'Desactivado' 
                          END AS pordefecto,
                          c.dimension as kid_dimension,  -- Ahora esta columna está después de pordefecto
                          s.fecha_creacion
                    FROM presentaciones s
                    JOIN dimensiones c ON s.kid_dimension = c.id_dimension 
                    WHERE s.kid_estatus = 1";

    $resultado = $conexion->prepare($consultaselect);
    $resultado->execute();
    $data=$resultado->fetchAll(PDO::FETCH_ASSOC);

    $consult = "SELECT dimension,pordefecto FROM dimensiones WHERE kid_estatus = 1 ORDER BY pordefecto DESC, orden ASC";
    $resultado = $conexion->prepare($consult);
    $resultado->execute();
    $dimensiones = $resultado->fetchAll(PDO::FETCH_ASSOC);
    // Transformar el array a un formato donde las claves son 'id_categoria' y los valores son 'categoria'
    $dimensiones = array_map(fn($item) => [
      'valor' => $item['dimension'],
      'pordefecto' => $item['pordefecto']
    ], $dimensiones);
    


    /*$consulta = "SELECT id_estatus,estatus FROM estatus";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $estatuss=$resultado->fetchAll(PDO::FETCH_ASSOC);*/
    $PageSection = "Presentaciones";
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

  $id = 'presentaciones';
  $ButtonAddLabel = "Nueva Presentación";
  $titulos = ['ID', 'Orden','Presentación', 'Por Defecto','Dimensión','Fecha de creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data,true, []);
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Presentación',
      'Title3'=>'Ver Presentación',
      'ModalType'=>'modal-dialog-centered', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'catalogo'
    ],
    [
      CreateInput(['type'=>'text','id'=>'presentacion','etiqueta'=>'Presentación','required' => '']),
      CreateSelect(['id'=>'kid_dimension','etiqueta'=>'Dimensión','required' => ''],$dimensiones),
      CreateInput(['type'=>'number','id'=>'orden','etiqueta'=>'Orden','required' => '']),
      CreatSwitchCheck(['id'=>'pordefecto','etiqueta'=>'Por defecto'])
      
    ]);

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
