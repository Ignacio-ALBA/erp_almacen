<?php

// Sanitizar la entrada del pathResult
$resultado = processRequest();

if($resultado){
    $pathResult = $resultado['pathResult'];
    $queryParams = $resultado['queryParams'];
    $hash_id = isset($queryParams['id']) ? $queryParams['id'] : null;
    $id = $hash_id ? decodificar($hash_id) : null;


    // Función para cargar la vista correspondiente

    // Controlador de vistas
    $vista = '';
    $data = [];

    $data_script['botones_acciones'] = [
        '<button class="ModalDataView btn btn-primary primary" modalCRUD="${modalCRUD}"><i class="bi bi-eye"></i> Ver</button>',
        '<button class="ModalDataEdit btn btn-warning warning" modalCRUD="${modalCRUD}"><i class="bi bi-pencil"></i> Editar</button>',
        '<button class="ModalDataDelete btn btn-danger danger" modalCRUD="${modalCRUD}"><i class="bi bi-trash"></i> Eliminar</button>'
    ];


    $objeto = new Conexion();
    $conexion = $objeto->Conectar();
    $data['data_show']['nombre_modulo'] = 'Compras';
    $data['data_show']['breadcrumb'] = null;
    $data['data_show']['AllowADDButton'] = true;


    switch ($pathResult) {
        case 'proveedores':
            $vista = 'proveedores';
            $consultaselect = "SELECT id_proveedor,
                orden,
                codigo,
                proveedor,
                CONCAT(calificacion,' <i class=\"bi bi-star-fill\"></i>') AS calificacion,
                razon_social,
                rfc,
                email1,
                CASE 
                    WHEN pordefecto = 1 THEN 'SÍ' 
                    ELSE 'NO' 
                END AS pordefecto,  -- Se añadió un alias aquí
                fecha_creacion
            FROM proveedores
            WHERE kid_estatus != 3 
            ORDER BY calificacion DESC";
            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();
            $modalCRUD = 'comentarios_proveedores';
            $nuevo_boton = '
                <button class="ModalNewAdd1 btn btn-secondary secondary" modalCRUD="'.$modalCRUD.'"><i class="bi bi-chat-left-text"></i> Comentario</button>
            ';
            array_splice($data_script['botones_acciones'], 1, 0, $nuevo_boton);
            $data['data_show']['botones_acciones'] = $data_script['botones_acciones'];
            
            $data_script['NewAdd1'] =['data_list_column'=>[
                'kid_proveedor'=>5,
                
            ]];

            $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
            $data['data_show']['regimenes'] = GetRegimenesListForSelect();
            $data['data_show']['paises'] = GetPaisesListForSelect();
            $data['data_show']['estados'] = GetEstadosListForSelect();
            $data['data_show']['proveedores'] = GetProvedoresListForSelect();
            $data['data_show']['tipo_comentario'] = GetTiposComentariosListForSelect();

            break;

        case 'comentarios_proveedores':
            $vista = 'comentarios_proveedores';
            $consultaselect = "SELECT cp.id_comentario_proveedor , 
                p.nombre_comercial AS kid_proveedor, 
                cp.comentario_proveedor,
                tc.tipo_comentario AS kid_tipo_comentario,
                cp.fecha_creacion
            FROM 
                comentarios_proveedores cp
            LEFT JOIN 
                proveedores p ON cp.kid_proveedor = p.id_proveedor
            LEFT JOIN 
                tipos_comentarios tc ON cp.kid_tipo_comentario = tc.id_tipo_comentario
            WHERE cp.kid_estatus !=3";
            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();

            $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
            $data['data_show']['tipo_comentario'] = GetTiposComentariosListForSelect();
            $data['data_show']['proveedores'] = GetProvedoresListForSelect();
            break;

        case 'listas_compras':
            $vista = 'listas_compras';
            $estatus = GetEstatusLabels();
            $caseEstatus = "CASE \n";
            foreach ($estatus as $key => $value) {
                $caseEstatus .= "    WHEN lc.kid_estatus = $key THEN '$value'\n";
            }
            $caseEstatus .= "    ELSE 'Desconocido' \nEND AS kid_estatus";
            // Construcción de la consulta
            $consultaselect = "SELECT 
            lc.id_lista_compra,
            lc.orden,
            CASE 
                WHEN lc.lista_compra = 'Default' AND (lc.kid_proyecto IS NOT NULL OR lc.kid_cuenta_bancaria IS NOT NULL) THEN NULL
                ELSE lc.lista_compra 
            END as lista_compra,
            $caseEstatus,
            (SELECT email FROM colaboradores u WHERE u.id_colaborador = lc.kid_creacion LIMIT 1) AS kid_creacion,
            COALESCE((SELECT email FROM colaboradores u2 WHERE u2.id_colaborador = lc.kid_autorizo LIMIT 1), 'Sin Autorizar') AS kid_autorizo,
            lc.fecha_creacion
        FROM listas_compras lc
        WHERE lc.kid_estatus != 3 
        AND (lc.lista_compra != 'Default' 
            OR (lc.lista_compra = 'Default' AND (lc.kid_proyecto IS NOT NULL OR lc.kid_cuenta_bancaria IS NOT NULL)))";
            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();

            $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
            $data['data_show']['proyectos'] = GetProyectosListForSelect();
            $data['data_show']['cuentas_bancarias'] = GetCuentasBancariasListForSelect();
            $data['data_show']['articulos'] = GetArticulosListForSelect();
            $data['data_show']['estatus'] = GetEstatusListForSelect();

            $modalCRUD = 'detalles_listas_compras';
            $nuevo_boton = '
                <button class="ModalNewAdd3 btn btn-info info" modalCRUD="'.$modalCRUD.'"><i class="bi bi-file-spreadsheet"></i> Ver Detalles</button>
            ';
            //array_splice($data_script['botones_acciones'], 0, 0, $nuevo_boton);
            array_push($data_script['botones_acciones'], $nuevo_boton);
            $data['data_show']['botones_acciones'] = $data_script['botones_acciones'];
            $optionkey = 'NewAdd3';
            $data_script[$optionkey] =['data_list_column'=>[]];
            $data['list_js_scripts']['../vistas/compras/lista_compras_script'] =['data'=> $data_script];
            $data['list_styles']['../vistas/compras/lista_compras_styles'] = [];
            break;
        case 'detalles_listas_compras':
            $vista = 'detalles_listas_compras';
            $consultaselect = "SELECT dlc.id_detalle_lista_compras,
                lc.lista_compra AS kid_lista_compras,
                a.articulo AS kid_articulo,
                dlc.cantidad,
                dlc.costo_unitario_total,
                dlc.costo_unitario_neto,
                CONCAT(dlc.porcentaje_descuento,' %'),
                dlc.monto_total,
                dlc.monto_neto,
                dlc.fecha_creacion
            FROM detalles_listas_compras dlc
            LEFT JOIN listas_compras lc ON dlc.kid_lista_compras = lc.id_lista_compra
            LEFT JOIN articulos a ON dlc.kid_articulo = a.id_articulo
            WHERE dlc.kid_estatus !=3";
            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();

            $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
            $data['data_show']['listas_compras'] = GetListaComprasForSelect();
            $data['data_show']['articulos'] = GetArticulosListForSelect();
            break;
        case 'cotizaciones_compras':
            $vista = 'cotizaciones_compras';
            $estatus = GetEstatusLabels();
            $estatus_name = GetEstatusList();
   
            $consultaselect = "SELECT 
                cc.id_cotizacion_compra,
                cc.cotizacion_compras,
                cc.grupo,
                (SELECT razon_social FROM proveedores prov WHERE prov.id_proveedor = cc.kid_proveedor LIMIT 1) AS kid_proveedor,
                cc.kid_estatus,
                (SELECT email FROM colaboradores u WHERE u.id_colaborador = cc.kid_creacion LIMIT 1) AS kid_creacion,
                COALESCE((SELECT email FROM colaboradores u2 WHERE u2.id_colaborador = cc.kid_autorizo LIMIT 1), 'Sin Autorizar') AS kid_autorizo,
                cc.fecha_creacion
            FROM cotizaciones_compras cc
            WHERE cc.kid_estatus != 3";
            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();
            $cotizaciones_compras = $resultado->fetchAll(PDO::FETCH_ASSOC);
            $estatus = GetEstatusLabels();
            $estatus_name = GetEstatusList();

            $cotizaciones_compras = array_map(function ($row) {
                global $data_script, $estatus, $estatus_name;
                $botones_acciones = $data_script['botones_acciones'];

                $bloque = 'compras';
                $modalCRUD =  'update_estatus_cotizaciones_compras';
                if(!in_array($row['kid_estatus'], [5,6,7])){
                    $nuevo_boton = '<button class="UpdateEstatus btn btn-success" bloque="'. $bloque.'" name="'.$estatus_name[6].'" modalCRUD="'.$modalCRUD.'"><i class="bi bi-check2"></i> Revisar I</button>';
                    array_unshift($botones_acciones,$nuevo_boton);
                }else if($row['kid_estatus'] == 6){
                    $nuevo_boton = '<button class="UpdateEstatus btn btn-success" bloque="'. $bloque.'" name="'.$estatus_name[7].'" modalCRUD="'.$modalCRUD.'"><i class="bi bi-check2-all"></i> Revisar II</button>';
                    array_unshift($botones_acciones,$nuevo_boton);
                }if($row['kid_estatus'] == 7){
                    $nuevo_boton = '<button class="UpdateEstatus btn btn-success" bloque="'. $bloque.'" name="'.$estatus_name[5].'" modalCRUD="'.$modalCRUD.'"><i class="bi bi-check2-circle"></i> Autorizar</button>';
                    array_unshift($botones_acciones,$nuevo_boton);
                }
                
                $hashed_id = codificar($row['id_cotizacion_compra']);
                $nuevo_boton = '<a href="/rutas/compras.php/detalles_cotizaciones_compras?id=' . $hashed_id . '" class="btn btn-secondary "><i class="bi bi-journal-text"></i> Contenido</a>';
                array_push($botones_acciones, $nuevo_boton);
                $nuevo_boton = '<button class="GenerarReporte btn btn-success success" reporte="proveedores_cuadro_comparativo" form="proveedores_cuadro_comparativo" data-id="'.$hashed_id.'"><i class="bi bi-play-circle"></i> Cuadro Comparativo</button>';
                array_push($botones_acciones, $nuevo_boton);
                $row['botones'] = GenerateCustomsButtons($botones_acciones, 'cotizaciones_compras');
                $row['kid_estatus'] = $estatus[$row['kid_estatus']];
                return $row;
            }, $cotizaciones_compras);

            $data['data_show']['data'] = $cotizaciones_compras;
            $data['data_show']['proyectos'] = GetProyectosListForSelect();
            $data['data_show']['proveedores'] = GetProvedoresListForSelect();
            $data['data_show']['estatus'] = GetEstatusListForSelect();
            $data['data_show']['tiempos_entrega'] = GetTiemposEntregaListForSelect();
            $data['data_show']['tipos_pago'] = GetTiposPagoListForSelect();
            $data['data_show']['colaboradores'] = GetUsuariosListForSelect();

            $data['data_show']['botones_acciones'] = $data_script['botones_acciones'];
            $optionkey = 'NewAdd3';
            $data_script[$optionkey] =['data_list_column'=>[]];
            break;
        case 'detalles_cotizaciones_compras':
            $vista = 'detalles_cotizaciones_compras';
            if ($id != null) {
                $consultaselect = "SELECT dcc.id_detalle_cotizacion_compras,
                    cc.cotizacion_compras AS kid_cotizacion_compra,
                    a.articulo AS kid_articulo,
                    dcc.cantidad,
                    dcc.costo_unitario_total,
                    dcc.costo_unitario_neto,
                    dcc.monto_total,
                    dcc.monto_neto,
                    dcc.fecha_creacion
                FROM detalles_cotizaciones_compras dcc
                LEFT JOIN cotizaciones_compras cc ON dcc.kid_cotizacion_compra = cc.id_cotizacion_compra 
                LEFT JOIN articulos a ON dcc.kid_articulo = a.id_articulo
                WHERE dcc.kid_estatus  !=3 AND  dcc.kid_cotizacion_compra = $id";

                $resultado = $conexion->prepare("SELECT cotizacion_compras FROM cotizaciones_compras WHERE id_cotizacion_compra = $id");
                $resultado->execute();
                $breadcrumb_data = $resultado->fetch(PDO::FETCH_ASSOC);

                $breadcrumb = '
                <li class="breadcrumb-item"><a href="/rutas/compras.php/cotizaciones_compras">Cotizaciones</a></li>
                <li class="breadcrumb-item">'.$breadcrumb_data['cotizacion_compras'].'</li>
                <li class="breadcrumb-item active">Contenido</li>
                ';
                $data['data_show']['breadcrumb'] = $breadcrumb;
                $data['data_show']['valor_id'] = $breadcrumb_data['cotizacion_compras'];
                $data['data_show']['articulos'] = GetArticulosListForSelect();

            }else{
                $consultaselect = "SELECT dcc.id_detalle_cotizacion_compras,
                cc.cotizacion_compras AS kid_cotizacion_compra,
                a.articulo AS kid_articulo,
                dcc.cantidad,
                dcc.costo_unitario_total,
                dcc.costo_unitario_neto,
                dcc.monto_total,
                dcc.monto_neto,
                dcc.fecha_creacion
            FROM detalles_cotizaciones_compras dcc
            LEFT JOIN cotizaciones_compras cc ON dcc.kid_cotizacion_compra = cc.id_cotizacion_compra 
            LEFT JOIN articulos a ON dcc.kid_articulo = a.id_articulo
            WHERE dcc.kid_estatus  !=3";
            $data['data_show']['AllowADDButton'] = false;
            $data['data_show']['articulos'] = GetArticulosListForSelect();
            }
            
            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();

            $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
            
            break;
            case 'recepcion_orden':
                $vista = 'recepcion_orden';
                $estatus = GetEstatusLabels();
                $estatus_name = GetEstatusList();
            
                // Consulta inicial
                $consultaselect = "SELECT oc.id_orden_compras,
                    oc.orden_compras,
                    oc.codigo_externo,
                    oc.grupo_cotizacion,
                    (SELECT proyecto FROM proyectos p WHERE p.id_proyecto = oc.kid_proyecto LIMIT 1) AS kid_proyecto,
                    (SELECT proveedor FROM proveedores prov WHERE prov.id_proveedor = oc.kid_proveedor LIMIT 1) AS kid_proveedor,
                    oc.monto_total,
                    oc.monto_neto,
                    oc.kid_estatus,
                    oc.fecha_creacion
                FROM ordenes_compras oc
                WHERE oc.kid_estatus != 3";
            
                $resultado = $conexion->prepare($consultaselect);
                $resultado->execute();
                $ordenes_compras = $resultado->fetchAll(PDO::FETCH_ASSOC);
            
                // Mapear los datos y agregar botones
                $ordenes_compras = array_map(function ($row) {
                    global $data_script, $estatus, $estatus_name;
                    $botones_acciones = $data_script['botones_acciones'];
            
                    $bloque = 'compras';
                    $modalCRUD = 'update_estatus_ordenes_compras';
            
                    // Agregar botones según el estado
                    if(!in_array($row['kid_estatus'], [5,6,9])) {
                        $nuevo_boton = '<button class="UpdateEstatus btn btn-success" bloque="'.$bloque.'" name="'.$estatus_name[6].'" modalCRUD="'.$modalCRUD.'"><i class="bi bi-check2"></i> Revisar</button>';
                        array_unshift($botones_acciones, $nuevo_boton);
                    } else if($row['kid_estatus'] == 6) {
                        $nuevo_boton = '<button class="UpdateEstatus btn btn-success" bloque="'.$bloque.'" name="'.$estatus_name[5].'" modalCRUD="'.$modalCRUD.'"><i class="bi bi-check2-circle"></i> Autorizar</button>';
                        array_unshift($botones_acciones, $nuevo_boton);
                    }
            
                    // Agregar botones de contenido y reporte
                    $hashed_id = codificar($row['id_orden_compras']);
                    $nuevo_boton = '<a href="/rutas/compras.php/detalles_ordenes_compras?id='.$hashed_id.'" class="btn btn-secondary"><i class="bi bi-journal-text"></i> Contenido</a>';
                    array_push($botones_acciones, $nuevo_boton);
                    
                    $nuevo_boton = '<button class="GenerarReporte btn btn-success success" reporte="ordenes_compras_reporte" data-id="'.$hashed_id.'"><i class="bi bi-play-circle"></i> Generar PDF</button>';
                    array_push($botones_acciones, $nuevo_boton);
            
                    $row['botones'] = GenerateCustomsButtons($botones_acciones, 'ordenes_compras');
                    $row['kid_estatus'] = $estatus[$row['kid_estatus']];
                    return $row;
                }, $ordenes_compras);
            
                // Configurar data_show
                $data['data_show'] = [
                    'data' => $ordenes_compras,
                    'AllowADDButton' => true,
                    'proyectos' => GetProyectosListForSelect(),
                    'proveedores' => GetProvedoresListForSelect(),
                    'estatus' => GetEstatusListForSelect(),
                    'tiempos_entrega' => GetTiemposEntregaListForSelect(),
                    'tipos_pago' => GetTiposPagoListForSelect(),
                    'colaboradores' => GetUsuariosListForSelect(),
                    'almacenes' => GetAlmacenesListForSelect(), // Agregar esta línea
                    'botones_acciones' => $data_script['botones_acciones']
                ];
            
                // Configurar NewAdd3 si es necesario
                $optionkey = 'NewAdd3';
                $data_script[$optionkey] = ['data_list_column'=>[]];
                break;
            case 'ordenes_compras':
                $vista = 'ordenes_compras';
                $estatus = GetEstatusLabels();
                $estatus_name = GetEstatusList();
            
                // Consulta inicial
                $consultaselect = "SELECT oc.id_orden_compras,
                    oc.orden_compras,
                    oc.codigo_externo,
                    oc.grupo_cotizacion,
                    (SELECT proyecto FROM proyectos p WHERE p.id_proyecto = oc.kid_proyecto LIMIT 1) AS kid_proyecto,
                    (SELECT proveedor FROM proveedores prov WHERE prov.id_proveedor = oc.kid_proveedor LIMIT 1) AS kid_proveedor,
                    oc.monto_total,
                    oc.monto_neto,
                    oc.kid_estatus,
                    oc.fecha_creacion
                FROM ordenes_compras oc
                WHERE oc.kid_estatus != 3";
            
                $resultado = $conexion->prepare($consultaselect);
                $resultado->execute();
                $ordenes_compras = $resultado->fetchAll(PDO::FETCH_ASSOC);
            
                // Mapear los datos y agregar botones
                $ordenes_compras = array_map(function ($row) {
                    global $data_script, $estatus, $estatus_name;
                    $botones_acciones = $data_script['botones_acciones'];
            
                    $bloque = 'compras';
                    $modalCRUD = 'update_estatus_ordenes_compras';
            
                    // Agregar botones según el estado
                    if(!in_array($row['kid_estatus'], [5,6,9])) {
                        $nuevo_boton = '<button class="UpdateEstatus btn btn-success" bloque="'.$bloque.'" name="'.$estatus_name[6].'" modalCRUD="'.$modalCRUD.'"><i class="bi bi-check2"></i> Revisar</button>';
                        array_unshift($botones_acciones, $nuevo_boton);
                    } else if($row['kid_estatus'] == 6) {
                        $nuevo_boton = '<button class="UpdateEstatus btn btn-success" bloque="'.$bloque.'" name="'.$estatus_name[5].'" modalCRUD="'.$modalCRUD.'"><i class="bi bi-check2-circle"></i> Autorizar</button>';
                        array_unshift($botones_acciones, $nuevo_boton);
                    }
            
                    // Agregar botones de contenido y reporte
                    $hashed_id = codificar($row['id_orden_compras']);
                    $nuevo_boton = '<a href="/rutas/compras.php/detalles_ordenes_compras?id='.$hashed_id.'" class="btn btn-secondary"><i class="bi bi-journal-text"></i> Contenido</a>';
                    array_push($botones_acciones, $nuevo_boton);
                    
                    $nuevo_boton = '<button class="GenerarReporte btn btn-success success" reporte="ordenes_compras_reporte" data-id="'.$hashed_id.'"><i class="bi bi-play-circle"></i> Generar PDF</button>';
                    array_push($botones_acciones, $nuevo_boton);
            
                    $row['botones'] = GenerateCustomsButtons($botones_acciones, 'ordenes_compras');
                    $row['kid_estatus'] = $estatus[$row['kid_estatus']];
                    return $row;
                }, $ordenes_compras);
            
                // Configurar data_show
                $data['data_show'] = [
                    'data' => $ordenes_compras,
                    'AllowADDButton' => true,
                    'proyectos' => GetProyectosListForSelect(),
                    'proveedores' => GetProvedoresListForSelect(),
                    'estatus' => GetEstatusListForSelect(),
                    'tiempos_entrega' => GetTiemposEntregaListForSelect(),
                    'tipos_pago' => GetTiposPagoListForSelect(),
                    'colaboradores' => GetUsuariosListForSelect(),
                    'almacenes' => GetAlmacenesListForSelect(), // Agregar esta línea
                    'botones_acciones' => $data_script['botones_acciones']
                ];
            
                // Configurar NewAdd3 si es necesario
                $optionkey = 'NewAdd3';
                $data_script[$optionkey] = ['data_list_column'=>[]];
                break;
        case 'detalles_ordenes_compras':
            $vista = 'detalles_ordenes_compras';
            if ($id != null) {
                $consultaselect = "SELECT doc.id_detalle_orden_compra,
                    oc.orden_compras AS kid_orden_compra,
                    doc.grupo_cotizacion,
                    a.articulo AS kid_articulo,
                    doc.cantidad,
                    doc.costo_unitario_total,
                    doc.costo_unitario_neto,
                    doc.monto_total,
                    doc.monto_neto,
                    doc.fecha_creacion
                FROM detalles_ordenes_compras doc
                LEFT JOIN articulos a ON doc.kid_articulo = a.id_articulo
                LEFT JOIN ordenes_compras oc ON doc.kid_orden_compras = oc.id_orden_compras
                WHERE doc.kid_estatus !=3 AND doc.kid_orden_compras = $id";


                $resultado = $conexion->prepare("SELECT orden_compras FROM ordenes_compras WHERE id_orden_compras = $id");
                $resultado->execute();
                $breadcrumb_data = $resultado->fetch(PDO::FETCH_ASSOC);

                $breadcrumb = '
                <li class="breadcrumb-item"><a href="/rutas/compras.php/ordenes_compras">Orden de Compra</a></li>
                <li class="breadcrumb-item">'.$breadcrumb_data['orden_compras'].'</li>
                <li class="breadcrumb-item active">Contenido</li>
                ';
                $data['data_show']['breadcrumb'] = $breadcrumb;
                $data['data_show']['valor_id'] = $breadcrumb_data['orden_compras'];
                $data['data_show']['articulos'] = GetArticulosListForSelect();

            }else{
                    
                $consultaselect = "SELECT doc.id_detalle_orden_compra,
                    oc.orden_compras AS kid_orden_compra,
                    doc.grupo_cotizacion,
                    a.articulo AS kid_articulo,
                    doc.cantidad,
                    doc.costo_unitario_total,
                    doc.costo_unitario_neto,
                    doc.monto_total,
                    doc.monto_neto,
                    doc.fecha_creacion
                FROM detalles_ordenes_compras doc
                LEFT JOIN articulos a ON doc.kid_articulo = a.id_articulo
                LEFT JOIN ordenes_compras oc ON doc.kid_orden_compras = oc.id_orden_compras
                WHERE doc.kid_estatus  !=3";
                $data['data_show']['AllowADDButton'] = false;
            }
            $resultado = $conexion->prepare($consultaselect);
                $resultado->execute();

            $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
            $data['data_show']['cotizaciones'] = GetCotizacionesListForSelect();
            $data['data_show']['articulos'] = GetArticulosListForSelect();
            break;
        case 'recepciones_compras':
            $vista = 'recepciones_compras';
            $estatus = GetEstatusLabels();
            $caseEstatus = "CASE \n";
            foreach ($estatus as $key => $value) {
                $caseEstatus .= "    WHEN rc.kid_estatus = $key THEN '$value'\n";
            }
            $caseEstatus .= "    ELSE 'Desconocido' \nEND AS kid_estatus";
            $consultaselect = "SELECT rc.id_recepcion_compras,
                rc.recepcion_compras,
                rc.codigo_externo,
                (SELECT proyecto FROM proyectos p WHERE p.id_proyecto =rc.kid_proyecto LIMIT 1) AS kid_proyecto,
                (SELECT proveedor FROM proveedores prov WHERE prov.id_proveedor = rc.kid_proveedor LIMIT 1) AS kid_proveedor,
                (SELECT almacen FROM almacenes alm WHERE alm.id_almacen  = rc.kid_almacen LIMIT 1) AS kid_almacen,
                (SELECT orden_compras FROM ordenes_compras oc WHERE oc.id_orden_compras  = rc.kid_orden_compras LIMIT 1) AS kid_orden_compras,
                $caseEstatus,
                fecha_creacion
            FROM recepciones_compras rc
            WHERE rc.kid_estatus !=3";
            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();

            $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
            $data['data_show']['colaboradores'] = GetUsuariosListForSelect();
            $data['data_show']['almacenes'] = GetAlmacenesListForSelect();
            
            $modalCRUD = 'detalles_recepciones_compras';
            $nuevo_boton = '
                <button class="ModalNewAdd3 btn btn-info info" modalCRUD="'.$modalCRUD.'"><i class="bi bi-file-spreadsheet"></i> Ver Detalles</button>';
            //array_splice($data_script['botones_acciones'], 0, 0, $nuevo_boton);
            array_push($data_script['botones_acciones'], $nuevo_boton);
            $data['data_show']['botones_acciones'] = $data_script['botones_acciones'];
            $optionkey = 'NewAdd3';
            $data_script[$optionkey] =['data_list_column'=>[]];

            $data_script[$optionkey] =['data_list_column'=>[]];

            break;

            case 'recepciones_pedidos':
                $vista = 'recepciones_pedidos';
                $estatus = GetEstatusLabels();
                $caseEstatus = "CASE \n";
                foreach ($estatus as $key => $value) {
                    $caseEstatus .= "    WHEN rc.kid_estatus = $key THEN '$value'\n";
                }
                $caseEstatus .= "    ELSE 'Desconocido' \nEND AS kid_estatus";
                $consultaselect = "SELECT rc.id_recepcion_compras,
                    rc.recepcion_compras,
                    rc.codigo_externo,
                    (SELECT proyecto FROM proyectos p WHERE p.id_proyecto =rc.kid_proyecto LIMIT 1) AS kid_proyecto,
                    (SELECT proveedor FROM proveedores prov WHERE prov.id_proveedor = rc.kid_proveedor LIMIT 1) AS kid_proveedor,
                    (SELECT almacen FROM almacenes alm WHERE alm.id_almacen  = rc.kid_almacen LIMIT 1) AS kid_almacen,
                    (SELECT orden_compras FROM ordenes_compras oc WHERE oc.id_orden_compras  = rc.kid_orden_compras LIMIT 1) AS kid_orden_compras,
                    $caseEstatus,
                    fecha_creacion
                FROM recepciones_compras rc
                WHERE rc.kid_estatus !=3";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->execute();
    
                $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
                $data['data_show']['colaboradores'] = GetUsuariosListForSelect();
                $data['data_show']['almacenes'] = GetAlmacenesListForSelect();
                
                $modalCRUD = 'detalles_recepciones_compras';
                $nuevo_boton = '
                    <button class="ModalNewAdd3 btn btn-info info" modalCRUD="'.$modalCRUD.'"><i class="bi bi-file-spreadsheet"></i> Ver Detalles</button>';
                //array_splice($data_script['botones_acciones'], 0, 0, $nuevo_boton);
                array_push($data_script['botones_acciones'], $nuevo_boton);
                $data['data_show']['botones_acciones'] = $data_script['botones_acciones'];
                $optionkey = 'NewAdd3';
                $data_script[$optionkey] =['data_list_column'=>[]];
    
                $data_script[$optionkey] =['data_list_column'=>[]];
    
                break;
        case 'detalles_recepciones_compras':
            $vista = 'detalles_recepciones_compras';
            $consultaselect = "SELECT drc.id_detalle_recepcion_compras,
                a.articulo AS kid_articulo,
                rc.recepcion_compras AS kid_recepcion_compras,
                drc.cantidad,
                drc.costo_unitario_total,
                drc.costo_unitario_neto,
                drc.monto_total,
                drc.monto_neto,
                drc.fecha_creacion
            FROM detalles_recepciones_compras drc
            LEFT JOIN articulos a ON drc.kid_articulo = a.id_articulo
            LEFT JOIN recepciones_compras rc ON drc.kid_recepcion_compras = rc.id_recepcion_compras
            WHERE drc.kid_estatus  !=3";
            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();

            $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
            $data['data_show']['cotizaciones'] = GetCotizacionesListForSelect();
            $data['data_show']['articulos'] = GetArticulosListForSelect();
            $data['data_show']['tipo_comentario'] = GetTiposComentariosListForSelect();
            $modalCRUD = 'comentarios_recepciones';
            $nuevo_boton = '
                <button class="ModalNewAdd1 btn btn-secondary secondary" modalCRUD="'.$modalCRUD.'"><i class="bi bi-chat-left-text"></i> Comentario</button>
            ';
            array_splice($data_script['botones_acciones'], 1, 0, $nuevo_boton);
            $data['data_show']['botones_acciones'] = $data_script['botones_acciones'];
            
            $data_script['NewAdd1'] =['data_list_column'=>[
                'kid_recepcion_compras-NewAdd1'=>2,
                'kid_detalle_recepcion_compras'=>0
                
            ]];
            break;
        case 'comentarios_recepciones':
            $vista = 'comentarios_recepciones';
            $consultaselect = "SELECT cr.id_comentario_recepcion, 
                rc.recepcion_compras AS kid_recepcion_compras, 
                ar.articulo,
                cr.comentario_recepcion_compras,
                tc.tipo_comentario AS kid_tipo_comentario,
                cr.fecha_creacion
            FROM 
                comentarios_recepciones cr
            LEFT JOIN 
                recepciones_compras rc ON cr.kid_recepcion_compras = rc.id_recepcion_compras 
            LEFT JOIN 
                detalles_recepciones_compras drc ON cr.kid_detalle_recepcion_compras = drc.id_detalle_recepcion_compras 
            LEFT JOIN 
                articulos ar ON drc.kid_articulo = ar.id_articulo
            LEFT JOIN 
                tipos_comentarios tc ON cr.kid_tipo_comentario = tc.id_tipo_comentario
            WHERE cr.kid_estatus !=3";
            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();

            $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
            $data['data_show']['tipo_comentario'] = GetTiposComentariosListForSelect();
            break;

        case 'asignacion_viaticos':
            $vista = 'asignacion_viaticos';
            $consultaselect = "SELECT av.id_asignacion_viaticos,
                tv.tipo_viatico,
                av.justificacion,
                av.monto_asignado,
                av.monto_real,
                CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) as kid_personal_asignado,
                p.proyecto,
                da.actividad,
                av.fecha_creacion
            FROM 
                asignacion_viaticos av
            LEFT JOIN 
                tipos_viaticos tv ON av.kid_tipo_viatico = tv.id_tipo_viatico
            LEFT JOIN 
                colaboradores u ON av.kid_responsable = u.id_colaborador
            LEFT JOIN 
                proyectos p ON av.kid_proyecto = p.id_proyecto
            LEFT JOIN 
                detalles_actividades da ON av.kid_detalle_actividad = da.id_detalle_actividad
            WHERE av.kid_estatus !=3";
            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();

            $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
            $data['data_show']['tipos_viaticos'] = GetTiposViaticosListForSelect();
            $data['data_show']['colaboradores'] = GetUsuariosListForSelect();
            break;

        case 'tipos_viaticos':
            $vista = 'tipos_viaticos';
            $consultaselect = "SELECT id_tipo_viatico , 
                tipo_viatico,
                orden,
                pordefecto,
                fecha_creacion
            FROM 
                tipos_viaticos
            WHERE kid_estatus !=3";
            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();

            $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
            $data['data_show']['tipos_viaticos'] = GetTiposViaticosListForSelect();
            $data['data_show']['colaboradores'] = GetUsuariosListForSelect();
            break;

        case 'tiempos_entregas':
            $vista = 'tiempos_entregas';
            $consultaselect = "SELECT id_tiempo_entrega, 
                tiempo_entrega,
                orden,
                pordefecto,
                fecha_creacion
            FROM 
                tiempos_entregas
            WHERE kid_estatus !=3";
            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();

            $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
            break;

        case 'tipos_pagos':
            $vista = 'tipos_pagos';
            $consultaselect = "SELECT id_tipo_pago, 
                tipo_pago,
                orden,
                pordefecto,
                fecha_creacion
            FROM 
                tipos_pagos
            WHERE kid_estatus !=3";
            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();

            $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
            break;

        default:
            $vista = '404'; // Vista de error 404 si no se encuentra la ruta
            break;
    }

    $data['list_js_scripts']['formularios_script'] =$data_script;

    renderView($vista, $data);
}else{
    header("Location: /index.php");
}


?>