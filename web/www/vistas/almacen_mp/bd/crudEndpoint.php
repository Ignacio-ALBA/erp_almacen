<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once $_SERVER['DOCUMENT_ROOT'] . '/helpers/main.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

$data = []; // Inicializa la variable $data

// --- INICIO: ENDPOINTS API GET tipo AJAX ---
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['api'])) {
    switch ($_GET['api']) {
        case 'get_detalles_recepcion_mp':
            header('Content-Type: application/json; charset=utf-8');
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            $response = ['ok' => false, 'detalles' => []];
            if ($id > 0) {
                // Revisa que estos campos existan en tu tabla detalles_recepciones_mp
                $sql = "SELECT kid_articulo, peso_estimado, peso_real, valor_codigoqr 
                FROM detalles_recepciones_mp 
                WHERE kid_recepcion_mp = :id AND kid_estatus != 3";
                $stmt = $conexion->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $response['ok'] = true;
                $response['detalles'] = $detalles;
            }
            echo json_encode($response);
            exit;

            echo json_encode($response);
            print json_encode(['status' => 'error', 'message' => 'Operación no válida'], JSON_UNESCAPED_UNICODE);
            break;
        case 'get_detalles_orden':
            header('Content-Type: application/json; charset=utf-8');
            $response = ['ok' => false, 'detalles' => [], 'nombre_orden' => '', 'proveedor' => ''];

            $orderId = isset($_GET['id']) ? intval($_GET['id']) : 0;
            if ($orderId > 0) {
                // Detalles de insumos
                $sql = "SELECT doc.kid_articulo, a.articulo AS nombre_articulo, doc.cantidad
                        FROM detalles_ordenes_compras doc
                        LEFT JOIN articulos a ON doc.kid_articulo = a.id_articulo
                        WHERE doc.kid_estatus != 3 AND doc.kid_orden_compras = :id";
                $stmt = $conexion->prepare($sql);
                $stmt->bindParam(':id', $orderId, PDO::PARAM_INT);
                $stmt->execute();
                $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Nombre de la orden y proveedor
                $stmt2 = $conexion->prepare(
                    "SELECT oc.orden_compras, prov.proveedor
                     FROM ordenes_compras oc 
                     LEFT JOIN proveedores prov ON oc.kid_proveedor = prov.id_proveedor
                     WHERE oc.id_orden_compras = :id"
                );
                $stmt2->bindParam(':id', $orderId, PDO::PARAM_INT);
                $stmt2->execute();
                $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);

                $response['ok'] = true;
                $response['detalles'] = $detalles;
                $response['nombre_orden'] = $row2['orden_compras'] ?? '';
                $response['proveedor'] = $row2['proveedor'] ?? '';
            }
            echo json_encode($response);
            exit;
            break;
        case 'get_pesos_tarimas':
            header('Content-Type: application/json; charset=utf-8');
            $response = ['ok' => false, 'pesos' => []];

            try {
                // Si tienes una tabla con pesos de tarimas, utiliza esta consulta
                $sql = "SELECT id, descripcion, valor FROM pesos_tarimas WHERE kid_estatus = 1 ORDER BY descripcion";
                $stmt = $conexion->prepare($sql);
                $stmt->execute();
                $pesos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $response['ok'] = true;
                $response['pesos'] = $pesos;
            } catch (Exception $e) {
                // Si no tienes una tabla, proporciona algunos valores predeterminados
                $response['ok'] = true;
                $response['pesos'] = [
                    ['id' => 1, 'descripcion' => 'Tarima estándar', 'valor' => 25],
                    ['id' => 2, 'descripcion' => 'Tarima ligera', 'valor' => 15],
                    ['id' => 3, 'descripcion' => 'Tarima reforzada', 'valor' => 35]
                ];
            }

            echo json_encode($response);
            exit;
            break;

        case 'get_almacenes':
            header('Content-Type: application/json; charset=utf-8');
            $response = ['ok' => false, 'data' => []];

            try {
                $sql = "SELECT id_almacen AS kid_almacen, almacen AS nombre 
                    FROM almacenes 
                    WHERE kid_estatus = 1";
                $stmt = $conexion->prepare($sql);
                $stmt->execute();
                $almacenes = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $response['ok'] = true;
                $response['data'] = $almacenes;
            } catch (Exception $e) {
                $response['message'] = 'Error al obtener almacenes: ' . $e->getMessage();
            }

            echo json_encode($response);
            exit;
            break;

        case 'get_insumo_peso':
            header('Content-Type: application/json; charset=utf-8');
            $response = ['ok' => false, 'peso' => null];

            try {
                $sql = "SELECT valor FROM configuraciones WHERE parametro ='peso_tarima' and id_status = 1";
                $stmt = $conexion->prepare($sql);
                $stmt->execute();
                $peso = $stmt->fetchColumn();

                if ($peso !== false) {
                    $response['ok'] = true;
                    $response['peso'] = floatval($peso);
                } else {
                    $response['message'] = 'Insumo no encontrado o sin peso estimado';
                }
            } catch (Exception $e) {
                $response['message'] = 'Error al obtener peso del insumo: ' . $e->getMessage();
            }

            echo json_encode($response);
            exit;
            break;
        case 'get_ubicaciones':
            header('Content-Type: application/json; charset=utf-8');
            $response = ['ok' => false, 'data' => []];

            if (!isset($_GET['id_almacen'])) {
                $response['message'] = 'ID de almacén requerido';
                echo json_encode($response);
                exit;
            }

            try {
                $id_almacen = intval($_GET['id_almacen']);
                $sql = "SELECT id_ubicacion AS kid_locacion_almacen, 
                           codigo_localizacion AS nombre 
                    FROM ubicacion_almacen 
                    WHERE kid_almacen = :id 
                    AND kid_estatus = 1";
                $stmt = $conexion->prepare($sql);
                $stmt->bindParam(':id', $id_almacen, PDO::PARAM_INT);
                $stmt->execute();
                $ubicaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $response['ok'] = true;
                $response['data'] = $ubicaciones;
            } catch (Exception $e) {
                $response['message'] = 'Error al obtener ubicaciones: ' . $e->getMessage();
            }

            echo json_encode($response);
            exit;
            break;
            if (isset($_GET['api']) && $_GET['api'] === 'get_peso_tarima_by_detalle') {
                header('Content-Type: application/json');
                require_once "conexion.php"; // Asegúrate de que este archivo crea la variable $conexion (mysqli o PDO)
                $id_detalle = isset($_GET['id_detalle_recepcion_compras']) ? intval($_GET['id_detalle_recepcion_compras']) : 0;
                $peso_tarima = null;
                $descripcion = null;

                if ($id_detalle > 0) {
                    $sql = "SELECT peso_tarima FROM detalles_recepciones_compras WHERE id_detalle_recepcion_compras = ?";
                    if ($stmt = $conexion->prepare($sql)) {
                        $stmt->bind_param('i', $id_detalle);
                        $stmt->execute();
                        $stmt->bind_result($peso_tarima);
                        if ($stmt->fetch()) {
                            // Si necesitas una descripción, puedes traerla también de la tabla si existe ese campo
                            $descripcion = "Peso tarima para detalle ID $id_detalle";
                        }
                        $stmt->close();
                    }
                }

                echo json_encode([
                    'peso_tarima' => $peso_tarima ? floatval($peso_tarima) : null,
                    'descripcion' => $descripcion,
                    'success' => $peso_tarima !== null
                ]);
                exit();
            }

        // Aquí puedes agregar más endpoints GET tipo API...
    }
    // Si llegamos aquí, ya respondimos al API, detenemos el script.
    exit;
}
// --- FIN: ENDPOINTS API GET tipo AJAX ---

