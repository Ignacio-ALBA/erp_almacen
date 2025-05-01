<?php
$objeto = new Conexion();
$conexion = $objeto->Conectar();

$data = []; // Inicializa la variable $data

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['modalCRUD']) && isset($_POST['firstColumnValue'])) {
        $modalCRUD = $_POST['modalCRUD'];
        $elementID = $_POST['firstColumnValue'];

        switch ($modalCRUD) {
            case 'proveedores':
                $consultaselect = "SELECT p.*,
                e.estado AS kid_estado
                FROM proveedores p
                LEFT JOIN estados e ON p.kid_estado = e.id_estados
                WHERE p.kid_estatus != 3 AND p.id_proveedor  = :id";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':id', $elementID);
                $resultado->execute();
                $data = $resultado->fetch(PDO::FETCH_ASSOC);
                $data['id_proveedor'] = null;

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'comentarios_proveedores':
                if(isset($_POST['opcion'])) {
                    $consultaselect = "SELECT cp.id_comentario_proveedor, 
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
                    WHERE cp.kid_estatus !=3 AND cp.kid_proveedor  = :id";
                    $resultado = $conexion->prepare($consultaselect);
                    $resultado->bindParam(':id', $elementID);
                    $resultado->execute();
                    $data['data'] = $resultado->fetchAll(PDO::FETCH_NUM);
                
                    $consultaselect = "SELECT a.articulo
                        FROM ordenes_compras oc
                        LEFT JOIN detalles_ordenes_compras doc ON oc.id_orden_compras = doc.kid_orden_compras
                        LEFT JOIN articulos a ON doc.kid_articulo = a.id_articulo
                        WHERE a.kid_estatus != 3 AND doc.kid_estatus != 3 AND oc.kid_estatus = 6
                        AND oc.kid_proyecto = (
                            SELECT cc.kid_proyecto 
                            FROM cotizaciones_compras cc 
                            WHERE doc.kid_orden_compras = :id
                        ) 
                        ORDER BY a.articulo ASC;";
                    $resultado = $conexion->prepare($consultaselect);
                    $resultado->bindParam(':id', $elementID);
                    $resultado->execute();
                    $select = $resultado->fetchAll(PDO::FETCH_ASSOC);

                    $data['options']['kid_articulo'] = array_map(fn($item) => [
                        'valor'=> $item['articulo'],
                        'pordefecto' => 0,
                    ], $select);

                }else{
                    $consultaselect = "SELECT p.nombre_comercial AS kid_proveedor, 
                        cp.comentario_proveedor,
                        tc.tipo_comentario AS kid_tipo_comentario
                    FROM 
                        comentarios_proveedores cp
                    LEFT JOIN 
                        proveedores p ON cp.kid_proveedor = p.id_proveedor
                    LEFT JOIN 
                        tipos_comentarios tc ON cp.kid_tipo_comentario = tc.id_tipo_comentario
                    WHERE cp.kid_estatus !=3 AND cp.id_comentario_proveedor  = :id";
                    $resultado = $conexion->prepare($consultaselect);
                    $resultado->bindParam(':id', $elementID);
                    $resultado->execute();
                    $data = $resultado->fetch(PDO::FETCH_ASSOC);
                    $data['id_detalle_cotizacion_compras'] = null;
                }

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'listas_compras':
                $consultaselect = "SELECT lc.id_lista_compra,
                    lc.orden,
                    lc.lista_compra,
                    p.proyecto AS kid_proyecto,
                    cb.cuenta_bancaria AS kid_cuenta_bancaria,
                    es.estatus AS kid_estatus,
                    lc.fecha_creacion
                FROM listas_compras lc
                LEFT JOIN cuentas_bancarias cb ON lc.kid_cuenta_bancaria = cb.id_cuenta_bancaria
                LEFT JOIN proyectos p ON lc.kid_proyecto = p.id_proyecto
                LEFT JOIN estatus es ON lc.kid_estatus = es.id_estatus
                WHERE lc.kid_estatus != 3 AND lc.id_lista_compra = :id";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':id', $elementID);
                $resultado->execute();
                $data = $resultado->fetch(PDO::FETCH_ASSOC);
                // $data['id_lista_compra'] = null;
                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'detalles_listas_compras':
                if(isset($_POST['opcion'])) {
                    $consultaselect = "SELECT dlc.id_detalle_lista_compras,
                        lc.lista_compra AS kid_lista_compras,
                        a.articulo AS kid_articulo,
                        dlc.cantidad,
                        dlc.costo_unitario_total,
                        dlc.costo_unitario_neto,
                        dlc.monto_total,
                        dlc.monto_neto,
                        dlc.fecha_creacion,
                        dlc.porcentaje_descuento
                    FROM detalles_listas_compras dlc
                    LEFT JOIN listas_compras lc ON dlc.kid_lista_compras = lc.id_lista_compra
                    LEFT JOIN articulos a ON dlc.kid_articulo = a.id_articulo
                    WHERE dlc.kid_estatus != 3 AND dlc.kid_lista_compras = :id";
                    $resultado = $conexion->prepare($consultaselect);
                    $resultado->bindParam(':id', $elementID);
                    $resultado->execute();
                    $data = $resultado->fetchAll(PDO::FETCH_NUM);
                }else{
                    $consultaselect = "SELECT dlc.id_detalle_lista_compras,
                        lc.lista_compra AS kid_lista_compras,
                        a.articulo AS kid_articulo,
                        a.id_articulo,
                        dlc.cantidad,
                        dlc.costo_unitario_total,
                        dlc.costo_unitario_neto,
                        dlc.monto_total,
                        dlc.monto_neto,
                        dlc.fecha_creacion,
                        dlc.porcentaje_descuento
                    FROM detalles_listas_compras dlc
                    LEFT JOIN listas_compras lc ON dlc.kid_lista_compras = lc.id_lista_compra
                    LEFT JOIN articulos a ON dlc.kid_articulo = a.id_articulo
                    WHERE dlc.kid_estatus != 3 AND id_detalle_lista_compras = :id";
                    $resultado = $conexion->prepare($consultaselect);
                    $resultado->bindParam(':id', $elementID);
                    $resultado->execute();
                    $data = $resultado->fetch(PDO::FETCH_ASSOC);

                    // Añadir options para el select de artículos
                    if ($data) {
                        $data['options'] = [
                            'kid_articulo' => [
                                [
                                    'valor' => $data['id_articulo'],
                                    'texto' => $data['kid_articulo'],
                                    'pordefecto' => 1
                                ]
                            ]
                        ];
                        // Mantener el valor original para el campo kid_articulo
                        $data['kid_articulo'] = $data['id_articulo'];
                    }
                }

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'cotizaciones_compras':
                $consultaselect = "SELECT cc.id_cotizacion_compra,
                    cc.cotizacion_compras,
                    cc.grupo,
                    p.proyecto AS kid_proyecto,
                    prov.proveedor AS kid_proveedor,
                    es.estatus AS kid_estatus,
                    cc.fecha_cotizacion,
                    te.tiempo_entrega AS kid_tiempo_entrega,
                    tp.tipo_pago AS kid_tipo_pago,
                    cc.especificaciones_adicionales,
                    cc.fecha_creacion
                FROM cotizaciones_compras cc
                LEFT JOIN proyectos p ON cc.kid_proyecto = p.id_proyecto
                LEFT JOIN proveedores prov ON cc.kid_proveedor = prov.id_proveedor
                LEFT JOIN estatus es ON cc.kid_estatus = es.id_estatus
                LEFT JOIN tiempos_entregas te ON cc.kid_tiempo_entrega = te.id_tiempo_entrega 
                LEFT JOIN tipos_pagos tp ON cc.kid_tipo_pago = tp.id_tipo_pago 
                WHERE cc.kid_estatus != 3 AND cc.id_cotizacion_compra = :id";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':id', $elementID);
                $resultado->execute();
                $data = $resultado->fetch(PDO::FETCH_ASSOC);
                $data['id_cotizacion_compra'] = null;
                

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'detalles_cotizaciones_compras':
                if(isset($_POST['opcion'])) {
                    $consultaselect = "SELECT dcc.id_detalle_cotizacion_compras,
                        cc.cotizacion_compras AS kid_cotizacion_compra,
                        a.articulo AS kid_articulo,
                        dcc.cantidad,
                        dcc.costo_unitario_total,
                        dcc.costo_unitario_neto,
                        dcc.monto_total,
                        dcc.monto_neto,
                        dcc.fecha_creacion,
                        dcc.porcentaje_descuento
                    FROM detalles_cotizaciones_compras dcc
                    LEFT JOIN cotizaciones_compras cc ON dcc.kid_cotizacion_compra = cc.id_cotizacion_compra 
                    LEFT JOIN articulos a ON dcc.kid_articulo = a.id_articulo
                    WHERE dcc.kid_estatus != 3 AND id_detalle_cotizacion_compras  = :id";
                   $resultado = $conexion->prepare($consultaselect);
                    $resultado->bindParam(':id', $elementID);
                    $resultado->execute();
                    $data['data'] = $resultado->fetchAll(PDO::FETCH_NUM);
                
                    $consultaselect = "SELECT a.articulo
                        FROM listas_compras lc
                        LEFT JOIN detalles_listas_compras dlc ON lc.id_lista_compra = dlc.kid_lista_compras
                        LEFT JOIN articulos a ON dlc.kid_articulo = a.id_articulo
                        WHERE a.kid_estatus != 3 AND dlc.kid_estatus != 3 AND lc.kid_estatus = 6
                        AND lc.kid_proyecto = (
                            SELECT cc.kid_proyecto 
                            FROM cotizaciones_compras cc 
                            WHERE cc.id_cotizacion_compra = :id
                        ) 
                        ORDER BY a.articulo ASC;";
                    $resultado = $conexion->prepare($consultaselect);
                    $resultado->bindParam(':id', $elementID);
                    $resultado->execute();
                    $select = $resultado->fetchAll(PDO::FETCH_ASSOC);

                    $data['options']['kid_articulo'] = array_map(fn($item) => [
                        'valor'=> $item['articulo'],
                        'pordefecto' => 0,
                    ], $select);

                }else{
                    $consultaselect = "SELECT dcc.id_detalle_cotizacion_compras,
                        cc.cotizacion_compras AS kid_cotizacion_compra,
                        a.articulo AS kid_articulo,
                        a.id_articulo,
                        dcc.cantidad,
                        dcc.costo_unitario_total,
                        dcc.costo_unitario_neto,
                        dcc.monto_total,
                        dcc.monto_neto,
                        dcc.fecha_creacion,
                        dcc.porcentaje_descuento
                    FROM detalles_cotizaciones_compras dcc
                    LEFT JOIN cotizaciones_compras cc ON dcc.kid_cotizacion_compra = cc.id_cotizacion_compra 
                    LEFT JOIN articulos a ON dcc.kid_articulo = a.id_articulo
                    WHERE dcc.kid_estatus != 3 AND dcc.id_detalle_cotizacion_compras  = :id";
                    $resultado = $conexion->prepare($consultaselect);
                    $resultado->bindParam(':id', $elementID);
                    $resultado->execute();
                    $data = $resultado->fetch(PDO::FETCH_ASSOC);
                    if ($data) {
                        $data['options'] = [
                            'kid_articulo' => [
                                [
                                    'valor' => $data['id_articulo'],
                                    'texto' => $data['kid_articulo'],
                                    'pordefecto' => 1
                                ]
                            ]
                        ];
                        // Mantener el valor original para el campo kid_articulo
                        $data['kid_articulo'] = $data['id_articulo'];
                    }
                    $data['id_detalle_cotizacion_compras'] = null;
                }

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'ordenes_compras':
                $consultaselect = "SELECT oc.id_orden_compras,
                    oc.orden_compras,
                    oc.codigo_externo,
                    oc.grupo_cotizacion,
                    p.proyecto AS kid_proyecto,
                    prov.proveedor AS kid_proveedor,
                    oc.monto_total,
                    oc.monto_neto,
                    oc.fecha_creacion
                FROM ordenes_compras oc
                LEFT JOIN proyectos p ON oc.kid_proyecto = p.id_proyecto
                LEFT JOIN proveedores prov ON oc.kid_proveedor = prov.id_proveedor
                WHERE oc.kid_estatus !=3 AND oc.id_orden_compras  = :id";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':id', $elementID);
                $resultado->execute();
                $data = $resultado->fetch(PDO::FETCH_ASSOC);
                $data['id_orden_compras'] = null;
                

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'detalles_ordenes_compras':
                if(isset($_POST['opcion'])) {
                    $consultaselect = "SELECT doc.id_detalle_orden_compra,
                        oc.orden_compras AS kid_orden_compras,
                        doc.grupo_cotizacion,
                        a.articulo AS kid_articulo,
                        doc.cantidad,
                        doc.costo_unitario_total,
                        doc.costo_unitario_neto,
                        doc.monto_total,
                        doc.monto_neto,
                        doc.fecha_creacion,
                        doc.porcentaje_descuento
                    FROM detalles_ordenes_compras doc
                    LEFT JOIN articulos a ON doc.kid_articulo = a.id_articulo
                    LEFT JOIN ordenes_compras oc ON doc.kid_orden_compras = oc.id_orden_compras
                    WHERE doc.kid_estatus  !=3 AND doc.kid_orden_compras  = :id";
                    $resultado = $conexion->prepare($consultaselect);
                    $resultado->bindParam(':id', $elementID);
                    $resultado->execute();
                    $data['data'] = $resultado->fetchAll(PDO::FETCH_NUM);
                
                    $consultaselect = "SELECT a.articulo
                        FROM ordenes_compras oc
                        LEFT JOIN detalles_ordenes_compras doc ON oc.id_orden_compras = doc.kid_orden_compras
                        LEFT JOIN articulos a ON doc.kid_articulo = a.id_articulo
                        WHERE a.kid_estatus != 3 AND doc.kid_estatus != 3 AND oc.kid_estatus = 6
                        AND oc.kid_proyecto = (
                            SELECT cc.kid_proyecto 
                            FROM cotizaciones_compras cc 
                            WHERE doc.kid_orden_compras = :id
                        ) 
                        ORDER BY a.articulo ASC;";
                    $resultado = $conexion->prepare($consultaselect);
                    $resultado->bindParam(':id', $elementID);
                    $resultado->execute();
                    $select = $resultado->fetchAll(PDO::FETCH_ASSOC);

                    $data['options']['kid_articulo'] = array_map(fn($item) => [
                        'valor'=> $item['articulo'],
                        'pordefecto' => 0,
                    ], $select);

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
                        doc.fecha_creacion,
                        doc.porcentaje_descuento
                    FROM detalles_ordenes_compras doc
                    LEFT JOIN articulos a ON doc.kid_articulo = a.id_articulo
                    LEFT JOIN ordenes_compras oc ON doc.kid_orden_compras = oc.id_orden_compras
                    WHERE doc.kid_estatus  !=3 AND doc.id_detalle_orden_compra  = :id";
                    $resultado = $conexion->prepare($consultaselect);
                    $resultado->bindParam(':id', $elementID);
                    $resultado->execute();
                    $data = $resultado->fetch(PDO::FETCH_ASSOC);
                    $data['id_detalle_cotizacion_compras'] = null;
                }

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'recepciones_compras':
                $consultaselect = "SELECT rc.recepcion_compras,
                    rc.codigo_externo,
                    (SELECT proyecto FROM proyectos p WHERE p.id_proyecto =rc.kid_proyecto LIMIT 1) AS kid_proyecto,
                    (SELECT proveedor FROM proveedores prov WHERE prov.id_proveedor = rc.kid_proveedor LIMIT 1) AS kid_proveedor,
                    (SELECT almacen FROM almacenes alm WHERE alm.id_almacen  = rc.kid_almacen LIMIT 1) AS kid_almacen,
                    (SELECT orden_compras FROM ordenes_compras oc WHERE oc.id_orden_compras  = rc.kid_orden_compras LIMIT 1) AS kid_orden_compras,
                    (SELECT email FROM colaboradores u WHERE u.id_colaborador  = rc.kid_recibe LIMIT 1) AS kid_recibe,
                    (SELECT email FROM colaboradores u WHERE u.id_colaborador  = rc.kid_reclama LIMIT 1) AS kid_reclama,
                    (SELECT email FROM colaboradores u WHERE u.id_colaborador  = rc.kid_regresa LIMIT 1) AS kid_regresa,
                    rc.monto_total,
                    rc.monto_neto
                FROM recepciones_compras rc
                WHERE rc.kid_estatus !=3 AND rc.id_recepcion_compras  = :id";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':id', $elementID);
                $resultado->execute();
                $data = $resultado->fetch(PDO::FETCH_ASSOC);
                $data['id_orden_compras'] = null;
                

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;


            case 'detalles_recepciones_compras':
                if(isset($_POST['opcion'])) {
                    $consultaselect = "SELECT drc.id_detalle_recepcion_compras,
                        a.articulo AS kid_articulo,
                        rc.recepcion_compras AS kid_recepcion_compras,
                        drc.cantidad,
                        drc.costo_unitario_total,
                        drc.costo_unitario_neto,
                        drc.monto_total,
                        drc.monto_neto,
                        drc.fecha_creacion,
                        drc.porcentaje_descuento
                    FROM detalles_recepciones_compras drc
                    LEFT JOIN articulos a ON drc.kid_articulo = a.id_articulo
                    LEFT JOIN recepciones_compras rc ON drc.kid_recepcion_compras = rc.id_recepcion_compras
                    WHERE drc.kid_estatus  !=3 AND drc.kid_recepcion_compras  = :id";
                    $resultado = $conexion->prepare($consultaselect);
                    $resultado->bindParam(':id', $elementID);
                    $resultado->execute();
                    $data['data'] = $resultado->fetchAll(PDO::FETCH_NUM);
                
                    $consultaselect = "SELECT a.articulo
                        FROM ordenes_compras oc
                        LEFT JOIN detalles_ordenes_compras doc ON oc.id_orden_compras = doc.kid_orden_compras
                        LEFT JOIN articulos a ON doc.kid_articulo = a.id_articulo
                        WHERE a.kid_estatus != 3 AND doc.kid_estatus != 3 AND oc.kid_estatus = 6
                        AND oc.kid_proyecto = (
                            SELECT cc.kid_proyecto 
                            FROM cotizaciones_compras cc 
                            WHERE doc.kid_orden_compras = :id
                        ) 
                        ORDER BY a.articulo ASC;";
                    $resultado = $conexion->prepare($consultaselect);
                    $resultado->bindParam(':id', $elementID);
                    $resultado->execute();
                    $select = $resultado->fetchAll(PDO::FETCH_ASSOC);

                    $data['options']['kid_articulo'] = array_map(fn($item) => [
                        'valor'=> $item['articulo'],
                        'pordefecto' => 0,
                    ], $select);

                }else{
                    $consultaselect = "SELECT a.articulo AS kid_articulo,
                        rc.recepcion_compras AS kid_recepcion_compras,
                        drc.cantidad,
                        drc.costo_unitario_total,
                        drc.costo_unitario_neto,
                        drc.monto_total,
                        drc.monto_neto,
                        drc.fecha_creacion,
                        drc.porcentaje_descuento
                    FROM detalles_recepciones_compras drc
                    LEFT JOIN articulos a ON drc.kid_articulo = a.id_articulo
                    LEFT JOIN recepciones_compras rc ON drc.kid_recepcion_compras = rc.id_recepcion_compras
                    WHERE drc.kid_estatus  !=3 AND drc.id_detalle_recepcion_compras  = :id";
                    $resultado = $conexion->prepare($consultaselect);
                    $resultado->bindParam(':id', $elementID);
                    $resultado->execute();
                    $data = $resultado->fetch(PDO::FETCH_ASSOC);
                    $data['id_detalle_cotizacion_compras'] = null;
                }

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;


            case 'comentarios_recepciones':
                if(isset($_POST['opcion'])) {
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
                    WHERE drc.kid_estatus  !=3 AND drc.kid_recepcion_compras  = :id";
                    $resultado = $conexion->prepare($consultaselect);
                    $resultado->bindParam(':id', $elementID);
                    $resultado->execute();
                    $data['data'] = $resultado->fetchAll(PDO::FETCH_NUM);
                
                    $consultaselect = "SELECT a.articulo
                        FROM ordenes_compras oc
                        LEFT JOIN detalles_ordenes_compras doc ON oc.id_orden_compras = doc.kid_orden_compras
                        LEFT JOIN articulos a ON doc.kid_articulo = a.id_articulo
                        WHERE a.kid_estatus != 3 AND doc.kid_estatus != 3 AND oc.kid_estatus = 6
                        AND oc.kid_proyecto = (
                            SELECT cc.kid_proyecto 
                            FROM cotizaciones_compras cc 
                            WHERE doc.kid_orden_compras = :id
                        ) 
                        ORDER BY a.articulo ASC;";
                    $resultado = $conexion->prepare($consultaselect);
                    $resultado->bindParam(':id', $elementID);
                    $resultado->execute();
                    $select = $resultado->fetchAll(PDO::FETCH_ASSOC);

                    $data['options']['kid_articulo'] = array_map(fn($item) => [
                        'valor'=> $item['articulo'],
                        'pordefecto' => 0,
                    ], $select);

                }else{
                    $consultaselect = "SELECT cr.id_comentario_recepcion, 
                        rc.recepcion_compras AS kid_recepcion_compras, 
                        ar.articulo AS kid_detalle_recepcion_compras,
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
                    WHERE cr.kid_estatus  !=3 AND cr.id_comentario_recepcion  = :id";
                    $resultado = $conexion->prepare($consultaselect);
                    $resultado->bindParam(':id', $elementID);
                    $resultado->execute();
                    $data = $resultado->fetch(PDO::FETCH_ASSOC);
                    $data['id_detalle_cotizacion_compras'] = null;
                }

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'GETArticulosProyecto':
                /*-------------------- Obtener Tablas Foráneas --------------------*/
                $consultaselect = "SELECT id_proyecto FROM proyectos WHERE proyecto = :Proyecto";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':Proyecto', $elementID);
                $resultado->execute();
                $data = $resultado->fetch(PDO::FETCH_ASSOC);
                $elementID = $data['id_proyecto'];
                /*------------------- Fin Obtener Tablas Foráneas ------------------*/

                $consultaselect = "SELECT a.articulo
                    FROM listas_compras lc
                    JOIN detalles_listas_compras dlc ON lc.id_lista_compra = dlc.kid_lista_compras
                    JOIN articulos a ON dlc.kid_articulo = a.id_articulo
                    WHERE a.kid_estatus != 3 AND lc.kid_proyecto = :id AND lc.kid_estatus = 5  
                    ORDER BY a.articulo ASC";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':id', $elementID);
                $resultado->execute();
                $data = $resultado->fetchAll(PDO::FETCH_ASSOC);

                $data = array_map(fn($item) => [
                    'valor'=> $item['articulo'],
                    'pordefecto' => 0,
                ], $data);

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'ordenes_comprasComplement':
                $consultaselect = "SELECT monto_total,
                        monto_neto,
                        fecha_creacion
                    FROM detalles_cotizaciones_compras
                    WHERE kid_estatus != 3 AND kid_cotizacion_compra = :id";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':id', $elementID);
                $resultado->execute();
                $data = $resultado->fetch(PDO::FETCH_ASSOC);

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;


            case 'GETMunicipios':
                /*-------------------- Obtener Tablas Foráneas --------------------*/
                $consultaselect = "SELECT id_estados FROM estados WHERE estado = :Estado";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':Estado', $elementID);
                $resultado->execute();
                $data = $resultado->fetch(PDO::FETCH_ASSOC);
                $elementID = $data['id_estados'];
                /*------------------- Fin Obtener Tablas Foráneas ------------------*/

                $consultaselect = "SELECT m.municipio, 
                        m.pordefecto, 
                        e.simbolo as simbolo
                    FROM 
                        municipios m
                    LEFT JOIN 
                        estados e ON m.kid_estado = e.id_estados 
                    WHERE m.kid_estatus = 1 AND m.kid_estado = :idEstado 
                    ORDER BY m.pordefecto DESC, m.orden ASC, m.municipio ASC";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':idEstado', $elementID);
                $resultado->execute();
                $data = $resultado->fetchAll(PDO::FETCH_ASSOC);

                $data = array_map(fn($item) => [
                    'valor'=> $item['municipio'],
                    'text' => trim(implode('-', array_filter([$item['municipio'], $item['simbolo']]))),
                    'pordefecto' => $item['pordefecto'],
                ], $data);

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'GETColonia':
                $consultaselect = "SELECT colonia FROM colonias WHERE kid_estatus = 1 AND cp = :CP";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':CP', $elementID);
                $resultado->execute();
                $data = $resultado->fetch(PDO::FETCH_ASSOC);
                
                //$data = $item['cp'];
                // Verifica si se encontraron datos
                if ($data) {
                    $respuesta['nombre_colonia'] = $data['colonia'];
                    $data = $respuesta;
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;


            case 'asignacion_viaticos':
                if(isset($_POST['opcion'])) {
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
                    WHERE av.kid_estatus !=3 AND av.kid_actividad  = :id";
                    $resultado = $conexion->prepare($consultaselect);
                    $resultado->bindParam(':id', $elementID);
                    $resultado->execute();
                    $data['data'] = $resultado->fetchAll(PDO::FETCH_NUM);
                
                    $consultaselect = "SELECT
                        p.proyecto
                        FROM actividades a
                        LEFT JOIN proyectos p ON a.kid_proyecto = p.id_proyecto
                        WHERE a.kid_estatus != 3 AND a.id_actividad = :id";

                    $resultado = $conexion->prepare($consultaselect);
                    $resultado->bindParam(':id', $elementID);
                    $resultado->execute();
                    $select = $resultado->fetchAll(PDO::FETCH_ASSOC);

                    $data['options']['kid_proyecto'] = array_map(fn($item) => [
                        'valor'=> $item['proyecto'],
                        'pordefecto' => 0,
                    ], $select);

                    $data['options']['kid_actividad'] = [['valor'=> $elementID,'pordefecto' => 0]];

                    $data['options']['kid_detalle_actividad'] = GetDetallesActividadesListForSelect(["kid_actividad"=>$elementID]);
                    

                }else{
                    $consultaselect = "SELECT av.id_asignacion_viaticos,
                        tv.tipo_viatico,
                        av.justificacion,
                        av.monto_asignado,
                        av.monto_real,
                        u.email as kid_responsable,
                        av.grupo,
                        p.proyecto as kid_proyecto,
                        av.kid_actividad,
                        da.actividad as kid_detalle_actividad,
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
                    WHERE av.kid_estatus !=3 AND av.id_asignacion_viaticos  = :id";
                    $resultado = $conexion->prepare($consultaselect);
                    $resultado->bindParam(':id', $elementID);
                    $resultado->execute();
                    $data = $resultado->fetch(PDO::FETCH_ASSOC);
                    $data['id_detalle_cotizacion_compras'] = null;
                }

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'tipos_viaticos':
                $consultaselect = "SELECT
                    tipo_viatico,
                    orden,
                    pordefecto,
                    fecha_creacion
                FROM 
                    tipos_viaticos
                WHERE kid_estatus !=3 AND id_tipo_viatico  = :id";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':id', $elementID);
                $resultado->execute();
                $data = $resultado->fetch(PDO::FETCH_ASSOC);
                $data['id_proveedor'] = null;

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;


            case 'tiempos_entregas':
                $consultaselect = "SELECT
                    tiempo_entrega,
                    orden,
                    pordefecto,
                    fecha_creacion
                FROM 
                    tiempos_entregas
                WHERE kid_estatus !=3 AND id_tiempo_entrega = :id";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':id', $elementID);
                $resultado->execute();
                $data = $resultado->fetch(PDO::FETCH_ASSOC);

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'tipos_pagos':
                $consultaselect = "SELECT
                    tipo_pago,
                    orden,
                    pordefecto,
                    fecha_creacion
                FROM 
                    tipos_pagos
                WHERE kid_estatus !=3 AND id_tipo_pago = :id";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':id', $elementID);
                $resultado->execute();
                $data = $resultado->fetch(PDO::FETCH_ASSOC);

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;




            default:
                print json_encode(['status' => 'error', 'message' => 'Operación no válida'], JSON_UNESCAPED_UNICODE);
                break;
                
        }
    } else {
        print json_encode(['status' => 'error', 'message' => 'Faltan datos requeridos'], JSON_UNESCAPED_UNICODE);
    }
} else {
    print json_encode(['status' => 'error', 'message' => 'Método no permitido'], JSON_UNESCAPED_UNICODE);
}
?>