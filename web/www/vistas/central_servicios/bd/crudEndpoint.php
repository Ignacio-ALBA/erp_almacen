<?php
$objeto = new Conexion();
$conexion = $objeto->Conectar();

$data = []; // Inicializa la variable $data

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['modalCRUD']) && isset($_POST['firstColumnValue'])) {
        $modalCRUD = $_POST['modalCRUD'];
        $elementID = $_POST['firstColumnValue'];

        switch ($modalCRUD) {
            case 'central_mp':
                // Consulta para obtener los detalles de un registro específico
                $consultaselect = "SELECT da.id_detalle_almacen,
                    a.almacen AS kid_almacen,
                    ar.articulo AS kid_articulo,
                    da.cantidad,
                    da.peso,
                    da.lo_lo,
                    da.lo,
                    da.high,
                    da.high_high,
                    CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) AS kid_creacion,
                    da.fecha_creacion,
                    e.estatus AS kid_estatus
                FROM detalles_almacenes da
                LEFT JOIN almacenes a ON da.kid_almacen = a.id_almacen
                LEFT JOIN articulos ar ON da.kid_articulo = ar.id_articulo
                LEFT JOIN colaboradores u ON da.kid_creacion = u.id_colaborador
                LEFT JOIN estatus e ON da.kid_estatus = e.id_estatus
                WHERE da.kid_estatus != 3 AND da.id_detalle_almacen = :id";
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

            case 'GETAlmacenes':
                // Consulta para obtener la lista de almacenes
                $consultaselect = "SELECT id_almacen, almacen FROM almacenes WHERE kid_estatus != 3 ORDER BY almacen ASC";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->execute();
                $data = $resultado->fetchAll(PDO::FETCH_ASSOC);

                $data = array_map(fn($item) => [
                    'valor' => $item['id_almacen'],
                    'text' => $item['almacen'],
                    'pordefecto' => 0,
                ], $data);

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'GETArticulos':
                // Consulta para obtener la lista de artículos
                $consultaselect = "SELECT id_articulo, articulo FROM articulos WHERE kid_estatus != 3 ORDER BY articulo ASC";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->execute();
                $data = $resultado->fetchAll(PDO::FETCH_ASSOC);

                $data = array_map(fn($item) => [
                    'valor' => $item['id_articulo'],
                    'text' => $item['articulo'],
                    'pordefecto' => 0,
                ], $data);

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