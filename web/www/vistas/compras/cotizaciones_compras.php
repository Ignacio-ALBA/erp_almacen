<?php
    ob_start(); // Inicia la captura del buffer de salida

    $PageSection = "Cotizaciones de Compras";
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

  $id = 'cotizaciones_compras';
  $ButtonAddLabel = "Nueva Cotización";
  $titulos = ['ID', 'Cotización','Grupo','Proveedor','Estado','La Creo','La Autorizo','Fecha de creación'];
  CreateTable($id, $ButtonAddLabel, $titulos, $data,true,'ButtonsInRow');
  CreateModalForm(
    [
      'id'=> $id, 
      'Title'=>$ButtonAddLabel,
      'Title2'=>'Editar Cotización',
      'Title3'=>'Ver Cotización',
      'ModalType'=>'modal-dialog-scrollable', 
      'method'=>'POST',
      'action'=>'bd/crudSummit.php',
      'bloque'=>'compras',
      'data-select-column'=>'[3]',
      'data-input-fill'=>'[kid_proveedor]'
    ],
    [
      CreateInput(['type'=>'text','maxlength'=>'97','id'=>'cotizacion_compras','etiqueta'=>'Cotización','required' => '']),
      CreateSelect(['id'=>'kid_proyecto','etiqueta'=>'Proyecto','div_style'=>'display:none;','required'=>'','class'=>'OnEditReadOnly'],$proyectos),
      CreateSelect(['id'=>'kid_proveedor','etiqueta'=>'Proveedor','required' => ''],$proveedores),
      CreateSelect(['id'=>'kid_estatus','etiqueta'=>'Estado','div_style'=>'display:none;','class'=>'OnlyInEdit'],$estatus),
      CreateSelect(['id'=>'kid_tiempo_entrega','etiqueta'=>'Tiempo de Entrega','required' => ''],$tiempos_entrega),
      CreateSelect(['id'=>'kid_tipo_pago','etiqueta'=>'Tipo de Pago','required' => ''],$tipos_pago),
      CreateInput(['id'=>'fecha_cotizacion','type'=>'date','etiqueta'=>'Fecha de Cotización','required' => '']),
      
      CreateTextArea(['id'=>'especificaciones_adicionales','maxlength'=>'300','etiqueta'=>'Especificaciones Adicionales','required' => '']),
    ]);
    $id = 'ordenes_compras';
    $ButtonAddLabel = "Nueva Orden de Compra";
    CreateModalForm(
        [
          'id'=> $id, 
          'Title'=>$ButtonAddLabel,
          'Title2'=>'Editar Orden de Compra',
          'Title3'=>'Ver Orden de Compra',
          'ModalType'=>'modal-dialog-scrollable', 
          'method'=>'POST',
          'action'=>'bd/crudSummit.php',
          'bloque'=>'compras'
        ],
        [
          CreateInput(['type'=>'text','maxlength'=>'100','id'=>'orden_compras-SetData','etiqueta'=>'Orden de Compras','required' => '']),
          CreateInput(['type'=>'text','maxlength'=>'80','id'=>'codigo_externo-SetData','etiqueta'=>'Código Externo','required' => '']),
          CreateInput(['id'=>'kid_proyecto-SetData','etiqueta'=>'Proyecto','required' => '','readonly' => '']),
          CreateInput(['id'=>'kid_proveedor-SetData','etiqueta'=>'Proveedor','required' => '','readonly' => '']),
        ]);

        // Modal de pantalla completa para detalles de cotizaciones
        $id = 'detalles_cotizaciones_compras';
        $ButtonAddLabel = "Nuevo Detalle de Cotización";
        $titulos = ['ID', 'Cotización','Articulos','Cantidad','Costo Unitario Total','Costo Unitario Neto','Monto Total','Monto Neto','Fecha de creación'];
      
        ob_start();
        CreateTable($id, $ButtonAddLabel, $titulos, [], true, [], '', $atributos = ['data-select-column'=>1]);
        $detailsTableOutput = ob_get_clean();
    
        CreateModal([
          'id'=> $id.'-View', 
          'Title'=>'Detalle de Cotización',
          'Title2'=>'Editar Cotización',
          'Title3'=>'Ver Cotización',
          'ModalType'=>'modal-fullscreen modal-dialog-scrollable', 
          'method'=>'POST',
          'action'=>'bd/crudSummit.php',
          'bloque'=>'compras'
        ],
        [
          $detailsTableOutput
        ],
        ['<button type="button" class="btn btn-secondary secondary" data-bs-dismiss="modal">Cancelar</button>']);

        // Modal para agregar/editar detalles de cotización
        CreateModalForm([
            'id'=> $id, 
            'Title'=>$ButtonAddLabel,
            'Title2'=>'Editar Detalle de Cotización',
            'Title3'=>'Ver Detalle de Cotización',
            'ModalType'=>'modal-dialog-scrollable', 
            'method'=>'POST',
            'action'=>'bd/crudSummit.php',
            'bloque'=>'compras',
            'data-select-column'=>'[1]',
            'data-input-fill'=>'[kid_cotizacion_compra]',
            'onSubmit'=>'return validateDetallesCotizacion(this);'
          ],
          [
            CreateInput(['id'=>'kid_cotizacion_compra','etiqueta'=>'Cotización','required' => '','readonly'=>'','class'=>'OnEditReadOnly']),
            CreateSelect(['id'=>'kid_articulo','etiqueta'=>'Insumo','required' => '','class'=>'OnEditReadOnly'],$articulos),
            CreateInput(['type'=>'number','id'=>'cantidad','etiqueta'=>'Cantidad De Super Sacos','required' => '','class'=>'MUL-1 MUL-2']),
            CreateInput(['type'=>'number','id'=>'costo_unitario_total','etiqueta'=>'Costo Unitario Total','required' => '','class'=>'MUL-1']),
            CreateInput(['type'=>'number','id'=>'costo_unitario_neto','etiqueta'=>'Costo Unitario Neto','required' => '','readonly'=>'readonly','class'=>'MUL-2']),
            CreateInput(['type'=>'number','id'=>'monto_total','etiqueta'=>'Monto Total','required' => '','readonly'=>'readonly','class'=>'RESULT-1 RESULT-3']),
            CreateInput(['type'=>'number','id'=>'monto_neto','etiqueta'=>'Monto Neto','required' => '','readonly'=>'readonly','class'=>'RESULT-2 RESULT-4']),
            CreateInput(['type'=>'number','value'=>'0','id'=>'porcentaje_descuento','etiqueta'=>'Porcentaje de Descuento','required' => '','class'=>'DESC-3 DESC-4'])
          ]);

        CreateModalForm(
          [
            'id'=> 'proveedores_cuadro_comparativo', 
            'Title'=>'Seleccione Revisores',
            'Title2'=>'',
            'Title3'=>'',
            'PrimaryButttonName'=>'Enviar',
            'ModalType'=>'modal-dialog-scrollable', 
            'method'=>'POST',
            'action'=>'bd/crudSummit.php',
            'bloque'=>'compras'
          ],
          [
            CreateSelect(['id'=>'kid_revisor_ingenieria','etiqueta'=>'Revisor de Ingeniería','required' => ''],$colaboradores),
            CreateSelect(['id'=>'kid_revisor_servicios','etiqueta'=>'Revisor de Ingeniería de servicios','required' => ''],$colaboradores),
            CreateSelect(['id'=>'kid_revisor_proveedores','etiqueta'=>'Revisor de Proveedores','required' => ''],$colaboradores)
            
          ]);
?>
<?php
  $wrapper_dashboard = ob_get_clean(); // Obtiene el contenido del buffer y lo asigna a $content

  include 'wrapper.php'; // Incluye el wrapper
?>