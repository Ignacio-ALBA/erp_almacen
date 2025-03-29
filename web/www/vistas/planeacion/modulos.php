<?php
ob_start(); // Inicia la captura del buffer de salida
$PageSection = "Modulos";
?>


<div class="pagetitle">
  <h1><?php echo $PageSection; ?></h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="/">Home</a></li>
      <li class="breadcrumb-item"><?php echo $nombre_modulo; ?></li>
      <li class="breadcrumb-item active"><?php echo $PageSection; ?></li>
    </ol>
  </nav>
</div><!-- End Page Title -->
<?php

$id = 'modulos';
$ButtonAddLabel = "Nuevo Modulo";
$titulos = ['ID', 'Modulo', 'Descripción', 'Lo Creo', 'Fecha de creación'];
CreateTable($id, $ButtonAddLabel, $titulos, $data,true,$botones_acciones);
CreateModalForm(
  [
    'id' => $id,
    'Title' => $ButtonAddLabel,
    'Title2' => 'Editar Modulo',
    'Title3' => 'Ver Modulo',
    'ModalType' => 'modal-dialog-centered',
    'method' => 'POST',
    'action' => 'bd/crudSummit.php',
    'bloque' => 'planeacion'
  ],
  [
    CreateInput(['type' => 'text','maxlength'=>'80', 'id' => 'modulo', 'etiqueta' => 'Modulo', 'required' => '']),
    CreateInput(['type' => 'text','maxlength'=>'200', 'id' => 'descripcion', 'etiqueta' => 'Descripción', 'required' => '']),
  ]
);

$wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

include 'wrapper.php'; // Incluye el wrapper
?>