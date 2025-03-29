<?php
$objeto = new Conexion();
$conexion = $objeto->Conectar();

$data_script['botones_acciones'] = [
    '<button class="ModalDataView btn btn-primary primary" modalCRUD="${modalCRUD}"><i class="bi bi-eye"></i> Ver</button>',
    '<button class="ModalDataEdit btn btn-warning warning" modalCRUD="${modalCRUD}"><i class="bi bi-pencil"></i> Editar</button>',
    '<button class="ModalDataDelete btn btn-danger danger" modalCRUD="${modalCRUD}"><i class="bi bi-trash"></i> Eliminar</button>'
];

function getButtonstoDetallesActividades(){
    $estatus = GetEstatusLabels();
    $permiso = 1;
    $fuc_mapping = null;
    if ($permiso!= 3){
        if ($permiso == 2){

            $botones = [
                '<button class="ModalDataView btn btn-primary primary" modalCRUD="${modalCRUD}"><i class="bi bi-eye"></i> Ver</button>',
                '<button class="ModalDataEdit btn btn-secondary" modalCRUD="${modalCRUD}"><i class="bi bi-person-check"></i></i> Asignar</button>'
            ];

            $fuc_mapping = function ($row) {
                $botones = [
                    '<button class="ModalDataView btn btn-primary primary" modalCRUD="${modalCRUD}"><i class="bi bi-eye"></i> Ver</button>',
                    '<button class="ModalDataEdit btn btn-secondary" modalCRUD="${modalCRUD}"><i class="bi bi-person-check"></i></i> Asignar</button>'
                ];
                global $estatus;
                $row['buttons'] = GenerateCustomsButtons($botones,'detalles_actividades');
                $row['kid_estatus'] = $estatus[$row['kid_estatus']];
                return $row;
            };
        }else{
            $fuc_mapping = function ($row) {
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
            };
        }
    }else{
        $fuc_mapping = function ($row) {
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
        };
    }
    return $fuc_mapping;
}

function selectDetallesActividades($id){
    $permiso = 3;
    $idcolumn = 'id_detalle_actividad';
    global $conexion, $data_script;
    $estatus = GetEstatusLabels();
    $caseEstatus = "CASE \n";
    foreach ($estatus as $key => $value) {
        $caseEstatus .= "WHEN da.kid_estatus = $key THEN '$value'\n";
    }
    $caseEstatus .= "ELSE 'Desconocido' \nEND AS kid_estatus";
    $consultaselect = "SELECT da.id_detalle_actividad, 
        da.actividad,
        da.kid_actividad,
        CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) as kid_personal_asignado,
        $caseEstatus,
        COALESCE(da.fecha_inicial_real, 'Sin Iniciar') AS fecha_inicial_real,
        CASE 
            WHEN da.fecha_inicial_real IS NOT NULL AND da.fecha_final_real IS NULL THEN 'Sin Finalizar'
            ELSE COALESCE(da.fecha_final_real, 'Sin Iniciar')
        END AS fecha_final_real,
        COALESCE(a.dias_totales_reales, 0) AS dias_totales_reales,
        COALESCE(a.horas_totales_reales, 0) AS horas_totales_reales,
        da.progreso,
        da.kid_estatus
    FROM 
        detalles_actividades da
    LEFT JOIN actividades a ON da.kid_actividad = a.id_actividad 
    LEFT JOIN colaboradores u ON da.kid_personal_asignado = u.id_colaborador
    WHERE da.kid_estatus != 3 and ".$idcolumn." = :".$idcolumn;
    $resultado = $conexion->prepare($consultaselect);
    $resultado->bindParam(":$idcolumn", $id, PDO::PARAM_INT);
    $resultado->execute();
    $data_query = $resultado->fetch(PDO::FETCH_ASSOC);
    //debug($data_query);

    if ($permiso != 3){

        if ($permiso == 2){
            array_pop($data_script['botones_acciones']);
        }
        $data['data_show']['botones_acciones'] = $data_script['botones_acciones'];
        $data_query['kid_estatus'] = $estatus['kid_estatus'];
    }else{ 
        $botones = [$data_script['botones_acciones'][0]];
        //$botones = array_splice($botones, -3);

        $modalCRUD = 'update_estatus_detalles_actividades';
        if($data_query['kid_estatus'] == 1){
            $nuevo_boton = '<button class="UpdateEstatus btn btn-success success" bloque="ingenieria_servicios" name="iniciar" modalCRUD="'.$modalCRUD.'"><i class="bi bi-play-circle"></i> Iniciar</button>';
            array_splice($botones, 0, 0, $nuevo_boton);
        }else if($data_query['kid_estatus'] == 2){
            $modalCRUD = 'pausar_reanudar_detalles_actividades';
            $nuevo_boton = '<button class="ModalSetData btn btn-success success" title="Title2" modalCRUD="'.$modalCRUD.'"><i class="bi bi-play-circle"></i> Reanudar</button>';
            array_splice($botones, 0, 0, $nuevo_boton);
        }else if(in_array($data_query['kid_estatus'],[10,11])){
            
            $modalCRUD = 'finalizar_detalles_actividades';
            $nuevo_boton = '<button class="ModalSetData btn btn-secondary secondary" modalCRUD="'.$modalCRUD.'"><i class="bi bi-stop-circle"></i> Finalizar</button>';
            array_splice($botones, 0, 0, $nuevo_boton);
            $modalCRUD = 'pausar_reanudar_detalles_actividades';
            $nuevo_boton = '<button class="ModalSetData btn btn-info info" title="Title1" modalCRUD="'.$modalCRUD.'"><i class="bi bi-pause-circle"></i> Pausar</button>';
            array_splice($botones, 0, 0, $nuevo_boton);
        }
        //debug($data_query);
        $data_query['buttons'] = GenerateCustomsButtons($botones,'detalles_actividades');
        $data_query['kid_estatus'] = $estatus[$data_query['kid_estatus']];
        
    }

    return $data_query;
}

