<?php
    ob_start(); // Inicia la captura del buffer de salida


    

    $PageSection = "Contenido de Planeación Talento Humano";
?>


  <div class="pagetitle">
    <h1><?php echo $PageSection; ?></h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Home</a></li>
        <li class="breadcrumb-item"><?php echo $nombre_modulo; ?></li>
        <?php 
          if($breadcrumb){
            echo $breadcrumb;
          } else{
            echo '<li class="breadcrumb-item active">'.$PageSection.'</li>';
          }
        ?>
      </ol>
    </nav>
  </div><!-- End Page Title -->
<?php 
  $id = 'detalles_planeaciones_rrhh';
  $ButtonAddLabel = "Nuevo Contenido de TH";
  $titulos = ['ID', 'Planeación','Personal','Costo','Cantidad','Tipo Costo','Modalidad','Costo Total','Fecha de creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data,$AllowADDButton);
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Contenido de Planeación',
      'Title3'=>'Ver Contenido de Planeación',
      'ModalType'=>'modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'planeacion'
    ],
    [
      CreateInput(['id'=>'kid_planeaciones_rrhh','etiqueta'=>'Planeación de Recursos Humanos','required' => '','readonly' => '','value'=>isset($valor_id)?$valor_id:'']),
      CreateSelect(['id'=>'kid_personal','etiqueta'=>'Personal','required' => ''],$colaboradores),
      CreateSelect(['id'=>'kid_interno_externo','etiqueta'=>'Modalidad','required' => ''],$modalidad),
      CreateSelect(['id'=>'kid_tipo_cantidad','etiqueta'=>'Tipo de Costo','required' => ''],$tipo_cantidad),
      CreateInput(['type'=>'number','id'=>'costo','etiqueta'=>'Costo','required' => '','class'=>'MUL-1']),
      CreateInput(['type'=>'number','id'=>'cantidad','etiqueta'=>'Cantidad','required' => '','class'=>'MUL-1']),
      CreateInput(['type'=>'number','id'=>'costo_total','etiqueta'=>'Costo Total','required' => '','readonly' => '','class'=>'RESULT-1'])

    ]);

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
