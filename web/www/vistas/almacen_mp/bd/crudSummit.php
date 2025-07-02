<?php
$objeto = new Conexion();
$conexion = $objeto->Conectar();


$data_script['botones_acciones'] = [
    '<button class="ModalDataView btn btn-primary primary" modalCRUD="${modalCRUD}"><i class="bi bi-eye"></i> Ver</button>',
    '<button class="ModalDataEdit btn btn-warning warning" modalCRUD="${modalCRUD}"><i class="bi bi-pencil"></i> Editar</button>',
    '<button class="ModalDataDelete btn btn-danger danger" modalCRUD="${modalCRUD}"><i class="bi bi-trash"></i> Eliminar</button>'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("POST en crudSummit.php: " . print_r($_POST, true));
    if (isset($_POST['modalCRUD']) && isset($_POST['opcion']) && isset($_POST['formDataJson'])) {
        $modalCRUD = $_POST['modalCRUD'];
        $opcion = $_POST['opcion'];
        $formDataJson = $_POST['formDataJson'];
        
        if (!is_array($formDataJson)) {
            $formDataJson = json_decode($formDataJson, true);
        }
// --- DEBUG GLOBAL: Contenido que recibe el backend ---
if (isset($_GET['debug']) || isset($_POST['debug'])) {
    echo json_encode([
        'modalCRUD' => $modalCRUD,
        'opcion' => $opcion,
        'formDataJson' => $formDataJson
    ]);
    exit;
}
        // Validaciones mínimas
       // if (empty($formDataJson['kid_orden_compras']) || empty($formDataJson['detalles'])) {
          // echo json_encode(['status' => 'error', 'message' => 'Faltan datos requeridos']);
          //  exit;
    //    }

        switch ($modalCRUD) {
case 'recepciones_mp':
    $tabla = 'recepciones_mp';
    $idcolumn = "id_recepcion_mp";

    // FINALIZAR RECEPCIÓN
    if ($opcion === 'finalizar') {
        if (!isset($formDataJson['id_recepcion_mp']) || empty($formDataJson['id_recepcion_mp'])) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Falta id_recepcion_mp en el payload',
                'payload' => $formDataJson
            ]);
            exit;
        }
        try {
            $id_recepcion_mp = intval($formDataJson['id_recepcion_mp']);
            // Marcar la recepción como finalizada
            $stmt = $conexion->prepare("UPDATE recepciones_mp SET kid_estatus = 2 WHERE id_recepcion_mp = :id");
            $stmt->execute([':id' => $id_recepcion_mp]);

            // Además, marcar la orden de compra como finalizada (estatus 8)
            $sql = "UPDATE ordenes_compras SET kid_estatus = 8 WHERE id_orden_compras = (SELECT kid_orden_compras FROM recepciones_mp WHERE id_recepcion_mp = :id)";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([':id' => $id_recepcion_mp]);

            echo json_encode([
                'status' => 'success',
                'message' => 'Recepción finalizada'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'No se pudo finalizar: ' . $e->getMessage()
            ]);
        }
    }
    // CREAR NUEVA RECEPCIÓN
    else if ($opcion == 1) {
        if (empty($formDataJson['kid_orden_compras']) || empty($formDataJson['detalles'])) {
            echo json_encode(['status' => 'error', 'message' => 'Faltan datos requeridos para crear recepción']);
            exit;
        }

        $kid_orden_compras = $formDataJson['kid_orden_compras'];
        $usuarioActual = $_SESSION["s_id"] ?? null;
        $fechaActual = date('Y-m-d H:i:s');
        $detalles = $formDataJson['detalles'];
        unset($formDataJson['detalles']);

        try {
            $conexion->beginTransaction();

            // NO PERMITIR duplicados o varias recepciones
            $sql = "SELECT id_recepcion_mp FROM recepciones_mp WHERE kid_orden_compras = :kid_orden_compras AND kid_estatus IN (2, 8)";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([':kid_orden_compras' => $kid_orden_compras]);
            $recepcionExistente = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($recepcionExistente) {
                $conexion->rollBack();
                echo json_encode(['status' => 'error', 'message' => 'Esta orden de compra ya fue recibida y finalizada.']);
                exit;
            }

            // Verificar recepción existente (NO finalizada, sólo en estatus 1 o null)
            $sql = "SELECT id_recepcion_mp FROM recepciones_mp 
                    WHERE kid_orden_compras = :kid_orden_compras 
                    AND (kid_estatus IS NULL OR kid_estatus = 1) LIMIT 1";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([':kid_orden_compras' => $kid_orden_compras]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $id_recepcion_mp = $row['id_recepcion_mp'];
            } else {
                // Obtener datos de la orden
                $sqlOrden = "SELECT codigo_externo, grupo_cotizacion, kid_proyecto, 
                           kid_proveedor, id_orden_compras, monto_total, monto_neto 
                           FROM ordenes_compras WHERE id_orden_compras = :id";
                $stmtOrd = $conexion->prepare($sqlOrden);
                $stmtOrd->execute([':id' => $kid_orden_compras]);
                $orden = $stmtOrd->fetch(PDO::FETCH_ASSOC);

                if (!$orden) throw new Exception('Orden de compra no encontrada');

                // Preparar datos del encabezado
                $camposRecepcion = [
                    'recepcion_mp'        => 'Recepción OC ' . $orden['id_orden_compras'],
                    'numero_tarimas'      => $formDataJson['numero_tarimas'] ?? 1,
                    'peso_tarimas'        => $formDataJson['peso_tarimas'] ?? 0,
                    'codigo_externo'      => $orden['codigo_externo'],
                    'grupo_cotizacion'    => $orden['grupo_cotizacion'],
                    'kid_proyecto'        => $orden['kid_proyecto'],
                    'kid_proveedor'       => $orden['kid_proveedor'],
                    'kid_orden_compras'   => $orden['id_orden_compras'],
                    'kid_almacen'         => $formDataJson['kid_almacen'] ?? null,
                    'kid_recibe'          => $usuarioActual,
                    'monto_total'         => $orden['monto_total'],
                    'monto_neto'          => $orden['monto_neto'],
                    'kid_creacion'        => $usuarioActual,
                    'fecha_creacion'      => $fechaActual,
                    'kid_estatus'         => 1
                ];

                // Insertar encabezado
                $cols = implode(',', array_keys($camposRecepcion));
                $vals = ':' . implode(',:', array_keys($camposRecepcion));
                $sqlIns = "INSERT INTO recepciones_mp ($cols) VALUES ($vals)";
                $stmtIns = $conexion->prepare($sqlIns);
                $stmtIns->execute($camposRecepcion);
                $id_recepcion_mp = $conexion->lastInsertId();
            }

            // Procesar detalles
            foreach ($detalles as $detalle) {
                // Obtener detalles de la orden de compra
                $sqlDetOrd = "SELECT * FROM detalles_ordenes_compras 
                            WHERE kid_orden_compras = :oc AND kid_articulo = :art";
                $stmtDetOrd = $conexion->prepare($sqlDetOrd);
                $stmtDetOrd->execute([
                    ':oc' => $kid_orden_compras,
                    ':art' => $detalle['kid_articulo']
                ]);
                $detOrden = $stmtDetOrd->fetch(PDO::FETCH_ASSOC);

                if (!$detOrden) throw new Exception('Detalle de OC no encontrado para artículo: ' . $detalle['kid_articulo']);

                // Preparar datos del detalle
                $camposDetalle = [
                    'kid_articulo'         => $detalle['kid_articulo'],
                    'kid_recepcion_mp'     => $id_recepcion_mp,
                    'costo_unitario_total' => $detOrden['costo_unitario_total'],
                    'costo_unitario_neto'  => $detOrden['costo_unitario_neto'],
                    'monto_total'          => $detOrden['monto_total'],
                    'monto_neto'           => $detOrden['monto_neto'],
                    'retencion_iva'        => $detOrden['porcentaje_descuento'] ?? 0,
                    'kid_locacion_almacen' => $detalle['kid_locacion_almacen'] ?? null,
                    'peso_real'            => $detalle['peso_real'],
                    'peso_estimado'        => $detOrden['cantidad'],
                    'diferencia_peso'      => $detalle['diferencia_peso'],
                    'valor_codigoqr'       => $detalle['valor_codigoqr'],
                    'imagen_codigo_qr'     => $detalle['imagen_codigo_qr'],
                    'pdf_generado'         => $detalle['pdf_generado'] ?? null,
                    'kid_creacion'         => $usuarioActual,
                    'fecha_creacion'       => $fechaActual,
                    'kid_estatus'          => 1
                ];

                // Insertar detalle
                $colsDet = implode(',', array_keys($camposDetalle));
                $valsDet = ':' . implode(',:', array_keys($camposDetalle));
                $sqlDet = "INSERT INTO detalles_recepciones_mp ($colsDet) VALUES ($valsDet)";
                $stmtDet = $conexion->prepare($sqlDet);
                foreach ($camposDetalle as $k => $v) {
                    $stmtDet->bindValue(":$k", $v);
                }
                $stmtDet->execute();
            }

            // Consulta final para retornar datos
            $consultaselect = "SELECT r.*, 
                                    d.peso_real, 
                                    d.peso_estimado,
                                    d.diferencia_peso,
                                    d.valor_codigoqr
                             FROM $tabla r
                             LEFT JOIN detalles_recepciones_mp d 
                                ON r.id_recepcion_mp = d.kid_recepcion_mp
                             WHERE r.$idcolumn = :$idcolumn";
            
            $stmt = $conexion->prepare($consultaselect);
            $stmt->bindValue(":$idcolumn", $id_recepcion_mp);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            // MARCAR OC COMO FINALIZADA
            $sql = "UPDATE ordenes_compras SET kid_estatus = 8 WHERE id_orden_compras = :id";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([':id' => $kid_orden_compras]);

            $conexion->commit();
            echo json_encode([
                'status' => 'success', 
                'id_recepcion_mp' => $id_recepcion_mp,
                'data' => $data
            ]);
        } catch (Exception $e) {
            $conexion->rollBack();
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
    break;
}
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Faltan parámetros requeridos'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Método no permitido'
    ]);
}