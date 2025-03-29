<?php
$objeto = new Conexion();
$conexion = $objeto->Conectar();

$data = []; // Inicializa la variable $data

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['modalCRUD']) && isset($_POST['firstColumnValue'])) {
        $modalCRUD = $_POST['modalCRUD'];
        $elementID = $_POST['firstColumnValue'];

        switch ($modalCRUD) {
            case 'bancos':
                $consultaselect = "SELECT orden,
                    banco,
                    pordefecto,
                    fecha_creacion
                FROM bancos
                WHERE kid_estatus = 1 AND id_banco  = :idBanco ";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':idBanco', $elementID);
                $resultado->execute();
                $data = $resultado->fetch(PDO::FETCH_ASSOC);

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;


            case 'tipos_cuentas_bancarias':
                $consultaselect = "SELECT orden,
                    tipo_cuenta_bancaria,
                    pordefecto,
                    fecha_creacion
                FROM tipos_cuentas_bancarias
                WHERE kid_estatus = 1 AND id_tipo_cuenta_bancaria  = :idTipoCuentaBancaria ";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':idTipoCuentaBancaria', $elementID);
                $resultado->execute();
                $data = $resultado->fetch(PDO::FETCH_ASSOC);

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'cuentas_bancarias':
                $consultaselect = "SELECT cb.cuenta_bancaria,
                    b.banco AS kid_banco,
                    cb.numero_cuenta_bancaria,
                    cb.cable,
                    cb.tarjeta,
                    tcb.tipo_cuenta_bancaria,
                    cb.saldo,
                    cb.deuda,
                    cb.cuenta_maestra,
                    cb.fecha_creacion
                FROM cuentas_bancarias cb
                LEFT JOIN bancos b ON cb.kid_banco = b.id_banco
                LEFT JOIN tipos_cuentas_bancarias tcb ON cb.kid_tipo_cuenta_bancaria = tcb.id_tipo_cuenta_bancaria 
                WHERE cb.kid_estatus = 1 AND cb.id_cuenta_bancaria  = :idCuentaBancaria ";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':idCuentaBancaria', $elementID);
                $resultado->execute();
                $data = $resultado->fetch(PDO::FETCH_ASSOC);

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'detalles_cuentas_bancarias':
                $consultaselect = "SELECT dcb.id_detalle_cuenta_bancaria,
                    dcb.detalle_cuenta_bancaria,
                    cb.cuenta_bancaria AS kid_cuenta_bancaria,
                    p.proyecto AS kid_proyecto,
                    dcb.monto_asignado,
                    dcb.monto_disponible,
                    dcb.monto_adeudado,
                    dcb.monto_gastado
                FROM detalles_cuentas_bancarias dcb
                LEFT JOIN cuentas_bancarias cb ON dcb.kid_cuenta_bancaria = cb.id_cuenta_bancaria
                LEFT JOIN proyectos p ON dcb.kid_proyecto = p.id_proyecto 
                WHERE dcb.kid_estatus = 1 AND dcb.id_detalle_cuenta_bancaria  = :idDetallecuentaBancaria ";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':idDetallecuentaBancaria', $elementID);
                $resultado->execute();
                $data = $resultado->fetch(PDO::FETCH_ASSOC);

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;

                case 'compras_cuentas_bancarias':
                    $consultaselect = "SELECT ccb.id_compra_cuenta_bancaria ,
                        cb.cuenta_bancaria AS kid_cuenta_bancaria,
                        p.proyecto AS kid_proyecto,
                        ccb.monto_total,
                        ccb.monto_neto,
                        ccb.fecha_creacion
                    FROM compras_cuentas_bancarias ccb
                    LEFT JOIN cuentas_bancarias cb ON ccb.kid_cuenta_bancaria = cb.id_cuenta_bancaria
                    LEFT JOIN proyectos p ON ccb.kid_proyecto = p.id_proyecto 
                    WHERE ccb.kid_estatus = 1 AND ccb.id_compra_cuenta_bancaria  = :idCompraCuentaBancaria ";
                    $resultado = $conexion->prepare($consultaselect);
                    $resultado->bindParam(':idCompraCuentaBancaria', $elementID);
                    $resultado->execute();
                    $data = $resultado->fetch(PDO::FETCH_ASSOC);
    
                    // Verifica si se encontraron datos
                    if ($data) {
                        print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                    } else {
                        print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                    }
                    break;

                case 'facturas_clientes':
                    $consultaselect = "SELECT
                    c.nombre AS kid_cliente,
                    p.proyecto AS kid_proyecto,
                    cb.cuenta_bancaria AS kid_cuenta_bancaria,
                    monto_total,
                    monto_neto,
                    fecha_factura
                    FROM facturas_clientes fc
                    LEFT JOIN proyectos p ON fc.kid_proyecto = p.id_proyecto 
                    LEFT JOIN clientes c ON fc.kid_cliente = c.id_cliente 
                    LEFT JOIN cuentas_bancarias cb ON fc.kid_cuenta_bancaria = cb.id_cuenta_bancaria
                    WHERE fc.kid_estatus !=3 AND fc.id_factura_cliente = :id";
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

                case 'monedas':
                    $consultaselect = "SELECT
                        orden,
                        moneda,
                        simbolo,
                        codigo,
                        pordefecto,
                        fecha_creacion
                        FROM monedas WHERE kid_estatus !=3 AND id_moneda = :id";
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

                case 'tipos_reportes_cb':
                    $consultaselect = "SELECT
                    orden,
                    tipo_reporte_cb,
                    pordefecto,
                    fecha_creacion
                    FROM tipos_reportes_cb WHERE kid_estatus !=3 AND id_tipo_reporte_cb = :id";
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

                case 'observaciones_reportes_cb':
                    $consultaselect = "SELECT
                    kid_reporte_cuenta_bancaria,
                    observacion,
                    fecha_creacion
                    FROM observaciones_reportes_cb WHERE kid_estatus !=3 AND id_observacion_reporte_cb = :id";
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