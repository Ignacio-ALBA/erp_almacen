<?php
    ob_start(); // Inicia la captura del buffer de salida
    $PageSection = "Comentarios de Proveedores";
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

  $id = 'comentarios_proveedores';
  $ButtonAddLabel = "Nuevo Comentario";
  $titulos = ['ID','Proveedor','Comentario', 'Tipo de Comentario','Fecha de creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data,false, []);
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Comentario',
      'Title3'=>'Ver Comentario',
      'ModalType'=>'modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'compras'
    ],
    [
      CreateSelect(['id'=>'kid_proveedor','etiqueta'=>'Proveedor','disabled' => '','class'=>'OnEditReadOnly'],$proveedores),
      CreateTextArea(['type'=>'text', 'maxlength'=>'300','id'=>'comentario_proveedor','etiqueta'=>'Comentario','required' => '']),
      CreateSelect(['id'=>'kid_tipo_comentario','etiqueta'=>'Tipo de Comentario','disabled' => ''],$tipo_comentario)

      //CreatSwitchCheck(['id'=>'pordefecto','etiqueta'=>'Por defecto'])
      
    ]);

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
