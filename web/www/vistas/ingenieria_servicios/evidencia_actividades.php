<?php
    ob_start(); // Inicia la captura del buffer de salida
    $PageSection = "Evidencia de Actividades";
?>

  <div class="pagetitle">
    <h1><?php echo $PageSection; ?></h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Home</a></li>
        <li class="breadcrumb-item"><?php echo $nombre_modulo; ?></li>
        <li class="breadcrumb-item active" ><?php echo $PageSection; ?></li>
      </ol>
    </nav>
  </div><!-- End Page Title -->
<?php 
  
  $id = 'evidencia_actividades';
  $ButtonAddLabel = "evidencia_actividades";
  $titulos = ['ID','Nombre Actividad','Comentario','Personal','Fecha de Creación'];


  CreateTable($id, $ButtonAddLabel, $titulos, $data,false, ['<button class="ModalDataView btn btn-primary primary" modalCRUD="evidencia_actividades"><i class="bi bi-eye"></i> Ver</button>']);

  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>"Ver Evidencias de  Actividad",
      'Title2'=>'Ver Evidencias de  Actividad',
      'Title3'=>'Ver Evidencias de actividad',
      'ModalType'=>'modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'ingenieria_servicios',
      'modalCRUD'=>'finalizar_detalles_actividades'
    ],
    [
      CreateCarousel(['title'=>'Fotos Subidas','id'=>'view_fotos', 'class'=>'ViewFotos'])
    ]
    );

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
