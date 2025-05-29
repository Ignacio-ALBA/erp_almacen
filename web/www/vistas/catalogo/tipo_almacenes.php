<?php
    ob_start(); // Inicia la captura del buffer de salida

    // Consulta principal
    $consultaselect = "SELECT 
                          id_tipo_almacen, 
                          tipo_almacen, 
                          apodo, 
                          fecha_creacion 
                       FROM 
                          tipo_almacenes 
                       WHERE 
                          kid_estatus != 3";

    $resultado = $conexion->prepare($consultaselect);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);

    $PageSection = "Tipo de Almacenes";
?>

<div class="pagetitle">
  <h1><?php echo $PageSection; ?></h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="/">Home</a></li>
      <li class="breadcrumb-item">Catálogo</li>
      <li class="breadcrumb-item active"><?php echo $PageSection; ?></li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<?php 
  $id = 'tipo_almacenes';
  $ButtonAddLabel = "Nuevo Tipo de Almacén";
  $titulos = ['ID', 'Tipo de Almacén', 'Apodo', 'Fecha de creación'];

  CreateTable($id, $ButtonAddLabel, $titulos, $data, true, []);

  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Tipo de Almacén',
      'Title3'=>'Ver Tipo de Almacén',
      'ModalType'=>'modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'catalogo' 
    ],
    [
      CreateInput(['type'=>'text','id'=>'tipo_almacen','etiqueta'=>'Tipo de Almacén','required' => '']),
      CreateInput(['type'=>'text','id'=>'apodo','etiqueta'=>'Apodo']),
    ]
  );

  $wrapper_dashboard = ob_get_clean(); // Captura final del contenido

  include 'wrapper.php'; // Renderiza la vista
?>
