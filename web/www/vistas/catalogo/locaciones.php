<?php
    ob_start(); // Inicia la captura del buffer de salida
    


    /*$consulta = "SELECT id_estatus,estatus FROM estatus";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $estatuss=$resultado->fetchAll(PDO::FETCH_ASSOC);*/
    $PageSection = "Locaciones";
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

$id = 'ubicacion_almacen';
$ButtonAddLabel = "Nueva Ubicación de Almacén";
$titulos = ['ID', 'Almacén', 'Código Localización', 'Descripción', 'Creación', 'Fecha de Creación'];

CreateTable($id, $ButtonAddLabel, $titulos, $data, true, $botones_acciones);

CreateModalForm(
    [
        'id' => $id,
        'Title' => $ButtonAddLabel,
        'Title2' => 'Editar Ubicación',
        'Title3' => 'Ver Ubicación',
        'ModalType' => 'modal-dialog-scrollable',
        'method' => 'POST',
        'action' => 'bd/crudSummit.php',
        'bloque' => 'catalogo',
    ],
    [
        CreateSelect(['id' => 'kid_almacen', 'etiqueta' => 'Almacén', 'required' => 'true'], $almacenes),
        CreateInput(['type' => 'text', 'id' => 'codigo_localizacion', 'etiqueta' => 'Código Localización', 'required' => 'true']),
        CreateInput(['type' => 'text', 'id' => 'descripcion', 'etiqueta' => 'Descripción']),
    ]
);
  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
