<?php
    ob_start(); // Inicia la captura del buffer de salida
    
    


    /*$consulta = "SELECT id_estatus,estatus FROM estatus";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $estatuss=$resultado->fetchAll(PDO::FETCH_ASSOC);*/
    $PageSection = "Mermas";
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

$id = 'mermas';
$ButtonAddLabel = "Nueva Merma";
$titulos = ['ID', 'Producción', 'Artículo', 'Tipo de Merma', 'Título', 'Descripción', 'Cantidad', 'Creación', 'Fecha de Creación'];

CreateTable($id, $ButtonAddLabel, $titulos, $data, true, $botones_acciones);

CreateModalForm(
    [
        'id' => $id,
        'Title' => $ButtonAddLabel,
        'Title2' => 'Editar Merma',
        'Title3' => 'Ver Merma',
        'ModalType' => 'modal-dialog-scrollable',
        'method' => 'POST',
        'action' => 'bd/crudSummit.php',
        'bloque' => 'catalogo',
    ],
    [
        CreateSelect(['id' => 'kid_produccion', 'etiqueta' => 'Producción', 'required' => 'true'], $producciones),
        CreateSelect(['id' => 'kid_articulo', 'etiqueta' => 'Artículo', 'required' => 'true'], $articulos),
        CreateSelect(['id' => 'tipo_merma', 'etiqueta' => 'Tipo de Merma', 'required' => 'true'], [
            ['valor' => 'merma_reproceso', 'texto' => 'Merma Reproceso', 'pordefecto' => 0],
            ['valor' => 'merma_produccion', 'texto' => 'Merma Producción', 'pordefecto' => 0],
        ]),
        CreateInput(['type' => 'text', 'id' => 'titulo', 'etiqueta' => 'Título', 'required' => 'true']),
        CreateInput(['type' => 'text', 'id' => 'descripcion', 'etiqueta' => 'Descripción']),
        CreateInput(['type' => 'number', 'id' => 'cantidad', 'etiqueta' => 'Cantidad', 'required' => 'true', 'step' => '0.01']),
    ]
);

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
