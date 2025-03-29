<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'].'/helpers/main.php'; 
$objeto = new Conexion();
$conexion = $objeto->Conectar();

$data = []; // Inicializa la variable $data

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['modalCRUD']) && isset($_POST['firstColumnValue'])) {
        $modalCRUD = $_POST['modalCRUD'];
        $elementID = $_POST['firstColumnValue'];

        switch ($modalCRUD) {
            case 'estados':
                $consultaselect = "SELECT e.id_estados , 
                        e.orden, 
                        e.estado, 
                        e.simbolo, 
                        e.pordefecto,
                        p.pais AS kid_pais ,  -- Cambiamos el alias a kid_categoria
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
                $consultaselect = "SELECT m.id_municipio   , 
                    m.orden, 
                    m.municipio, 
                    m.pordefecto,
                    e.estado as kid_estado,
                    p.pais as pais,  -- Ahora esta columna está después de pordefecto
                    m.fecha_creacion
                FROM municipios m
                JOIN estados e ON m.kid_estado = e.id_estados
                JOIN paises p ON e.kid_pais = p.id_pais
                WHERE m.kid_estatus = 1 AND m.id_municipio   = :idMunicipio ";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':idMunicipio', $elementID);
                $resultado->execute();
                $data = $resultado->fetch(PDO::FETCH_ASSOC);

                // Verifica si se encontraron datos
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