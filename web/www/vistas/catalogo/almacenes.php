<?php
    ob_start(); // Inicia la captura del buffer de salida

    $consultaselect = "SELECT a.id_almacen, 
                          a.orden, 
                          a.almacen, 
                          a.ubicacion, 
                          s.sucursal AS kid_sucursal,
                          CASE 
                              WHEN a.pordefecto = 1 THEN 'SÍ' 
                              ELSE 'NO' 
                          END AS pordefecto,
                          s.fecha_creacion
                      FROM 
                          almacenes a
                      LEFT JOIN 
                          sucursales s ON a.kid_sucursal = s.id_sucursal  
                      WHERE 
                          a.kid_estatus != 3";

    $resultado = $conexion->prepare($consultaselect);
    $resultado->execute();
    $data=$resultado->fetchAll(PDO::FETCH_ASSOC);

    $consult = "SELECT email, nombre,apellido_paterno, apellido_materno  FROM colaboradores WHERE kid_estatus = 1";

    $resultado = $conexion->prepare($consult);
    $resultado->execute();
    $colaboradores = $resultado->fetchAll(PDO::FETCH_ASSOC);

    $consult = "SELECT sucursal FROM sucursales WHERE kid_estatus = 1";
    $resultado = $conexion->prepare($consult);
    $resultado->execute();
    $sucursales = $resultado->fetchAll(PDO::FETCH_ASSOC);

    // Transformar el array a un formato donde las claves son 'id_categoria' y los valores son 'categoria'

    $colaboradores = array_map(fn($item) => [
      'valor'=> $item['email'],
      'text' => trim(implode(' ', array_filter([$item['nombre'], $item['apellido_paterno'], $item['apellido_materno']]))),
      'pordefecto' => 0
    ], $colaboradores);

    $sucursales = array_map(fn($item) => [
      'valor'=> $item['sucursal'],
      'pordefecto' => 0
    ], $sucursales);


    


    /*$consulta = "SELECT id_estatus,estatus FROM estatus";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $estatuss=$resultado->fetchAll(PDO::FETCH_ASSOC);*/
    $PageSection = "Almacenes";
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

  $id = 'almacenes';
  $ButtonAddLabel = "Nueva Almacén";
  $titulos = ['ID', 'Orden','Almacén','Ubicación','Sucursal','Por Defecto','Fecha de creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data,true, []);
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Almacén',
      'Title3'=>'Ver Almacén',
      'ModalType'=>'modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'catalogo'
    ],
    [
      CreateInput(['type'=>'text','id'=>'almacen','etiqueta'=>'Almacén','required' => '']),
      CreateInput(['type'=>'text','id'=>'ubicacion','etiqueta'=>'Ubicación']),
      CreateSelect(['id'=>'kid_sucursal','etiqueta'=>'Sucursal'],$sucursales),
      CreateSelect(['id'=>'kid_encargado','etiqueta'=>'Encargado'],$colaboradores),
      CreateInput(['type'=>'number','id'=>'telefono_encargado','etiqueta'=>'Teléfono Encargado','class'=>'ValidateTelefono']),
      CreateInput(['type'=>'number','id'=>'celular_encargado','etiqueta'=>'Celular Encargado','class'=>'ValidateTelefono']),
      CreateInput(['type'=>'text','id'=>'latitud','etiqueta'=>'Latitud']),
      CreateInput(['type'=>'text','id'=>'longitud','etiqueta'=>'Longitud']),
      CreateInput(['type'=>'text','id'=>'orden','etiqueta'=>'Orden','required' => '']),
      
      CreatSwitchCheck(['id'=>'pordefecto','etiqueta'=>'Por defecto'])
      
    ]);

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
