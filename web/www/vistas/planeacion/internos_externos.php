<?php
    ob_start(); // Inicia la captura del buffer de salida
    $PageSection = "Trabajadores Internos Y Externos";
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

  $id = 'internos_externos';
  $ButtonAddLabel = "Nuevo Tipo";
  $titulos = ['ID', 'Orden','Tipo','Por Defecto','Fecha de creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data,true,[]);
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Tipo',
      'Title3'=>'Ver Tipo',
      'ModalType'=>'modal-dialog-centered', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'planeacion'
    ],
    [
      CreateInput(['type'=>'text','id'=>'internos_externos','etiqueta'=>'Tipo','required' => '']),
      CreateInput(['type'=>'number','id'=>'orden','etiqueta'=>'Orden','required' => '']),
      CreatSwitchCheck(['id'=>'pordefecto','etiqueta'=>'Por defecto'])
    ]);

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
