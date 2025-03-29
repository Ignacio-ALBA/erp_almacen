<?php
$objeto = new Conexion();
$conexion = $objeto->Conectar();

$data = []; // Inicializa la variable $data

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['modalCRUD']) && isset($_POST['firstColumnValue'])) {
        $modalCRUD = $_POST['modalCRUD'];
        $elementID = $_POST['firstColumnValue'];

        switch ($modalCRUD) {
            case 'proyectos':
                $consultaselect = "SELECT p.*, 
                    b.bolsa_proyecto as kid_bolsa_proyecto,
                    u.email as kid_responsable
                FROM proyectos p
                LEFT JOIN bolsas_proyectos b ON p.kid_bolsa_proyecto = b.id_bolsa_proyecto
                LEFT JOIN colaboradores u ON p.kid_responsable = u.id_colaborador 
                WHERE p.kid_estatus = 1 AND p.id_proyecto  = :idProyecto ";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':idProyecto', $elementID);
                $resultado->execute();
                $data = $resultado->fetch(PDO::FETCH_ASSOC);
                $data['id_proyecto'] = null;

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;


            case 'detalles_proyectos':
                $consultaselect = "SELECT d.*, 
                        p.proyecto as kid_proyecto,
                        u.email as kid_responsable
                FROM detalles_proyectos d
                LEFT JOIN proyectos p ON d.kid_proyecto = p.id_proyecto  
                LEFT JOIN colaboradores u ON d.kid_responsable = u.id_colaborador 
                WHERE d.kid_estatus = 1 AND d.id_detalle_proyecto  = :idDetalleProyecto ";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':idDetalleProyecto', $elementID);
                $resultado->execute();
                $data = $resultado->fetch(PDO::FETCH_ASSOC);
                $data['id_detalle_proyecto'] = null;

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'bolsas_proyectos':
                $consultaselect = "SELECT b.*, 
                    c.nombre as kid_cliente
            FROM bolsas_proyectos b
            LEFT JOIN clientes c ON b.kid_cliente = c.id_cliente  
            WHERE b.kid_estatus = 1 AND b.id_bolsa_proyecto  = :idbolsaProyecto ";
            $resultado = $conexion->prepare($consultaselect);
            $resultado->bindParam(':idbolsaProyecto', $elementID);
            $resultado->execute();
            $data = $resultado->fetch(PDO::FETCH_ASSOC);
            $data['id_bolsa_proyecto'] = null;

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