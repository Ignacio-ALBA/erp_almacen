<?php

// Sanitizar la entrada del pathResult
$resultado = processRequest();

if($resultado){
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
    $data['data_show']['nombre_modulo'] = 'Ingeniería de Servicios';


    switch ($pathResult) {
        case 'actividades':
            $vista = 'actividades';
            $estatus = GetEstatusLabels();
            $caseEstatus = "CASE \n";
            foreach ($estatus as $key => $value) {
                $caseEstatus .= "    WHEN a.kid_estatus = $key THEN '$value'\n";
            }
            $caseEstatus .= "    ELSE 'Desconocido' \nEND AS kid_estatus";
            $consultaselect = "SELECT a.id_actividad, 
                p.proyecto,
                bp.bolsa_proyecto,
                c.nombre,
                a.cantidad_actividades,
                a.actividades_finalizadas,
                a.actividades_pendientes,
                a.actividades_retrasadas,
                COALESCE(a.fecha_inicial_real, 'Sin Iniciar') AS fecha_inicial_real,
                CASE 
                    WHEN a.fecha_inicial_real IS NOT NULL AND a.fecha_final_real IS NULL THEN 'Sin Finalizar'
                    ELSE COALESCE(a.fecha_final_real, 'Sin Iniciar')
                END AS fecha_final_real,
                COALESCE(a.dias_totales_reales, 0) AS dias_totales_reales,
                COALESCE(a.horas_totales_reales, 0) AS horas_totales_reales,
                $caseEstatus
            FROM actividades a
            LEFT JOIN proyectos p ON a.kid_proyecto = p.id_proyecto
            LEFT JOIN clientes c ON a.kid_cliente = c.id_cliente
            LEFT JOIN bolsas_proyectos bp ON a.kid_bolsa_proyecto = bp.id_bolsa_proyecto 
            WHERE a.kid_estatus != 3";

            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();
            $data['data_show']['data']=$resultado->fetchAll(PDO::FETCH_ASSOC);
            $data['data_show']['bolsas_proyectos'] = GetBolsaProyectosListForSelect();
            $data['data_show']['articulos'] = GetArticulosListForSelect();
            $data['data_show']['tipos_viaticos'] = GetTiposViaticosListForSelect();
            $data['data_show']['colaboradores'] = GetUsuariosListForSelect();

            $modalCRUD = 'detalles_planeaciones_compras';
            $nuevo_boton = '
                <button class="ModalNewAdd3 btn btn-info info" modalCRUD="'.$modalCRUD.'"><i class="bi bi-file-spreadsheet"></i> Contenido</button>';
            array_splice($data_script['botones_acciones'], 0, 0, $nuevo_boton);
            $data['data_show']['botones_acciones'] = $data_script['botones_acciones'];
            $optionkey = 'NewAdd3';
            $data_script[$optionkey] = ['data_list_column'=>[]];
            
            break;

        case 'detalles_actividades':
            $data['data_show']['permiso'] = 1;
            $estatus = GetEstatusLabels();
            $vista = 'detalles_actividades';
            if ($data['data_show']['permiso'] != 3){
                $consultaselect = "SELECT da.id_detalle_actividad, 
                    da.actividad,
                    da.kid_actividad,
                    p.proyecto,
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
                LEFT JOIN proyectos p ON a.kid_proyecto = p.id_proyecto 
                LEFT JOIN colaboradores u ON da.kid_personal_asignado = u.id_colaborador
                WHERE da.kid_estatus != 3";
            
                $resultado = $conexion->prepare($consultaselect);
                $resultado->execute();
                $data_query = $resultado->fetchAll(PDO::FETCH_ASSOC);

                if ($data['data_show']['permiso'] == 2){

                    $botones = [
                        '<button class="ModalDataView btn btn-primary primary" modalCRUD="${modalCRUD}"><i class="bi bi-eye"></i> Ver</button>',
                        '<button class="ModalDataEdit btn btn-secondary" modalCRUD="${modalCRUD}"><i class="bi bi-person-check"></i></i> Asignar</button>'
                    ];
                    $data_query = array_map(function ($row) {
                        global $botones, $estatus;
                        $row['buttons'] = GenerateCustomsButtons($botones,'detalles_actividades');
                        $row['kid_estatus'] = $estatus[$row['kid_estatus']];
                        return $row;
                    }, $data_query);
                    
                }else{
                    
                    $data_query = array_map(function ($row) {
                        global $data_script, $estatus;
                        $botones = $data_script['botones_acciones'];

                        $modalCRUD = 'update_estatus_detalles_actividades';
                        if($row['kid_estatus'] == 1){
                            $nuevo_boton = '<button class="UpdateEstatus btn btn-success" bloque="ingenieria_servicios" name="iniciar" modalCRUD="'.$modalCRUD.'"><i class="bi bi-play-circle"></i> Iniciar</button>';
                            array_unshift($botones,$nuevo_boton);
                            
                            if($row['kid_personal_asignado'] == ''){
                                $nuevo_boton = '<button class="ModalDataEdit btn btn-secondary" modalCRUD="detalles_actividades-setpersonal"><i class="bi bi-person-check"></i></i> Asignar</button>';
                                array_unshift($botones,$nuevo_boton);
                            }
                            
                        }else if($row['kid_estatus'] == 2){
                            $modalCRUD = 'pausar_reanudar_detalles_actividades';
                            $nuevo_boton = '<button class="ModalSetData btn btn-success" title="Title2" modalCRUD="'.$modalCRUD.'"><i class="bi bi-play-circle"></i> Reanudar</button>';
                            array_unshift($botones,$nuevo_boton);
                        }else if(in_array($row['kid_estatus'],[10,11])){
                            
                            
                            $modalCRUD = 'finalizar_detalles_actividades';
                            $nuevo_boton = '<button class="ModalSetData btn btn-secondary" modalCRUD="'.$modalCRUD.'"><i class="bi bi-stop-circle"></i> Finalizar</button>';
                            array_unshift($botones,$nuevo_boton);
    
                            $modalCRUD = 'subir_evidencias';
                            $nuevo_boton = '<button class="ModalSetData btn btn-success" modalCRUD="'.$modalCRUD.'"><i class="bi bi-image"></i> Subir Evidencia</button>';
                            array_unshift($botones,$nuevo_boton);
    
                            $modalCRUD = 'pausar_reanudar_detalles_actividades';
                            $nuevo_boton = '<button class="ModalSetData btn btn-info" title="Title1" modalCRUD="'.$modalCRUD.'"><i class="bi bi-pause-circle"></i> Pausar</button>';
                            array_unshift($botones,$nuevo_boton);
                        }
                        
                        $row['buttons'] = GenerateCustomsButtons($botones,'detalles_actividades');
                        $row['kid_estatus'] = $estatus[$row['kid_estatus']];
                        return $row;
                    }, $data_query);
                }
                
                $data['data_show']['botones_acciones'] = $data_script['botones_acciones'];


            }else{
                $usuario = $_SESSION["s_id"];
                $consultaselect = "SELECT da.id_detalle_actividad, 
                    da.actividad,
                    da.kid_actividad,
                    p.proyecto,
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
                LEFT JOIN proyectos p ON a.kid_proyecto = p.id_proyecto  
                LEFT JOIN colaboradores u ON da.kid_personal_asignado = u.id_colaborador
                WHERE da.kid_estatus != 3 and da.kid_personal_asignado = $usuario";
            
                $resultado = $conexion->prepare($consultaselect);
                $resultado->execute();
                $data_query = $resultado->fetchAll(PDO::FETCH_ASSOC);

                
                $data_query = array_map(function ($row) {
                        global $data_script, $estatus;
                    $botones = [$data_script['botones_acciones'][0]];
                    //$botones = array_splice($botones, -3);
    
                    $modalCRUD = 'update_estatus_detalles_actividades';
                    if($row['kid_estatus'] == 1){
                        $nuevo_boton = '<button class="UpdateEstatus btn btn-success success" bloque="ingenieria_servicios" name="iniciar" modalCRUD="'.$modalCRUD.'"><i class="bi bi-play-circle"></i> Iniciar</button>';
                        array_splice($botones, 0, 0, $nuevo_boton);
                    }else if($row['kid_estatus'] == 2){
                        $modalCRUD = 'pausar_reanudar_detalles_actividades';
                        $nuevo_boton = '<button class="ModalSetData btn btn-success success" title="Title2" modalCRUD="'.$modalCRUD.'"><i class="bi bi-play-circle"></i> Reanudar</button>';
                        array_splice($botones, 0, 0, $nuevo_boton);
                    }else if(in_array($row['kid_estatus'],[10,11])){
                        
                        $modalCRUD = 'finalizar_detalles_actividades';
                        $nuevo_boton = '<button class="ModalSetData btn btn-secondary secondary" modalCRUD="'.$modalCRUD.'"><i class="bi bi-stop-circle"></i> Finalizar</button>';
                        array_splice($botones, 0, 0, $nuevo_boton);

                        $modalCRUD = 'subir_evidencias';
                        $nuevo_boton = '<button class="ModalSetData btn btn-success success" modalCRUD="'.$modalCRUD.'"><i class="bi bi-image"></i> Subir Evidencia</button>';
                        array_splice($botones, 0, 0, $nuevo_boton);

                        $modalCRUD = 'pausar_reanudar_detalles_actividades';
                        $nuevo_boton = '<button class="ModalSetData btn btn-info info" title="Title1" modalCRUD="'.$modalCRUD.'"><i class="bi bi-pause-circle"></i> Pausar</button>';
                        array_splice($botones, 0, 0, $nuevo_boton);
                    }
                    
                    $row['buttons'] = GenerateCustomsButtons($botones,'detalles_actividades');
                    $row['kid_estatus'] = $estatus[$row['kid_estatus']];
                    return $row;
                }, $data_query);
                
            }
            $data['data_show']['botones_acciones'] = $data_script['botones_acciones'];
            //$data['data_show']['data']=$resultado->fetchAll(PDO::FETCH_ASSOC);
            $data['data_show']['data'] = $data_query;
            $data['data_show']['personal'] = GetUsuariosListForSelect();
            break;

        case 'justificaciones_actividades':
            $estatus = GetEstatusLabels();
            $vista = 'justificaciones_actividades';

            $consultaselect = "SELECT ja.id_justificacion_actividad, 
                ja.kid_actividad,
                da.actividad,
                CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) as kid_responsable,
                ja.justificacion,
                CONCAT(ja.latitud,' ',ja.longitud),
                ja.fecha_creacion
            FROM 
                justificaciones_actividades ja
            LEFT JOIN detalles_actividades da ON ja.kid_detalle_actividad = da.id_detalle_actividad
            LEFT JOIN colaboradores u ON ja.kid_responsable = u.id_colaborador
            WHERE da.kid_estatus != 3";
        
            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();
            $data_query = $resultado->fetchAll(PDO::FETCH_ASSOC);
            
            
            $data['data_show']['data'] = $data_query;
            $data['data_show']['personal'] = GetUsuariosListForSelect();
            break;

        case 'evidencia_actividades':
            $vista = 'evidencia_actividades';

            $consultaselect = "SELECT ea.id_evidencia_actividad, 
                da.actividad,
                ea.comentario,
                CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) as kid_responsable,
                ea.fecha_creacion
            FROM 
                evidencia_actividades ea
            LEFT JOIN detalles_actividades da ON ea.kid_detalle_actividad = da.id_detalle_actividad
            LEFT JOIN colaboradores u ON da.kid_personal_asignado = u.id_colaborador
            WHERE da.kid_estatus != 3";
        
            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();
            $data_query = $resultado->fetchAll(PDO::FETCH_ASSOC);
            
            
            $data['data_show']['data'] = $data_query;
            $data['data_show']['personal'] = GetUsuariosListForSelect();
            break;

            

        default:
            $vista = '404'; // Vista de error 404 si no se encuentra la ruta
            break;
    }

    $data['list_js_scripts']['formularios_script'] =$data_script;

    renderView($vista, $data);
}else{
    header("Location: /index.php");
}


?>