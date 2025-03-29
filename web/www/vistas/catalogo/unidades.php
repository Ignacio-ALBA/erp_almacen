<?php
    ob_start(); // Inicia la captura del buffer de salida
    

    $consultaselect = "SELECT id_unidad, orden, unidad, simbolo, 
             CASE 
                 WHEN pordefecto = 1 THEN 'Activado' 
                 ELSE 'Desactivado' 
             END AS pordefecto, 
             fecha_creacion
             FROM unidades 
             WHERE kid_estatus = 1";

    $resultado = $conexion->prepare($consultaselect);
    $resultado->execute();
    $data=$resultado->fetchAll(PDO::FETCH_ASSOC);

    $PageSection = "Unidades";
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

  $id = 'unidades';
  $ButtonAddLabel = "Nueva Unidad";
  $titulos = ['ID', 'Orden','Unidad', 'Símbolo','Por Defecto','Fecha de creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data,true, []);
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Unidad',
      'Title3'=>'Ver Unidad',
      'ModalType'=>'modal-dialog-centered', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'catalogo'
    ],
    [
      CreateInput(['type'=>'text','id'=>'unidad','etiqueta'=>'Unidad','required' => '']),
      CreateInput(['type'=>'text','id'=>'simbolo','etiqueta'=>'Símbolo','required' => '']),
      CreateInput(['type'=>'number','id'=>'orden','etiqueta'=>'Orden','required' => '']),
      CreatSwitchCheck(['id'=>'pordefecto','etiqueta'=>'Por defecto'])
      
    ]);

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
