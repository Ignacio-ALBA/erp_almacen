<?php
$objeto = new Conexion();
$conexion = $objeto->Conectar();

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
            $consulta = "SELECT COUNT(*) AS existe FROM $tabla WHERE $column = :valor and kid_estatus = 1";
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
                    $consulta = "SELECT $column FROM $tabla WHERE $column LIKE :valor and kid_estatus = 1";
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
        $AlertDataSimilar = filter_var($_POST['AlertDataSimilar'], FILTER_VALIDATE_BOOLEAN);

        $tabla = null;
        $idcolumn = null;
        $consultaselect = null;
        $newformDataJson = null;

        switch ($modalCRUD) {
            case 'proyectos':
                $tabla = 'proyectos';
                $idcolumn= "id_proyecto";

                /*-------------------- Obtener Tablas Foráneas --------------------*/
                $colaboradores = GetUsuariosListById();
                $bolsa_proyectos = GetBolsaProyectosListById();
                /*------------------- Fin Obtener Tablas Foráneas ------------------*/

                if (!empty($formDataJson['kid_responsable']) && isset($colaboradores[$formDataJson['kid_responsable']])) {
                    $formDataJson['kid_responsable'] = $colaboradores[$formDataJson['kid_responsable']];
                }
                if (!empty($formDataJson['kid_bolsa_proyecto']) && isset($bolsa_proyectos[$formDataJson['kid_bolsa_proyecto']])) {
                    $formDataJson['kid_bolsa_proyecto'] = $bolsa_proyectos[$formDataJson['kid_bolsa_proyecto']];
                }


                $editformDataJson = $formDataJson;
                //$formDataJson = insertarDespuesDeClave($formDataJson, 'marca', ['fecha_creacion'=>date('Y-m-d H:i:s')]);
                $newformDataJson = $formDataJson;
                $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                $newformDataJson['kid_estatus'] = 1;
                $consultaselect = "SELECT p.id_proyecto , 
                        p.proyecto, 
                        b.bolsa_proyecto as kid_bolsa_proyecto,
                        p.ubicacion, 
                        p.presupuesto,
                        p.objetivo,
                        CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) as kid_responsable,
                        p.fecha_inicio,
                        p.fecha_fin
                FROM proyectos p
                LEFT JOIN bolsas_proyectos b ON p.kid_bolsa_proyecto = b.id_bolsa_proyecto
                LEFT JOIN colaboradores u ON p.kid_responsable = u.id_colaborador
                WHERE ".$idcolumn." = :".$idcolumn;

                $ColumnsCheck = [
                    ['column'=>"proyecto","check_similar"=>true]
                ];

                
                break;

            case 'detalles_proyectos':
                $tabla = 'detalles_proyectos';
                $idcolumn= "id_detalle_proyecto";
                /*-------------------- Obtener Tablas Foráneas --------------------*/
                $colaboradores = GetUsuariosListById();
                $proyectos = GetProyectosListById();
                /*------------------- Fin Obtener Tablas Foráneas ------------------*/

                if (!empty($formDataJson['kid_responsable']) && isset($colaboradores[$formDataJson['kid_responsable']])) {
                    $formDataJson['kid_responsable'] = $colaboradores[$formDataJson['kid_responsable']];
                }
                if (!empty($formDataJson['kid_proyecto']) && isset($proyectos[$formDataJson['kid_proyecto']])) {
                    $formDataJson['kid_proyecto'] = $proyectos[$formDataJson['kid_proyecto']];
                }
                $editformDataJson = $formDataJson;
                //$formDataJson = insertarDespuesDeClave($formDataJson, 'marca', ['fecha_creacion'=>date('Y-m-d H:i:s')]);
                $newformDataJson = $formDataJson;
                $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                $newformDataJson['kid_estatus'] = 1;
                $consultaselect = "SELECT d.id_detalle_proyecto, 
                        d.detalle_proyecto, 
                        p.proyecto as kid_proyecto,
                        d.presupuesto,
                        d.objetivo,
                        CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) as kid_responsable,
                        d.fecha_inicio,
                        d.fecha_fin
                FROM detalles_proyectos d
                LEFT JOIN proyectos p ON d.kid_proyecto = p.id_proyecto  
                LEFT JOIN colaboradores u ON d.kid_responsable = u.id_colaborador 
                WHERE ".$idcolumn." = :".$idcolumn;
                $ColumnsCheck = [
                    ['column'=>"detalle_proyecto","check_similar"=>true]
                ];

                
                break;
            case 'bolsas_proyectos':
                $tabla = 'bolsas_proyectos';
                $idcolumn= "id_bolsa_proyecto";

                /*-------------------- Obtener Tablas Foráneas --------------------*/
                $clientes = GetClientesListById();
                /*------------------- Fin Obtener Tablas Foráneas ------------------*/

                if (!empty($formDataJson['kid_cliente']) && isset($clientes[$formDataJson['kid_cliente']])) {
                    $formDataJson['kid_cliente'] = $clientes[$formDataJson['kid_cliente']];
                }
                $editformDataJson = $formDataJson;
                //$formDataJson = insertarDespuesDeClave($formDataJson, 'marca', ['fecha_creacion'=>date('Y-m-d H:i:s')]);
                $newformDataJson = $formDataJson;
                $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                $newformDataJson['kid_estatus'] = 1;
                $consultaselect = "SELECT b.id_bolsa_proyecto , 
                    b.bolsa_proyecto, 
                    c.nombre as kid_cliente,
                    b.comentarios,
                    b.fecha_creacion
                FROM bolsas_proyectos b
                LEFT JOIN clientes c ON b.kid_cliente = c.id_cliente
                WHERE ".$idcolumn." = :".$idcolumn;

                $ColumnsCheck = [
                    ['column'=>"bolsa_proyecto","check_similar"=>true]
                ];

                break;

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

                            $data = $data_resultado;
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
}
?>