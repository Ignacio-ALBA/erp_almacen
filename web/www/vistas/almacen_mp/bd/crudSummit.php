<?php
$objeto = new Conexion();
$conexion = $objeto->Conectar();

$data_script['botones_acciones'] = [
    '<button class="ModalDataView btn btn-primary primary" modalCRUD="${modalCRUD}"><i class="bi bi-eye"></i> Ver</button>',
    '<button class="ModalDataEdit btn btn-warning warning" modalCRUD="${modalCRUD}"><i class="bi bi-pencil"></i> Editar</button>',
    '<button class="ModalDataDelete btn btn-danger danger" modalCRUD="${modalCRUD}"><i class="bi bi-trash"></i> Eliminar</button>'
];
function GetDefaultProjectId() {
    global $conexion;
    $query = "SELECT id_proyecto FROM proyectos WHERE proyecto = 'Proyecto Por Defecto' LIMIT 1";
    $stmt = $conexion->prepare($query);
    $stmt->execute();
    return $stmt->fetchColumn();
}

function GetDefaultTiempoEntregaId() {
    global $conexion;
    $query = "SELECT id_tiempo_entrega FROM tiempos_entregas 
              WHERE pordefecto = 1 AND kid_estatus = 1 
              LIMIT 1";
    $stmt = $conexion->prepare($query);
    $stmt->execute();
    return $stmt->fetchColumn() ?: 1;
}

function GetDefaultTipoPagoId() {
    global $conexion;
    $query = "SELECT id_tipo_pago FROM tipos_pagos 
              WHERE pordefecto = 1 AND kid_estatus = 1 
              LIMIT 1";
    $stmt = $conexion->prepare($query);
    $stmt->execute();
    return $stmt->fetchColumn() ?: 1;
}


