<?php
    ob_start();
    // $productos = GetProductosListForSelect(); // Función que debes crear
    // $proveedores = GetProveedoresListForSelect(); // Función que debes crear
    // $localizaciones = GetLocalizacionesListForSelect(); // Función que debes crear

    // Ya no necesitas $_GET['id'], todo vendrá de localStorage vía JS
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
    color: #7fdbff !important;
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
/* NUEVO: Inputs readonly con fondo gris */
input[readonly]:not(#peso_bascula), textarea[readonly], select[readonly], .form-control[readonly]:not(#peso_bascula) {
    background-color: #e9ecef !important;
    color: #495057 !important;
    opacity: 1;
}
.input-group-4 {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
}
.input-group-4 > div {
    flex: 1 1 22%;
    min-width: 180px;
}
</style>

<?php 
    $PageSection = "Pesaje De Materia Prima";
?> 

<div class="pagetitle">
    <h1><?php echo $PageSection; ?></h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Home</a></li>
        <li class="breadcrumb-item">Compras</li>
        <li class="breadcrumb-item active" ><?php echo $PageSection; ?></li>
      </ol>
    </nav>
</div><!-- End Page Title -->

<?php 
  $id = 'recepciones_compras';
  $ButtonAddLabel = "Nueva materia prima";

echo '<div class="card mb-3"><div class="card-body">';

// Fila de la balanza
echo '<div class="row mb-3">
    <div class="col-12 text-start">';
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
            'style' => 'font-family: Digital-7, sans-serif; font-size: 4.58rem; text-align: start; color: #7fdbff; background: #001f3f; border: none; width: 300px;'
        ]);
echo '</div></div>';

// Fila 1: 4 inputs
echo '<div class="row input-group-4 mb-3">';
echo '<div>';
echo CreateInput([
    'type' => 'text',
    'id' => 'num_pedido',
    'etiqueta' => 'Número de OC',
    'readonly' => 'readonly',
    'value' => '',
    'class' => 'form-control form-control-sm'
]);
echo '</div>';
echo '<div>';
echo CreateInput([
    'type' => 'text',
    'id' => 'nombre_orden',
    'etiqueta' => 'Nombre de OC',
    'readonly' => 'readonly',
    'value' => '',
    'class' => 'form-control form-control-sm'
]);
echo '</div>';
echo '<div>';
echo CreateInput([
    'type' => 'text',
    'id' => 'proveedor_orden',
    'etiqueta' => 'Proveedor',
    'readonly' => 'readonly',
    'value' => '',
    'class' => 'form-control form-control-sm'
]);
echo '</div>';
echo '<div>';
echo CreateInput([
    'type' => 'number',
    'id' => 'num_tarimas',
    'etiqueta' => 'Número de Tarimas',
    'value' => '',
    'class' => 'form-control form-control-sm',
    'min' => '1'
]);
echo '</div>';
echo '</div>';

// Fila 2: insumo, cantidad, modo pesaje, valor tarima
echo '<div class="row input-group-4 mb-3">';
echo '<div>';
echo CreateSelect([
    'type' => 'text',
    'id' => 'insumo_peso',
    'etiqueta' => 'Insumo pesado',
    'required' => 'true',
    'class' => 'form-control form-control-sm'
], []);
echo '</div>';
echo '<div>';
echo CreateInput([
    'type' => 'number',
    'id' => 'cantidad_insumo',
    'etiqueta' => 'Peso Estimado (Kg)',
    'readonly' => 'readonly',
    'value' => '',
    'class' => 'form-control form-control-sm'
]);
echo '</div>';
echo '<div>';
echo CreateSelect([
    'id' => 'modo_pesaje',
    'etiqueta' => 'Modo Pesaje',
    'class' => 'form-control form-control-sm'
], [
    ['valor' => '', 'texto' => 'Selecciona tu manera de pesar la tarima', 'pordefecto' => 1],
    ['valor' => 'Pesaje Automatico', 'texto' => 'Pesaje Automático', 'pordefecto' => 0],
    ['valor' => 'Captura Manual', 'texto' => 'Captura Manual', 'pordefecto' => 0],
    ['valor' => 'Captura Estatica', 'texto' => 'Captura Estática', 'pordefecto' => 0]
]);
echo '</div>';
echo '<div id=\"contenedor_valor_tarima\">';
// Aquí el JS insertará dinámicamente el input/select según el modo de pesaje
echo '</div>';
echo '</div>';

// Fila 3: almacén y contenedor destino
echo '<div class="row input-group-4 mb-3">';
echo '<div>';
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
echo '</div>';
echo '<div>';
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
echo '</div>';
echo '</div>';

echo '<div class="row mt-3 justify-content-center">';
echo '  <div class="col-auto">';
echo '      <button type="button" id="btn_guardar_pesaje" class="btn btn-secondary">';
echo '          <i class="bi bi-qr-code"></i> Guardar Pesaje';
echo '      </button>';
echo '  </div>';
echo '  <div class="col-auto">';
echo '      <button type="button" id="btn_finalizar_recepcion" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_detalles_recepcion_mp">';
echo '          <i class="bi bi-check-circle"></i> Finalizar recepción';
echo '      </button>';
echo '  </div>';
echo '</div>';

