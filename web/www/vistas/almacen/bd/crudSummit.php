<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'].'/helpers/main.php'; 
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
        $AlertDataSimilar = filter_var($_POST['AlertDataSimilar'], FILTER_VALIDATE_BOOLEAN);

        $tabla = null;
        $idcolumn = null;
        $consultaselect = null;
        $newformDataJson = null;

        switch ($modalCRUD) {
            case 'estados':
                $tabla = 'estados';
                $idcolumn= "id_estados";

                /*-------------------- Obtener Tablas Foráneas --------------------*/
                $consulta = "SELECT id_pais, pais FROM paises WHERE kid_estatus = 1";
                $resultado = $conexion->prepare($consulta);
                $resultado->execute();
                $paises = $resultado->fetchAll(PDO::FETCH_ASSOC);
                $paises = array_column($paises, 'id_pais', 'pais');
                /*------------------- Fin Obtener Tablas Foráneas ------------------*/

                if (!empty($formDataJson['kid_pais']) && isset($paises[$formDataJson['kid_pais']])) {
                $formDataJson['kid_pais'] = $paises[$formDataJson['kid_pais']];
                }
                $editformDataJson = $formDataJson;
                //$formDataJson = insertarDespuesDeClave($formDataJson, 'marca', ['fecha_creacion'=>date('Y-m-d H:i:s')]);
                $newformDataJson = $formDataJson;
                $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                $newformDataJson['kid_estatus'] = 1;
                $consultaselect = "SELECT e.id_estados  , 
                                        e.orden, 
                                        e.estado, 
                                        e.simbolo, 
                                        CASE 
                                            WHEN e.pordefecto = 1 THEN 'SÍ' 
                                            ELSE 'NO' 
                                        END AS pordefecto,
                                        p.pais as kid_pais,  -- Ahora esta columna está después de pordefecto
                                        e.fecha_creacion
                                    FROM $tabla e
                                    JOIN paises p ON e.kid_pais = p.id_pais 
                                    WHERE $idcolumn = :$idcolumn";
                                    

                $ColumnsCheck = [
                    ['column'=>"estado","check_similar"=>true]
                ];
                break;

            case 'municipios':
                $tabla = 'municipios';
                $idcolumn= "id_municipio";

                /*-------------------- Obtener Tablas Foráneas --------------------*/
                $consulta = "SELECT id_estados, estado FROM estados WHERE kid_estatus = 1";
                $resultado = $conexion->prepare($consulta);
                $resultado->execute();
                $estados = $resultado->fetchAll(PDO::FETCH_ASSOC);
                $estados = array_column($estados, 'id_estados', 'estado');
                /*------------------- Fin Obtener Tablas Foráneas ------------------*/

                if (!empty($formDataJson['kid_estado']) && isset($estados[$formDataJson['kid_estado']])) {
                $formDataJson['kid_estado'] = $estados[$formDataJson['kid_estado']];
                }
                $editformDataJson = $formDataJson;
                //$formDataJson = insertarDespuesDeClave($formDataJson, 'marca', ['fecha_creacion'=>date('Y-m-d H:i:s')]);
                $newformDataJson = $formDataJson;
                $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                $newformDataJson['kid_estatus'] = 1;
                $consultaselect = "SELECT m.id_municipio   , 
                                        m.orden, 
                                        m.municipio, 
                                        CASE 
                                            WHEN m.pordefecto = 1 THEN 'SÍ' 
                                            ELSE 'NO' 
                                        END AS pordefecto,
                                        e.estado as kid_estado,
                                        p.pais as pais,  -- Ahora esta columna está después de pordefecto
                                        m.fecha_creacion
                                        FROM municipios m
                                        JOIN estados e ON m.kid_estado = e.id_estados
                                        JOIN paises p ON e.kid_pais = p.id_pais
                                        WHERE $idcolumn = :$idcolumn";

                $ColumnsCheck = [
                    ['column'=>"municipio","check_similar"=>true]
                ];
                break;

            case 'empresas':
                $tabla = 'empresas';
                $idcolumn= "id_empresa";

                /*-------------------- Obtener Tablas Foráneas --------------------*/
                $consulta = "SELECT id_colaborador, email FROM colaboradores WHERE kid_estatus = 1";
                $resultado = $conexion->prepare($consulta);
                $resultado->execute();
                $colaboradores = $resultado->fetchAll(PDO::FETCH_ASSOC);
                $colaboradores = array_column($colaboradores, 'id_colaborador', 'email');
                /*------------------- Fin Obtener Tablas Foráneas ------------------*/

                $formDataJson['kid_propietario'] = !empty($formDataJson['kid_propietario']) && isset($colaboradores[$formDataJson['kid_propietario']]) ? $colaboradores[$formDataJson['kid_propietario']] : -1;
                $formDataJson['kid_representante_legal'] = !empty($formDataJson['kid_representante_legal']) && isset($colaboradores[$formDataJson['kid_representante_legal']]) ? $colaboradores[$formDataJson['kid_representante_legal']] : -1;
                $formDataJson['kid_representante_tecnico'] = !empty($formDataJson['kid_representante_tecnico']) && isset($colaboradores[$formDataJson['kid_representante_tecnico']]) ? $colaboradores[$formDataJson['kid_representante_tecnico']] : -1;
                $formDataJson['kid_representante_administrativo'] = !empty($formDataJson['kid_representante_administrativo']) && isset($colaboradores[$formDataJson['kid_representante_administrativo']]) ? $colaboradores[$formDataJson['kid_representante_administrativo']] : -1;

                $formDataJson['telefono_contacto'] = $formDataJson['telefono_contacto'] ?: null;
                $formDataJson['celular_contacto'] = $formDataJson['telefono_contacto'] ?: null;
                $formDataJson['telefono_representante_legal'] = $formDataJson['telefono_contacto'] ?: null;
                $formDataJson['celular_representante_legal'] = $formDataJson['telefono_contacto'] ?: null;
                $formDataJson['telefono_representante_tecnico'] = $formDataJson['telefono_contacto'] ?: null;
                $formDataJson['celular_representante_tecnico'] = $formDataJson['telefono_contacto'] ?: null;
                $formDataJson['telefono_representante_administrativo'] = $formDataJson['telefono_contacto'] ?: null;
                $formDataJson['celular_representante_administrativo'] = $formDataJson['telefono_contacto'] ?: null;


                $editformDataJson = $formDataJson;
                //$formDataJson = insertarDespuesDeClave($formDataJson, 'marca', ['fecha_creacion'=>date('Y-m-d H:i:s')]);
                $newformDataJson = $formDataJson;
                $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                $newformDataJson['kid_estatus'] = 1;
                $consultaselect = "SELECT e.id_empresa, 
                            e.empresa, 
                            e.razon_social, 
                            e.rfc, 
                            CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) AS kid_propietario,
                            CONCAT(u2.nombre, ' ', u2.apellido_paterno, ' ', u2.apellido_materno) AS kid_representante_legal,
                            CONCAT(u3.nombre, ' ', u3.apellido_paterno, ' ', u3.apellido_materno) AS kid_representante_tecnico,
                            CONCAT(u4.nombre, ' ', u4.apellido_paterno, ' ', u4.apellido_materno) AS kid_representante_administrativo,
                            e.fecha_creacion
                        FROM 
                            empresas e
                        LEFT JOIN 
                            colaboradores u ON e.kid_propietario = u.id_colaborador 
                        LEFT JOIN 
                            colaboradores u2 ON e.kid_representante_legal = u2.id_colaborador 
                        LEFT JOIN 
                            colaboradores u3 ON e.kid_representante_tecnico = u3.id_colaborador 
                        LEFT JOIN 
                            colaboradores u4 ON e.kid_representante_administrativo = u4.id_colaborador
                        WHERE $idcolumn = :$idcolumn";

                $ColumnsCheck = [
                    ['column'=>"empresa","check_similar"=>true],
                    ['column'=>"razon_social","check_similar"=>true],
                    ['column'=>"rfc","check_similar"=>false]
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