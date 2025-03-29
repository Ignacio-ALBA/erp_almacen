<?php
    ob_start(); // Inicia la captura del buffer de salida
    

    $consultaselect = "SELECT m.id_municipio   , 
                          m.orden, 
                          m.municipio, 
                          CASE 
                              WHEN m.pordefecto = 1 THEN 'SÍ' 
                              ELSE 'NO' 
                          END AS pordefecto,
                          e.estado as kid_estado,
                          p.pais as pais,  -- Ahora esta columna está después de pordefecto
                          m.fecha_creacion
                    FROM municipios m
                    JOIN estados e ON m.kid_estado = e.id_estados
                    JOIN paises p ON e.kid_pais = p.id_pais
                    WHERE m.kid_estatus = 1";

    $resultado = $conexion->prepare($consultaselect);
    $resultado->execute();
    $data=$resultado->fetchAll(PDO::FETCH_ASSOC);

    $consult = "SELECT e.estado,p.simbolo ,e.pordefecto  FROM estados e JOIN paises p ON e.kid_pais = p.id_pais WHERE e.kid_estatus = 1 ORDER BY e.pordefecto DESC, e.orden ASC";

    $resultado = $conexion->prepare($consult);
    $resultado->execute();
    $estados = $resultado->fetchAll(PDO::FETCH_ASSOC);
    // Transformar el array a un formato donde las claves son 'id_categoria' y los valores son 'categoria'

    $estados = array_map(fn($item) => [
      'valor' => $item['estado'],
      'text' => $item['estado'].' - '.$item['simbolo'],
      'pordefecto' => $item['pordefecto']
    ], $estados);
    
    


    /*$consulta = "SELECT id_estatus,estatus FROM estatus";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $estatuss=$resultado->fetchAll(PDO::FETCH_ASSOC);*/
    $PageSection = "Municipios";
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

  $id = 'municipios';
  $ButtonAddLabel = "Nuevo Municipio";
  $titulos = ['ID', 'Orden','Municipio','Por Defecto','Estado','País','Fecha de creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data,true, []);
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Municipio',
      'Title3'=>'Ver Municipio',
      'ModalType'=>'modal-dialog-centered', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'catalogo'
    ],
    [
      CreateInput(['type'=>'text','id'=>'municipio','etiqueta'=>'Municipio','required' => '']),
      CreateInput(['type'=>'number','id'=>'orden','etiqueta'=>'Orden','required' => '']),
      CreateSelect(['id'=>'kid_estado','etiqueta'=>'Estado','cambios'=>'pais','required' => ''],$estados),
      //CreateSelect(['id'=>'pais','etiqueta'=>'País','readonly' => '','disabled' => ''],$paises),
      CreatSwitchCheck(['id'=>'pordefecto','etiqueta'=>'Por defecto'])
      
    ]);

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