function updateActividad($id,$conexion) {
    // Consulta 1: Obtener detalles de la actividad
    $consulta = "SELECT * FROM detalles_actividades WHERE kid_actividad = :id";
    $resultado = $conexion->prepare($consulta);
    $resultado->bindParam(':id', $id);
    $resultado->execute();
    $detallles = $resultado->fetchAll(PDO::FETCH_ASSOC);

    if (!$detallles) {
        throw new Exception("Error al obtener los detalles de la actividad.");
    }

    // Consulta 2: Obtener la actividad principal
    $consulta = "SELECT * FROM actividades WHERE id_actividad = :id";
    $resultado = $conexion->prepare($consulta);
    $resultado->bindParam(':id', $id);
    $resultado->execute();
    $actividad = $resultado->fetch(PDO::FETCH_ASSOC);

    if (!$actividad) {
        throw new Exception("Error al obtener la actividad principal.");
    }

    // Consulta 3: Obtener detalles de planeaciones de RRHH
    $consulta = "SELECT dprh.* FROM detalles_planeaciones_rrhh dprh 
                    INNER JOIN planeaciones_rrhh prh ON dprh.kid_planeaciones_rrhh = prh.id_planeacion_rrhh 
                    WHERE prh.kid_cliente = :id";
    $resultado = $conexion->prepare($consulta);
    $resultado->bindParam(':id', $actividad['kid_cliente']);
    $resultado->execute();
    $detalles_planeaciones_rrhh = $resultado->fetchAll(PDO::FETCH_ASSOC);

    if (!$detalles_planeaciones_rrhh) {
        throw new Exception("Error al obtener los detalles de planeaciones de RRHH.");
    }

    $array_kid_personal = array_column($detalles_planeaciones_rrhh, null, 'kid_personal');


    $data_a_actualizar = [];
    $actividades_terminadas = 0;
    $data_a_actualizar['dias_totales'] = 0;
    $data_a_actualizar['dias_totales_reales'] = 0;
    $data_a_actualizar['dias_totales_finalizados'] = 0;
    $data_a_actualizar['dias_totales_pendientes'] = 0;
    $data_a_actualizar['dias_totales_retrasados'] = 0;
    $data_a_actualizar['horas_totales'] = 0;
    $data_a_actualizar['horas_totales_reales'] = 0;
    $data_a_actualizar['horas_totales_finalizadas'] = 0;
    $data_a_actualizar['horas_totales_pendientes'] = 0;
    $data_a_actualizar['horas_totales_retrasadas'] = 0;
    $data_a_actualizar['actividades_retrasadas'] = 0;
    $data_a_actualizar['cantidad_internos'] = 0;
    $data_a_actualizar['cantidad_no_internos'] = 0;
    $data_a_actualizar['actividades_finalizadas'] = 0;
    $data_a_actualizar['fecha_inicial_real'] = $actividad['fecha_inicial_real'] ? $actividad['fecha_inicial_real'] : date('Y-m-d H:i:s');

    $total_horas_esperadas = 0;
    $total_horas_trabajadas = 0;
    $total_horas_totales = 0;
    $total_retraso_horas = 0;

    foreach ($detallles as $detalle) {
        if($detalle['kid_actividad'] == 1 && $detalle['fecha_inicial_real'] !=null && 
            $actividad['fecha_inicial_real'] == null && $data_a_actualizar['fecha_inicial_real'] > $detalle['fecha_inicial_real']){
            $data_a_actualizar['fecha_inicial_real'] = $detalle['fecha_inicial_real'];
        }
        

        if($detalle['kid_actividad'] == 8 && $detalle['fecha_final_real'] !=null && $actividad['fecha_final_real'] == null){
            $actividades_terminadas++;
            $data_a_actualizar['dias_totales_finalizados'] += $detalle['dias_totales_reales'];
            $data_a_actualizar['horas_totales_finalizadas'] += $detalle['horas_totales_reales'];
            $data_a_actualizar['actividades_finalizadas']++;
        }

        $data_a_actualizar['dias_totales'] += $detalle['dias_totales'];
        $data_a_actualizar['dias_totales_reales'] += $detalle['dias_totales_reales'];
        $data_a_actualizar['dias_totales_pendientes'] += $detalle['dias_totales_pendientes'];
        $data_a_actualizar['dias_totales_retrasados'] += $detalle['dias_totales_retrasados'];

        $data_a_actualizar['horas_totales'] += $detalle['horas_totales'];
        $data_a_actualizar['horas_totales_reales'] += $detalle['horas_totales_reales'];
        $data_a_actualizar['horas_totales_pendientes'] += $detalle['horas_totales_pendientes'];
        $data_a_actualizar['horas_totales_retrasadas'] += $detalle['horas_totales_retrasadas'];

        if(
            // Actividad inició tarde
            ($detalle['fecha_inicial_real'] !== null && $detalle['fecha_inicial'] < $detalle['fecha_inicial_real'])|| 
            // Actividad aún no termina y ya pasó su fecha de finalización
            ($detalle['fecha_final_real'] == null && $detalle['fecha_final'] < date('Y-m-d H:i:s')) || 
            // Actividad no ha iniciado y ya pasó su fecha de inicio
            ($detalle['fecha_inicial_real'] == null && $detalle['fecha_inicial'] < date('Y-m-d H:i:s')) || 
            // Actividad terminó después de la fecha programada
            ($detalle['fecha_final_real'] != null && $detalle['fecha_final_real'] > $detalle['fecha_final'])
        ){
            $data_a_actualizar['actividades_retrasadas']++;
        }
        //debug($array_kid_personal);
        //debug($detalle);
        
        if($detalle['kid_personal_asignado'] && $array_kid_personal[$detalle['kid_personal_asignado']]['kid_interno_externo'] == 1){
            $data_a_actualizar['cantidad_internos']++;
        }else{
            $data_a_actualizar['cantidad_no_internos']++;
        }

        // Calcula las horas diarias
        $horas_diarias = $detalle['horas_totales'] / $detalle['dias_totales'];

        // Calcula días transcurridos
        $fecha_inicio = strtotime($detalle['fecha_inicial']);
        $fecha_actual = $detalle['fecha_final_real'] 
            ? strtotime($detalle['fecha_final_real']) 
            : strtotime('now');

        $dias_transcurridos = floor(($fecha_actual - $fecha_inicio) / (60 * 60 * 24));

        // Ajusta días transcurridos si son mayores que los días totales
        if ($dias_transcurridos > $detalle['dias_totales']) {
            $dias_transcurridos = $detalle['dias_totales'];
        } elseif ($dias_transcurridos < 0) {
            $dias_transcurridos = 0;
        }

        // Calcula horas esperadas
        $horas_esperadas = $dias_transcurridos * $horas_diarias;
        $total_horas_esperadas += $horas_esperadas;

        // Calcula horas trabajadas
        $horas_trabajadas = $detalle['fecha_final_real'] 
            ? (($fecha_actual - $fecha_inicio) / 3600) 
            : 0;
        $total_horas_trabajadas += $horas_trabajadas;

        // Acumula las horas totales planeadas
        $total_horas_totales += $detalle['horas_totales'];

        // Calcula retraso o adelanto en horas
        $retraso_horas = $horas_esperadas - $horas_trabajadas;

        // Solo acumula retrasos (ignora adelantos)
        if ($retraso_horas > 0) {
            $total_retraso_horas += $retraso_horas;
        }
    }

    // Calcula el progreso temporal (en porcentaje)
    if ($total_horas_totales > 0) {
        $data_a_actualizar['progreso_temporal'] = round(($total_horas_trabajadas * 100) / $total_horas_totales, 2);
    } else {
        $data_a_actualizar['progreso_temporal'] = 0; // Si no hay actividades, el progreso es 0
    }

    // Calcula el retraso temporal (en porcentaje respecto a las horas totales)
    if ($total_horas_totales > 0) {
        $data_a_actualizar['retraso_temporal'] = round(($total_retraso_horas * 100) / $total_horas_totales, 2);
    } else {
        $data_a_actualizar['retraso_temporal'] = 0; // Si no hay actividades, no hay retraso
    }


    if($actividades_terminadas >= count($detallles)){$data_a_actualizar['fecha_final_real'] = date('Y-m-d H:i:s');}

    $data_a_actualizar['cantidad_actividades'] = count($detallles);
    if (!empty($data_a_actualizar['cantidad_actividades'])) {
        $data_a_actualizar['actividades_pendientes'] = $data_a_actualizar['cantidad_actividades']-$data_a_actualizar['actividades_finalizadas'];
        $data_a_actualizar['progreso_cantidad_actividades'] = round(($data_a_actualizar['actividades_finalizadas'] * 100) / $data_a_actualizar['cantidad_actividades'], 2);
        $data_a_actualizar['retraso_cantidad_actividades'] = round(($data_a_actualizar['actividades_retrasadas'] * 100) / $data_a_actualizar['cantidad_actividades'], 2);
        
    } else {
        $data_a_actualizar['progreso_cantidad_actividades'] = 0;
        $data_a_actualizar['retraso_cantidad_actividades'] = 0;
        $data_a_actualizar['actividades_pendientes'] = 0;
    }

    $data_a_actualizar = CleanJson($data_a_actualizar);

    $columnas = array_keys($data_a_actualizar);
    $set_parts = array_map(function ($columna) {
        return "$columna = :$columna";
    }, $columnas);
    $set_clause = implode(', ', $set_parts);
    $consulta_update = "UPDATE actividades SET $set_clause WHERE id_actividad = :id_actividad";
    $resultado_update = $conexion->prepare($consulta_update);

    foreach ($data_a_actualizar as $columna => $valor) {
        $resultado_update->bindValue(":$columna", $valor);
    }
    $resultado_update->bindValue(":id_actividad", $id);

    if (!$resultado_update->execute()) {
        throw new Exception("Error al actualizar la actividad.");
    }
    return $resultado_update;
}

// Incluir la biblioteca getID3
require_once 'getid3/getid3.php'; // Asegúrate de que la ruta sea correcta

