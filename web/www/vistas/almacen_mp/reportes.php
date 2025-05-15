<?php
    ob_start(); // Inicia la captura del buffer de salida
    



    /*$consulta = "SELECT id_estatus,estatus FROM estatus";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $estatuss=$resultado->fetchAll(PDO::FETCH_ASSOC);*/
    $PageSection = "Reporte de producción";
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

$id = 'detalles_produccion';
$ButtonAddLabel = "Nuevo Detalle de Producción";
$titulos = ['ID', 'Fecha de Producción', 'Artículo', 'Cantidad Usada', 'Ubicación', 'Creación', 'Código QR', 'Fecha de Creación'];

CreateTable($id, $ButtonAddLabel, $titulos, $data, true, $botones_acciones);

CreateModalForm(
    [
        'id' => $id,
        'Title' => $ButtonAddLabel,
        'Title2' => 'Editar Detalle de Producción',
        'Title3' => 'Ver Detalle de Producción',
        'ModalType' => 'modal-dialog-scrollable',
        'method' => 'POST',
        'action' => 'bd/crudSummit.php',
        'bloque' => 'produccion',
    ],
    [
        CreateSelect(['id' => 'kid_produccion', 'etiqueta' => 'Producción', 'required' => 'true'], $producciones),
        CreateSelect(['id' => 'kid_articulo', 'etiqueta' => 'Artículo', 'required' => 'true'], $articulos),
        CreateInput(['type' => 'number', 'id' => 'cantidad_usada', 'etiqueta' => 'Cantidad Usada', 'required' => 'true', 'step' => '0.01']),
        CreateSelect(['id' => 'kid_ubicacion', 'etiqueta' => 'Ubicación', 'required' => 'true'], $ubicaciones),
        CreateInput(['type' => 'text', 'id' => 'codigo_qr', 'etiqueta' => 'Código QR']),
    ]
);

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