// --- INICIO: Lógica POST clásica (modalCRUD) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['modalCRUD']) && isset($_POST['firstColumnValue'])) {
        $modalCRUD = $_POST['modalCRUD'];
        $elementID = $_POST['firstColumnValue'];

        switch ($modalCRUD) {
            case 'estados':
                $consultaselect = "SELECT e.id_estados, 
                        e.orden, 
                        e.estado, 
                        e.simbolo, 
                        e.pordefecto,
                        p.pais AS kid_pais,  
                        e.fecha_creacion  
                    FROM estados e
                    JOIN paises p ON e.kid_pais  = p.id_pais
                    WHERE e.kid_estatus = 1 AND e.id_estados  = :idEstados ";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':idEstados', $elementID);
                $resultado->execute();
                $data = $resultado->fetch(PDO::FETCH_ASSOC);

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'municipios':
                $consultaselect = "SELECT m.id_municipio, 
                    m.orden, 
                    m.municipio, 
                    m.pordefecto,
                    e.estado as kid_estado,
                    p.pais as pais,  
                    m.fecha_creacion
                FROM municipios m
                JOIN estados e ON m.kid_estado = e.id_estados
                JOIN paises p ON e.kid_pais = p.id_pais
                WHERE m.kid_estatus = 1 AND m.id_municipio = :idMunicipio ";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':idMunicipio', $elementID);
                $resultado->execute();
                $data = $resultado->fetch(PDO::FETCH_ASSOC);

                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'empresas':
                $consultaselect = "SELECT e.id_empresa, 
                        e.empresa, 
                        e.razon_social, 
                        e.rfc, 
                        u.email as kid_propietario,
                        u2.email as kid_representante_legal,
                        u3.email as kid_representante_tecnico,
                        u4.email as kid_representante_administrativo,
                        e.fecha_creacion
                    FROM 
                        empresas e
                    LEFT JOIN 
                        colaboradores u ON e.kid_propietario = u.id_colaborador 
                    LEFT JOIN 
                        colaboradores u2 ON e.kid_representante_legal = u2.id_colaborador 
                    LEFT JOIN 
                        colaboradores u3 ON e.kid_representante_tecnico = u3.id_colaborador 
                    LEFT JOIN 
                        colaboradores u4 ON e.kid_representante_administrativo = u4.id_colaborador
                    WHERE e.kid_estatus = 1 AND e.id_empresa   = :idEmpresa ";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':idEmpresa', $elementID);
                $resultado->execute();
                $data = $resultado->fetch(PDO::FETCH_ASSOC);

                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;
        }
    } else {
        print json_encode(['status' => 'error', 'message' => 'Faltan datos requeridos'], JSON_UNESCAPED_UNICODE);
    }
} else if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    print json_encode(['status' => 'error', 'message' => 'Método no permitido'], JSON_UNESCAPED_UNICODE);
}
// --- FIN: Lógica POST clásica (modalCRUD) ---
?>