echo '</div></div>';



  $titulos = ['ID', 'Recepción','Código Externo','Proyecto','Proveedor','Almacén','Orden de Compra','Estado','Fecha de creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data, true, $botones_acciones);
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar recepción',
      'Title3'=>'Ver Lista',
      'ModalType'=>'modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'compras'
    ],
    [
      CreateInput(['type'=>'text','maxlength'=>'100','id'=>'recepcion_compras','etiqueta'=>'Recepción','required' => '']),
      CreateInput(['type'=>'text','maxlength'=>'100','id'=>'codigo_externo','etiqueta'=>'Código Externo','required' => '']),
      CreateInput(['type'=>'text','id'=>'kid_proyecto','etiqueta'=>'Proyecto','readonly' => '']),
      CreateInput(['type'=>'text','id'=>'kid_proveedor','etiqueta'=>'Proveedor','readonly' => '']),
      CreateInput(['type'=>'text','id'=>'kid_orden_compras','etiqueta'=>'Orden de Compra','readonly' => '']),
      CreateSelect(['type'=>'text','id'=>'kid_almacen','etiqueta'=>'Almacén','readonly' => ''],$almacenes),
      CreateSelect(['id'=>'kid_recibe','etiqueta'=>'Recibió','required' => ''],$colaboradores),
      CreateSelect(['id'=>'kid_reclama','etiqueta'=>'Reclamo','required' => ''],$colaboradores),
      CreateSelect(['id'=>'kid_regresa','etiqueta'=>'Regreso ','required' => ''],$colaboradores),
      CreateInput(['type'=>'number','id'=>'monto_total','etiqueta'=>'Monto Total']),
      CreateInput(['type'=>'number','id'=>'monto_neto','etiqueta'=>'Monto Neto']),
    ]);

     // Add centered button


// Modal para mostrar detalles de la recepción
echo '<div class="modal fade" id="modal_detalles_recepcion_mp" tabindex="-1" aria-labelledby="modalDetallesRecepcionMPLabel" aria-hidden="true">';
echo '  <div class="modal-dialog modal-lg">';
echo '    <div class="modal-content">';
echo '      <div class="modal-header">';
echo '        <h5 class="modal-title" id="modalDetallesRecepcionMPLabel">Detalles de la Recepción</h5>';
echo '        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>';
echo '      </div>';
echo '      <div class="modal-body">';
echo '        <table class="table table-sm table-bordered">';
echo '          <thead>';
echo '            <tr>';
echo '              <th>Insumo</th>';
echo '              <th>Peso Estimado</th>';
echo '              <th>Peso Real</th>';
echo '              <th>QR</th>';
echo '            </tr>';
echo '          </thead>';
echo '          <tbody id="modal_detalles_recepcion_mp_tbody"></tbody>';
echo '        </table>';
echo '      </div>';
echo '    </div>';
echo '  </div>';
echo '</div>';



    $id='detalles_recepciones_compras';
    $ButtonAddLabel = "Nuevo Detalle";
    $titulos = ['ID','Artículos', 'Recepción','Cantidad','Costo Unitario Total','Costo Unitario Neto','Monto Total','Monto Neto','Fecha de creación'];
  

    ob_start();
    CreateTable($id, $ButtonAddLabel, $titulos, [],false,[],'',$atributos = ['data-select-column'=>2]);
    $detailsTableOutput = ob_get_clean();

  CreateModal( [
    'id'=> $id.'-View', 
    'Title'=>'Detalle de Lista de Compras',
    'Title2'=>'Editar Lista',
    'Title3'=>'Ver Lista',
    'ModalType'=>'modal-fullscreen modal-dialog-scrollable', 
    'method'=>'POST',
    'action'=>'bd/crudSummit.php',
    'bloque'=>'compras'
  ],
  [
    $detailsTableOutput
  ],
  ['<button type="button" class="btn btn-secondary secondary" data-bs-dismiss="modal">Cancelar</button>']);

  CreateModalForm(
  [
    'id'=> $id, 
    'Title'=>$ButtonAddLabel,
    'Title2'=>'Editar Detalle',
    'Title3'=>'Ver Detalle',
    'ModalType'=>'modal-dialog-scrollable', 
    'method'=>'POST',
    'action'=>'bd/crudSummit.php',
    'bloque'=>'compras',
    'data-select-column'=>'[2]',
    'data-input-fill'=>'[kid_lista_compras, orden]'
  ],
  [
    CreateInput(['id'=>'kid_recepcion_compras','etiqueta'=>'Recepción','required' => '','class'=>'OnEditReadOnly']),
      CreateInput(['id'=>'kid_articulo','etiqueta'=>'Articulo','required' => '','class'=>'OnEditReadOnly']),
      CreateInput(['type'=>'number','id'=>'cantidad','etiqueta'=>'Cantidad','required' => '','class'=>'MUL-1 MUL-2']),
      CreateInput(['type'=>'number','id'=>'costo_unitario_total','etiqueta'=>'Costo Unitario Total','required' => '','class'=>'MUL-1']),
      CreateInput(['type'=>'number','id'=>'costo_unitario_neto','etiqueta'=>'Costo Unitario Neto','required' => '','class'=>'MUL-2']),
      CreateInput(['type'=>'number','id'=>'monto_total','etiqueta'=>'Monto Total','required' => '','readonly' => '','class'=>'RESULT-1 RESULT-3']),
      CreateInput(['type'=>'number','id'=>'monto_neto','etiqueta'=>'Monto Neto','required' => '','readonly' => '','class'=>'RESULT-2 RESULT-4']),
      CreateInput(['type'=>'number','value'=>'0','id'=>'porcentaje_descuento','etiqueta'=>'Porcentaje de Descuento','required' => '','class'=>'DESC-3 DESC-4']),
  ]);

  $wrapper_dashboard = ob_get_clean();
  include 'wrapper.php'; // Incluye el wrapper
?>