function obtenerMetadatos($rutaArchivo) {
    // Verificar si el archivo existe
    if (!file_exists($rutaArchivo)) {
        echo "El archivo no existe: $rutaArchivo";
        return null;
    }

    // Instanciar la clase getID3
    $getID3 = new getID3();

    // Analizar el archivo de imagen y obtener metadatos
    $informacionArchivo = $getID3->analyze($rutaArchivo);
    //debug($informacionArchivo);

    // Comprobar si existen metadatos EXIF
    $returnInfo = [];

    // Verificar si hubo algún error durante el análisis
    if (isset($informacionArchivo['error'])) {
        return $returnInfo;
    } else {
        // Obtener los metadatos básicos
        $returnInfo['fileformat'] = $informacionArchivo['fileformat'] ?? null;
        $returnInfo['mime_type'] = $informacionArchivo['mime_type'] ?? null;
        $returnInfo['filesize'] = $informacionArchivo['filesize'] ?? null;
        $returnInfo['filename'] = $informacionArchivo['filename'] ?? null;
        //$returnInfo['filepath'] = $informacionArchivo['filepath'] ?? null;

        if (isset($informacionArchivo[$returnInfo['fileformat']])) {
            $exif = $informacionArchivo[$returnInfo['fileformat']]['exif'];
            $returnInfo['IFD0'] = $exif['IFD0'] ?? null;
            $returnInfo['EXIF'] = $exif['EXIF'] ?? null;
            $returnInfo['GPS'] = $exif['GPS'] ?? null;
        }

        // También podemos acceder a otros datos como la resolución y el tipo de compresión
        if (isset($informacionArchivo['video'])) {
            $returnInfo['video'] = $informacionArchivo['video'];
        }
    }

    // Retornar los metadatos completos
    return $returnInfo;
}




function updateDetalleActividad($id,$conexion){
    // Consulta 1: Obtener detalles de la actividad
    $consulta = "SELECT * FROM detalles_actividades WHERE id_detalle_actividad = :id";
    $resultado = $conexion->prepare($consulta);
    $resultado->bindParam(':id', $id);
    $resultado->execute();
    $detalle = $resultado->fetch(PDO::FETCH_ASSOC);

    $consulta = "WITH cte AS (
        SELECT *,
            ROW_NUMBER() OVER (PARTITION BY kid_tipo_justificacion ORDER BY fecha_creacion) AS rn
        FROM justificaciones_actividades
        WHERE kid_tipo_justificacion IN (1, 2)
    )
    SELECT *
    FROM cte
    WHERE kid_detalle_actividad = :id
    ORDER BY
        rn,
        CASE
            WHEN kid_tipo_justificacion = 1 THEN 0
            WHEN kid_tipo_justificacion = 2 THEN 1
            ELSE 2
        END,
        fecha_creacion";

    $resultado = $conexion->prepare($consulta);
    $resultado->bindParam(':id', $id);
    $resultado->execute();
    $justificaciones = $resultado->fetchAll(PDO::FETCH_ASSOC);
    
    $totalPausado = 0;
    $fechaPausa = null;

    foreach ($justificaciones as $justificacion) {
        // Si es una pausa (kid_tipo_justificacion = 1)
        if ($justificacion['kid_tipo_justificacion'] == 1) {
            $fechaPausa = new DateTime($justificacion['fecha_creacion']);
        }
        // Si es una reanudación (kid_tipo_justificacion = 2) y tenemos una pausa pendiente
        if ($justificacion['kid_tipo_justificacion'] == 2 && $fechaPausa) {
            $fechaReanudacion = new DateTime($justificacion['fecha_creacion']);
            
            // Calcular la diferencia en días entre la pausa y la reanudación
            $diferencia = $fechaPausa->diff($fechaReanudacion);
            $totalPausado += $diferencia->days;
            
            // Resetear la variable de fechaPausa después de calcular el intervalo
            $fechaPausa = null;
        }
    }

    // Si hay una pausa sin reanudación, calcular la diferencia hasta el tiempo actual
    if ($fechaPausa) {
        $fechaActual = new DateTime(); // Obtener la fecha y hora actual
        $diferencia = $fechaPausa->diff($fechaActual);
        $totalPausado += $diferencia->days; // Sumar los días de pausa hasta el momento actual
    }

    $data_a_actualizar = [];
    

    if (!$detalle) {
        throw new Exception("Error al obtener el detalle de la actividad.");
    }
    $data_a_actualizar['dias_totales_reales'] = $detalle['dias_totales_reales']?$detalle['dias_totales_reales']:0;
    $data_a_actualizar['dias_totales_pendientes'] = $detalle['dias_totales_pendientes']?$detalle['dias_totales_pendientes']:0;
    $data_a_actualizar['dias_totales_retrasados'] = $detalle['dias_totales_retrasados']?$detalle['dias_totales_retrasados']:0;
    $data_a_actualizar['horas_totales_reales'] =  $detalle['horas_totales_reales']?$detalle['horas_totales_reales']:0;
    $data_a_actualizar['horas_totales_pendientes'] = $detalle['horas_totales_pendientes']?$detalle['horas_totales_pendientes']:0;
    $data_a_actualizar['horas_totales_retrasadas'] = $detalle['horas_totales_retrasadas']?$detalle['horas_totales_retrasadas']:0;
    $data_a_actualizar['dias_totales_pausados'] = $detalle['horas_totales_retrasadas']?$detalle['horas_totales_retrasadas']:0;

    $data_a_actualizar['progreso'] =  $detalle['progreso']?$detalle['progreso']:0;
    $data_a_actualizar['retraso'] =  $detalle['retraso']?$detalle['retraso']:0;
    $horas_por_dia = $detalle['dias_totales'] ? $detalle['horas_totales'] / $detalle['dias_totales']: 0;
    if($detalle['fecha_final_real'] != null){
        $fecha_actual = new DateTime();
        $fecha_inicial_real = new DateTime($detalle['fecha_inicial_real']);
        $fecha_final = new DateTime($detalle['fecha_final']);
        $dias_totales_reales = $fecha_actual->diff($fecha_inicial_real)->days;
        $dias_totales_retrasados = $fecha_actual->diff($fecha_final)->days;

        $data_a_actualizar['dias_totales_reales'] = $fecha_actual < $fecha_inicial_real ? max($dias_totales_reales - $detalle['dias_totales_pausados'],0): 0;
        $data_a_actualizar['dias_totales_retrasados'] = $dias_totales_retrasados > 0 ? max($dias_totales_retrasados - $detalle['dias_totales_pausados'],0): 0;
        $data_a_actualizar['dias_totales_pendientes'] = $detalle['dias_totales'] - $data_a_actualizar['dias_totales_reales'];

        $data_a_actualizar['horas_totales_reales'] = $horas_por_dia * $data_a_actualizar['dias_totales_reales'];
        $data_a_actualizar['horas_totales_pendientes'] = $detalle['horas_totales'] - $data_a_actualizar['horas_totales_reales'];
        $data_a_actualizar['horas_totales_retrasadas'] = $horas_por_dia * $data_a_actualizar['dias_totales_retrasados'];
    }
    if ($detalle['horas_totales'] > 0) {
        $data_a_actualizar['progreso'] = round(($data_a_actualizar['horas_totales_reales'] * 100) / $detalle['horas_totales'], 2);
        $data_a_actualizar['retraso'] = round(($data_a_actualizar['horas_totales_retrasadas'] * 100) / $detalle['horas_totales'], 2);
    } else {
        $data_a_actualizar['progreso'] = 0;
        $data_a_actualizar['retraso'] = 0;
    }
    $data_a_actualizar['dias_totales_pausados'] = $totalPausado;
    $data_a_actualizar['horas_totales_retrasadas'] += $horas_por_dia * $data_a_actualizar['dias_totales_pausados'];


    $data_a_actualizar = CleanJson($data_a_actualizar);

    $columnas = array_keys($data_a_actualizar);
    $set_parts = array_map(function ($columna) {
        return "$columna = :$columna";
    }, $columnas);
    $set_clause = implode(', ', $set_parts);
    $consulta_update = "UPDATE detalles_actividades SET $set_clause WHERE id_detalle_actividad  = :id_actividad";
    $resultado_update = $conexion->prepare($consulta_update);

    foreach ($data_a_actualizar as $columna => $valor) {
        $resultado_update->bindValue(":$columna", $valor);
    }
    $resultado_update->bindValue(":id_actividad", $id);

    if (!$resultado_update->execute()) {
        throw new Exception("Error al actualizar la actividad.");
    }
    return $resultado_update;
}

