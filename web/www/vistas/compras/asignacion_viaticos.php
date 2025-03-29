<?php
    ob_start(); // Inicia la captura del buffer de salida


    

    $PageSection = "Asignaciones de Viáticos";
?>

  <div class="pagetitle">
    <h1><?php echo $PageSection; ?></h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Home</a></li>
        <li class="breadcrumb-item">Compras</li>
        <li class="breadcrumb-item active" ><?php echo $PageSection; ?></li>
      </ol>
    </nav>
  </div><!-- End Page Title -->
<?php 

  $id = 'asignacion_viaticos';
  $ButtonAddLabel = "Nueva Asignación";
  $titulos = ['ID', 'Tipo de Viático','Justificación','Monto','Monto Real','Responsable','Proyecto','Actividad','Fecha de Creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data);
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Asignación',
      'Title3'=>'Ver Asignación',
      'ModalType'=>'modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'compras'
    ],
    [
      CreateSelect(['id'=>'kid_tipo_viatico','etiqueta'=>'Tipo de Viatico'],$tipos_viaticos),
      CreateInput(['type'=>'text','maxlength'=>'100','id'=>'justificacion','etiqueta'=>'Justificación']),
      CreateInput(['type'=>'number','id'=>'monto_asignado','etiqueta'=>'Monto Asignado']),
      CreateInput(['type'=>'number','id'=>'monto_real','etiqueta'=>'Monto Real']),
      CreateSelect(['id'=>'kid_responsable','etiqueta'=>'Responsable'],$colaboradores),
      CreateSelect(['id'=>'kid_proyecto','etiqueta'=>'Proyecto'],[]),
      CreateInput(['type'=>'number','id'=>'grupo','etiqueta'=>'Grupo']),
      CreateSelect(['id'=>'kid_detalle_actividad','etiqueta'=>'Actividad'],[]),
      CreateSelect(['id'=>'kid_actividad','etiqueta'=>'No. Actividad'],[]),
      
    ]);

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
