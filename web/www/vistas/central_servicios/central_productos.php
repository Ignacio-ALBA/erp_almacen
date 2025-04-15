<?php
    ob_start(); // Inicia la captura del buffer de salida
    
    $consultaselect = "SELECT e.id_estados  , 
                          e.orden, 
                          e.estado, 
                          e.simbolo, 
                          CASE 
                              WHEN e.pordefecto = 1 THEN 'SÍ' 
                              ELSE 'NO' 
                          END AS pordefecto,
                          p.pais as kid_pais,  -- Ahora esta columna está después de pordefecto
                          e.fecha_creacion
                    FROM estados e
                    JOIN paises p ON e.kid_pais = p.id_pais 
                    WHERE e.kid_estatus = 1";

    $resultado = $conexion->prepare($consultaselect);
    $resultado->execute();
    $data=$resultado->fetchAll(PDO::FETCH_ASSOC);

    $consult = "SELECT pais,pordefecto FROM paises WHERE kid_estatus = 1 ORDER BY pordefecto DESC, orden ASC";
    $resultado = $conexion->prepare($consult);
    $resultado->execute();
    $paises = $resultado->fetchAll(PDO::FETCH_ASSOC);
    // Transformar el array a un formato donde las claves son 'id_categoria' y los valores son 'categoria'
    $paises = array_map(fn($item) => [
      'valor' => $item['pais'],
      'pordefecto' => $item['pordefecto']
    ], $paises);
    


    /*$consulta = "SELECT id_estatus,estatus FROM estatus";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $estatuss=$resultado->fetchAll(PDO::FETCH_ASSOC);*/
    $PageSection = "Central de productos";
?>


  <div class="pagetitle">
    <h1><?php echo $PageSection; ?></h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Home</a></li>
        <li class="breadcrumb-item">Central de productos</li>
        <li class="breadcrumb-item active" ><?php echo $PageSection; ?></li>
      </ol>
    </nav>
  </div><!-- End Page Title -->
<?php 

  $id = 'central_productos';
  $ButtonAddLabel = "Nuevo producto";
  $titulos = ['ID', 'Orden','Estado', 'Símbolo','Por Defecto','País','Fecha de creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data,true, []);
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Producto',
      'Title3'=>'Ver Producto',
      'ModalType'=>'modal-dialog-centered', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'central_servicios'
    ],
    [
      CreateInput(['type'=>'text','id'=>'estado','etiqueta'=>'Estado','required' => '']),
      CreateInput(['type'=>'text','id'=>'simbolo','etiqueta'=>'Símbolo','required' => '']),
      CreateInput(['type'=>'number','id'=>'orden','etiqueta'=>'Orden','required' => '']),
      CreateSelect(['id'=>'kid_pais','etiqueta'=>'País','required' => ''],$paises),
      CreatSwitchCheck(['id'=>'pordefecto','etiqueta'=>'Por defecto'])
      
    ]);

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
