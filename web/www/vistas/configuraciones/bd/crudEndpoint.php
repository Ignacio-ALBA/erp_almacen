<?php
$objeto = new Conexion();
$conexion = $objeto->Conectar();

$data = []; // Inicializa la variable $data

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['modalCRUD']) && isset($_POST['firstColumnValue'])) {
        $modalCRUD = $_POST['modalCRUD'];
        $elementID = $_POST['firstColumnValue'];

        switch ($modalCRUD) {
            case 'clientes':
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
            case 'planeaciones_compras':
                if(isset($_POST['opcion'])) {
                    $estatus = GetEstatusLabels();
                    $caseEstatus = "CASE \n";
                    foreach ($estatus as $key => $value) {
                        $caseEstatus .= "    WHEN pc.kid_estatus = $key THEN '$value'\n";
                    }
                    $caseEstatus .= "    ELSE 'Desconocido' \nEND AS kid_estatus";
                    $consultaselect = "SELECT pc.id_planeacion_compras , 
                        bp.bolsa_proyecto, 
                        p.proyecto,
                        c.nombre,
                        $caseEstatus,
                        CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) as kid_creacion,
                        pc.fecha_creacion
                    FROM 
                        planeaciones_compras pc
                    LEFT JOIN clientes c ON pc.kid_cliente = c.id_cliente
                    LEFT JOIN proyectos p ON pc.kid_proyecto = p.id_proyecto
                    LEFT JOIN bolsas_proyectos bp ON pc.kid_bolsa_proyecto = bp.id_bolsa_proyecto
                    LEFT JOIN colaboradores u ON pc.kid_creacion = u.id_colaborador
                    WHERE 
                        pc.kid_estatus !=3 AND pc.kid_cliente  = :id";
                    $resultado = $conexion->prepare($consultaselect);
                    $resultado->bindParam(':id', $elementID);
                    $resultado->execute();
                    $data['data'] = $resultado->fetchAll(PDO::FETCH_NUM);
                    $data['options'] = [];

                }else{
                    $estatus = GetEstatusLabels();
                    $caseEstatus = "CASE \n";
                    foreach ($estatus as $key => $value) {
                        $caseEstatus .= "    WHEN pc.kid_estatus = $key THEN '$value'\n";
                    }
                    $caseEstatus .= "    ELSE 'Desconocido' \nEND AS kid_estatus";
                    $consultaselect = "SELECT pc.*, 
                        bp.bolsa_proyecto as kid_bolsa_proyecto, 
                        p.proyecto as kid_proyecto,
                        c.nombre as kid_cliente,
                        $caseEstatus,
                        pc.kid_creacion,
                        pc.fecha_creacion
                    FROM 
                        planeaciones_compras pc
                    LEFT JOIN clientes c ON pc.kid_cliente = c.id_cliente
                    LEFT JOIN proyectos p ON pc.kid_proyecto = p.id_proyecto
                    LEFT JOIN bolsas_proyectos bp ON pc.kid_bolsa_proyecto = bp.id_bolsa_proyecto
                    LEFT JOIN colaboradores u ON pc.kid_creacion = u.id_colaborador
                    WHERE 
                        pc.kid_estatus !=3 AND pc.id_planeacion_compras  = :id";
                    $resultado = $conexion->prepare($consultaselect);
                    $resultado->bindParam(':id', $elementID);
                    $resultado->execute();
                    $data = $resultado->fetch(PDO::FETCH_ASSOC);
                    $data['id_planeacion_compras'] = null;
                }

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'detalles_planeaciones_compras':
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
                    $consultaselect = "SELECT dpc.*, 
                        a.articulo as kid_articulo
                    FROM 
                        detalles_planeaciones_compras dpc
                    LEFT JOIN articulos a ON dpc.kid_articulo = a.id_articulo
                    WHERE dpc.kid_estatus != 3 AND dpc.id_detalle_planeacion_compras  = :id";
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


            case 'cambios_planeaciones_compras':
                $consultaselect = "SELECT kid_registro_tabla,
                t.tablas as kid_tabla,
                cpc.cambio
                FROM 
                    cambios_planeaciones_compras cpc
                LEFT JOIN tablas t ON cpc.kid_tabla = t.id_tabla
                LEFT JOIN colaboradores u ON cpc.kid_creacion = u.id_colaborador
                WHERE cpc.kid_estatus != 3 AND cpc.id_cambio_recepcion_compras = :id";
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

            case 'tablas':
                $consultaselect = "SELECT t.id_tabla, t.tablas, m.modulo as kid_modulo, t.descripcion
                FROM tablas t
                LEFT JOIN modulos m ON m.id_modulo = t.kid_modulo
                WHERE t.kid_estatus != 3 AND t.id_tabla = :id";
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

            case 'modulos':
                $consultaselect = "SELECT id_modulo, modulo,descripcion
                FROM modulos WHERE kid_estatus != 3 AND id_modulo = :id";
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


            case 'planeaciones_recursos_humanos':
                if(isset($_POST['opcion'])) {
                    $estatus = GetEstatusLabels();
                    $caseEstatus = "CASE \n";
                    foreach ($estatus as $key => $value) {
                        $caseEstatus .= "    WHEN prh.kid_estatus = $key THEN '$value'\n";
                    }
                    $caseEstatus .= "    ELSE 'Desconocido' \nEND AS kid_estatus";
                    $consultaselect = "SELECT prh.id_planeacion_rrhh , 
                        bp.bolsa_proyecto, 
                        p.proyecto,
                        c.nombre,
                        prh.cantidad_internos,
                        prh.cantidad_externos,
                        $caseEstatus,
                        CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) as kid_creacion,
                        prh.fecha_creacion
                    FROM planeaciones_rrhh prh
                    LEFT JOIN clientes c ON prh.kid_cliente = c.id_cliente
                    LEFT JOIN proyectos p ON prh.kid_proyecto = p.id_proyecto
                    LEFT JOIN bolsas_proyectos bp ON prh.kid_bolsa_proyecto = bp.id_bolsa_proyecto
                    LEFT JOIN colaboradores u ON prh.kid_creacion = u.id_colaborador
                    WHERE prh.kid_estatus !=3 AND prh.kid_cliente  = :id";
                    $resultado = $conexion->prepare($consultaselect);
                    $resultado->bindParam(':id', $elementID);
                    $resultado->execute();
                    $data['data'] = $resultado->fetchAll(PDO::FETCH_NUM);
                    $data['options'] = [];

                }else{
                    $consultaselect = "SELECT prh.*, 
                        bp.bolsa_proyecto as kid_bolsa_proyecto, 
                        p.proyecto as kid_proyecto,
                        c.nombre as kid_cliente,
                        CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) as kid_creacion
                    FROM 
                        planeaciones_rrhh prh
                    LEFT JOIN clientes c ON prh.kid_cliente = c.id_cliente
                    LEFT JOIN proyectos p ON prh.kid_proyecto = p.id_proyecto
                    LEFT JOIN bolsas_proyectos bp ON prh.kid_bolsa_proyecto = bp.id_bolsa_proyecto
                    LEFT JOIN colaboradores u ON prh.kid_creacion = u.id_colaborador
                    WHERE 
                        prh.kid_estatus !=3 AND prh.id_planeacion_rrhh  = :id";
                    $resultado = $conexion->prepare($consultaselect);
                    $resultado->bindParam(':id', $elementID);
                    $resultado->execute();
                    $data = $resultado->fetch(PDO::FETCH_ASSOC);
                    $data['id_planeacion_rrhh'] = null;
                }

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;


            case 'detalles_planeaciones_rrhh':
                if(isset($_POST['opcion'])) {
                    $consultaselect = "SELECT dprh.id_detalle_planeaciones_rrhh, 
                        dprh.kid_planeaciones_rrhh,
                        CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) as kid_personal,
                        dprh.costo,
                        dprh.cantidad,
                        tp.tipo_costo as kid_tipo_cantidad,
                        ie.internos_externos as kid_interno_externo,
                        dprh.costo_total,
                        dprh.fecha_creacion
                    FROM 
                        detalles_planeaciones_rrhh dprh
                    LEFT JOIN tipos_costo tp ON dprh.kid_tipo_cantidad = tp.id_tipo_costo
                    LEFT JOIN internos_externos ie ON dprh.kid_interno_externo = ie.id_internos_externos
                    LEFT JOIN colaboradores u ON dprh.kid_personal = u.id_colaborador
                    WHERE dprh.kid_estatus != 3 AND dprh.kid_planeaciones_rrhh  = :id";
                    $resultado = $conexion->prepare($consultaselect);
                    $resultado->bindParam(':id', $elementID);
                    $resultado->execute();
                    $data['data'] = $resultado->fetchAll(PDO::FETCH_NUM);
                    $data['options'] = [];

                }else{
                    $consultaselect = "SELECT dprh.*,
                        u.email as kid_personal,
                        tp.tipo_costo as kid_tipo_cantidad,
                        ie.internos_externos as kid_interno_externo
                    FROM 
                        detalles_planeaciones_rrhh dprh
                    LEFT JOIN tipos_costo tp ON dprh.kid_tipo_cantidad = tp.id_tipo_costo
                    LEFT JOIN internos_externos ie ON dprh.kid_interno_externo = ie.id_internos_externos
                    LEFT JOIN colaboradores u ON dprh.kid_personal = u.id_colaborador
                    WHERE dprh.kid_estatus != 3 AND dprh.id_detalle_planeaciones_rrhh  = :id";
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


            case 'internos_externos':
                $consultaselect = "SELECT orden,
                internos_externos,
                pordefecto
                FROM internos_externos
                WHERE kid_estatus !=3 AND id_internos_externos  = :id";
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

            case 'tipos_costo':
                $consultaselect = "SELECT orden,
                tipo_costo,
                pordefecto
                FROM tipos_costo
                WHERE kid_estatus !=3 AND id_tipo_costo  = :id";
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

            case 'GETProyectoByBolsa':
                /*-------------------- Obtener Tablas Foráneas --------------------*/
                $consultaselect = "SELECT id_bolsa_proyecto FROM bolsas_proyectos WHERE bolsa_proyecto = :Bolsa";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':Bolsa', $elementID);
                $resultado->execute();
                $data = $resultado->fetch(PDO::FETCH_ASSOC);
                $elementID = $data['id_bolsa_proyecto'];
                /*------------------- Fin Obtener Tablas Foráneas ------------------*/

                $consultaselect = "SELECT proyecto FROM proyectos
                    WHERE kid_estatus != 3 AND kid_bolsa_proyecto = :id ORDER BY proyecto ASC";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':id', $elementID);
                $resultado->execute();
                $data = $resultado->fetchAll(PDO::FETCH_ASSOC);

                $data = array_map(fn($item) => [
                    'valor'=> $item['proyecto'],
                    'pordefecto' => 0,
                ], $data);

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'planeaciones_actividades':
                if(isset($_POST['opcion'])) {
                    $estatus = GetEstatusLabels();
                    $caseEstatus = "CASE \n";
                    foreach ($estatus as $key => $value) {
                        $caseEstatus .= "    WHEN pa.kid_estatus = $key THEN '$value'\n";
                    }
                    $caseEstatus .= "    ELSE 'Desconocido' \nEND AS kid_estatus";
                    $consultaselect = "SELECT pa.id_planeacion_actividad, 
                        bp.bolsa_proyecto, 
                        p.proyecto,
                        c.nombre,
                        pa.fecha_inicial,
                        pa.fecha_final,
                        pa.dias_totales,
                        $caseEstatus,
                        CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) as kid_creacion,
                        pa.fecha_creacion
                    FROM 
                        planeaciones_actividades pa
                    LEFT JOIN clientes c ON pa.kid_cliente = c.id_cliente
                    LEFT JOIN proyectos p ON pa.kid_proyecto = p.id_proyecto
                    LEFT JOIN bolsas_proyectos bp ON pa.kid_bolsa_proyecto = bp.id_bolsa_proyecto
                    LEFT JOIN colaboradores u ON pa.kid_creacion = u.id_colaborador
                    WHERE pa.kid_estatus != 3 AND pa.kid_cliente  = :id";
                    $resultado = $conexion->prepare($consultaselect);
                    $resultado->bindParam(':id', $elementID);
                    $resultado->execute();
                    $data['data'] = $resultado->fetchAll(PDO::FETCH_NUM);
                    $data['options'] = [];

                }else{
                    $consultaselect = "SELECT pa.*, 
                        bp.bolsa_proyecto as kid_bolsa_proyecto, 
                        p.proyecto as kid_proyecto,
                        c.nombre as kid_cliente
                    FROM 
                        planeaciones_actividades pa
                    LEFT JOIN clientes c ON pa.kid_cliente = c.id_cliente
                    LEFT JOIN proyectos p ON pa.kid_proyecto = p.id_proyecto
                    LEFT JOIN bolsas_proyectos bp ON pa.kid_bolsa_proyecto = bp.id_bolsa_proyecto
                    WHERE 
                        pa.kid_estatus !=3 AND pa.id_planeacion_actividad  = :id";
                    $resultado = $conexion->prepare($consultaselect);
                    $resultado->bindParam(':id', $elementID);
                    $resultado->execute();
                    $data = $resultado->fetch(PDO::FETCH_ASSOC);
                    $data['id_planeacion_actividad'] = null;
                }

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;


            case 'detalles_planeaciones_actividades':
                if(isset($_POST['opcion'])) {
                    $consultaselect = "SELECT dpa.id_detalle_planeacion_actividad, 
                        dpa.kid_planeacion_actividad,
                        dpa.actividad,
                        dpa.fecha_inicial,
                        dpa.fecha_final,
                        dpa.dias_totales,
                        dpa.fecha_creacion
                    FROM detalles_planeaciones_actividades dpa WHERE dpa.kid_estatus != 3 AND dpa.kid_planeacion_actividad  = :id";
                    $resultado = $conexion->prepare($consultaselect);
                    $resultado->bindParam(':id', $elementID);
                    $resultado->execute();
                    $data['data'] = $resultado->fetchAll(PDO::FETCH_NUM);
                    $data['options'] = [];

                }else{
                    $consultaselect = "SELECT dpa.*
                    FROM detalles_planeaciones_actividades dpa
                    WHERE dpa.kid_estatus != 3 AND dpa.id_detalle_planeacion_actividad = :id";
                    $resultado = $conexion->prepare($consultaselect);
                    $resultado->bindParam(':id', $elementID);
                    $resultado->execute();
                    $data = $resultado->fetch(PDO::FETCH_ASSOC);
                    $data['id_detalle_planeacion_actividad'] = null;
                }

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'GETCantidadArticulos':
                /*-------------------- Obtener Tablas Foráneas --------------------*/
                $elementID = $elementID ? GetIDArticuloByName($elementID) : null;
                /*------------------- Fin Obtener Tablas Foráneas ------------------*/
                $data = [];

                $consultaselect = "SELECT 
                    kid_almacen, 
                    cantidad, 
                    (SELECT SUM(cantidad) FROM detalles_almacenes WHERE kid_articulo = :id AND kid_estatus != 3 ) AS total_cantidad
                    FROM 
                    detalles_almacenes
                    WHERE 
                    kid_articulo = :id AND kid_estatus != 3";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':id', $elementID);
                $resultado->execute();
                $data_almacenes = $resultado->fetchAll(PDO::FETCH_ASSOC);

                $consultaselect = "SELECT SUM(cantidad) FROM detalles_almacenes WHERE kid_articulo = :id AND kid_estatus != 3";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':id', $elementID);
                $resultado->execute();
                $data_total = $resultado->fetch(PDO::FETCH_COLUMN);
                $data['cantidad_en_almacen'] = $data_total;

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