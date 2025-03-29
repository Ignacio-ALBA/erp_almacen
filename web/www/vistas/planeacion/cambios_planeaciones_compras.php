<?php
ob_start(); // Inicia la captura del buffer de salida
$PageSection = "Cambios En Tablas";
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

$id = 'cambios_planeaciones_compras';
$ButtonAddLabel = "";
$titulos = ['ID', 'No. De Registro','Tabla', 'Descripción', 'Lo Creo', 'Fecha de creación'];
CreateTable($id, $ButtonAddLabel, $titulos, $data,false,$botones_acciones);


CreateModalForm(
  [
    'id' => $id,
    'Title' => $ButtonAddLabel,
    'Title2' => 'Editar Cambio',
    'Title3' => 'Ver Cambio',
    'ModalType' => 'modal-dialog-centered',
    'method' => 'POST',
    'action' => 'bd/crudSummit.php',
    'bloque' => 'planeacion'
  ],
  [
    CreateInput(['type' => 'text','id' => 'kid_registro_tabla', 'etiqueta' => 'Registro de Tabla', 'required' => '']),
    CreateInput(['type' => 'text','id' => 'kid_tabla', 'etiqueta' => 'Tabla', 'required' => '']),
    CreateTextArea(['id' => 'cambio', 'etiqueta' => 'Descripción del Cambio', 'required' => '']),
  ]
);

$wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

include 'wrapper.php'; // Incluye el wrapper
?>