<?php
    ob_start();
   // $productos = GetProductosListForSelect(); // Función que debes crear
//$proveedores = GetProveedoresListForSelect(); // Función que debes crear
//$localizaciones = GetLocalizacionesListForSelect(); // Función que debes crear
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
        right: -85px;
        top: 50%;
        transform: translateY(-50%);
        background-color: #001f3f;
        color: #7fdbff;
        padding: 0 5px;
        font-weight: bold;
        border-radius: 3px;
        margin-left: 5px;
        max-width: 450px; /* Ajustar el ancho máximo */
        font-size: 3.5rem; /* Aumentar el tamaño de fuente */
       
        
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

<?php 
    $PageSection = "Recepciones de compras";
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
              echo CreateInput([ 
                  'type' => 'text',
                  'id' => 'num_pedido',
                  'etiqueta' => 'Número de Pedido',
                  'readonly' => 'readonly',
                  'value' => '001',
                  'class' => 'form-control form-control-sm'
              ]);
        
          echo CreateSelect([
            'type' => 'text',
            'id' => 'insumo_peso',
            'etiqueta' => 'Insumo pesado',
            'required' => 'true',
            'class' => 'form-control form-control-sm'
        ], [
            ['valor' => 'POLIPROPILENO NEGRO', 'texto' => 'POLIPROPILENO NEGRO', 'pordefecto' => 0],
            ['valor' => 'POLIPROPILENO BLANCO', 'texto' => 'POLIPROPILENO BLANCO', 'pordefecto' => 0],
            ['valor' => 'POLIPROPILENO MULTICOLOR', 'texto' => 'POLIPROPILENO MULTICOLOR', 'pordefecto' => 0]
          ]);
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
              echo CreateButtonP([
                  'id' => 'btn_guardar_materia',
                  'type' => 'button',
                  'class' => 'btn btn-primary',
                  'text' => '<i class="bi bi-save2-fill"></i> Registrar peso',
                  'html' => true
              ]);
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
// ... existing code ...

// Replace the existing button row with this updated version
echo '<div class="row mt-3">
    <div class="col-12 text-center">
        <button type="button" id="btn_guardar_incompleto" class="btn btn-warning me-2">
            <i class="bi bi-exclamation-triangle"></i> Guardar pesaje incompleto
        </button>
        <button type="button" id="btn_finalizar_recepcion" class="btn btn-primary">
            <i class="bi bi-check-circle"></i> Finalizar recepción
        </button>
    </div>
</div>';


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

  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>
