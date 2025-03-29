<?php
    ob_start(); // Inicia la captura del buffer de salida
    $PageSection = "Adicionales de Asistencia";
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

  $id = 'adicionales_asistencias_th';
  $ButtonAddLabel = "Nueva Adicional";
  $titulos = ['ID','Colaborador','Fecha Entrada','Fecha Salida','Modalidad','Fecha de creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data, true, []);

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
        CreateSelect(['id'=>'kid_tipo_adicional_th','etiqueta'=>'Tipo Adicional','required' => ''],$tipoadicionales),
        CreateTextArea(['id'=>'comentario','maxlength'=>'200','etiqueta'=>'Comentario','type'=>'text','required' => '']),
        CreateInput(['id'=>'evidencia','type'=>'file','etiqueta'=>'Evidencia']),
      ]);

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
