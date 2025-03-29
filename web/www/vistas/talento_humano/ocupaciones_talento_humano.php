<?php
    ob_start(); // Inicia la captura del buffer de salida
    $PageSection = "Ocupaciones ";
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

  $id = 'ocupaciones_th';
  $ButtonAddLabel = "Nuevo Colaborador";
  $titulos = ['ID','Colaborador','Proyecto','Fecha Inicio','Fecha Fin','Libre','Fecha de creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data, false, $botones_acciones);
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Colaborador',
      'Title3'=>'Ver Colaborador',
      'ModalType'=>'modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'talento_humano'
    ],
    [
      CreateSelect(['id'=>'kid_colaborador','etiqueta'=>'Colaborador','required' => ''],$colaboradores),
      CreateSelect(['id'=>'kid_bolsa_proyecto','etiqueta'=>'Bolsa de Proyectos','required' => ''],$bolsas_proyectos),
      CreateSelect(['id'=>'kid_proyecto','etiqueta'=>'Proyectos','required' => ''],$proyectos),
      CreateInput(['id'=>'estampa_inicio','etiqueta'=>'Fecha de Inicio','type'=>'date','required' => '']),
      CreateInput(['id'=>'estampa_fin','etiqueta'=>'Fecha de Fin','type'=>'date','required' => '']),
      CreateSelect(['id'=>'kid_internos_externos','etiqueta'=>'Modalidad de Trabajo','required' => ''],$internos_externos),
      CreateSelect(['id'=>'kid_tipos_cantidad','etiqueta'=>'Tipo de Costo','required' => ''],$tipo_cantidad),
      CreateInput(['type'=>'text','maxlength'=>'11','id'=>'cantidad_periodo','etiqueta'=>'Periodo','required' => '']),
      CreateInput(['type'=>'text','maxlength'=>'11','id'=>'finalizado','etiqueta'=>'Libre','required' => '']),
    ]);

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
