<?php
$objeto = new Conexion();
$conexion = $objeto->Conectar();

$data = []; // Inicializa la variable $data

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['modalCRUD']) && isset($_POST['firstColumnValue'])) {
        $modalCRUD = $_POST['modalCRUD'];
        $elementID = $_POST['firstColumnValue'];

        switch ($modalCRUD) {

            case 'cronograma':
                $consultaselect = "SELECT c.*,
                    e.estado AS kid_estado
                FROM clientes c
                LEFT JOIN estados e ON c.kid_estado = e.id_estados 
                WHERE id_cliente = :idCliente";

                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':idCliente', $elementID);
                $resultado->execute();
                $data = $resultado->fetch(PDO::FETCH_ASSOC);
                $data['id_cliente']=null;

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;
            case 'detalles_actividades':
                $permiso = 1;
                if(isset($_POST['opcion'])) {
                    $estatus = GetEstatusLabels();
                    $consultaselect = "SELECT da.id_detalle_actividad, 
                        da.actividad,
                        da.kid_actividad,
                        CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) as kid_personal_asignado,
                        da.kid_estatus,
                        COALESCE(da.fecha_inicial_real, 'Sin Iniciar') AS fecha_inicial_real,
                        CASE 
                            WHEN da.fecha_inicial_real IS NOT NULL AND da.fecha_final_real IS NULL THEN 'Sin Finalizar'
                            ELSE COALESCE(da.fecha_final_real, 'Sin Iniciar')
                        END AS fecha_final_real,
                        COALESCE(a.dias_totales_reales, 0) AS dias_totales_reales,
                        COALESCE(a.horas_totales_reales, 0) AS horas_totales_reales,
                        da.progreso
                    FROM 
                        detalles_actividades da
                    LEFT JOIN actividades a ON da.kid_actividad = a.id_actividad 
                    LEFT JOIN colaboradores u ON da.kid_personal_asignado = u.id_colaborador
                    WHERE da.kid_estatus != 3 AND da.kid_actividad  = :id";
                    $resultado = $conexion->prepare($consultaselect);
                    $resultado->bindParam(':id', $elementID);
                    $resultado->execute();
                    $data['data'] = $resultado->fetchAll(PDO::FETCH_NUM);
                    $data['options'] = [];

                }else{
                    $columnas = [
                        1 => "u.email as kid_personal_asignado, da.actividad, da.fecha_inicial, da.fecha_final, da.dias_totales, da.horas_totales, da.grupo_paralelo, da.grupo_seriado, da.nivel_profundidad",
                        2 => "u.email as kid_personal_asignado",
                        3 => "da.actividad, da.fecha_inicial, da.fecha_final, da.dias_totales, da.horas_totales, da.fecha_inicial_real, da.fecha_final_real, da.dias_totales_reales, da.horas_totales_reales"
                    ];
                    
                    if (isset($columnas[$permiso])) {
                        $consultaselect = "SELECT " . $columnas[$permiso] . ",
                            da.kid_actividad
                        FROM 
                            detalles_actividades da
                        LEFT JOIN actividades a ON da.kid_actividad = a.id_actividad 
                        LEFT JOIN colaboradores u ON da.kid_personal_asignado = u.id_colaborador
                        WHERE da.kid_estatus != 3 AND da.id_detalle_actividad  = :id";
                        
                        $resultado = $conexion->prepare($consultaselect);
                        $resultado->bindParam(':id', $elementID);
                        $resultado->execute();
                        $data = $resultado->fetch(PDO::FETCH_ASSOC);
                    }
                    
                }
                if ($permiso != 3 && $data['kid_personal_asignado'] == '') {
                    $kid_actividad = $data['kid_actividad'];
                    $consulta = "SELECT 
                                    c.email,
                                    CONCAT(c.nombre,' ',c.apellido_paterno,' ',c.apellido_materno) AS nombre_completo
                                FROM actividades a 
                                LEFT JOIN 
                                    planeaciones_rrhh prh ON a.kid_proyecto = prh.kid_proyecto
                                LEFT JOIN 
                                    detalles_planeaciones_rrhh dprh ON dprh.kid_planeaciones_rrhh = prh.id_planeacion_rrhh
                                LEFT JOIN 
                                    colaboradores c ON dprh.kid_personal = c.id_colaborador   
                                WHERE 
                                    a.kid_estatus != 3 AND a.id_actividad = $kid_actividad 
                                ORDER BY 
                                    c.nombre ASC";
                    $resultado = $conexion->prepare($consulta);
                    $resultado->execute();
                    $data_kid_personal_asignado = $resultado->fetchAll(PDO::FETCH_ASSOC);
                    $data_kid_personal_asignado = array_map(fn($item) => [
                        'valor'=> $item['email'],
                        'text' => $item['nombre_completo'],
                        'pordefecto' => 0
                    ], $data_kid_personal_asignado);
                    

                    
                    $data['kid_personal_asignado'] = $data_kid_personal_asignado;
                }


                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'justificaciones_actividades':
                if(isset($_POST['opcion'])) {
                    $consultaselect = "SELECT dpc.id_detalle_planeacion_compras, 
                        dpc.kid_planeacion_compras,
                        a.articulo as kid_articulo,
                        dpc.cantidad_solicitada,
                        dpc.cantidad_en_almacen,
                        dpc.cantidad_a_comprar,
                        dpc.fecha_creacion
                    FROM 
                        detalles_planeaciones_compras dpc
                    LEFT JOIN articulos a ON dpc.kid_articulo = a.id_articulo
                    WHERE dpc.kid_estatus != 3 AND dpc.kid_planeacion_compras  = :id";
                    $resultado = $conexion->prepare($consultaselect);
                    $resultado->bindParam(':id', $elementID);
                    $resultado->execute();
                    $data['data'] = $resultado->fetchAll(PDO::FETCH_NUM);
                    $data['options'] = [];

                }else{
                    $consultaselect = "SELECT ja.id_justificacion_actividad, 
                        ja.kid_actividad,
                        da.actividad as kid_detalle_actividad,
                        u.email as kid_responsable,
                        ja.justificacion,
                        ja.latitud,
                        ja.longitud
                    FROM 
                        justificaciones_actividades ja
                    LEFT JOIN detalles_actividades da ON ja.kid_detalle_actividad = da.id_detalle_actividad
                    LEFT JOIN colaboradores u ON ja.kid_responsable = u.id_colaborador
                    WHERE da.kid_estatus != 3 and ja.id_justificacion_actividad  = :id";
                    $resultado = $conexion->prepare($consultaselect);
                    $resultado->bindParam(':id', $elementID);
                    $resultado->execute();
                    $data = $resultado->fetch(PDO::FETCH_ASSOC);
                    $data['id_detalle_planeacion_compras'] = null;
                }

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;


            case 'evidencia_actividades':
                if(isset($_POST['opcion'])) {
                    $consultaselect = "SELECT dpc.id_detalle_planeacion_compras, 
                        dpc.kid_planeacion_compras,
                        a.articulo as kid_articulo,
                        dpc.cantidad_solicitada,
                        dpc.cantidad_en_almacen,
                        dpc.cantidad_a_comprar,
                        dpc.fecha_creacion
                    FROM 
                        detalles_planeaciones_compras dpc
                    LEFT JOIN articulos a ON dpc.kid_articulo = a.id_articulo
                    WHERE dpc.kid_estatus != 3 AND dpc.kid_planeacion_compras  = :id";
                    $resultado = $conexion->prepare($consultaselect);
                    $resultado->bindParam(':id', $elementID);
                    $resultado->execute();
                    $data['data'] = $resultado->fetchAll(PDO::FETCH_NUM);
                    $data['options'] = [];

                }else{
                    $consultaselect = "SELECT ea.id_evidencia_actividad, 
                        da.actividad,
                        ea.ruta_nombre_archivo,
                        CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) as kid_responsable,
                        ea.fecha_creacion
                    FROM 
                        evidencia_actividades ea
                    LEFT JOIN detalles_actividades da ON ea.kid_detalle_actividad = da.id_detalle_actividad
                    LEFT JOIN colaboradores u ON da.kid_personal_asignado = u.id_colaborador
                    WHERE da.kid_estatus != 3 and ea.id_evidencia_actividad = :id";
                    $resultado = $conexion->prepare($consultaselect);
                    $resultado->bindParam(':id', $elementID);
                    $resultado->execute();
                    $data = $resultado->fetch(PDO::FETCH_ASSOC);

                    // Verificar si existe la ruta
                    if (!empty($data['ruta_nombre_archivo']) && file_exists($data['ruta_nombre_archivo'])) {
                        // Leer el contenido de la imagen
                        $imageContent = file_get_contents($data['ruta_nombre_archivo']);
                        
                        // Codificar la imagen en Base64
                        $base64Image = base64_encode($imageContent);
                        
                        // Agregar al array de datos
                        $data['imgs'] = ["data:image/" . pathinfo($data['ruta_nombre_archivo'], PATHINFO_EXTENSION) . ";base64," . $base64Image];
                    } else {
                        $data['imgs'] = null; // O manejar el caso de error
                    }

                    // Devolver la respuesta como JSON
                    header('Content-Type: application/json');
                    //echo json_encode($data);

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