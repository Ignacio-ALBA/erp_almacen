<?php
    ob_start();
    $PageSection = isset($_GET['section']) && $_GET['section'] == 'ordenes' ? 
        "Órdenes de Compra" : "Cotizaciones de Compras";


    // Preparar arrays para los select con el campo pordefecto
    $proveedores = array_map(function($item) {
        $item['pordefecto'] = 0;
        return $item;
    }, $proveedores);

    $estatus = array_map(function($item) {
        $item['pordefecto'] = 0;
        return $item;
    }, $estatus);

    $tiempos_entrega = array_map(function($item) {
        $item['pordefecto'] = isset($item['pordefecto']) ? $item['pordefecto'] : 0;
        return $item;
    }, $tiempos_entrega);

    $tipos_pago = array_map(function($item) {
        $item['pordefecto'] = isset($item['pordefecto']) ? $item['pordefecto'] : 0;
        return $item;
    }, $tipos_pago);

    $colaboradores = array_map(function($item) {
        $item['pordefecto'] = 0;
        return $item;
    }, $colaboradores);


?>

<div class="pagetitle">
    <h1><?php echo $PageSection; ?></h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item">Compras</li>
            <li class="breadcrumb-item active"><?php echo $PageSection; ?></li>
        </ol>
    </nav>
</div>

<?php 
if (!isset($_GET['section']) || $_GET['section'] != 'ordenes') {
    // Sección de Cotizaciones
    $id = 'cotizaciones_compras';
    $ButtonAddLabel = "Nueva Cotización";
    $titulos = ['ID', 'Cotización','Grupo','Proveedor','Estado','La Creo','La Autorizo','Fecha de creación'];
    CreateTable($id, $ButtonAddLabel, $titulos, $data, true, 'ButtonsInRow');
    CreateModalForm(
        [
            'id'=> $id, 
            'Title'=>$ButtonAddLabel,
            'Title2'=>'Editar cotización',
            'Title3'=>'Ver cotización',
            'ModalType'=>'modal-dialog-scrollable', 
            'method'=>'POST',
            'action'=>'bd/crudSummit.php',
            'bloque'=>'compras'
        ],
        [
            CreateInput(['type'=>'text','id'=>'cotizacion','etiqueta'=>'Cotización','required' => '']),
            CreateSelect(['id'=>'kid_proveedor','etiqueta'=>'Proveedor','required' => ''],$proveedores),
            CreateSelect(['id'=>'kid_estatus','etiqueta'=>'Estado','div_style'=>'display:none;','class'=>'OnlyInEdit'],$estatus),
            CreateSelect(['id'=>'kid_tiempo_entrega','etiqueta'=>'Tiempo de Entrega','required' => ''],$tiempos_entrega),
            CreateSelect(['id'=>'kid_tipo_pago','etiqueta'=>'Tipo de Pago','required' => ''],$tipos_pago),
            CreateInput(['id'=>'fecha_cotizacion','type'=>'date','etiqueta'=>'Fecha de Cotización','required' => '', 'value'=>date('Y-m-d')]),
            CreateTextArea(['id'=>'especificaciones_adicionales','maxlength'=>'300','etiqueta'=>'Especificaciones Adicionales','required' => '']),
            // Solo en alta:
            (isset($_GET['edit']) && $_GET['edit'] == '1' ? '' : CreateInput(['type'=>'number','id'=>'num_articulos','etiqueta'=>'Número de Insumos','required' => '','min'=>'1']) . '<div id="articulos_container"></div>')
        ]
    );

    // Detalles de Cotizaciones
    $id = 'detalles_cotizaciones_compras';
    $ButtonAddLabel = "Nuevo Detalle de Cotización";
    $titulos = ['ID', 'Cotización','Articulos','Cantidad','Costo Unitario Total','Costo Unitario Neto','Monto Total','Monto Neto','Fecha de creación'];

    ob_start();
    CreateTable($id, $ButtonAddLabel, $titulos, [], true, [], '', $atributos = ['data-select-column'=>1]);
    $detailsTableOutput = ob_get_clean();

    CreateModal([
        'id'=> $id.'-View', 
        'Title'=>'Detalle de Cotización',
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
        'data-input-fill'=>'[kid_cotizacion_compra, orden]'
    ],
    [
        CreateInput(['id'=>'kid_cotizacion_compra','etiqueta'=>'Cotización','required' => '','readonly'=>'','class'=>'OnEditReadOnly']),
        CreateSelect(['id'=>'kid_articulo','etiqueta'=>'Articulo','required' => '','readonly'=>'','class'=>'OnEditReadOnly'],[]),
        CreateInput(['type'=>'number','id'=>'cantidad','etiqueta'=>'Cantidad','required' => '','class'=>'MUL-1 MUL-2']),
        CreateInput(['type'=>'number','id'=>'costo_unitario_total','etiqueta'=>'Costo Unitario Total','required' => '','class'=>'MUL-1']),
        CreateInput(['type'=>'number','id'=>'costo_unitario_neto','etiqueta'=>'Costo Unitario Neto','required' => '','class'=>'MUL-2']),
        CreateInput(['type'=>'number','id'=>'monto_total','etiqueta'=>'Monto Total','required' => '','readonly' => '','class'=>'RESULT-1 RESULT-3']),
        CreateInput(['type'=>'number','id'=>'monto_neto','etiqueta'=>'Monto Neto','required' => '','readonly' => '','class'=>'RESULT-2 RESULT-4']),
        CreateInput(['type'=>'number','value'=>'0','id'=>'porcentaje_descuento','etiqueta'=>'Porcentaje de Descuento','required' => '','class'=>'DESC-3 DESC-4'])
    ]);

    // Modal para Cuadro Comparativo
    CreateModalForm([
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
        CreateSelect(['id'=>'kid_revisor_operaciones','etiqueta'=>'Revisor de Operaciones','required' => ''],$colaboradores),
        CreateSelect(['id'=>'kid_revisor_finanzas','etiqueta'=>'Revisor de Finanzas','required' => ''],$colaboradores)
    ]);

} else {
    // Sección de Órdenes de Compra
    $id = 'ordenes_compras';
    $ButtonAddLabel = "Nueva Orden de Compra";
    $titulos = ['ID', 'Orden','Código Externo','Proyecto','Proveedor','Monto Total','Monto Neto','Estado','La Creo','La Autorizo','Fecha de creación'];
    CreateTable($id, $ButtonAddLabel, $titulos, $data, true, 'ButtonsInRow');
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
            CreateInput(['type'=>'text','maxlength'=>'100','id'=>'orden_compras','etiqueta'=>'Orden de Compras','required' => '']),
            CreateInput(['type'=>'text','maxlength'=>'80','id'=>'codigo_externo','etiqueta'=>'Código Externo','required' => '']),
            CreateInput(['type'=>'text','id'=>'kid_proyecto-NewAdd2','etiqueta'=>'Proyecto','required' => '','readonly' => '']),
            CreateInput(['type'=>'text','id'=>'kid_proveedor-NewAdd2','etiqueta'=>'Proveedor','required' => '','readonly' => '']),
            CreateInput(['type'=>'number','id'=>'monto_total-NewAdd2','etiqueta'=>'Monto Total','required' => '','readonly' => '']),
            CreateInput(['type'=>'number','id'=>'monto_neto-NewAdd2','etiqueta'=>'Monto Neto','required' => '','readonly' => ''])
        ]
    );
}

$wrapper_dashboard = ob_get_clean();
include 'wrapper.php';
?>