<?php
    ob_start(); // Inicia la captura del buffer de salida
    $PageSection = "Asistencias ";
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

  $id = 'asistencias_th';
  $ButtonAddLabel = "Nuevas Asistencias";
  $titulos = ['ID','Colaborador','Fecha Entrada','Fecha Salida','Modalidad','Fecha de creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data, true, $botones_acciones);
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Asistencias',
      'Title3'=>'Ver Asistencias',
      'ModalType'=>'modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'talento_humano'
    ],
    [
      CreateInput(['type'=>'file','accept'=>'.xls,.xlsx','maxlength'=>'11','id'=>'finalizado','etiqueta'=>'Formato de Asistencias','required' => '']),
    ]);

    $id = 'editar_asistencias_th';
    CreateModalForm(
      [
        'id'=> $id, 
        'Title'=>$ButtonAddLabel,
        'Title2'=>'Editar Asistencias',
        'Title3'=>'Ver Asistencias',
        'ModalType'=>'modal-dialog-scrollable', 
        'method'=>'POST',
        'action'=>'bd/crudSummit.php',
        'bloque'=>'talento_humano'
      ],
      [
        CreateSelect(['id'=>'kid_colaborador','etiqueta'=>'Colaborador','required' => ''],$colaboradores),
        CreateInput(['id'=>'estampa_entrada_date','etiqueta'=>'Fecha de Entrada','type'=>'date','required' => '']),
        CreateInput(['id'=>'estampa_entrada_time','etiqueta'=>'Hora de Entrada', 'step'=>'1','type'=>'time','required' => '']),
        CreateInput(['id'=>'estampa_salida_date','etiqueta'=>'Fecha de Salida', 'step'=>'1','type'=>'date','required' => '']),
        CreateInput(['id'=>'estampa_salida_time','etiqueta'=>'Hora de Salida','type'=>'time','required' => '']),
        CreateSelect(['id'=>'kid_internos_externos','etiqueta'=>'Modalidad de Trabajo','required' => ''],$internos_externos),
        CreateSelect(['id'=>'kid_tipos_cantidad','etiqueta'=>'Tipo de Costo','required' => ''],$tipo_cantidad),
        CreateInput(['type'=>'text','maxlength'=>'11','id'=>'cantidad_periodo','etiqueta'=>'Periodo','required' => ''])
      ]);

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
