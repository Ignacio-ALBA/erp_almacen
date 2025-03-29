<?php
  ob_start(); // Inicia la captura del buffer de salida
  $PageSection = "Detalles de Proyectos";
?>


  <div class="pagetitle">
    <h1><?php echo $PageSection; ?></h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Home</a></li>
        <li class="breadcrumb-item">Administración</li>
        <li class="breadcrumb-item active" ><?php echo $PageSection; ?></li>
      </ol>
    </nav>
  </div><!-- End Page Title -->
<?php 
  $id = 'detalles_proyectos';
  $ButtonAddLabel = "Nuevo Detalle";
  $titulos = ['ID','Detalle','Proyecto','Presupuesto','Objetivo','Responsable','Fecha de Inicio','Fecha de Fin'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data,true);
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Detalle',
      'Title3'=>'Ver Detalle',
      'ModalType'=>'modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'administracion'
    ],
    [
      CreateInput(['type'=>'text','id'=>'detalle_proyecto','etiqueta'=>'Detalle','required' => '']),
      CreateSelect(['id'=>'kid_proyecto','etiqueta'=>'Proyecto','required' => ''],$proyectos),
      CreateInput(['type'=>'number','id'=>'presupuesto','etiqueta'=>'Presupuesto','required' => '']),
      CreateInput(['type'=>'text','id'=>'objetivo','etiqueta'=>'Objetivo','required' => '']),
      CreateSelect(['id'=>'kid_responsable','etiqueta'=>'Colaborador Responsable','required' => ''],$colaboradores),
      CreateInput(['type'=>'date','id'=>'fecha_inicio','etiqueta'=>'Fecha de Inicio','required' => '']),
      CreateInput(['type'=>'date','id'=>'fecha_fin','etiqueta'=>'Fecha de Fin','required' => ''])

      //CreatSwitchCheck(['id'=>'pordefecto','etiqueta'=>'Por defecto'])
      
    ]);

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
