<?php
    ob_start(); // Inicia la captura del buffer de salida


    

    $PageSection = "Planeación de Actividades";
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

  $id = 'planeaciones_actividades';
  $ButtonAddLabel = "Nueva Planeación de Actividad";
  $titulos = ['ID', 'Bolsa de Proyectos','Proyecto','Cliente','Fecha de Inicio','Fecha de Fin','Total de Días','Estado','La Creo','Fecha de creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data, $AllowADDButton,'ButtonsInRow');
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Planeación de Actividad',
      'Title3'=>'Ver Planeación de Actividad',
      'ModalType'=>'modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'planeacion'
    ],
    [
      CreateSelect(['id'=>'kid_bolsa_proyecto','etiqueta'=>'Bolsa de Proyectos','required' => '','class'=>'DataGET Data-GETProyectoByBolsa'],$bolsas_proyectos),
      CreateSelect(['id'=>'kid_proyecto','etiqueta'=>'Proyecto','required' => '','class'=>'kid_bolsa_proyecto'],[]),
      CreateInput(['type'=>'text','id'=>'kid_cliente','etiqueta'=>'Cliente','readonly' => '','value'=>isset($valor_id)?$valor_id:'']),
      CreateInput(['type'=>'number','id'=>'cantidad_actividades','etiqueta'=>'Número de Tareas','required' => '']),
      CreateTextArea(['maxlength'=>'300','id'=>'comentario','etiqueta'=>'Comentario',]),
      CreatSwitchCheck(['id'=>'dias_festivos','etiqueta'=>'Se Trabajara en Días Festivos','class'=>'OnHolidaysAllow']),
      CreatSwitchCheck(['id'=>'dia_sabado','etiqueta'=>'Se Trabajara en Dias Sábados','class'=>'OnSaturdayAllow']),
      CreatSwitchCheck(['id'=>'dia_domingo','etiqueta'=>'Se Trabajara en Dias Domingos','class'=>'OnSundayAllow']),
      CreateInput(['type'=>'date','id'=>'fecha_inicial','etiqueta'=>'Fecha de Inicio','required' => '', 'class'=>'DateStartCal-1']),
      CreateInput(['type'=>'date','id'=>'fecha_final','etiqueta'=>'Fecha de Fin','required' => '', 'class'=>'DateEndCal-1']),
      CreateInput(['type'=>'number','id'=>'dias_totales','etiqueta'=>'Dias Totales','required' => '','readonly' => '', 'class'=>'DateResultCal-1']),
      CreateInput(['type'=>'number','id'=>'cantidad_rrhh','etiqueta'=>'Cantidad de Personal','required' => ''])
    ]);

    $id = 'detalles_planeaciones_actividades';
    $ButtonAddLabel = "Nuevo Contenido Planeación de Actividades";
    $titulos = ['ID', 'Planeación','Actividad','Fecha de Inicio','Fecha de Fin','Días Totales','Fecha de creación'];
  

    ob_start();
    CreateTable($id, $ButtonAddLabel, $titulos, [],true,[],'',$atributos = ['data-select-column'=>0]);
    $detailsTableOutput = ob_get_clean();

    CreateModal( [
      'id'=> $id.'-View', 
      'Title'=>'Contenido de Planeación de Actividades',
      'Title2'=>'Editar Planeación de RRHH',
      'Title3'=>'Ver Planeación de RRHH',
      'ModalType'=>'modal-fullscreen modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'planeacion'
    ],
    [
      $detailsTableOutput
    ],
    ['<button type="button" class="btn btn-secondary secondary" data-bs-dismiss="modal">Cancelar</button>']);

    CreateModalForm(
      [
        'id'=> $id, 
        'Title'=>$ButtonAddLabel,
        'Title2'=>'Editar Contenido Planeación de Actividades',
        'Title3'=>'Ver Contenido Planeación de Actividades',
        'ModalType'=>'modal-dialog-scrollable', 
        'method'=>'POST',
        'action'=>'bd/crudSummit.php',
        'bloque'=>'planeacion',
        'data-select-column'=>'[0]',
        'data-input-fill'=>'[kid_planeacion_actividad]'
      ],
      [
        CreateInput(['id'=>'kid_planeacion_actividad','etiqueta'=>'Planeación de Actividad','readonly' => '']),
        CreateInput(['type'=>'text', 'maxlength'=>'80','id'=>'actividad','etiqueta'=>'Actividad','required' => '']),
        CreateInput(['type'=>'date','id'=>'fecha_inicial','etiqueta'=>'Fecha de Inicio','required' => '', 'class'=>'DateStartCal-2']),
        CreateInput(['type'=>'date','id'=>'fecha_final','etiqueta'=>'Fecha de Fin','required' => '', 'class'=>'DateEndCal-2']),
        CreateInput(['type'=>'number','id'=>'dias_totales','etiqueta'=>'Dias Totales','required' => '','readonly' => '', 'class'=>'DateResultCal-2']),
        CreateInput(['type'=>'number','id'=>'cantidad_rrhh','etiqueta'=>'Cantidad de Personal','required' => ''])
  
      ]);

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
