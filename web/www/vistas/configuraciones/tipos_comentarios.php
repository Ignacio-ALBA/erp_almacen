<?php
    ob_start(); // Inicia la captura del buffer de salida
    

    $consultaselect = "SELECT id_tipo_comentario, 
                            orden, 
                            tipo_comentario, 
                            CASE 
                                WHEN pordefecto = 1 THEN 'SÍ' 
                                ELSE 'NO' 
                            END AS pordefecto,
                            fecha_creacion
                      FROM 
                          tipos_comentarios
                      WHERE 
                          kid_estatus = 1";

    $resultado = $conexion->prepare($consultaselect);
    $resultado->execute();
    $data=$resultado->fetchAll(PDO::FETCH_ASSOC);

    $PageSection = "Tipos de Comentarios";
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

  $id = 'tipos_comentarios';
  $ButtonAddLabel = "Nuevo Tipo";
  $titulos = ['ID','Orden','Tipo de Comentario','Por Defecto','Fecha de creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data,true, []);
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Tipo',
      'Title3'=>'Ver Tipo',
      'ModalType'=>'modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'catalogo'
    ],
    [
      CreateInput(['type'=>'text','id'=>'tipo_comentario','etiqueta'=>'Tipo de Comentario','required' => '']),
      CreateInput(['type'=>'number','id'=>'orden','etiqueta'=>'Orden','required' => '']),
      CreatSwitchCheck(['id'=>'pordefecto','etiqueta'=>'Por defecto'])
      
    ]);

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
