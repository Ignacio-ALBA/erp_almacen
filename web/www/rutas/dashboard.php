<?php
$result = processRequest();
if($result){
    $pathResult = $result['pathResult'];
    $queryParams = $result['queryParams'];
    // Controlador de vistas
    $vista = '';
    $data = [];
    $data_script['botones_acciones'] = ['
        <button class="ModalDataEdit btn btn-warning warning" modalCRUD="${modalCRUD}">Editar</button>','
        <button class="ModalDataDelete btn btn-danger danger" modalCRUD="${modalCRUD}">Eliminar</button>'
    ];

    //debug($pathResult);
    $objeto = new Conexion();
    $conexion = $objeto->Conectar();
    $data['list_js_scripts']['formularios_script'] =$data_script;
    $data['list_js_scripts']['../vistas/dashboard/dashboard_script'] = [];
    switch ($pathResult) {
        case'':
            $vista = 'dashboard';

            $consultaselect = "SELECT id_proveedor,
                orden,
                codigo,
                proveedor,
                CONCAT(calificacion,' <i class=\"bi bi-star-fill\"></i>') AS calificacion,
                razon_social,
                rfc,
                email1,
                CASE 
                    WHEN pordefecto = 1 THEN 'SÍ' 
                    ELSE 'NO' 
                END AS pordefecto,  -- Se añadió un alias aquí
                fecha_creacion
            FROM proveedores
            WHERE kid_estatus != 3 and calificacion >= 4
            ORDER BY calificacion DESC";
        
            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();
            $data_query_proveedores = $resultado->fetchAll(PDO::FETCH_ASSOC);

            $consulta = "SELECT CONCAT(da.actividad, '-', u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno)
            FROM detalles_actividades da 
            LEFT JOIN colaboradores u ON da.kid_personal_asignado = u.id_colaborador 
            WHERE da.kid_estatus != 3";
        
            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();
            $data_query_actividades_dia = $resultado->fetchAll(PDO::FETCH_ASSOC);
            
            $data['data_show']['data_actividades_dia'] = $data_query_actividades_dia;
            $data['data_show']['data_proveedores'] = $data_query_proveedores;

            break;
        case 'dashboard':
            $vista = 'dashboard';
            $consultaselect = "SELECT id_proveedor,
                proveedor,
                CONCAT(calificacion,' <i class=\"bi bi-star-fill\"></i>') AS calificacion
            FROM proveedores
            WHERE kid_estatus != 3 and calificacion >= 4
            ORDER BY calificacion DESC";
        
            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();
            $data_query_proveedores = $resultado->fetchAll(PDO::FETCH_ASSOC);
            
            $consultaselect = "SELECT 
            CONCAT(da.actividad, ' - ', u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) as actividad,
            da.horas_totales,
            da.horas_totales_reales,
            da.kid_estatus
            FROM detalles_actividades da 
            LEFT JOIN colaboradores u ON da.kid_personal_asignado = u.id_colaborador 
            WHERE (da.kid_estatus != 3 AND da.fecha_inicial = CURDATE()) 
            OR (da.kid_estatus = 10 AND da.fecha_final_real IS NULL)";

        
            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();
            $data_query_actividades_dia = $resultado->fetchAll(PDO::FETCH_ASSOC);

            $consultaselect = "SELECT 
                COUNT(DISTINCT a.kid_proyecto) AS total_proyectos
            FROM 
                actividades a
            WHERE 
                a.kid_estatus IN (1, 2, 10, 11)";

            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();
            $data_query_proyectos = $resultado->fetchColumn();

            $consultaselect = "SELECT 
                COUNT(DISTINCT a.kid_proyecto) AS total_proyectos
            FROM 
                actividades a
            WHERE 
                a.kid_estatus IN (1, 2, 10, 11)";

            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();
            $data_query_clientes = $resultado->fetchColumn();
           // debug($data_query_proyectos);

            $estatus = GetEstatusLabels();
            $caseEstatus = "CASE \n";
            foreach ($estatus as $key => $value) {
                $caseEstatus .= "    WHEN cc.kid_estatus = $key THEN '$value'\n";
            }
            $caseEstatus .= "    ELSE 'Desconocido' \nEND AS kid_estatus";
            $consultaselect = "SELECT cc.id_cotizacion_compra,
                cc.cotizacion_compras,
                (SELECT proveedor FROM proveedores prov WHERE prov.id_proveedor = cc.kid_proveedor LIMIT 1) AS kid_proveedor,
                $caseEstatus
            FROM cotizaciones_compras cc
            WHERE cc.kid_estatus != 3";
            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();
            $data_query_cotizaciones = $resultado->fetchAll(PDO::FETCH_ASSOC);


            $estatus = GetEstatusLabels();
            $caseEstatus = "CASE \n";
            foreach ($estatus as $key => $value) {
                $caseEstatus .= "    WHEN oc.kid_estatus = $key THEN '$value'\n";
            }
            $caseEstatus .= "    ELSE 'Desconocido' \nEND AS kid_estatus";


            $estatus = GetEstatusLabels();
            $consultaselect = "SELECT oc.id_orden_compras,
                oc.orden_compras,
                oc.codigo_externo,
                (SELECT proveedor FROM proveedores prov WHERE prov.id_proveedor = oc.kid_proveedor LIMIT 1) AS kid_proveedor
            FROM ordenes_compras oc
            LEFT JOIN proyectos p ON oc.kid_proyecto = p.id_proyecto
            LEFT JOIN proveedores prov ON oc.kid_proveedor = prov.id_proveedor
            WHERE oc.kid_estatus !=3";
            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();
            $data_query_compras = $resultado->fetchAll(PDO::FETCH_ASSOC);

            $estatus = GetEstatusLabels();
            $caseEstatus = "CASE \n";
            foreach ($estatus as $key => $value) {
                $caseEstatus .= "    WHEN rc.kid_estatus = $key THEN '$value'\n";
            }
            $caseEstatus .= "    ELSE 'Desconocido' \nEND AS kid_estatus";
            $consultaselect = "SELECT rc.id_recepcion_compras,
                rc.recepcion_compras,
                (SELECT proveedor FROM proveedores prov WHERE prov.id_proveedor = rc.kid_proveedor LIMIT 1) AS kid_proveedor,
                (SELECT almacen FROM almacenes alm WHERE alm.id_almacen  = rc.kid_almacen LIMIT 1) AS kid_almacen
            FROM recepciones_compras rc
            WHERE rc.kid_estatus !=3";
            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();
            $data_query_recepciones = $resultado->fetchAll(PDO::FETCH_ASSOC);

            $consultaselect = "SELECT 
                u.id_colaborador,
                CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) AS nombre_completo,
                ROUND(
                    (COUNT(CASE WHEN da.kid_estatus = 8 THEN 1 END) / COUNT(da.id_detalle_actividad)) * 100, 
                    2
                ) AS porcentaje_finalizado,
                MAX(a.fecha_final_real) AS max_fecha_final_real
            FROM detalles_actividades da
            LEFT JOIN colaboradores u ON da.kid_personal_asignado = u.id_colaborador
            LEFT JOIN actividades a ON da.kid_actividad = a.id_actividad
            WHERE da.kid_personal_asignado IS NOT NULL
            GROUP BY u.id_colaborador, nombre_completo
            HAVING 
                -- Si el porcentaje es 100%, solo incluir si max_fecha_final_real es igual a la fecha actual
                (porcentaje_finalizado = 100 AND DATE(max_fecha_final_real) = CURDATE())
                -- O si el porcentaje está entre 80% y menor al 100%
                OR (porcentaje_finalizado >= 80 and porcentaje_finalizado < 100)";


            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();
            $data_query_personal80 = $resultado->fetchAll(PDO::FETCH_ASSOC);

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
            WHERE cp.kid_estatus !=3
            ORDER BY cp.fecha_creacion DESC
            LIMIT 5";

            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();
            $data_query_proveedor_comentarios = $resultado->fetchAll(PDO::FETCH_ASSOC);

           // debug( $data_query_personal80);

            $data['data_show']['data_personal80'] = $data_query_personal80;
            $data['data_show']['data_recepciones'] = $data_query_recepciones;
            $data['data_show']['colaboradores'] = GetUsuariosListForSelect();
            $data['data_show']['almacenes'] = GetAlmacenesListForSelect();
            

            $data['data_show']['data_compras'] = $data_query_compras;
            $data['data_show']['data_cotizaciones'] = $data_query_cotizaciones;
            $data['data_show']['proyectos'] = GetProyectosListForSelect();
            $data['data_show']['proveedores'] = GetProvedoresListForSelect();
            $data['data_show']['estatus'] = GetEstatusListForSelect();

            $data['data_show']['clientes_activos'] = $data_query_clientes;
            $data['data_show']['numero_proyectos'] = $data_query_proyectos;
            $data['data_show']['data_actividades_dia'] = $data_query_actividades_dia;
            $data['data_show']['data_proveedores'] = $data_query_proveedores;
            $data['data_show']['data_query_proveedor_comentarios'] = $data_query_proveedor_comentarios;
            $data['data_show']['regimenes'] = GetRegimenesListForSelect();
            $data['data_show']['paises'] = GetPaisesListForSelect();
            $data['data_show']['estados'] = GetEstadosListForSelect();
            $data['data_show']['tipo_comentario'] = GetTiposComentariosListForSelect();
            $data['data_show']['proveedores'] = GetProvedoresListForSelect();
            break;
        case 'cambiarpass':
            $vista = 'cambiarpass';
            break;
        default:
            $vista = '404'; // Vista de error 404 si no se encuentra la ruta
            break;
    }

renderView($vista, $data);

}else{
    header("Location: /index.php");
}

?>