function GetDefaultBankAccountId() {
    global $conexion;
    $query = "SELECT id_cuenta_bancaria FROM cuentas_bancarias WHERE id_cuenta_bancaria = 1 LIMIT 1";
    $stmt = $conexion->prepare($query);
    $stmt->execute();
    return $stmt->fetchColumn();
}
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
    if (isset($_POST['modalCRUD']) && isset($_POST['opcion']) && isset($_POST['formDataJson'])) {
        $modalCRUD = $_POST['modalCRUD'];
        $opcion = $_POST['opcion'];
        $formDataJson = $_POST['formDataJson'];
        if (!is_array($formDataJson)) {
            $formDataJson = json_decode($formDataJson, true);
        }
        foreach ($formDataJson as $key => $value) {
            if ($value === '' || $value === null) {
                $formDataJson[$key] = null;
            }
        }
        $AlertDataSimilar = isset($_POST['AlertDataSimilar']) ? filter_var($_POST['AlertDataSimilar'], FILTER_VALIDATE_BOOLEAN) : null;

        $tabla = null;
        $idcolumn = null;
        $consultaselect = null;
        $newformDataJson = null;
        $add_detalles = [];
        $add_detalles_table = null;
        $update_row_consult = null;
        $custombt = false;
        $estatus_name = GetEstatusList();
        $estatus = GetEstatusLabels();

        switch ($modalCRUD) {
      case 'recepciones_mp':
    $formDataJson = $_POST['formDataJson'];
    if (!is_array($formDataJson)) $formDataJson = json_decode($formDataJson, true);

    // 1. Normalización de datos (aquí puedes resolver claves foráneas si recibes nombre en vez de ID)

    // 2. Prepara $newformDataJson (como usas en otros cases)
    $newformDataJson = $formDataJson;
    $newformDataJson['fecha_creacion'] = date('Y-m-d H:i:s');
    $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
    $newformDataJson['kid_estatus'] = 1;

    // 3. Validación de unicidad (si aplica)
    // $ColumnsCheck = []; // Si quieres validar algún campo único
    // list($resultados, $checkdata) = verificarDatos($conexion, 'recepciones_mp', $ColumnsCheck, $newformDataJson, $AlertDataSimilar);

    try {
        $conexion->beginTransaction();

        // 4. Insertar encabezado
        $camposEncabezado = $newformDataJson;
        $detalles = $camposEncabezado['detalles'] ?? [];
        unset($camposEncabezado['detalles']);
        $campos = array_keys($camposEncabezado);
        $campos2 = array_map(fn($c) => ":$c", $campos);
        $stmt = $conexion->prepare("INSERT INTO recepciones_mp (".implode(',', $campos).") VALUES (".implode(',', $campos2).")");
        foreach ($camposEncabezado as $k => $v) $stmt->bindValue(":$k", $v ?? null);
        $stmt->execute();
        $id_recepcion_mp = $conexion->lastInsertId();

        // 5. Insertar detalles
        foreach ($detalles as $detalle) {
            $detalle['kid_recepcion_mp'] = $id_recepcion_mp;
            $detalle['fecha_creacion'] = date('Y-m-d H:i:s');
            $detalle['kid_creacion'] = $_SESSION["s_id"];
            $detalle['kid_estatus'] = 1;
            $camposDet = array_keys($detalle);
            $camposDet2 = array_map(fn($c) => ":$c", $camposDet);
            $stmt2 = $conexion->prepare("INSERT INTO detalles_recepciones_mp (".implode(',', $camposDet).") VALUES (".implode(',', $camposDet2).")");
            foreach ($detalle as $k => $v) $stmt2->bindValue(":$k", $v ?? null);
            $stmt2->execute();
        }

        $conexion->commit();
        echo json_encode(['status'=>'success']);
    } catch (Exception $e) {
        $conexion->rollBack();
        echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
    }
    exit;

            default:
                print json_encode(['status' => 'error', 'message' => 'Operación no válida'], JSON_UNESCAPED_UNICODE);
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

                        //debug($consulta);
                        $resultado = $conexion->prepare($consulta);
                        foreach ($newformDataJson as $key => $value) {
                            $resultado->bindParam(':'.$key, $newformDataJson[$key]);
                            //debug((':'.$key.' = '. $newformDataJson[$key]));
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
                            }else if($data && $update_row_consult){
                                $resultado = $conexion->prepare($update_row_consult);
                                $resultado->execute();
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

                                $resultado = $conexion->prepare($consultaselect);
                                $resultado->bindParam(":$idcolumn", $lastId, PDO::PARAM_INT);
                                $resultado->execute();
                                $data_resultado = $resultado->fetch(PDO::FETCH_ASSOC);

                                if(isset($fuc_mapping)){
                                    $data_resultado = array_map($fuc_mapping, [$data_resultado])[0];
                                }
                                //debug($data_resultado);
                                $data = $data_resultado;
                            }
                        } else {
                            print json_encode(['status' => 'error', 'message' => 'Elemento no valido.'], JSON_UNESCAPED_UNICODE);
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
                        print json_encode(['status' => 'error', 'message' => 'Elemento no valido.'], JSON_UNESCAPED_UNICODE);
                    }
                    break;

                case 4:
                    break;
    
                default:
                    print json_encode(['status' => 'error', 'message' => 'Operación no válida'], JSON_UNESCAPED_UNICODE);
                    break;
            }
            if ($data && !$checkdata) {
                print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
            } else if($checkdata){
                print json_encode(['status' => 'error', 'checkdata' => $data], JSON_UNESCAPED_UNICODE);
            }else{
                print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
            }
        }

    }else{
        print json_encode(['status' => 'error', 'message' => 'Faltan datos requeridos'], JSON_UNESCAPED_UNICODE);
    } 

} else {
    print json_encode(['status' => 'error', 'message' => 'Método no permitido'], JSON_UNESCAPED_UNICODE);
    echo 'Esta página solo admite solicitudes POST.';
}
?>