<?php
    ob_start(); // Inicia la captura del buffer de salida
    


    /*$consulta = "SELECT id_estatus,estatus FROM estatus";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $estatuss=$resultado->fetchAll(PDO::FETCH_ASSOC);*/
    $PageSection = "Capturar producción";
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

$id = 'capturar_produccion';
$ButtonAddLabel = "Nueva Producción";
$titulos = ['ID', 'Fecha de Producción', 'Artículo', 'Cantidad Producida', 'Almacén', 'Creación', 'Fecha de Creación'];

CreateTable($id, $ButtonAddLabel, $titulos, $data, true, $botones_acciones);

CreateModalForm(
    [
        'id' => $id,
        'Title' => $ButtonAddLabel,
        'Title2' => 'Editar Producción',
        'Title3' => 'Ver Producción',
        'ModalType' => 'modal-dialog-scrollable',
        'method' => 'POST',
        'action' => 'bd/crudSummit.php',
        'bloque' => 'produccion',
    ],
    [
        CreateInput(['type' => 'datetime-local', 'id' => 'fecha_produccion', 'etiqueta' => 'Fecha de Producción', 'required' => 'true']),
        CreateSelect(['id' => 'kid_articulo', 'etiqueta' => 'Artículo', 'required' => 'true'], $articulos),
        CreateInput(['type' => 'number', 'id' => 'cantidad_producida', 'etiqueta' => 'Cantidad Producida', 'required' => 'true', 'step' => '0.01']),
        CreateSelect(['id' => 'kid_almacen', 'etiqueta' => 'Almacén', 'required' => 'true'], $almacenes),
    ]
);

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
