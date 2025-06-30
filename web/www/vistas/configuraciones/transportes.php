<?php
    ob_start(); // Inicia la captura del buffer de salida
    $PageSection = "Transportes";
    
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
$id = 'transportes';
$ButtonAddLabel = "Nuevo Transporte";
$titulos = [ 'ID', 'Transporte', 'Fecha de creación'];

CreateTable($id, $ButtonAddLabel, $titulos, $data, true, $botones_acciones);

CreateModalForm(
    [
        'id' => $id,
        'Title' => $ButtonAddLabel,
        'Title2' => 'Editar Transporte',
        'Title3' => 'Ver Transporte',
        'ModalType' => 'modal-dialog-scrollable',
        'method' => 'POST',
        'action' => 'bd/crudSummit.php',
        'bloque' => 'configuraciones',
    ],
    [
        CreateInput(['type' => 'text', 'id' => 'nombre_transporte', 'etiqueta' => 'Transporte', 'required' => 'true']),
        CreateInput(['type' => 'hidden', 'id' => 'kid_estatus', 'value' => '1']) // Valor por defecto para kid_estatus
    ]
);

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
