<?php
    ob_start(); // Inicia la captura del buffer de salida


    

    $PageSection = "Planeación de Talento Humanos";
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

  $id = 'planeaciones_recursos_humanos';
  $ButtonAddLabel = "Nueva Planeación de Talento Humanos";
  $titulos = ['ID', 'Bolsa de Proyectos','Proyecto','Cliente','No. Internos','No. Externos','Estado','La Creo','Fecha de creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data, $AllowADDButton, 'ButtonsInRow');
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Planeación de Talento Humanos',
      'Title3'=>'Ver Planeación de Talento Humanos',
      'ModalType'=>'modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'planeacion'
    ],
    [
      CreateSelect(['id'=>'kid_bolsa_proyecto','etiqueta'=>'Bolsa de Proyectos','required' => '','class'=>'DataGET Data-GETProyectoByBolsa'],$bolsas_proyectos),
      CreateSelect(['id'=>'kid_proyecto','etiqueta'=>'Proyecto','required' => '','class'=>'kid_bolsa_proyecto'],[]),
      CreateInput(['type'=>'text','id'=>'kid_cliente','etiqueta'=>'Cliente','readonly' => '','value'=>isset($valor_id)?$valor_id:'']),
      CreateInput(['type'=>'number','id'=>'cantidad_internos','etiqueta'=>'Número de Interno','required' => '','class'=>'MUL-1']),
      CreateInput(['type'=>'number','id'=>'costo_cantidad_internos','etiqueta'=>'Costo por Número de Internos','required' => '','class'=>'MUL-1']),
      CreateInput(['type'=>'number','id'=>'cantidad_externos','etiqueta'=>'Número de Externos','required' => '','class'=>'MUL-2']),
      CreateInput(['type'=>'number','id'=>'costo_cantidad_externos','etiqueta'=>'Costo por Número de Externos','required' => '','class'=>'MUL-2']),
      CreateInput(['type'=>'number','id'=>'monto_total','etiqueta'=>'Monto Total','required' => '','readonly' => '','class'=>'RESULT-3']),
    ]);
    echo CreateInput(['type'=>'number','id'=>'RESULT-1SUM-3','div_style'=>'display:none;','disabled' => '','class'=>'RESULT-1 SUM-3']);
    echo CreateInput(['type'=>'number','id'=>'RESULT-2SUM-3','div_style'=>'display:none;','disabled' => '','class'=>'RESULT-2 SUM-3']);

    $id = 'detalles_planeaciones_rrhh';
    $ButtonAddLabel = "Nuevo Contenido de TH";
    $titulos = ['ID', 'Planeación','Personal','Costo','Cantidad','Tipo Costo','Modalidad','Costo Total','Fecha de creación'];
  

    ob_start();
    CreateTable($id, $ButtonAddLabel, $titulos, [],true,[],'',$atributos = ['data-select-column'=>0]);
    $detailsTableOutput = ob_get_clean();

    CreateModal( [
      'id'=> $id.'-View', 
      'Title'=>'Contenido de Planeación de TH',
      'Title2'=>'Editar Planeación de RRHH',
      'Title3'=>'Ver Planeación de RRHH',
      'ModalType'=>'modal-fullscreen modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'planeacion'
    ],
    [
      $detailsTableOutput
    ],
    ['<button type="button" class="btn btn-secondary secondary" data-bs-dismiss="modal">Cancelar</button>']);

    CreateModalForm(
      [
        'id'=> $id, 
        'Title'=>$ButtonAddLabel,
        'Title2'=>'Editar Contenido de Planeación',
        'Title3'=>'Ver Contenido de Planeación',
        'ModalType'=>'modal-dialog-scrollable', 
        'method'=>'POST',
        'action'=>'bd/crudSummit.php',
        'bloque'=>'planeacion',
        'data-select-column'=>'[0]',
        'data-input-fill'=>'[kid_planeaciones_rrhh]'
      ],
      [
        CreateInput(['id'=>'kid_planeaciones_rrhh','etiqueta'=>'Planeación de Recursos Humanos','readonly' => '']),
        CreateSelect(['id'=>'kid_personal','etiqueta'=>'Personal','required' => ''],$colaboradores),
        CreateInput(['type'=>'number','id'=>'costo','etiqueta'=>'Costo','required' => '']),
        CreateInput(['type'=>'number','id'=>'cantidad','etiqueta'=>'Cantidad','required' => '']),
        CreateSelect(['id'=>'kid_tipo_cantidad','etiqueta'=>'Tipo de Costo','required' => ''],$tipo_cantidad),
        CreateSelect(['id'=>'kid_interno_externo','etiqueta'=>'Modalidad','required' => ''],$modalidad),
        CreateInput(['type'=>'number','id'=>'costo_total','etiqueta'=>'Costo Total','required' => ''])
  
      ]);

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
