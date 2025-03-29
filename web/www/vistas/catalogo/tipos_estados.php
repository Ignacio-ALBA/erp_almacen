<?php
    ob_start(); // Inicia la captura del buffer de salida
    $PageSection = "Tipos de Estados";
?>


  <div class="pagetitle">
    <h1><?php echo $PageSection; ?></h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Home</a></li>
        <li class="breadcrumb-item">Catálogo</li>
        <li class="breadcrumb-item active" ><?php echo $PageSection; ?></li>
      </ol>
    </nav>
  </div><!-- End Page Title -->
<?php 

  $id = 'estatus';
  $ButtonAddLabel = "Nuevo Tipo de Estado";
  $titulos = ['ID', 'Estado','Color','Fecha de creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data,true,$botones_acciones);
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Tipo de Estado',
      'Title3'=>'Ver Tipo de  Estado',
      'ModalType'=>'modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'catalogo'
    ],
    [
      CreateInput(['type'=>'text','id'=>'estatus','etiqueta'=>'Tipo de Estado','required' => '']),
      CreateInput(['type'=>'color','id'=>'estatus_color','etiqueta'=>'Color','required' => ''])
    ]);


  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
