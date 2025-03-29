<?php
    ob_start(); // Inicia la captura del buffer de salida
    $PageSection = "Justificación de Actividades";
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
  
  $id = 'justificaciones_actividades';
  $ButtonAddLabel = "Justificación de Actividades";
  $titulos = ['ID', 'No. Actividad','Nombre Actividad','Personal','Justificación','Coordenadas','Fecha de Creación'];


  CreateTable($id, $ButtonAddLabel, $titulos, $data,false, []);

  CreateModalForm(
    [
      'id'=> "justificaciones_actividades", 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Justificación de Actividades',
      'Title3'=>'Ver Justificación de Actividades',
      'ModalType'=>'modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'ingenieria_servicios'
    ],
    [
      CreateInput(['type'=>'number','id'=>'kid_actividad','etiqueta'=>'No. Actividad','readonly' => '']),
      CreateInput(['type'=>'text','id'=>'kid_detalle_actividad','etiqueta'=>'Actividad','readonly' => '']),
      CreateSelect(['type'=>'text','id'=>'kid_responsable','etiqueta'=>'Responsable'],$personal),
      CreateTextArea(['type'=>'text','maxlength'=>'300','id'=>'justificacion','etiqueta'=>'Justificación']),
      CreateInput(['type'=>'number','id'=>'latitud','etiqueta'=>'Latitud','readonly' => '']),
      CreateInput(['type'=>'number','id'=>'longitud','etiqueta'=>'Longitud','readonly' => '']),
    ]
  );

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