function verificarDatos($conexion, $tabla, $ColumnsCheck, $newformDataJson, $AlertDataSimilar,$edit=false) {
    $resultados = [];
    $checkdata = false; // Variable para indicar si se encontró algún dato

    foreach ($ColumnsCheck as $index => $columnCheck) {
        $column = $columnCheck['column'];
        $valor = $newformDataJson[$column]; // Obtener el valor correspondiente
        $check_similar = $columnCheck['check_similar'];

        // Verificar que el valor no sea nulo o vacío
        if ($valor !== null && $valor !== '') {
            // Verificar existencia exacta
            $consulta = "SELECT COUNT(*) AS existe FROM $tabla WHERE $column = :valor and kid_estatus != 3";
            $stmt = $conexion->prepare($consulta);
            $stmt->execute([':valor' => $valor]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($data['existe'] > 0 && $edit == false) {
                $checkdata = true;
                if (!isset($resultados['DataExist'])) {
                    $resultados['DataExist'] = [];
                }
                $resultados['DataExist'][] = $column;
            } else {
                // Si no existe, verificar si hay valores similares
                if ($check_similar) {
                    $consulta = "SELECT $column FROM $tabla WHERE $column LIKE :valor and kid_estatus != 3";
                    $stmt = $conexion->prepare($consulta);
                    $valor = preg_replace('/[0-9\s]+$/', '', $valor);
                    $stmt->execute([':valor' => '%' . $valor . '%']);
                    $DataSimilar = $stmt->fetchAll(PDO::FETCH_COLUMN);

                    if (count($DataSimilar) > 0) {
                        $checkdata = true;
                        if (!isset($resultados['DataSimilar'])) {
                            $resultados['DataSimilar'] = [];
                        }
                        $resultados['DataSimilar'][$column] = $DataSimilar; // Almacena los valores similares
                    } 
                }
                if($AlertDataSimilar === true) {
                    $checkdata = false;
                }
                
            }
        }
    }

    return [$resultados, $checkdata]; // Retorna los resultados y el estado de verificación
}


$data = []; // Inicializa la variable $data

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = null;
    $checkdata = null;
    $data_return = ['status' => 'error', 'message' => 'No se encontraron datos'];
    if (isset($_POST['modalCRUD']) && isset($_POST['opcion']) && isset($_POST['formDataJson'])) {
        $modalCRUD = $_POST['modalCRUD'];
        $opcion = $_POST['opcion'];
        $formDataJson = $_POST['formDataJson'];
        $formDataOldJson = isset($_POST['formDataOldJson']) ? $_POST['formDataOldJson'] : [];
        if (!is_array($formDataJson)) {
            $formDataJson = json_decode($formDataJson, true);
        }
        foreach ($formDataJson as $key => $value) {
            if ($value === '' || $value === null) {
                $formDataJson[$key] = null;
            }
        }
        $AlertDataSimilar = isset($_POST['AlertDataSimilar']) ? filter_var($_POST['AlertDataSimilar'], FILTER_VALIDATE_BOOLEAN) : null;
        $check_cambios_data =  $formDataJson;
        $tabla = null;
        $idcolumn = null;
        $consultaselect = null;
        $newformDataJson = null;
        $add_detalles = [];
        $add_detalles_table = null;
        $update_row_consult = '';
        $custombt = false;
        $array_status_check = [];
        $add_detalles_table = null;
        $add_detalles = null;
        $estatus = GetEstatusLabels();
        $funcion_select = null;

        switch ($modalCRUD) {
            case 'detalles_actividades':
                $id = $_POST['firstColumnValue'];

                $consulta = "SELECT * FROM detalles_actividades WHERE id_detalle_actividad = :id";
                $resultado = $conexion->prepare($consulta);
                $resultado->bindParam(':id', $id);
                $resultado->execute();
                $detalle = $resultado->fetch(PDO::FETCH_ASSOC);
                    
                /*-------------------- Obtener Tablas Foráneas --------------------*/
                $formDataJson['kid_personal_asignado'] = isset($formDataJson['kid_personal_asignado']) ? GetIDUsuariosByName($formDataJson['kid_personal_asignado']) : null;
                /*------------------- Fin Obtener Tablas Foráneas ------------------*/

                if(isset($formDataJson['kid_personal_asignado']) && $detalle['kid_personal_asignado'] != null && $formDataJson['kid_personal_asignado'] != $detalle['kid_personal_asignado']){
                    unset($formDataJson['kid_personal_asignado']);
                    break;
                }else if(isset($formDataJson['kid_personal_asignado']) && $detalle['kid_personal_asignado'] == null){
                    try {
                        $conexion->beginTransaction();
                        $consulta = "SELECT * FROM actividades WHERE id_actividad = :id";
                        $resultado = $conexion->prepare($consulta);
                        $resultado->bindParam(':id', $detalle['kid_actividad']);
                        $resultado->execute();
                        $actividad = $resultado->fetch(PDO::FETCH_ASSOC);

                        $consulta = "SELECT * FROM colaboradores WHERE id_colaborador = :id";
                        $resultado = $conexion->prepare($consulta);
                        $resultado->bindParam(':id', $formDataJson['kid_personal_asignado']);
                        $resultado->execute();
                        $colaborador = $resultado->fetch(PDO::FETCH_ASSOC);


                        $consulta_insertar = "INSERT INTO ocupaciones_th (
                            kid_colaborador, 
                            kid_bolsa_proyecto, 
                            kid_proyecto, 
                            estampa_inicio, 
                            estampa_fin, 
                            kid_internos_externos, 
                            kid_tipos_cantidad, 
                            cantidad_periodo, 
                            finalizado, 
                            kid_creacion, 
                            fecha_creacion, 
                            kid_estatus
                        ) VALUES (
                            :kid_colaborador, 
                            :kid_bolsa_proyecto, 
                            :kid_proyecto, 
                            :estampa_inicio, 
                            :estampa_fin, 
                            :kid_internos_externos, 
                            :kid_tipos_cantidad, 
                            :cantidad_periodo, 
                            :finalizado, 
                            :kid_creacion, 
                            :fecha_creacion, 
                            :kid_estatus
                        )";
                        
                        $resultado = $conexion->prepare($consulta_insertar);
                        
                        // Asignación de valores a los parámetros
                        $resultado->bindParam(':kid_colaborador', $colaborador['id_colaborador']);
                        $resultado->bindParam(':kid_bolsa_proyecto', $actividad['kid_bolsa_proyecto']);
                        $resultado->bindParam(':kid_proyecto', $actividad['kid_proyecto']);
                        $resultado->bindParam(':estampa_inicio', $detalle['fecha_inicial']);
                        $resultado->bindParam(':estampa_fin', $detalle['fecha_final']);
                        $resultado->bindParam(':kid_internos_externos', $colaborador['kid_internos_externos']);
                        $resultado->bindParam(':kid_tipos_cantidad', $colaborador['kid_tipo_cantidad']);
                        $resultado->bindParam(':cantidad_periodo', $colaborador['cantidad_periodo']);
                        $resultado->bindValue(':finalizado',0);
                        $resultado->bindParam(':kid_creacion', $_SESSION["s_id"]);
                        $resultado->bindValue(':fecha_creacion', date('Y-m-d H:i:s'));
                        $resultado->bindValue(':kid_estatus', 1);
                        $resultado->execute();


                        $conexion->commit();

                    } catch (Exception $e) {
                        // Si se lanza una excepción, hacer un rollback de la transacción
                        $conexion->rollBack();
                        // Manejar el error
                        $data = "Error: " . $e->getMessage();
                        //debug($data);
                        break;
                    }
                } 

                
                $tabla = 'detalles_actividades';
                $idcolumn= "id_detalle_actividad";
                
                $editformDataJson = CleanJson($formDataJson);
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
                WHERE da.kid_estatus != 3 and ".$idcolumn." = :".$idcolumn;

                
                //$funcion_select = 'selectDetallesActividades';
                $newformDataJson = $formDataJson;
                $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                $newformDataJson['kid_estatus'] = 1;

                $fuc_mapping = getButtonstoDetallesActividades();

                $ColumnsCheck = [];

                $text_colums_edit = [];
                break;


            case 'justificaciones_actividades':
                $tabla = 'justificaciones_actividades';
                $idcolumn= "id_justificacion_actividad";

                /*-------------------- Obtener Tablas Foráneas --------------------*/
                $formDataJson['kid_responsable'] = isset($formDataJson['kid_responsable']) ? GetIDUsuariosByName($formDataJson['kid_responsable']) : null;
                /*------------------- Fin Obtener Tablas Foráneas ------------------*/

                $editformDataJson = CleanJson($formDataJson);
                unset($editformDataJson['kid_detalle_actividad']);
                unset($editformDataJson['kid_actividad']);
                $newformDataJson = $formDataJson;
                $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                $newformDataJson['kid_estatus'] = 1;

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
                WHERE da.kid_estatus != 3 and ".$idcolumn." = :".$idcolumn;
                

                $ColumnsCheck = [];

                $text_colums_edit = [];
                break;

            case 'pausar_reanudar_detalles_actividades':
                $opcion = 2;
                $id = $_POST['firstColumnValue'];
                $consulta = "SELECT * FROM detalles_actividades WHERE id_detalle_actividad = :id";
                $resultado = $conexion->prepare($consulta);
                $resultado->bindParam(':id', $id);
                $resultado->execute();
                $detalle = $resultado->fetch(PDO::FETCH_ASSOC); 
                //debug($detalle);
                if($detalle['kid_estatus'] == 8){
                    break;
                }
                
                $editformDataJson = CleanJson($formDataJson);

                $tabla = 'detalles_actividades';
                $idcolumn= "id_detalle_actividad";
                //$funcion_select = 'selectDetallesActividades';

                
                $estatus = GetEstatusLabels();
                $caseEstatus = "CASE \n";
                foreach ($estatus as $key => $value) {
                    $caseEstatus .= "WHEN da.kid_estatus = $key THEN '$value'\n";
                }
                $caseEstatus .= "ELSE 'Desconocido' \nEND AS kid_estatus";
                $consultaselect = "SELECT da.id_detalle_actividad, 
                    da.actividad,
                    da.kid_actividad,
                    CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) as kid_personal_asignado,
                    $caseEstatus,
                    COALESCE(da.fecha_inicial_real, 'Sin Iniciar') AS fecha_inicial_real,
                    CASE 
                        WHEN da.fecha_inicial_real IS NOT NULL AND da.fecha_final_real IS NULL THEN 'Sin Finalizar'
                        ELSE COALESCE(da.fecha_final_real, 'Sin Iniciar')
                    END AS fecha_final_real,
                    COALESCE(a.dias_totales_reales, 0) AS dias_totales_reales,
                    COALESCE(a.horas_totales_reales, 0) AS horas_totales_reales,
                    da.progreso,
                    da.kid_estatus as estatus
                FROM 
                    detalles_actividades da
                LEFT JOIN actividades a ON da.kid_actividad = a.id_actividad 
                LEFT JOIN colaboradores u ON da.kid_personal_asignado = u.id_colaborador
                WHERE da.kid_estatus != 3 and ".$idcolumn." = :".$idcolumn;

                //debug($detalle);
                $editformDataJson['kid_tipo_justificacion'] = ((int) $detalle['kid_estatus'] == 2) ? 2:1;
                //debug($editformDataJson);
                $consulta_insertar = "INSERT INTO justificaciones_actividades 
                            (kid_actividad, kid_responsable, kid_tipo_justificacion, justificacion, latitud, longitud, kid_detalle_actividad, kid_creacion, fecha_creacion, kid_estatus) 
                    VALUES (:kid_actividad, :kid_responsable, :kid_tipo_justificacion, :justificacion, :latitud, :longitud, :kid_detalle_actividad, :kid_creacion, NOW(), 1)";
                $resultado = $conexion->prepare($consulta_insertar);
                $resultado->bindParam(':kid_actividad', $detalle['kid_actividad']);
                $resultado->bindParam(':kid_responsable',  $_SESSION["s_id"]);
                $resultado->bindParam(':kid_tipo_justificacion', $editformDataJson['kid_tipo_justificacion']);
                $resultado->bindParam(':justificacion', $editformDataJson['justificacion']);
                $resultado->bindParam(':latitud', $editformDataJson['latitud']);
                $resultado->bindParam(':longitud', $editformDataJson['longitud']);
                $resultado->bindParam(':kid_detalle_actividad', $detalle['id_detalle_actividad']);
                $resultado->bindParam(':kid_creacion', $_SESSION["s_id"]);
                //debug($editformDataJson);
                $editformDataJson = [];

                if ($resultado->execute() && $resultado->rowCount() > 0) {
                    $editformDataJson['kid_estatus'] = $detalle['kid_estatus'] == 2 ? 11: 2 ;
                } 
                //debug($editformDataJson);
                $ColumnsCheck = [];

                $text_colums_edit = [];
                break;

            case 'update_estatus_detalles_actividades':

                $opcion = 2;
                $id = $_POST['firstColumnValue'];
                $consulta = "SELECT * FROM detalles_actividades WHERE id_detalle_actividad = :id";
                $resultado = $conexion->prepare($consulta);
                $resultado->bindParam(':id', $id);
                $resultado->execute();
                $detalle = $resultado->fetch(PDO::FETCH_ASSOC); 
                //debug($detalle);
                if($detalle['kid_estatus'] !=10 && $detalle['fecha_inicial_real'] == null){
                    $editformDataJson['fecha_inicial_real'] = date('Y-m-d H:i:s');
                }

                $statusMap = ['iniciar' => 10];

                if (isset($formDataJson['UpdateEstatus']) && array_key_exists($formDataJson['UpdateEstatus'], $statusMap)) {
                    $editformDataJson['kid_estatus'] = $statusMap[$formDataJson['UpdateEstatus']];
                    if($editformDataJson['kid_estatus'] == 10 && $detalle['kid_personal_asignado'] == ''){
                        $data_return = ['status' => 'error', 'message' => 'Antes de iniciar una actividad, debe seleccionar un responsable.'];
                        break;
                    }
                }else{
                    break;
                }

                $tabla = 'detalles_actividades';
                $idcolumn= "id_detalle_actividad";
                $fuc_mapping = getButtonstoDetallesActividades();

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
                WHERE da.kid_estatus != 3 and ".$idcolumn." = :".$idcolumn;


                //debug($editformDataJson);
                $ColumnsCheck = [];

                $text_colums_edit = [];
                break;

            case 'finalizar_detalles_actividades':
                try {
                    $opcion = 2;
                    $id = $_POST['firstColumnValue'];
                    $consulta = "SELECT da.*,
                        bp.bolsa_proyecto,
                        p.proyecto
                    FROM detalles_actividades da 
                    LEFT JOIN actividades a ON da.kid_actividad = a.id_actividad 
                    LEFT JOIN bolsas_proyectos bp ON a.kid_bolsa_proyecto = bp.id_bolsa_proyecto
                    LEFT JOIN proyectos p ON a.kid_proyecto = p.id_proyecto 
                    WHERE da.id_detalle_actividad = :id";
                    $resultado = $conexion->prepare($consulta);
                    $resultado->bindParam(':id', $id);
                    $resultado->execute();
                    $detalle = $resultado->fetch(PDO::FETCH_ASSOC); 
                    //$funcion_select = 'selectDetallesActividades';
                    //debug($detalle);
                    //debug($detalle);
                    if($detalle['kid_estatus'] == 8){
                        break;
                    }
                    
                    $editformDataJson = CleanJson($formDataJson);
            
                    $tabla = 'detalles_actividades';
                    $idcolumn= "id_detalle_actividad";
            
                    
                    $estatus = GetEstatusLabels();
                    $caseEstatus = "CASE \n";
                    foreach ($estatus as $key => $value) {
                        $caseEstatus .= "WHEN da.kid_estatus = $key THEN '$value'\n";
                    }
                    $caseEstatus .= "ELSE 'Desconocido' \nEND AS kid_estatus";
                    $consultaselect = "SELECT da.id_detalle_actividad, 
                        da.actividad,
                        da.kid_actividad,
                        CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) as kid_personal_asignado,
                        $caseEstatus,
                        COALESCE(da.fecha_inicial_real, 'Sin Iniciar') AS fecha_inicial_real,
                        CASE 
                            WHEN da.fecha_inicial_real IS NOT NULL AND da.fecha_final_real IS NULL THEN 'Sin Finalizar'
                            ELSE COALESCE(da.fecha_final_real, 'Sin Iniciar')
                        END AS fecha_final_real,
                        COALESCE(a.dias_totales_reales, 0) AS dias_totales_reales,
                        COALESCE(a.horas_totales_reales, 0) AS horas_totales_reales,
                        da.progreso,
                        da.kid_estatus as estatus
                    FROM 
                        detalles_actividades da
                    LEFT JOIN actividades a ON da.kid_actividad = a.id_actividad 
                    LEFT JOIN colaboradores u ON da.kid_personal_asignado = u.id_colaborador
                    WHERE da.kid_estatus != 3 and ".$idcolumn." = :".$idcolumn;
            
                    $consulta_insertar = "INSERT INTO evidencia_actividades 
                    (ruta_nombre_archivo, json_metadata, kid_detalle_actividad, kid_creacion, fecha_creacion, kid_estatus) 
                    VALUES (:ruta_nombre_archivo, :json_metadata, :kid_detalle_actividad,:kid_creacion, NOW(), 1)";
            
                    if (isset($_FILES['imgs'])) {
                         // Iniciar transacción
                        $conexion->beginTransaction();
                        foreach ($_FILES['imgs']['name'] as $key => $name) {
                            $tmpName = $_FILES['imgs']['tmp_name'][$key];
                            $error = $_FILES['imgs']['error'][$key];
                            $size = $_FILES['imgs']['size'][$key];
                            $type = $_FILES['imgs']['type'][$key];
            
                            // Validar si hay un error al cargar el archivo
                            if ($error !== UPLOAD_ERR_OK) {
                                throw new Exception("Error al cargar la imagen: $name");
                            }
            
                            // Obtener los metadatos de la imagen
                            $json_metadata = obtenerMetadatos($tmpName);

                            // Validar el formato del archivo
                            $foto_extension = pathinfo($name, PATHINFO_EXTENSION);
                            if (!in_array(strtolower($foto_extension), ['jpg', 'jpeg', 'png', 'gif'])) {
                                throw new Exception("Extensión de archivo no soportada para la imagen: $name");
                            }
            
                            // Crear la ruta donde se guardará la imagen
                            $bolsa_proyecto = strtolower(str_replace(' ', '_', $detalle['bolsa_proyecto']));
                            $proyecto = strtolower(str_replace(' ', '_', $detalle['proyecto']));
                            $id_actividad = strtolower(str_replace(' ', '_', $detalle['kid_actividad']));
                            $actividad = strtolower(str_replace(' ', '_', $detalle['actividad']));
                            $ruta_carpeta = dirname(__DIR__, 3)."/archivos/evidencias_actividades/$bolsa_proyecto/$proyecto/actividad_$id_actividad/$actividad";
            
                            if (!is_dir($ruta_carpeta)) {
                                mkdir($ruta_carpeta, 0777, true);
                            }
            
                            $nombre_evidencia = "evidencia_" . $actividad . "_" . $key . "_" . date("Ymd_His");
                            $ruta_destino = $ruta_carpeta . '/' . $nombre_evidencia . '.' . $foto_extension;
            
                            // Mover el archivo desde el directorio temporal al destino final
                            if (!move_uploaded_file($tmpName, $ruta_destino)) {
                                throw new Exception("Error al guardar la imagen en la ruta: $ruta_destino");
                            }
            
                            // Validar que el archivo fue guardado correctamente
                            if (!file_exists($ruta_destino)) {
                                throw new Exception("La imagen no existe en la ruta: $ruta_destino");
                            }
            
                            // Verificar los metadatos de la imagen
                            $image_info = getimagesize($ruta_destino);
                            if ($image_info === false) {
                                throw new Exception("La imagen guardada no es válida: $ruta_destino");
                            }
    
                
                            $json_metadata_string = json_encode($json_metadata, JSON_UNESCAPED_UNICODE);
                            $resultado = $conexion->prepare($consulta_insertar);
                            $resultado->bindParam(':ruta_nombre_archivo', $ruta_destino);
                            $resultado->bindParam(':json_metadata',  $json_metadata_string);
                            $resultado->bindParam(':kid_detalle_actividad', $detalle['id_detalle_actividad']);
                            $resultado->bindParam(':kid_creacion', $_SESSION["s_id"]);
                            $resultado->bindParam(':comentario', $formDataJson['comentario']);

                            // Ejecutar la consulta
                            if (!$resultado->execute()) {
                                // Si la consulta falla, lanzar una excepción
                                throw new Exception("Error al insertar datos");
                            }
                        }
                        // Confirmar la transacción si todas las consultas se ejecutaron correctamente
                        $conexion->commit();
                    }
                    $editformDataJson = [];
                    $editformDataJson['kid_estatus'] = 8;
            
                    $ColumnsCheck = [];
            
                    $text_colums_edit = [];
                } catch (Exception $e) {
                    // Si se lanza una excepción, hacer un rollback de la transacción
                    $conexion->rollBack();
                    // Manejar el error
                    $data = "Error: " . $e->getMessage();
                    $tabla = null;
                    $idcolumn = null;
                    //debug($data);
                }
                break;

            case 'subir_evidencias':
                try {
                    $opcion = 2;
                    $id = $_POST['firstColumnValue'];
                    $consulta = "SELECT da.*,
                        bp.bolsa_proyecto,
                        p.proyecto
                    FROM detalles_actividades da 
                    LEFT JOIN actividades a ON da.kid_actividad = a.id_actividad 
                    LEFT JOIN bolsas_proyectos bp ON a.kid_bolsa_proyecto = bp.id_bolsa_proyecto
                    LEFT JOIN proyectos p ON a.kid_proyecto = p.id_proyecto 
                    WHERE da.id_detalle_actividad = :id";
                    $resultado = $conexion->prepare($consulta);
                    $resultado->bindParam(':id', $id);
                    $resultado->execute();
                    $detalle = $resultado->fetch(PDO::FETCH_ASSOC); 
                    //$funcion_select = 'selectDetallesActividades';
                    //debug($detalle);
                    //debug($detalle);
                    if($detalle['kid_estatus'] == 8){
                        break;
                    }
                    
                    $editformDataJson = CleanJson($formDataJson);
            
                    $tabla = 'detalles_actividades';
                    $idcolumn= "id_detalle_actividad";
            
                    
                    $estatus = GetEstatusLabels();
                    $caseEstatus = "CASE \n";
                    foreach ($estatus as $key => $value) {
                        $caseEstatus .= "WHEN da.kid_estatus = $key THEN '$value'\n";
                    }
                    $caseEstatus .= "ELSE 'Desconocido' \nEND AS kid_estatus";
                    $consultaselect = "SELECT da.id_detalle_actividad, 
                        da.actividad,
                        da.kid_actividad,
                        CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) as kid_personal_asignado,
                        $caseEstatus,
                        COALESCE(da.fecha_inicial_real, 'Sin Iniciar') AS fecha_inicial_real,
                        CASE 
                            WHEN da.fecha_inicial_real IS NOT NULL AND da.fecha_final_real IS NULL THEN 'Sin Finalizar'
                            ELSE COALESCE(da.fecha_final_real, 'Sin Iniciar')
                        END AS fecha_final_real,
                        COALESCE(a.dias_totales_reales, 0) AS dias_totales_reales,
                        COALESCE(a.horas_totales_reales, 0) AS horas_totales_reales,
                        da.progreso,
                        da.kid_estatus as estatus
                    FROM 
                        detalles_actividades da
                    LEFT JOIN actividades a ON da.kid_actividad = a.id_actividad 
                    LEFT JOIN colaboradores u ON da.kid_personal_asignado = u.id_colaborador
                    WHERE da.kid_estatus != 3 and ".$idcolumn." = :".$idcolumn;
            
                    $consulta_insertar = "INSERT INTO evidencia_actividades 
                    (ruta_nombre_archivo, json_metadata, kid_detalle_actividad, kid_creacion, comentario, fecha_creacion, kid_estatus) 
                    VALUES (:ruta_nombre_archivo, :json_metadata, :kid_detalle_actividad,:kid_creacion,:comentario, NOW(), 1)";
            
                    if (isset($_FILES['imgs'])) {
                            // Iniciar transacción
                        $conexion->beginTransaction();
                        foreach ($_FILES['imgs']['name'] as $key => $name) {
                            $tmpName = $_FILES['imgs']['tmp_name'][$key];
                            $error = $_FILES['imgs']['error'][$key];
                            $size = $_FILES['imgs']['size'][$key];
                            $type = $_FILES['imgs']['type'][$key];
            
                            // Validar si hay un error al cargar el archivo
                            if ($error !== UPLOAD_ERR_OK) {
                                throw new Exception("Error al cargar la imagen: $name");
                            }
            
                            // Obtener los metadatos de la imagen
                            $json_metadata = obtenerMetadatos($tmpName);
                            // Validar el formato del archivo
                            $foto_extension = pathinfo($name, PATHINFO_EXTENSION);
                            if (!in_array(strtolower($foto_extension), ['jpg', 'jpeg', 'png', 'gif'])) {
                                throw new Exception("Extensión de archivo no soportada para la imagen: $name");
                            }
            
                            // Crear la ruta donde se guardará la imagen
                            $bolsa_proyecto = strtolower(str_replace(' ', '_', $detalle['bolsa_proyecto']));
                            $proyecto = strtolower(str_replace(' ', '_', $detalle['proyecto']));
                            $id_actividad = strtolower(str_replace(' ', '_', $detalle['kid_actividad']));
                            $actividad = strtolower(str_replace(' ', '_', $detalle['actividad']));
                            //debug(dirname(__DIR__, 3));
                            $ruta_carpeta = dirname(__DIR__, 3)."/archivos/evidencias_actividades/$bolsa_proyecto/$proyecto/actividad_$id_actividad/$actividad";
            
                            if (!is_dir($ruta_carpeta)) {
                                mkdir($ruta_carpeta, 0777, true);
                            }
            
                            $nombre_evidencia = "evidencia_" . $actividad . "_" . $key . "_" . date("Ymd_His");
                            $ruta_destino = $ruta_carpeta . '/' . $nombre_evidencia . '.' . $foto_extension;
            
                            // Mover el archivo desde el directorio temporal al destino final
                            if (!move_uploaded_file($tmpName, $ruta_destino)) {
                                throw new Exception("Error al guardar la imagen en la ruta: $ruta_destino");
                            }
            
                            // Validar que el archivo fue guardado correctamente
                            if (!file_exists($ruta_destino)) {
                                throw new Exception("La imagen no existe en la ruta: $ruta_destino");
                            }
            
                            // Verificar los metadatos de la imagen
                            $image_info = getimagesize($ruta_destino);
                            if ($image_info === false) {
                                throw new Exception("La imagen guardada no es válida: $ruta_destino");
                            }
    
                
                            $json_metadata_string = json_encode($json_metadata, JSON_UNESCAPED_UNICODE);
                            $resultado = $conexion->prepare($consulta_insertar);
                            $resultado->bindParam(':ruta_nombre_archivo', $ruta_destino);
                            $resultado->bindParam(':json_metadata',  $json_metadata_string);
                            $resultado->bindParam(':kid_detalle_actividad', $detalle['id_detalle_actividad']);
                            $resultado->bindParam(':kid_creacion', $_SESSION["s_id"]);
                            $resultado->bindParam(':comentario', $formDataJson['comentario']);
                
                            // Ejecutar la consulta
                            if (!$resultado->execute()) {
                                // Si la consulta falla, lanzar una excepción
                                throw new Exception("Error al insertar datos");
                            }
                        }
                        // Confirmar la transacción si todas las consultas se ejecutaron correctamente
                        $conexion->commit();
                    }
                    $editformDataJson = [];
                    $ColumnsCheck = [];
            
                    $text_colums_edit = [];
                } catch (Exception $e) {
                    // Si se lanza una excepción, hacer un rollback de la transacción
                    $conexion->rollBack();
                    // Manejar el error
                    $data = "Error: " . $e->getMessage();
                    debug($data);
                    $tabla = null;
                    $idcolumn = null;
                    //debug($data);
                }
                break;

            default:
            $data_return = ['status' => 'error', 'message' => 'Operación no válida'];
                break;
        }

        if($tabla != null &&  $idcolumn != null){
            switch ($opcion) {
                case 1:
                    $resultados = [];

                    list($resultados, $checkdata) = verificarDatos($conexion, $tabla, $ColumnsCheck, $newformDataJson,$AlertDataSimilar);

                    

                    if(!$checkdata){
                        $columnas = [];
                        $columnas2 = [];
                        foreach ($newformDataJson as $key => $value) {
                            $columnas[] = $key;
                            $columnas2[] = ':'.$key;
                        }
                        $consulta = "INSERT INTO ".$tabla." (".implode(',', $columnas).") VALUES (".implode(',', $columnas2).")";
                        $resultado = $conexion->prepare($consulta);
                        foreach ($newformDataJson as $key => $value) {
                            $resultado->bindParam(':'.$key, $newformDataJson[$key]);
                        }
                        if ($resultado->execute()) {
                            $columnas =[];
                            $lastId = $conexion->lastInsertId();
                            foreach ($formDataJson as $key => $value) {
                                $columnas[] = $key;
                            }

                            $resultado = $conexion->prepare($consultaselect);
                            $resultado->bindParam(":$idcolumn", $lastId, PDO::PARAM_INT);
                            $resultado->execute();
                            $data_resultado=$resultado->fetch(PDO::FETCH_ASSOC);
                            
                            if(isset($fuc_mapping)){
                                $data_resultado = array_map($fuc_mapping, [$data_resultado])[0];
                            }

                            $data = $data_resultado;

                            if (!empty($add_detalles)) {
                                // Iniciar la transacción
                                $conexion->beginTransaction();
                            
                                try {
                                    // Preparar la consulta de inserción
                                    $insertQuery = "INSERT INTO $add_detalles_table (" . implode(',', array_keys($add_detalles[0])) . ") VALUES ";
                            
                                    // Crear un array para almacenar los parámetros
                                    $params = [];
                                    $values = [];
                            
                                    // Generar los valores para la consulta
                                    foreach ($add_detalles as $detalle) {
                                        // Reemplazar el valor ':id' por el valor de $lastId
                                        foreach ($detalle as $key => $value) {
                                            if ($value === ':id') {
                                                $detalle[$key] = $lastId; // Asignar el valor de $lastId
                                            }
                                        }
                            
                                        $placeholders = [];
                                        foreach ($detalle as $key => $value) {
                                            $placeholders[] = ":$key"; // Crear un placeholder para cada valor
                                            $params[":$key"] = $value; // Asignar el valor al array de parámetros
                                        }
                                        $values[] = '(' . implode(',', $placeholders) . ')'; // Agregar los placeholders a la lista de valores
                                    }
                            
                                    // Completar la consulta
                                    $insertQuery .= implode(',', $values);
                            
                                    // Preparar la consulta
                                    $stmt = $conexion->prepare($insertQuery);
                            
                                    // Ejecutar la consulta con los parámetros
                                    if ($stmt->execute($params)) {
                                        // Confirmar la transacción si todas las inserciones fueron exitosas
                                        $resultado = $conexion->prepare($update_row_consult);
                                        $resultado->execute();
                                        $data_resultado = $resultado->fetch(PDO::FETCH_ASSOC);
                                        $conexion->commit();
                                    } else {
                                        // Si hay un error en la ejecución, lanzar una excepción
                                        throw new Exception("Error al insertar en la tabla $add_detalles_table.");
                                    }
                                } catch (Exception $e) {
                                    // Si hay un error, revertir la transacción
                                    $conexion->rollBack();
                                    echo "Transacción fallida: " . $e->getMessage();
                                }
                            }
                        }
                    }else{
                        $data =  $resultados;
                    }
                    break;

                case 2:
                    $data_element = [];
                    try {
                        $conexion->beginTransaction();
                        $resultados = [];

                        list($resultados, $checkdata) = verificarDatos($conexion, $tabla, $ColumnsCheck, $editformDataJson,$AlertDataSimilar,true);
                        
                        if(!$checkdata){
                            if (isset($_POST['firstColumnValue']) && is_numeric($_POST['firstColumnValue'])) {
                                $id = $_POST['firstColumnValue'];

                                $consulta = "SELECT * FROM $tabla WHERE $idcolumn = :id";
                                $resultado = $conexion->prepare($consulta);
                                $resultado->bindParam(":id", $id, PDO::PARAM_INT);
                                $resultado->execute();
                                $data_element = $resultado->fetch(PDO::FETCH_ASSOC);
                                //debug($data_element);

                                foreach ($editformDataJson as $key => $value) {
                                    if (array_key_exists($key, $data_element)) {
                                        if ($data_element[$key] !== null) {
                                            $dataType = gettype($data_element[$key]);
                                            settype($value, $dataType);
                                            if ($data_element[$key] === $value) {
                                                unset($editformDataJson[$key]);
                                                unset($text_colums_edit[$key]);
                                                unset($formDataOldJson[$key]);
                                            }
                                        }
                                    }
                                }

                                foreach ($text_colums_edit as $key => $value) {
                                    if (array_key_exists($key, $check_cambios_data)) {
                                        if (str_contains($text_colums_edit[$key], "#valor_anterior")) {
                                            $text_colums_edit[$key] = str_replace("#valor_anterior",strval($formDataOldJson[$key]), $text_colums_edit[$key]);
                                        }
                                        if (str_contains($text_colums_edit[$key], "#nuevo_valor")) {
                                            $text_colums_edit[$key] = str_replace("#nuevo_valor",strval($check_cambios_data[$key]), $text_colums_edit[$key]);
                                        }
                                        if (str_contains($text_colums_edit[$key], "#id_editado")) {
                                            $text_colums_edit[$key] = str_replace("#id_editado",strval($id), $text_colums_edit[$key]);
                                        }
                                    }
                                }

                                if (!empty($editformDataJson)) {
                                    $columnas = [];
                                    foreach ($editformDataJson as $key => $value) {
                                        $columnas[] = $key;
                                    }
                            
                                    $setPart = [];
                                    foreach ($columnas as $key) {
                                        $setPart[] = "$key = :$key";
                                    }
                                    
                                    $consulta = "UPDATE " . $tabla . " SET " . implode(', ', $setPart) . " WHERE " . $idcolumn . " = :id";
                                    
                                    $resultado = $conexion->prepare($consulta);
                                    
                                    foreach ($editformDataJson as $key => $value) {
                                        $resultado->bindValue(":$key", $value);
                                    }
                                    
                                    $resultado->bindValue(":id", $id);
                                    
                                    if ($resultado->execute()) {
                                        $columnas = [];
                                        $lastId = $id; // Usa el ID que ya tienes
                                        foreach ($formDataJson as $key => $value) {
                                            $columnas[] = $key;
                                        }
                                        //debug($consultaselect);
                                        //debug($lastId);

                                        if($funcion_select != null){
                                            $data_resultado = $funcion_select($lastId);
                                        }else{
                                            $resultado = $conexion->prepare($consultaselect);
                                            $resultado->bindParam(":$idcolumn", $lastId, PDO::PARAM_INT);
                                            $resultado->execute();
                                            $data_resultado = $resultado->fetch(PDO::FETCH_ASSOC);
                                        }

                                        if(isset($fuc_mapping)){
                                            $data_resultado = array_map($fuc_mapping, [$data_resultado])[0];
                                        }
                                        //debug($data_resultado);
                                        $data = $data_resultado;
                                    }
                                }else{
                                    $data_return = ['status' => 'nocambios', 'message' => 'No hay nuevos cambios que guardar.'];
                                }
                            } else {
                                $data_return = ['status' => 'error', 'message' => 'Elemento no valido.'];
                            }
                        }else{
                            $data =  $resultados;
                        }
                        
                        
                        // Confirmar la transacción si todo está bien
                        $conexion->commit();
                    } catch (Exception $e) {
                        // Hacer rollback en caso de error
                        $conexion->rollback();
                        $text = "Error en la base de datos: " . $e->getMessage();
                        echo("Error en la base de datos: " . $e->getMessage());
                        $data_return = ['status' => 'error', 'message' => $text];
                    }
                    try{
                        $conexion->beginTransaction();
                        if(isset($data_element['id_detalle_actividad'])){
                            updateDetalleActividad($data_element['id_detalle_actividad'],$conexion);
                        }
                        if(isset($data_element['kid_actividad'])){
                            updateActividad($data_element['kid_actividad'],$conexion);
                        }

                        
                        // Confirmar la transacción si todo está bien
                        $conexion->commit();
                    } catch (Exception $e) {
                        // Hacer rollback en caso de error
                        $conexion->rollback();
                        $text = "Error en la base de datos: " . $e->getMessage();
                        echo("Error en la base de datos: " . $e->getMessage());
                        $data_return = ['status' => 'error', 'message' => $text];
                    }
                    break;

                case 3: // Eliminar
                    if (isset($_POST['firstColumnValue']) && is_numeric($_POST['firstColumnValue'])) {
                        $id = $_POST['firstColumnValue'];

                        $consulta = "UPDATE ".$tabla." SET kid_estatus = :kid_estatus WHERE " . $idcolumn . " = :id";
                        $resultado = $conexion->prepare($consulta);
                        $kid_estatus = '3'; // Asignar el nuevo estatus
                        $resultado->bindParam(':kid_estatus', $kid_estatus);
                        $resultado->bindParam(':id', $id);
                        
                        if ($resultado->execute()) {
                            $consulta = "SELECT * FROM " . $tabla . " WHERE " . $idcolumn . " = :id and kid_estatus !=3";
                            $resultado = $conexion->prepare($consulta);
                            $resultado->bindParam(':id', $id); // Usa el ID que ya tienes
                            $resultado->execute();
                            $data_resultado = $resultado->fetch(PDO::FETCH_ASSOC);
                
                            $data = $data_resultado;
                            if($data_resultado){
                                $data = false;
                            }else{
                                $data = true;
                            }
                        }
                    } else {
                        $data_return = ['status' => 'error', 'message' => 'Elemento no valido.'];
                    }
                    break;

                case 4:
                    break;
    
                default:
                $data_return = ['status' => 'error', 'message' => 'Operación no válida'];
                    break;
            }
            if ($data && !$checkdata) {
                $data_return = ['status' => 'success', 'data' => $data];
            } else if($checkdata){
                $data_return = ['status' => 'error', 'checkdata' => $data];
            }
        }

    }else{
        $data_return = ['status' => 'error', 'message' => 'Faltan datos requeridos'];
    } 

} else {
    $data_return = ['status' => 'error', 'message' => 'Método no permitido'];
    echo 'Esta página solo admite solicitudes POST.';
}

print json_encode($data_return, JSON_UNESCAPED_UNICODE);
//debug($data_return)
?>