<?php
$objeto = new Conexion();
$conexion = $objeto->Conectar();

$data = []; // Inicializa la variable $data

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['modalCRUD']) && isset($_POST['firstColumnValue'])) {
        $modalCRUD = $_POST['modalCRUD'];
        $elementID = $_POST['firstColumnValue'];

        switch ($modalCRUD) {
            case 'colaboradores':
                $consultaselect = "SELECT u.*,
                    tu.tipo_usuario AS kid_tipo_usuario,
                    e.estado AS kid_estado,
                    ie.internos_externos as kid_internos_externos,
                    tc.tipo_costo as kid_tipo_cantidad
                FROM colaboradores u
                LEFT JOIN tipos_usuario tu ON u.kid_tipo_usuario = tu.id_tipo_usuario
                LEFT JOIN estados e ON u.kid_estado = e.id_estados 
                LEFT JOIN internos_externos ie ON u.kid_internos_externos = ie.id_internos_externos
                LEFT JOIN tipos_costo tc ON u.kid_tipo_cantidad = tc.id_tipo_costo
                WHERE id_colaborador = :id";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':id', $elementID);
                $resultado->execute();
                $data = $resultado->fetch(PDO::FETCH_ASSOC);
                $data['id_colaborador']=null;

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'ocupaciones_th':
                $consultaselect = "SELECT
                    c.email as kid_colaborador,
                    p.proyecto as kid_proyecto,
                    bp.bolsa_proyecto as kid_bolsa_proyecto,
                    o.estampa_inicio,
                    o.estampa_fin,
                    ie.internos_externos as kid_internos_externos,
                    tp.tipo_costo as kid_tipos_cantidad,
                    o.cantidad_periodo,
                    CASE 
                        WHEN o.finalizado = 1 THEN 'SÍ'  
                        ELSE 'NO' 
                    END AS finalizado,
                    o.fecha_creacion
                FROM 
                    ocupaciones_th o
                LEFT JOIN colaboradores c ON o.kid_colaborador = c.id_colaborador
                LEFT JOIN bolsas_proyectos bp ON o.kid_bolsa_proyecto = bp.id_bolsa_proyecto
                LEFT JOIN proyectos p ON o.kid_proyecto = p.id_proyecto
                LEFT JOIN internos_externos ie ON o.kid_internos_externos = ie.id_internos_externos 
                LEFT JOIN tipos_costo tp ON o.kid_tipos_cantidad = tp.id_tipo_costo 
                WHERE o.kid_estatus != 3 and  id_ocupacion_th = :id";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':id', $elementID);
                $resultado->execute();
                $data = $resultado->fetch(PDO::FETCH_ASSOC);
                $data['id_colaborador']=null;

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'editar_asistencias_th':
                $consultaselect = "SELECT
                    c.email as kid_colaborador,
                    a.estampa_entrada,
                    a.estampa_salida,
                    ie.internos_externos as kid_internos_externos,
                    tp.tipo_costo as kid_tipos_cantidad,
                    a.cantidad_periodo,
                    a.fecha_creacion
                FROM 
                    asistencias_th a
                LEFT JOIN colaboradores c ON a.kid_colaborador = c.id_colaborador
                LEFT JOIN internos_externos ie ON a.kid_internos_externos = ie.id_internos_externos 
                LEFT JOIN tipos_costo tp ON a.kid_tipos_cantidad = tp.id_tipo_costo 
                WHERE a.kid_estatus != 3 and id_asistencia_th = :id";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':id', $elementID);
                $resultado->execute();
                $data = $resultado->fetch(PDO::FETCH_ASSOC);
                $data['id_colaborador']=null;

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'adicionales_asistencias_th':
                $consultaselect = "SELECT
                    c.email as kid_colaborador,
                    ta.tipo_adicional_th as kid_tipo_adicional_th,
                    aa.comentario
                FROM 
                    adicionales_asistencias_th aa
                LEFT JOIN colaboradores c ON aa.kid_colaborador = c.id_colaborador
                LEFT JOIN internos_externos ie ON aa.kid_interno_externo = ie.id_internos_externos 
                LEFT JOIN tipos_adicionales_th ta ON aa.kid_tipo_adicional_th = ta.id_tipo_adicional_th
                LEFT JOIN tipos_costo tp ON aa.kid_tipos_cantidad = tp.id_tipo_costo  
                WHERE aa.kid_estatus != 3 AND aa.id_asistencia_th  = :id";
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

            case 'tipos_adicionales_th':
                $consultaselect = "SELECT orden,
                tipo_adicional_th,
                pordefecto
                FROM tipos_adicionales_th
                WHERE kid_estatus !=3 AND id_tipo_adicional_th  = :id";
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

            case 'tipos_usuario':
                $consultaselect = "SELECT tipo_usuario,
                    descripcion, 
                    pordefecto,
                    login
                FROM tipos_usuario
                WHERE kid_estatus != 3 AND id_tipo_usuario != 1 AND id_tipo_usuario = :id";
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

            case 'asignar_permisos':

                $data = GetAllowPermsList($elementID);

                $data = array_fill_keys($data, 1);
                if(!$data){
                    $data['empty'] = [];
                }

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