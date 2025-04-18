<?php
$objeto = new Conexion();
$conexion = $objeto->Conectar();

$data_script['botones_acciones'] = [
    '<button class="ModalDataView btn btn-primary primary" modalCRUD="${modalCRUD}"><i class="bi bi-eye"></i> Ver</button>',
    '<button class="ModalDataEdit btn btn-warning warning" modalCRUD="${modalCRUD}"><i class="bi bi-pencil"></i> Editar</button>',
    '<button class="ModalDataDelete btn btn-danger danger" modalCRUD="${modalCRUD}"><i class="bi bi-trash"></i> Eliminar</button>'
];

function insertarDespuesDeClave($array, $clave, $nuevoElemento) {
    // Obtener las claves del array
    $claves = array_keys($array);

    // Encontrar la posición de la clave
    $pos = array_search($clave, $claves);

    // Si se encuentra la clave, insertar el nuevo elemento después de ella
    if ($pos !== false) {
        // Dividir el array en dos partes: antes y después de la clave
        $antes = array_slice($array, 0, $pos + 1, true);
        $despues = array_slice($array, $pos + 1, null, true);

        // Combinar las partes con el nuevo elemento
        $array = array_merge($antes, $nuevoElemento, $despues);
    }

    return $array;
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
        $newbuttons = null;

        switch ($modalCRUD) {
            case 'colaboradores':
                if(isset($formDataJson['password1']) && isset($formDataJson['password2'])){

                    if($formDataJson['password1'] != $formDataJson['password2']){
                        $data = ['status' => 'error', 'message' => 'No se encontraron datos'];
                        return;
                    }
                    $password = $formDataJson['password1'];
                    $email = $formDataJson['email'];

                    $temp=$password.$email;
	                $hash = password_hash($temp, PASSWORD_DEFAULT);
                    unset($formDataJson['password1']);
                    unset($formDataJson['password2']);
                    $formDataJson['password'] = $hash;
                }else{
                    if (!isset($formDataJson['password1']) && $opcion == 1 && $formDataJson['login'] == 0) {
                        $password = "P4r@d1sE";
                        $email = $formDataJson['email'];

                        $temp=$password.$email;
                        $hash = password_hash($temp, PASSWORD_DEFAULT);
                        unset($formDataJson['password1']);
                        unset($formDataJson['password2']);
                        $formDataJson['password'] = $hash;
                    }
                    
                }
                unset($formDataJson['password1']);
                unset($formDataJson['password2']);
                $tabla = 'colaboradores';
                $idcolumn= "id_colaborador";
                
                /*-------------------- Obtener Tablas Foráneas --------------------*/
                $tipos_usuarios = GetTiposUsuariosListById();
                $estados = GetEstadosListById();
                $tipo_contrato = GetTipoContratoListById();
                $estado_civil = GetEstadoCivilListById();
                $formDataJson['kid_internos_externos'] = isset($formDataJson['kid_internos_externos']) ? GetIDInternosExternosByName($formDataJson['kid_internos_externos']): null;
                $formDataJson['kid_tipo_cantidad'] = isset($formDataJson['kid_tipo_cantidad']) ? GetIDTiposCostosByName($formDataJson['kid_tipo_cantidad']): null;
                /*------------------- Fin Obtener Tablas Foráneas ------------------*/

                if (!empty($formDataJson['kid_tipo_usuario']) && isset($tipos_usuarios[$formDataJson['kid_tipo_usuario']])) {
                    $formDataJson['kid_tipo_usuario'] = $tipos_usuarios[$formDataJson['kid_tipo_usuario']];
                }

                if (!empty($formDataJson['kid_estado']) && isset($estados[$formDataJson['kid_estado']])) {
                    $formDataJson['kid_estado'] = $estados[$formDataJson['kid_estado']];
                }

                if (!empty($formDataJson['kid_tipo_contrato']) && isset($tipo_contrato[$formDataJson['kid_tipo_contrato']])) {
                    $formDataJson['kid_tipo_contrato'] = $tipo_contrato[$formDataJson['kid_tipo_contrato']];
                }
                if (!empty($formDataJson['kid_estado_civil']) && isset($estado_civil[$formDataJson['kid_estado_civil']])) {
                    $formDataJson['kid_estado_civil'] = $estado_civil[$formDataJson['kid_estado_civil']];
                }

                $editformDataJson = CleanJson($formDataJson);

                
                $newformDataJson = $formDataJson;
                $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                $newformDataJson['kid_estatus'] = 1;
                $consultaselect = "SELECT u.id_colaborador, 
                    CONCAT(nombre, ' ', apellido_paterno, ' ', apellido_materno) AS nombre,
                    ie.internos_externos,
                    u.email,
                    tu.tipo_usuario AS kid_tipo_usuario,
                    CASE 
                        WHEN u.login = 1 THEN 'SÍ'  
                        ELSE 'NO' 
                    END,
                    u.fecha_creacion
                    FROM $tabla u
                    LEFT JOIN tipos_usuario tu ON u.kid_tipo_usuario = tu.id_tipo_usuario
                    LEFT JOIN internos_externos ie ON u.kid_internos_externos = ie.id_internos_externos
                    WHERE u.".$idcolumn." = :".$idcolumn;

                    $ColumnsCheck = isset($newformDataJson['email']) ? [['column' => "email", "check_similar" => true]] : [];
                    $text_colums_edit = [];

                break;


            case 'editar_asistencias_th':
                $tabla = 'asistencias_th';
                $idcolumn= "id_asistencia_th";

                /*-------------------- Obtener Tablas Foráneas --------------------*/
                $formDataJson['kid_colaborador'] = isset($formDataJson['kid_colaborador']) ? GetIDUsuariosByName($formDataJson['kid_colaborador']) : null;
                $formDataJson['kid_internos_externos'] = isset($formDataJson['kid_internos_externos']) ?GetIDInternosExternosByName($formDataJson['kid_internos_externos']) : null;
                $formDataJson['kid_tipos_cantidad'] = isset($formDataJson['kid_tipos_cantidad']) ? GetIDTiposCostosByName($formDataJson['kid_tipos_cantidad']) : null;
                /*------------------- Fin Obtener Tablas Foráneas ------------------*/

                $formDataJson['estampa_entrada'] = $formDataJson['estampa_entrada_date'].' '.$formDataJson['estampa_entrada_time'];
                unset($formDataJson['estampa_entrada_date']);
                unset($formDataJson['estampa_entrada_time']);
                $formDataJson['estampa_salida'] = $formDataJson['estampa_salida_date'].' '.$formDataJson['estampa_salida_time'];
                unset($formDataJson['estampa_salida_date']);
                unset($formDataJson['estampa_salida_time']);

                $editformDataJson = CleanJson($formDataJson);

                

                $newformDataJson = $formDataJson;
                $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                $newformDataJson['kid_estatus'] = 1;
                $consultaselect = "SELECT a.id_asistencia_th, 
                    CONCAT(c.nombre, ' ', c.apellido_paterno, ' ', c.apellido_materno) AS kid_colaborador,
                    a.estampa_entrada,
                    a.estampa_salida,
                    ie.internos_externos as kid_internos_externos,
                    a.fecha_creacion
                FROM 
                    asistencias_th a
                LEFT JOIN colaboradores c ON a.kid_colaborador = c.id_colaborador
                LEFT JOIN internos_externos ie ON a.kid_internos_externos = ie.id_internos_externos 
                WHERE 
                    a.kid_estatus != 3 and ".$idcolumn." = :".$idcolumn;

                $ColumnsCheck = [];


                $text_colums_edit = [];

                break;


            case 'adicionales_asistencias_th':
                $tabla = 'adicionales_asistencias_th';
                $idcolumn= "id_asistencia_th";
                

                /*-------------------- Obtener Tablas Foráneas --------------------*/
                $formDataJson['kid_colaborador'] = isset($formDataJson['kid_colaborador']) ? GetIDUsuariosByName($formDataJson['kid_colaborador']) : null;
                $formDataJson['kid_interno_externo'] = isset($formDataJson['kid_interno_externo']) ? GetIDInternosExternosByName($formDataJson['kid_interno_externo']) : null;
                $formDataJson['kid_tipos_cantidad'] = isset($formDataJson['kid_tipos_cantidad']) ? GetIDTiposCostosByName($formDataJson['kid_tipos_cantidad']) : null;
                $formDataJson['kid_tipo_adicional_th'] = isset($formDataJson['kid_tipo_adicional_th']) ? GetIDTiposAdicionalesByName($formDataJson['kid_tipo_adicional_th']) : null;
                /*------------------- Fin Obtener Tablas Foráneas ------------------*/
                //debug($_FILES);
                try {
                    $conexion->beginTransaction();
                    $consultaselect = "SELECT * FROM colaboradores WHERE kid_estatus != 3";
                    $resultado = $conexion->prepare($consultaselect);
                    $resultado->execute();
                    $colaborador = $resultado->fetch(PDO::FETCH_ASSOC);
                    $formDataJson['kid_interno_externo'] = $colaborador['kid_internos_externos'];
                    $formDataJson['kid_tipos_cantidad'] = $colaborador['kid_tipo_cantidad'];
                    $formDataJson['cantidad_periodo'] = $colaborador['cantidad_periodo'];
                    $conexion->commit();
                } catch (Exception $e) {
                    // Si se lanza una excepción, hacer un rollback de la transacción
                    $conexion->rollBack();
                    // Manejar el error
                    $data = "Error: " . $e->getMessage();
                    debug($data);
                    break;
                }
                

                $editformDataJson = CleanJson($formDataJson);

                $newformDataJson = $formDataJson;
                $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                $newformDataJson['kid_estatus'] = 1;
                $consultaselect = "SELECT aa.id_asistencia_th, 
                    CONCAT(c.nombre, ' ', c.apellido_paterno, ' ', c.apellido_materno) AS kid_colaborador,
                    ta.tipo_adicional_th,
                    aa.comentario,
                    ie.internos_externos as kid_internos_externos,
                    aa.fecha_creacion
                FROM 
                    adicionales_asistencias_th aa
                LEFT JOIN colaboradores c ON aa.kid_colaborador = c.id_colaborador
                LEFT JOIN internos_externos ie ON aa.kid_interno_externo = ie.id_internos_externos 
                LEFT JOIN tipos_adicionales_th ta ON aa.kid_tipo_adicional_th = ta.id_tipo_adicional_th
                LEFT JOIN tipos_costo tp ON aa.kid_tipos_cantidad = tp.id_tipo_costo  
                WHERE 
                    aa.kid_estatus != 3 and ".$idcolumn." = :".$idcolumn;

                $ColumnsCheck = [];


                $text_colums_edit = [];

                break;


            case 'tipos_adicionales_th':
                $tabla = 'tipos_adicionales_th';
                $idcolumn= "id_tipo_adicional_th";
                $editformDataJson = CleanJson($formDataJson);
                $newformDataJson = $formDataJson;
                $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                $newformDataJson['kid_estatus'] = 1;
                $consultaselect = "SELECT id_tipo_adicional_th,
                    orden,
                    tipo_adicional_th,
                    CASE 
                        WHEN pordefecto = 1 THEN 'SÍ' 
                        ELSE 'NO' 
                    END AS pordefecto,
                    fecha_creacion
                FROM tipos_adicionales_th
                WHERE kid_estatus != 3 and ".$idcolumn." = :".$idcolumn;

                $ColumnsCheck = [
                    ['column'=>"tipo_adicional_th","check_similar"=>true]
                ];

                $text_colums_edit = [];

                break;

            case 'tipos_usuario':
                $tabla = 'tipos_usuario';
                $idcolumn= "id_tipo_usuario";
                $editformDataJson = CleanJson($formDataJson);
                $newformDataJson = $formDataJson;
                $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                $newformDataJson['kid_estatus'] = 1;

                $consultaselect = "SELECT id_tipo_usuario,
                tipo_usuario,
                descripcion,
                CASE 
                    WHEN pordefecto = 1 THEN 'SÍ' 
                    ELSE 'NO' 
                END AS pordefecto,
                CASE 
                    WHEN login = 1 THEN 'SÍ' 
                    ELSE 'NO' 
                END AS login,
                fecha_creacion
                FROM tipos_usuario
                WHERE kid_estatus != 3 AND id_tipo_usuario != 1 and ".$idcolumn." = :".$idcolumn;


                $ColumnsCheck = [
                    ['column'=>"tipo_usuario","check_similar"=>true]
                ];

                $text_colums_edit = [];

                break;

            case 'asignar_permisos':
                $editformDataJson = CleanJson($formDataJson);
                $kid_tipo_usuario = $_POST['firstColumnValue'];

                $consultaselect = "SELECT p.permiso, tup.kid_estatus
                FROM tipos_usuarios_permisos tup
                LEFT JOIN permisos p ON p.id_permiso = tup.kid_permiso
                WHERE tup.kid_estatus != 3 AND tup.kid_tipo_usuario != 1 AND tup.kid_tipo_usuario = :id";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':id', $kid_tipo_usuario);
                $resultado->execute();
                $permisos_registrados = $resultado->fetchAll(PDO::FETCH_ASSOC);
                $permisos_array = array_column($permisos_registrados, 'kid_estatus', 'permiso');

                $consultaselect = "SELECT permiso, id_permiso
                FROM permisos WHERE kid_estatus != 3";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->execute();
                $permisos_id= $resultado->fetchAll(PDO::FETCH_ASSOC);
                $permisos_id = array_column($permisos_id, 'id_permiso', 'permiso');
                
                try {
                    // Iniciar transacción
                    $conexion->beginTransaction();
                    
                    foreach ($editformDataJson as $clave => $valor) {
                        if (isset($permisos_array[$clave])) {
                            if($permisos_array[$clave] != 1 && $valor == 1){
                                // Activar permiso
                                $stmt = $conexion->prepare("UPDATE tipos_usuarios_permisos 
                                SET kid_estatus = 1 WHERE kid_permiso = :kid_permiso AND kid_tipo_usuario = :kid_tipo_usuario");
                                $stmt->bindParam(':kid_permiso', $permisos_id[$clave]);
                                $stmt->bindParam(':kid_tipo_usuario', $kid_tipo_usuario);
                                $stmt->execute();
                            }else if($permisos_array[$clave] == 1 && $valor == 0){
                                // Desactivar permiso
                                $stmt = $conexion->prepare("UPDATE tipos_usuarios_permisos 
                                SET kid_estatus = 0 WHERE kid_permiso = :kid_permiso AND kid_tipo_usuario = :kid_tipo_usuario");
                                $stmt->bindParam(':kid_permiso', $permisos_id[$clave]);
                                $stmt->bindParam(':kid_tipo_usuario', $kid_tipo_usuario);
                                $stmt->execute();
                            }
                        } else if($valor == 1){
                            // Agregar permiso a la tabla
                            $stmt = $conexion->prepare("INSERT INTO tipos_usuarios_permisos 
                            (kid_tipo_usuario, kid_permiso, kid_creacion, fecha_creacion, kid_estatus) 
                            VALUES (:kid_tipo_usuario, :kid_permiso, :kid_creacion, :fecha_creacion, 1)");

                            $stmt->bindParam(':kid_tipo_usuario', $kid_tipo_usuario, PDO::PARAM_INT);
                            $stmt->bindParam(':kid_permiso', $permisos_id[$clave], PDO::PARAM_STR);
                            $stmt->bindParam(':kid_creacion', $_SESSION["s_id"], PDO::PARAM_INT);
                            $stmt->bindValue(':fecha_creacion', date('Y-m-d H:i:s'), PDO::PARAM_STR);

                            $stmt->execute();

                        }
                    }
                    
                    // Confirmar transacción
                    $conexion->commit();
                } catch (Exception $e) {
                    // Rechazar transacción en caso de error
                    $conexion->rollBack();
                    $data_return = ['status' => 'error', 'message' => "Error al realizar movimientos en la DB: " . $e->getMessage()];
                }
                
                $data_return = ['status' => 'success', 'data' => 'NoChanges'];

                $ColumnsCheck = [
                    ['column'=>"tipo_usuario","check_similar"=>true]
                ];

                $text_colums_edit = [];

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
                            if($custombt){
                                if(isset($data_resultado['estatus']) && $newbuttons){
                                    array_splice($data_script['botones_acciones'], 0, 0, $newbuttons[$data_resultado['estatus']]);
                                }
                                
                                $data_resultado['botones'][] = true;
                                $data_resultado['botones'][] = GenerateCustomsButtons($data_script['botones_acciones'],$tabla);
                                unset($data_resultado['estatus']);
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
                            //debug($editformDataJson);
                            //debug($data_element);
                            #id_editado
                            #valor_anterior
                            #nuevo_valor
                            
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
                        
                                    $resultado = $conexion->prepare($consultaselect);
                                    $resultado->bindParam(":$idcolumn", $lastId, PDO::PARAM_INT);
                                    $resultado->execute();
                                    $data_resultado = $resultado->fetch(PDO::FETCH_ASSOC);

                                    if (!empty($data_resultado)) {
                                        foreach ($text_colums_edit as $registro) {
                                            $consultaInsert = "INSERT INTO cambios_planeaciones_compras (kid_registro_tabla, kid_tabla, cambio, kid_creacion, fecha_creacion, kid_estatus) VALUES 
                                                            (:kid_registro_tabla, :kid_tabla, :cambio, :kid_creacion, :fecha_creacion, :kid_estatus)";
                                            $stmt = $conexion->prepare($consultaInsert);
                                            $stmt->bindParam(":kid_registro_tabla", $id, PDO::PARAM_INT);
                                            $kid_tabla = GetIDTablaNameByName($tabla);
                                            $stmt->bindParam(":kid_tabla", $kid_tabla, PDO::PARAM_INT);
                                            $stmt->bindParam(":cambio", $registro, PDO::PARAM_STR);
                                            $stmt->bindParam(":kid_creacion", $_SESSION["s_id"], PDO::PARAM_INT);
                                            $fecha_creacion = date('Y-m-d H:i:s');
                                            $stmt->bindParam(":fecha_creacion", $fecha_creacion);
                                            $stmt->bindValue(":kid_estatus", 1, PDO::PARAM_INT);
                                            $stmt->execute();
                                        }
                                    }

                                    if($custombt){
                                        if(isset($data_resultado['estatus'])){
                                            if(isset($newbuttons[$data_resultado['estatus']]) && $newbuttons){
                                                array_splice($data_script['botones_acciones'], 0, 0, $newbuttons[$data_resultado['estatus']]);
                                            }else  if(!in_array($data_resultado['kid_estatus'], $array_status_check) && $nuevo_boton != null){
                                                array_splice($data_script['botones_acciones'], 0, 0, $nuevo_boton);
                                            }
                                        }
                                        $data_resultado['botones'][] = true;
                                        $data_resultado['botones'][] = GenerateCustomsButtons($data_script['botones_acciones'],$tabla);
                                        unset($data_resultado['estatus']);
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