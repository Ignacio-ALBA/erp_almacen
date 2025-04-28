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
    
    .weight-container {
        position: relative;
        margin: 10px 0;
        max-width: 250px; /* Reducir el ancho máximo */
    display: inline-block; /* Hacer que el contenedor sea inline */

    }
    
    .weight-unit {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background-color: #001f3f;
        color: #7fdbff;
        padding: 0 5px;
        font-weight: bold;
        border-radius: 3px;
        margin-left: 5px;
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
    max-width: 250px; /* Reducir aún más el ancho */
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
      <div class="row align-items-end">
          <div class="col-md-4">'; // Reduced from col-md-4
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
                  'value' => '0.00'
              ]);
          echo '</div>
          <div class="col-md-4">'; // Reduced from col-md-4
              echo CreateInput([ 
                  'type' => 'text',
                  'id' => 'num_pedido',
                  'etiqueta' => 'Número de Pedido',
                  'readonly' => 'readonly',
                  'value' => '001',
                  'class' => 'form-control form-control-sm'
              ]);
          echo '</div>
          <div class="col-md-4">'; // Reduced from col-md-4
          echo CreateSelect([
            'type' => 'text',
            'id' => 'producto_peso',
            'etiqueta' => 'Producto pesado',
            'required' => 'true',
            'class' => 'form-control form-control-sm'
        ], [
            ['valor' => 'PET METALICO', 'texto' => 'PET METALICO', 'pordefecto' => 0],
            ['valor' => 'PET PLASTIFICADO', 'texto' => 'PET PLASTIFICADO', 'pordefecto' => 0]
        ]);
          echo '</div>
          <div class="col-md-3">'; // New column for kg input
              echo CreateInput([
                  'type' => 'number',
                  'id' => 'kg_producto',
                  'etiqueta' => 'Peso estimado (Kg)',
                  'required' => 'true',
                  'step' => '0.01',
                  'min' => '0',
                  'class' => 'form-control form-control-sm'
              ]);
          echo '</div>
      </div>
      <div class="row mt-3">
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
