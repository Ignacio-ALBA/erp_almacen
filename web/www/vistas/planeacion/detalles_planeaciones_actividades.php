<?php
    ob_start(); // Inicia la captura del buffer de salida


    

    $PageSection = "Contenido de Planeación de Actividades";
?>


  <div class="pagetitle">
    <h1><?php echo $PageSection; ?></h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Home</a></li>
        <li class="breadcrumb-item"><?php echo $nombre_modulo; ?></li>
        <?php 
          if($breadcrumb){
            echo $breadcrumb;
          } else{
            echo '<li class="breadcrumb-item active">'.$PageSection.'</li>';
          }
        ?>
      </ol>
    </nav>
  </div><!-- End Page Title -->
<?php 
  $id = 'detalles_planeaciones_actividades';
  $ButtonAddLabel = "Nuevo Contenido Planeación de Actividades";
  $titulos = ['ID', 'Planeación','Tarea','Fecha de Inicio','Fecha de Fin','Días Totales','Fecha de creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data,$AllowADDButton);
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Contenido Planeación de Actividades',
      'Title3'=>'Ver Contenido Planeación de Actividades',
      'ModalType'=>'modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'planeacion'
    ],
    [
      CreateInput(['id'=>'kid_planeacion_actividad','etiqueta'=>'Planeación de Actividad','required' => '','readonly' => '','value'=>isset($valor_id)?$valor_id:'']),
      CreateSelect(['id'=>'kid_tipo_actividad','etiqueta'=>'Planeación de Actividad','required' => ''],$tipo_actividad),
      CreateInput(['type'=>'text', 'maxlength'=>'80','id'=>'actividad','etiqueta'=>'Actividad','required' => '']),
      CreateTextArea(['maxlength'=>'300','id'=>'comentario','etiqueta'=>'Comentario',]),
      CreatSwitchCheck(['id'=>'dias_festivos','etiqueta'=>'Se Trabajara en Días Festivos','class'=>'OnHolidaysAllow']),
      CreatSwitchCheck(['id'=>'dia_sabado','etiqueta'=>'Se Trabajara en Dias Sábados','class'=>'OnSaturdayAllow']),
      CreatSwitchCheck(['id'=>'dia_domingo','etiqueta'=>'Se Trabajara en Dias Domingos','class'=>'OnSundayAllow']),
      CreateInput(['type'=>'date','id'=>'fecha_inicial','etiqueta'=>'Fecha de Inicio','required' => '', 'class'=>'DateStartCal-1']),
      CreateInput(['type'=>'date','id'=>'fecha_final','etiqueta'=>'Fecha de Fin','required' => '', 'class'=>'DateEndCal-1']),
      CreateInput(['type'=>'number','id'=>'dias_totales','etiqueta'=>'Dias Totales','required' => '','readonly' => '', 'class'=>'DateResultCal-1']),
      CreateInput(['type'=>'number','id'=>'cantidad_rrhh','etiqueta'=>'Cantidad de Personal','required' => ''])

    ]);

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
