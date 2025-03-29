<?php
  ob_start(); // Inicia la captura del buffer de salida
  $PageSection = "Monedas";
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
  $id = 'monedas';
  $ButtonAddLabel = "Nueva Moneda";
  $titulos = ['ID','Orden','Moneda','Símbolo','Código','Por Defecto','Fecha de Creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data,true,[]);
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Moneda',
      'Title3'=>'Ver Moneda',
      'ModalType'=>'modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'contabilidad'
    ],
    [
      CreateInput(['type'=>'text','id'=>'moneda','etiqueta'=>'Moneda','required' => '']),
      CreateInput(['type'=>'text','id'=>'simbolo','etiqueta'=>'Símbolo','required' => '']),
      CreateInput(['type'=>'text','id'=>'codigo','etiqueta'=>'Código','required' => '']),
      CreateInput(['type'=>'number','id'=>'orden','etiqueta'=>'Orden','required' => '']),
      CreatSwitchCheck(['id'=>'pordefecto','etiqueta'=>'Por defecto'])
      
    ]);

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
