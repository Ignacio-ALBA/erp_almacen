<?php
    ob_start(); // Inicia la captura del buffer de salida
    
    


    /*$consulta = "SELECT id_estatus,estatus FROM estatus";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $estatuss=$resultado->fetchAll(PDO::FETCH_ASSOC);*/
    $PageSection = "Mermas";
?>

<style>
    @font-face {
        font-family: 'Digital-7';
        src: url('/assets/fonts/digital_7/digital-7.ttf') format('truetype');
     
    }
    .digital-font {
      font-family: 'Digital-7', sans-serif; /* Usa la fuente Digital-7 */
    font-size: 4.58rem; /* Aplica el tamaño de fuente */
}
    .weight-container {
        position: relative;
        margin: 10px 0;
        max-width: 600px; /* Reducir el ancho máximo */
    display: inline-block; /* Hacer que el contenedor sea inline */

    }
    #peso_bascula {
    width: 360px !important; /* Aumentar el ancho en un 30% */
    height: calc(31px * 4.5); /* Aumentar la altura en un 30% (basado en la altura original de 31px) */
    /*font-size: 18.58rem; /* Ajustar el tamaño de la fuente proporcionalmente */
    padding: 0.35rem 0.7rem; /* Ajustar el padding para que el contenido no se vea comprimido */
    font-family: 'Digital-7', sans-serif; /* Usa la fuente Digital-7 */
    font-size: 7.58rem !important; /* Tamaño de fuente */
  }
    
    .weight-unit {
        position: absolute;
        right: -118px;
        top: 50.5%;
        transform: translateY(-50%);
        background-color: #001f3f;
        color: #7fdbff;
        padding: 0 5px;
        font-weight: bold;
        border-radius: 3px;
        margin-left: 5px;
        max-width: 450px; /* Ajustar el ancho máximo */
        font-size: 5.8rem; /* Aumentar el tamaño de fuente */
       
        
    }
   
.mb-3 {
    margin-bottom: 1rem;
}

.card {
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}



.row {
    display: flex;
    flex-wrap: wrap;
    margin-right: -15px;
    margin-left: -15px;
}

.col-md-6 {
    flex: 0 0 50%;
    max-width: 50%;
    padding-right: 15px;
    padding-left: 15px;
}

.form-control-sm {
    height: 31px;
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
  }
  
  .weight-container {
    max-width: 450px; /* Reducir aún más el ancho */
  }
  
  .form-group {
    margin-bottom: 0.5rem; /* Reducir el espacio entre elementos */
  }
  
  .card-body {
    padding: 1rem; /* Reducir el padding del card */
  }

    
    .weight-display:read-only {
        background-color: #001f3f !important;
        color: #7fdbff !important;
        opacity: 1;

    }
    </style>


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
echo '<div class="card" style="margin-bottom: 20px;">
  <div class="card-body">
      <div class="row justify-content-start"> 
          <div class="col-12 text-start">'; // Centrar el contenedor del peso
              // Añadir botón de conectar balanza
              echo CreateButtonP([
                  'id' => 'btn_conectar_balanza',
                  'type' => 'button',
                  'class' => 'btn btn-info btn-sm mb-2',
                  'text' => '<i class="bi bi-bluetooth"></i> Conectar Balanza',
                  'html' => true
              ]);
              
              echo CreateWeightLabel(['id' => 'peso_bascula', 'etiqueta' => 'Peso en Báscula']);
              echo CreateWeightInput([
                  'id' => 'peso_bascula',
                  'readonly' => 'readonly',
                  'value' => '0.00',
                  'style' => 'font-family: Digital-7, sans-serif; font-size: 4.58rem; text-align: start; color: #7fdbff; background: #001f3f; border: none; width: 300px;' // Estilo inline para centrar y aumentar el tamaño del peso
              ]);
          echo '</div>
      </div>
      <div class="row mt-4 justify-content-center">'; // Nueva fila para los inputs y selects
          echo '<div class="col-6">'; // Reduced from col-md-4
             // Crear el input y el select
echo CreateInput([
  'type' => 'text',
  'id' => 'num_pedido',
  'etiqueta' => 'Número de Pedido',
  'readonly' => 'readonly',
  'value' => $idOrdenCompra ?? '',
  'class' => 'form-control form-control-sm'
]);

$insumoOptions = [];
if (!empty($insumos)) {
  foreach ($insumos as $insumo) {
      $insumoOptions[] = [
          'valor' => $insumo['insumo'],
          'texto' => $insumo['insumo'],
          'pordefecto' => 0
      ];
  }
}

echo CreateSelect([
  'type' => 'text',
  'id' => 'insumo_peso',
  'etiqueta' => 'Insumo pesado',
  'required' => 'true',
  'class' => 'form-control form-control-sm'
], $insumoOptions);

          echo '</div>
     <div class="col-6">';  // Primera columna con dos elementos
          echo CreateSelect([
                  'type' => 'text',
                  'id' => 'almacen_destino',
                  'etiqueta' => 'Almacén de destino',
                  'required' => 'true',
                  'class' => 'form-control form-control-sm'
              ], [
                  ['valor' => 'ALMACEN MP', 'texto' => 'ALMACEN DE MATERIA PRIMA', 'pordefecto' => 0],
                  ['valor' => 'ALMACEN DE PRODUCCION', 'texto' => 'ALMACEN DE PRODUCCION', 'pordefecto' => 0]
              ]);
          
              echo CreateSelect([
                  'type' => 'text',
                  'id' => 'contenedor_destino',
                  'etiqueta' => 'Contenedor de destino',
                  'required' => 'true',
                  'class' => 'form-control form-control-sm'
              ], [
                  ['valor' => 'A-1', 'texto' => 'A-1', 'pordefecto' => 0],
                  ['valor' => 'A-2', 'texto' => 'A-2', 'pordefecto' => 0]
              ]);
          echo '</div>
      </div>
      <div class="row mt-4">
          <div class="col-12 d-flex justify-content-center">';
              // Existing register weight button

                 // Botón para generar QR
                 echo CreateButtonP([
                  'id' => 'btn_generar_qr',
                  'type' => 'button',
                  'class' => 'btn btn-secondary ms-2',
                  'text' => '<i class="bi bi-qr-code"></i> Generar Código QR',
                  'html' => true
              ]);

              // Botón para generar PDF
              echo CreateButtonP([
                  'id' => 'btn_generar_pdf',
                  'type' => 'button',
                  'class' => 'btn btn-success ms-2',
                  'text' => '<i class="bi bi-file-earmark-pdf"></i> Generar PDF',
                  'html' => true
              ]);
          echo '</div>
      </div>
  </div>
</div>';
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
