<?php
  ob_start(); // Inicia la captura del buffer de salida
  $PageSection = "Bolsas de Proyectos";
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
  $id = 'bolsas_proyectos';
  $ButtonAddLabel = "Nueva Bolsa";
  $titulos = ['ID','Bolsa','Cliente','Comentario','Fecha de Creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data,true);
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Bolsa',
      'Title3'=>'Ver Bolsa',
      'ModalType'=>'modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'administracion'
    ],
    [
      CreateInput(['type'=>'text','id'=>'bolsa_proyecto','etiqueta'=>'Bolsa de Proyecto','required' => '']),
      CreateSelect(['id'=>'kid_cliente','etiqueta'=>'Cliente','required' => ''],$clientes),
      CreateTextArea(['maxlength'=>'200','id'=>'comentarios','etiqueta'=>'Comentario','required' => '']),

      //CreatSwitchCheck(['id'=>'pordefecto','etiqueta'=>'Por defecto'])
      
    ]);

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
