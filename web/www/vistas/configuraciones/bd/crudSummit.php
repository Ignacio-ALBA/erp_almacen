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
        $array_status_check = [];
        $add_detalles_table = null;
        $add_detalles = null;
        $newbuttons = null;
        $estatus = GetEstatusLabels();
        $consecutivo = 0;

        switch ($modalCRUD) {
            case 'permisos':
                $tabla = 'permisos';
                $idcolumn= "id_permiso";
                $consecutivo = $_POST['firstColumnValue'];

                
                $UpdateEstatus = explode("/", $formDataJson['UpdateEstatus']);
                unset($formDataJson['UpdateEstatus']);
                $estatus_select = $UpdateEstatus [0];
                $permiso = $UpdateEstatus [1];
                

                /*-------------------- Obtener Tablas Foráneas --------------------*/
                $estados = GetEstadosListById();
                $_POST['firstColumnValue'] = $permiso ? GetIDPermisoByName($permiso) : null;
                /*------------------- Fin Obtener Tablas Foráneas ------------------*/
                
                if($estatus_select == 'desactivar'){
                    $formDataJson['kid_estatus'] = 12;
                }else if ($estatus_select == 'activar'){
                    $formDataJson['kid_estatus'] = 1;
                }


                $editformDataJson = CleanJson($formDataJson);
                //debug($editformDataJson);

                $newformDataJson = $formDataJson;
                $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                $newformDataJson['kid_estatus'] = 1;
                $consultaselect = "SELECT 
                    p.permiso,
                    p.etiqueta,
                    t.tablas AS tabla,
                    m.modulo,
                    p.kid_estatus
                FROM permisos p
                INNER JOIN tablas t ON p.kid_tabla = t.id_tabla
                INNER JOIN modulos m ON t.kid_modulo = m.id_modulo
                WHERE p.kid_estatus != 3 and id_permiso = :id_permiso
                ORDER BY m.modulo, t.tablas;";

                $fuc_mapping = function ($row) {
                    global  $estatus, $consecutivo;
                    $botones_acciones = [];
                    array_unshift($row, $consecutivo);
                    $bloque = 'configuraciones';
                    $modalCRUD = 'permisos';
                    if($row['kid_estatus'] == 1){
                        $boton = '<button class="UpdateEstatus btn btn-danger" bloque="'. $bloque.'" name="desactivar/'.$row['permiso'].'" modalCRUD="'.$modalCRUD.'"><i class="bi bi-ban"></i> Desactivar</button>';
                    }else{
                        $boton = '<button class="UpdateEstatus btn btn-success" bloque="'. $bloque.'" name="activar/'.$row['permiso'].'" modalCRUD="'.$modalCRUD.'"><i class="bi bi-check2-circle"></i> Activar</button>';
                    }
                    array_push($botones_acciones, $boton);
                    $row['botones'] = GenerateCustomsButtons($botones_acciones, 'permisos');
                    $row['kid_estatus'] = $estatus[$row['kid_estatus']];
                    unset($row['permiso']);
                    return $row;
                };


                $ColumnsCheck = [];
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
                                    //debug($data_resultado);

                                    if(isset($fuc_mapping)){
                                        $data_resultado = array_map($fuc_mapping, [$data_resultado])[0];
                                    }
                                    //debug($data_resultado);
                                    $data = $data_resultado;
                                    //debug($data);
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