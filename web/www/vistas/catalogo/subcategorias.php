<?php
    ob_start(); // Inicia la captura del buffer de salida
    

    $consultaselect = "SELECT s.id_subcategoria, 
                          s.orden, 
                          s.subcategoria, 
                          CASE 
                              WHEN s.pordefecto = 1 THEN 'Activado' 
                              ELSE 'Desactivado' 
                          END AS pordefecto,
                          c.categoria as kid_dimension,  -- Ahora esta columna está después de pordefecto
                          s.fecha_creacion
                    FROM subcategorias s
                    JOIN categorias c ON s.kid_categoria = c.id_categoria
                    WHERE s.kid_estatus = 1";

    $resultado = $conexion->prepare($consultaselect);
    $resultado->execute();
    $data=$resultado->fetchAll(PDO::FETCH_ASSOC);

    $consult = "SELECT categoria,pordefecto FROM categorias WHERE kid_estatus = 1 ORDER BY pordefecto DESC, orden ASC";
    $resultado = $conexion->prepare($consult);
    $resultado->execute();
    $categorias = $resultado->fetchAll(PDO::FETCH_ASSOC);
    // Transformar el array a un formato donde las claves son 'id_categoria' y los valores son 'categoria'
    $categorias = array_map(fn($item) => [
      'valor' => $item['categoria'],
      'pordefecto' => $item['pordefecto']
    ], $categorias);
    


    /*$consulta = "SELECT id_estatus,estatus FROM estatus";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $estatuss=$resultado->fetchAll(PDO::FETCH_ASSOC);*/
    $PageSection = "Subcategorías";
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

  $id = 'subcategorias';
  $ButtonAddLabel = "Nueva Subcategoría";
  $titulos = ['ID', 'Orden','Subcategoría', 'Por Defecto','Categoría','Fecha de creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data,true, []);
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Subcategoría',
      'Title3'=>'Ver Subcategoría',
      'ModalType'=>'modal-dialog-centered', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'catalogo'
    ],
    [
      CreateInput(['type'=>'text','id'=>'subcategoria','etiqueta'=>'Subcategoría','required' => '']),
      CreateSelect(['id'=>'kid_categoria','etiqueta'=>'Categoría','required' => ''],$categorias),
      CreateInput(['type'=>'number','id'=>'orden','etiqueta'=>'Orden','required' => '']),
      CreatSwitchCheck(['id'=>'pordefecto','etiqueta'=>'Por defecto'])
      
    ]);

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
