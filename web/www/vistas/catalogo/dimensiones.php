<?php
    ob_start(); // Inicia la captura del buffer de salida
    
    $consultaselect = "SELECT id_dimension , orden, dimension, simbolo, 
             CASE 
                 WHEN pordefecto = 1 THEN 'Activado' 
                 ELSE 'Desactivado' 
             END AS pordefecto, 
             fecha_creacion
             FROM dimensiones 
             WHERE kid_estatus = 1";

    $resultado = $conexion->prepare($consultaselect);
    $resultado->execute();
    $data=$resultado->fetchAll(PDO::FETCH_ASSOC);

    $PageSection = "Dimensiones";
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

  $id = 'dimensiones';
  $ButtonAddLabel = "Nueva Dimensión";
  $titulos = ['ID', 'Orden','Dimensión', 'Símbolo','Por Defecto','Fecha de creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data,true, []);
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Dimensión',
      'Title3'=>'Ver Dimensión',
      'ModalType'=>'modal-dialog-centered', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'catalogo'
    ],
    [
      CreateInput(['type'=>'text','id'=>'dimension','etiqueta'=>'Dimensión','required' => '']),
      CreateInput(['type'=>'text','id'=>'simbolo','etiqueta'=>'Símbolo','required' => '']),
      CreateInput(['type'=>'number','id'=>'orden','etiqueta'=>'Orden','required' => '']),
      CreatSwitchCheck(['id'=>'pordefecto','etiqueta'=>'Por defecto'])
      
    ]);

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
