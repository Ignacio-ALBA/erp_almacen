<?php

// Sanitizar la entrada del pathResult
$resultado = processRequest();


if ($resultado) {
    $pathResult = $resultado['pathResult'];
    $queryParams = $resultado['queryParams'];
    $hash_id = isset($queryParams['id']) ? $queryParams['id'] : null;
    $id = $hash_id ? decodificar($hash_id) : null;

    // Función para cargar la vista correspondiente

    // Controlador de vistas
    $vista = '';
    $data = [];

    $data_script['botones_acciones'] = [
        '<button class="ModalDataView btn btn-primary primary" modalCRUD="${modalCRUD}"><i class="bi bi-eye"></i> Ver</button>',
        '<button class="ModalDataEdit btn btn-warning warning" modalCRUD="${modalCRUD}"><i class="bi bi-pencil"></i> Editar</button>',
        '<button class="ModalDataDelete btn btn-danger danger" modalCRUD="${modalCRUD}"><i class="bi bi-trash"></i> Eliminar</button>'
    ];


    $objeto = new Conexion();
    $conexion = $objeto->Conectar();

    
    $data['data_show']['nombre_modulo'] = 'Planeación';
    $data['data_show']['breadcrumb'] = null;
    $data['data_show']['AllowADDButton'] = true;


    switch ($pathResult) {
        case 'clientes':
            $vista = 'clientes';
            $consultaselect = "SELECT c.id_cliente, 
                        c.codigo, 
                        c.nombre,
                        c.razon_social,
                        c.rfc,
                        c.email,
                        c.fecha_creacion
                    FROM 
                        clientes c
                    WHERE 
                        c.kid_estatus != 3";

            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();

            $clientes = $resultado->fetchAll(PDO::FETCH_ASSOC);

            $clientes = array_map(function ($row) {
                global $data_script, $estatus;
                $botones_acciones = $data_script['botones_acciones'];
                $hashed_id = codificar($row['id_cliente']);
                array_push($botones_acciones, '<a href="/rutas/planeacion.php/planeaciones_actividades?id=' . $hashed_id . '" class="btn btn-info "><i class="bi bi-file-spreadsheet"></i> Actividades</a>');
                array_push($botones_acciones, '<a href="/rutas/planeacion.php/planeaciones_recursos_humanos?id=' . $hashed_id . '" class="btn btn-info "><i class="bi bi-file-spreadsheet"></i> TH</a>');
                array_push($botones_acciones, '<a href="/rutas/planeacion.php/planeaciones_compras?id=' . $hashed_id . '" class="btn btn-info "><i class="bi bi-file-spreadsheet"></i> Compras</a>');
                array_push($botones_acciones, '<a href="/rutas/contabilidad.php/facturas_clientes?id=' . $hashed_id . '" class="btn btn-secondary "><i class="bi bi-receipt"></i> Facturas</a>');
                $row['botones'] = GenerateCustomsButtons($botones_acciones, 'clientes');
                return $row;
            }, $clientes);

            $data['data_show']['data'] = $clientes;

            $data['data_show']['regimenes'] = GetRegimenesListForSelect();
            $data['data_show']['paises'] = GetPaisesListForSelect();
            $data['data_show']['estados'] = GetEstadosListForSelect();
            $data['data_show']['bolsas_proyectos'] = GetBolsaProyectosListForSelect();
            break;

        case 'planeaciones_compras':
            $vista = 'planeaciones_compras';
            $estatus = GetEstatusLabels();
            $caseEstatus = "CASE \n";
            foreach ($estatus as $key => $value) {
                $caseEstatus .= "    WHEN pc.kid_estatus = $key THEN '$value'\n";
            }
            $caseEstatus .= "    ELSE 'Desconocido' \nEND AS kid_estatus";

            if ($id != null) {
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
                WHERE pc.kid_estatus != 3 AND pc.kid_cliente = $id";

                $resultado = $conexion->prepare("SELECT nombre FROM clientes WHERE id_cliente = $id");
                $resultado->execute();
                $breadcrumb_data = $resultado->fetch(PDO::FETCH_ASSOC);

                $breadcrumb = '
                <li class="breadcrumb-item"><a href="/rutas/planeacion.php/clientes">Clientes</a></li>
                <li class="breadcrumb-item">'.$breadcrumb_data['nombre'].'</li>
                <li class="breadcrumb-item active">Compras</li>
                ';
                $data['data_show']['breadcrumb'] = $breadcrumb;
                $data['data_show']['valor_id'] = $breadcrumb_data['nombre'];
            }else{
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
                WHERE pc.kid_estatus != 3";

            $data['data_show']['AllowADDButton'] = false;
            }

            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();
            $planeaciones_compras = $resultado->fetchAll(PDO::FETCH_ASSOC);

            $planeaciones_compras = array_map(function ($row) {
                global $data_script;
                $botones_acciones = $data_script['botones_acciones'];
                $hashed_id = codificar($row['id_planeacion_compras']);
                array_push($botones_acciones, '<a href="/rutas/planeacion.php/detalles_planeaciones_compras?id=' . $hashed_id . '" class="btn btn-secondary "><i class="bi bi-journal-text"></i> Contenido</a>');
                $row['botones'] = GenerateCustomsButtons($botones_acciones, 'planeaciones_compras');
                return $row;
            }, $planeaciones_compras);


            $data['data_show']['data'] = $planeaciones_compras;
            //$data['data_show']['botones_acciones'] = $data_script['botones_acciones'];
            $data['data_show']['bolsas_proyectos'] = GetBolsaProyectosListForSelect();
            $data['data_show']['articulos'] = GetArticulosListForSelect();

            break;

        case 'detalles_planeaciones_compras':
            $vista = 'detalles_planeaciones_compras';
            if ($id != null) {
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
                WHERE dpc.kid_estatus != 3 AND dpc.kid_planeacion_compras = $id";

                $resultado = $conexion->prepare("SELECT c.nombre,c.id_cliente FROM planeaciones_compras pc 
                LEFT JOIN clientes c ON pc.kid_cliente = c.id_cliente WHERE pc.id_planeacion_compras = $id");
                $resultado->execute();
                $breadcrumb_data = $resultado->fetch(PDO::FETCH_ASSOC);
                $breadcrumb = '
                <li class="breadcrumb-item"><a href="/rutas/planeacion.php/clientes">Clientes</a></li>
                <li class="breadcrumb-item">'.$breadcrumb_data['nombre'].'</li>
                <li class="breadcrumb-item"><a href="/rutas/planeacion.php/planeaciones_compras?id='.codificar($breadcrumb_data['id_cliente']).'">Compras</a></li>
                <li class="breadcrumb-item"> No. '.$id.'</li>
                <li class="breadcrumb-item active">Contenido</li>
                ';
                $data['data_show']['breadcrumb'] = $breadcrumb;
                $data['data_show']['valor_id'] = $id;

            } else {
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
                WHERE dpc.kid_estatus != 3";
                $data['data_show']['AllowADDButton'] = false;
            }


            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();
            $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
            $data['data_show']['planeacion_compras'] = GetPlaneacionesComprasListForSelect();
            $data['data_show']['articulos'] = GetArticulosListForSelect();
            break;

        case 'cambios_planeaciones_compras':
            $vista = 'cambios_planeaciones_compras';
            $consultaselect = "SELECT cpc.id_cambio_recepcion_compras,
            kid_registro_tabla,
            t.tablas as kid_tabla,
            cpc.cambio,
            CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) as kid_creacion,
            cpc.fecha_creacion
            FROM 
                cambios_planeaciones_compras cpc
            LEFT JOIN tablas t ON cpc.kid_tabla = t.id_tabla
            LEFT JOIN colaboradores u ON cpc.kid_creacion = u.id_colaborador
            WHERE cpc.kid_estatus != 3";

            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();
            $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);

            $data['data_show']['botones_acciones'] = [$data_script['botones_acciones'][0]];
            break;

        case 'tablas':
            $vista = 'tablas';
            $consultaselect = "SELECT t.id_tabla,
            t.tablas,
            modulos.modulo as kid_modulo,
            t.descripcion,
            CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) as kid_creacion,
            t.fecha_creacion
            FROM tablas t 
            LEFT JOIN colaboradores u ON t.kid_creacion = u.id_colaborador
            LEFT JOIN modulos ON modulos.id_modulo = t.kid_modulo
            WHERE t.kid_estatus != 3";

            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();
            $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
            $data['data_show']['modulos'] = GetModulosListForSelect();
            break;

        case 'modulos':
            $vista = 'modulos';
            $consultaselect = "SELECT m.id_modulo,
            m.modulo,
            m.descripcion,
            CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) as kid_creacion,
            m.fecha_creacion
            FROM modulos m 
            LEFT JOIN colaboradores u ON m.kid_creacion = u.id_colaborador
            WHERE m.kid_estatus != 3";

            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();
            $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
            break;

        case 'planeaciones_recursos_humanos':
            $vista = 'planeaciones_recursos_humanos';
            $estatus = GetEstatusLabels();
            $caseEstatus = "CASE \n";
            foreach ($estatus as $key => $value) {
                $caseEstatus .= "    WHEN prh.kid_estatus = $key THEN '$value'\n";
            }
            $caseEstatus .= "    ELSE 'Desconocido' \nEND AS kid_estatus";

            if ($id != null) {
                $consultaselect = "SELECT prh.id_planeacion_rrhh , 
                        bp.bolsa_proyecto, 
                        p.proyecto,
                        c.nombre,
                        prh.cantidad_internos,
                        prh.cantidad_externos,
                        $caseEstatus,
                        CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) as kid_creacion,
                        prh.fecha_creacion
                    FROM 
                        planeaciones_rrhh prh
                    LEFT JOIN clientes c ON prh.kid_cliente = c.id_cliente
                    LEFT JOIN proyectos p ON prh.kid_proyecto = p.id_proyecto
                    LEFT JOIN bolsas_proyectos bp ON prh.kid_bolsa_proyecto = bp.id_bolsa_proyecto
                    LEFT JOIN colaboradores u ON prh.kid_creacion = u.id_colaborador
                    WHERE prh.kid_estatus != 3 AND prh.kid_cliente = $id";

                    $resultado = $conexion->prepare("SELECT nombre FROM clientes WHERE id_cliente = $id");
                    $resultado->execute();
                    $breadcrumb_data = $resultado->fetch(PDO::FETCH_ASSOC);

                    $breadcrumb = '
                    <li class="breadcrumb-item"><a href="/rutas/planeacion.php/clientes">Clientes</a></li>
                    <li class="breadcrumb-item">'.$breadcrumb_data['nombre'].'</li>
                    <li class="breadcrumb-item active">Talento Humano</li>
                    ';
                    $data['data_show']['breadcrumb'] = $breadcrumb;
                    $data['data_show']['valor_id'] = $breadcrumb_data['nombre'];

            }else {
                $consultaselect = "SELECT prh.id_planeacion_rrhh , 
                    bp.bolsa_proyecto, 
                    p.proyecto,
                    c.nombre,
                    prh.cantidad_internos,
                    prh.cantidad_externos,
                    $caseEstatus,
                    CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) as kid_creacion,
                    prh.fecha_creacion
                FROM 
                    planeaciones_rrhh prh
                LEFT JOIN clientes c ON prh.kid_cliente = c.id_cliente
                LEFT JOIN proyectos p ON prh.kid_proyecto = p.id_proyecto
                LEFT JOIN bolsas_proyectos bp ON prh.kid_bolsa_proyecto = bp.id_bolsa_proyecto
                LEFT JOIN colaboradores u ON prh.kid_creacion = u.id_colaborador
                WHERE prh.kid_estatus != 3";
                $data['data_show']['AllowADDButton'] = false;
            }

            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();
            $planeaciones_rrh = $resultado->fetchAll(PDO::FETCH_ASSOC);

            $planeaciones_rrh = array_map(function ($row) {
                global $data_script;
                $botones_acciones = $data_script['botones_acciones'];
                $hashed_id = codificar($row['id_planeacion_rrhh']);
                array_push($botones_acciones, '<a href="/rutas/planeacion.php/detalles_planeaciones_recursos_humanos?id=' . $hashed_id . '" class="btn btn-secondary "><i class="bi bi-journal-text"></i> Contenido</a>');
                $row['botones'] = GenerateCustomsButtons($botones_acciones, 'planeaciones_recursos_humanos');
                return $row;
            }, $planeaciones_rrh);


            $data['data_show']['data'] = $planeaciones_rrh;
            $data['data_show']['bolsas_proyectos'] = GetBolsaProyectosListForSelect();
            $data['data_show']['colaboradores'] = GetUsuariosListForSelect();
            $data['data_show']['modalidad'] = GetInternosExternosListForSelect();
            $data['data_show']['tipo_cantidad'] = GetTiposCostosListForSelect();

            break;

        case 'detalles_planeaciones_recursos_humanos':
            $vista = 'detalles_planeaciones_recursos_humanos';
            if ($id != null) {
                $consultaselect = "SELECT dprh.id_detalle_planeaciones_rrhh , 
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
            WHERE dprh.kid_estatus != 3 AND  dprh.kid_planeaciones_rrhh = $id";

            $resultado = $conexion->prepare("SELECT c.nombre,c.id_cliente FROM planeaciones_rrhh prh
            LEFT JOIN clientes c ON prh.kid_cliente = c.id_cliente WHERE prh.id_planeacion_rrhh = $id");
            $resultado->execute();
            $breadcrumb_data = $resultado->fetch(PDO::FETCH_ASSOC);

            $breadcrumb = '
            <li class="breadcrumb-item"><a href="/rutas/planeacion.php/clientes">Clientes</a></li>
            <li class="breadcrumb-item">'.$breadcrumb_data['nombre'].'</li>
            <li class="breadcrumb-item"><a href="/rutas/planeacion.php/planeaciones_recursos_humanos?id='.codificar($breadcrumb_data['id_cliente']).'">Talento Humano</a></li>
            <li class="breadcrumb-item"> No. '.$id.'</li>
            <li class="breadcrumb-item active">Contenido</li>
            ';
            $data['data_show']['breadcrumb'] = $breadcrumb;
            $data['data_show']['valor_id'] = $id;

            } else {
                $consultaselect = "SELECT dprh.id_detalle_planeaciones_rrhh , 
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
            WHERE dprh.kid_estatus != 3";

            $data['data_show']['AllowADDButton'] = false;
            }


            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();
            $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
            $data['data_show']['planeacion_rrhh'] = GetPlaneacionesRRHHListForSelect();
            $data['data_show']['colaboradores'] = GetUsuariosListForSelect();
            $data['data_show']['modalidad'] = GetInternosExternosListForSelect();
            $data['data_show']['tipo_cantidad'] = GetTiposCostosListForSelect();
            break;

        case 'internos_externos':
            $vista = 'internos_externos';
            $consultaselect = "SELECT id_internos_externos,
            orden,
            internos_externos,
            CASE 
            WHEN pordefecto = 1 THEN 'SÍ' 
            ELSE 'NO' 
            END AS pordefecto,
            fecha_creacion
            FROM internos_externos WHERE kid_estatus != 3";

            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();
            $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
            break;

        case 'tipos_costos_total':
            $vista = 'tipos_costo';
            $consultaselect = "SELECT id_tipo_costo,
            orden,
            tipo_costo,
            CASE 
            WHEN pordefecto = 1 THEN 'SÍ' 
            ELSE 'NO' 
            END AS pordefecto,
            fecha_creacion
            FROM tipos_costo WHERE kid_estatus != 3";

            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();
            $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
            break;

        case 'planeaciones_actividades':
            $vista = 'planeaciones_actividades';
            $estatus = GetEstatusLabels();
            if ($id != null){
                $consultaselect = "SELECT pa.id_planeacion_actividad, 
                    bp.bolsa_proyecto, 
                    p.proyecto,
                    c.nombre,
                    pa.fecha_inicial,
                    pa.fecha_final,
                    pa.dias_totales,
                    pa.kid_estatus,
                    CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) as kid_creacion,
                    pa.fecha_creacion
                FROM 
                    planeaciones_actividades pa
                LEFT JOIN clientes c ON pa.kid_cliente = c.id_cliente
                LEFT JOIN proyectos p ON pa.kid_proyecto = p.id_proyecto
                LEFT JOIN bolsas_proyectos bp ON pa.kid_bolsa_proyecto = bp.id_bolsa_proyecto
                LEFT JOIN colaboradores u ON pa.kid_creacion = u.id_colaborador
                WHERE pa.kid_estatus != 3 AND  pa.kid_cliente = $id";

                $resultado = $conexion->prepare("SELECT nombre FROM clientes WHERE id_cliente = $id");
                $resultado->execute();
                $breadcrumb_data = $resultado->fetch(PDO::FETCH_ASSOC);

                $breadcrumb = '
                <li class="breadcrumb-item"><a href="/rutas/planeacion.php/clientes">Clientes</a></li>
                <li class="breadcrumb-item">'.$breadcrumb_data['nombre'].'</li>
                <li class="breadcrumb-item active">Actividades</li>
                ';
                $data['data_show']['breadcrumb'] = $breadcrumb;
                $data['data_show']['valor_id'] = $breadcrumb_data['nombre'];

                

            }else{
                $consultaselect = "SELECT pa.id_planeacion_actividad, 
                    bp.bolsa_proyecto, 
                    p.proyecto,
                    c.nombre,
                    pa.fecha_inicial,
                    pa.fecha_final,
                    pa.dias_totales,
                    pa.kid_estatus,
                    CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) as kid_creacion,
                    pa.fecha_creacion
                FROM 
                    planeaciones_actividades pa
                LEFT JOIN clientes c ON pa.kid_cliente = c.id_cliente
                LEFT JOIN proyectos p ON pa.kid_proyecto = p.id_proyecto
                LEFT JOIN bolsas_proyectos bp ON pa.kid_bolsa_proyecto = bp.id_bolsa_proyecto
                LEFT JOIN colaboradores u ON pa.kid_creacion = u.id_colaborador
                WHERE pa.kid_estatus != 3";
                
                $data['data_show']['AllowADDButton'] = false;
            }
            

            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();
            $data_query = $resultado->fetchAll(PDO::FETCH_ASSOC);

            $data_query = array_map(function ($row) {
                global $data_script, $estatus;
                $botones_acciones = $data_script['botones_acciones'];
                $hashed_id = codificar($row['id_planeacion_actividad']);
                $modalCRUD = 'check_planeaciones_actividades';
                if(!in_array($row['kid_estatus'],['5','8'])){
                    array_push($botones_acciones, '<button class="UpdateEstatus btn btn-success success" bloque="planeacion" name="autorizar" modalCRUD="'.$modalCRUD.'"><i class="bi bi-check2"></i> Autorizar</button>');
                }
                array_push($botones_acciones, '<a href="/rutas/planeacion.php/detalles_planeaciones_actividades?id=' . $hashed_id . '" class="btn btn-secondary "><i class="bi bi-journal-text"></i> Contenido</a>');
                $row['botones'] = GenerateCustomsButtons($botones_acciones, 'planeaciones_actividades');
                $row['kid_estatus'] = $estatus[$row['kid_estatus']];
                return $row;
            }, $data_query);

            $data['data_show']['data'] = $data_query;
            $data['data_show']['bolsas_proyectos'] = GetBolsaProyectosListForSelect();

            break;

        case 'detalles_planeaciones_actividades':
            $vista = 'detalles_planeaciones_actividades';
            if ($id != null) {
                $consultaselect = "SELECT dpa.id_detalle_planeacion_actividad, 
                    dpa.kid_planeacion_actividad,
                    dpa.actividad,
                    dpa.fecha_inicial,
                    dpa.fecha_final,
                    dpa.dias_totales,
                    dpa.fecha_creacion
                FROM detalles_planeaciones_actividades dpa WHERE dpa.kid_estatus != 3 AND dpa.kid_planeacion_actividad = $id";

                $resultado = $conexion->prepare("SELECT c.nombre,c.id_cliente FROM planeaciones_actividades pa 
                LEFT JOIN clientes c ON pa.kid_cliente = c.id_cliente WHERE pa.id_planeacion_actividad = $id");
                $resultado->execute();
                $breadcrumb_data = $resultado->fetch(PDO::FETCH_ASSOC);

                $breadcrumb = '
                <li class="breadcrumb-item"><a href="/rutas/planeacion.php/clientes">Clientes</a></li>
                <li class="breadcrumb-item">'.$breadcrumb_data['nombre'].'</li>
                <li class="breadcrumb-item"><a href="/rutas/planeacion.php/planeaciones_actividades?id='.codificar($breadcrumb_data['id_cliente']).'">Actividad</a></li>
                <li class="breadcrumb-item"> No. '.$id.'</li>
                <li class="breadcrumb-item active">Contenido</li>
                ';
                $data['data_show']['breadcrumb'] = $breadcrumb;
                $data['data_show']['valor_id'] = $id;

            }else{
                $consultaselect = "SELECT dpa.id_detalle_planeacion_actividad, 
                    dpa.kid_planeacion_actividad,
                    dpa.actividad,
                    dpa.fecha_inicial,
                    dpa.fecha_final,
                    dpa.dias_totales,
                    dpa.fecha_creacion
                FROM detalles_planeaciones_actividades dpa WHERE dpa.kid_estatus != 3";
                $data['data_show']['AllowADDButton'] = false;
            }
            
            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();
            $detalles_planeacion_actividades = $resultado->fetchAll(PDO::FETCH_ASSOC);
            
            $data['data_show']['data'] = $detalles_planeacion_actividades;
            $data['data_show']['planeacion_actividad'] = GetPlaneacionesActividadesListForSelect();
            $data['data_show']['tipo_actividad'] = GetTiposActividadesListForSelect();

            break;

        default:
            $vista = '404'; // Vista de error 404 si no se encuentra la ruta
            break;
    }

    $data['list_js_scripts']['formularios_script'] = $data_script;

    renderView($vista, $data);
    
} else {
    header("Location: /index.php");
}


?>