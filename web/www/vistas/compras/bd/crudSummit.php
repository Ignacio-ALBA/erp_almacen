<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');
ob_start();
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


$data = null; // Inicializa la variable $data como null

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Establecer headers para JSON
    header('Content-Type: application/json; charset=utf-8');
    
    try {
        $data = null;
        $checkdata = null;
        if (isset($_POST['modalCRUD']) && isset($_POST['opcion']) && isset($_POST['formDataJson'])) {
            $modalCRUD = $_POST['modalCRUD'];
            $opcion = $_POST['opcion'];
            $formDataJson = $_POST['formDataJson'];
            
            if (!is_array($formDataJson)) {
                $formDataJson = json_decode($formDataJson, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new Exception('Error al decodificar JSON: ' . json_last_error_msg());
                }
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

            // Excluir 'num_articulos' si está presente en los datos enviados
            unset($formDataJson['num_articulos']);

            switch ($modalCRUD) {
                case 'proveedores':
                    $tabla = 'proveedores';
                    $formDataJson['kid_estado'] = GetIDEstadoByName($formDataJson['kid_estado']);
                    /*------------------- Fin Obtener Tablas Foráneas ------------------*/

                    $editformDataJson = CleanJson($formDataJson);
                    

                    $newformDataJson = $formDataJson;
                    $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                    $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                    $newformDataJson['kid_estatus'] = 1;
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
                        END AS pordefecto,
                        fecha_creacion
                    FROM proveedores
                    WHERE kid_estatus != 3 and ".$idcolumn." = :".$idcolumn;

                    $ColumnsCheck = [
                        ['column'=>"razon_social","check_similar"=>false],
                        ['column'=>"rfc","check_similar"=>false]
                    ];
                    break;

                case 'comentarios_proveedores':
                    $tabla = 'comentarios_proveedores';
                    $idcolumn= "id_comentario_proveedor";

                    /*-------------------- Obtener Tablas Foráneas --------------------*/
                    //$colaboradores = GetUsuariosListById();
                    $formDataJson['kid_proveedor'] = isset($formDataJson['kid_proveedor']) ? GetIDProveedorByName($formDataJson['kid_proveedor']) : null;
                    $formDataJson['kid_tipo_comentario'] = isset($formDataJson['kid_tipo_comentario']) ? GetIDTipoComentarioByName($formDataJson['kid_tipo_comentario']) : null;  
                    /*------------------- Fin Obtener Tablas Foráneas ------------------*/


                    $editformDataJson = CleanJson($formDataJson);
                    //$formDataJson = insertarDespuesDeClave($formDataJson, 'marca', ['fecha_creacion'=>date('Y-m-d H:i:s')]);
                    $newformDataJson = $formDataJson;
                    $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                    $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                    $newformDataJson['kid_estatus'] = 1;

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
                    WHERE cp.kid_estatus !=3 AND $idcolumn = :$idcolumn";

                    $ColumnsCheck = [];
                    break;

                case 'listas_compras':
                    $tabla = 'listas_compras';
                    $idcolumn = "id_lista_compra";

                    // Eliminar campos no necesarios
                    unset($formDataJson['num_articulos']);

                    $editformDataJson = CleanJson($formDataJson);
                    $newformDataJson = $formDataJson;
                    $newformDataJson['fecha_creacion'] = date('Y-m-d H:i:s');
                    $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                    $newformDataJson['kid_estatus'] = 1;

                    // Antes de insertar en la tabla `listas_compras`, verificar y asignar un valor predeterminado para `kid_proyecto`
                    if (empty($newformDataJson['kid_proyecto'])) {
                        $newformDataJson['kid_proyecto'] = GetDefaultProjectId();
                    }

                    // Antes de insertar en la tabla `listas_compras`, verificar y asignar un valor predeterminado para `kid_cuenta_bancaria`
                    if (empty($newformDataJson['kid_cuenta_bancaria'])) {
                        $newformDataJson['kid_cuenta_bancaria'] = GetDefaultBankAccountId();
                    }

                    // Validate kid_cuenta_bancaria to ensure it is an integer
                    if (!is_numeric($newformDataJson['kid_cuenta_bancaria'])) {
                        $newformDataJson['kid_cuenta_bancaria'] = GetDefaultBankAccountId();
                    }
                    if (!is_numeric($newformDataJson['kid_proyecto'])) {
                        $newformDataJson['kid_proyecto'] = GetDefaultProjectId();
                    }
                    // Extraer los artículos del formData si existen
                    $articulos = [];
                    foreach ($formDataJson as $key => $value) {
                        if (preg_match('/^(kid_articulo|cantidad|costo_unitario_total|costo_unitario_neto|monto_total|monto_neto|porcentaje_descuento)_(\d+)$/', $key, $matches)) {
                            $index = $matches[2];
                            $field = $matches[1];
                            $articulos[$index][$field] = $value;
                            unset($newformDataJson[$key]); // Remover del formData principal
                        }
                    }

                    // Convertir el nombre del artículo a su ID antes de la inserción
                    foreach ($articulos as $index => $articulo) {
                        if (!is_numeric($articulo['kid_articulo'])) {
                            $query = "SELECT id_articulo FROM articulos WHERE articulo = :articulo LIMIT 1";
                            $stmt = $conexion->prepare($query);
                            $stmt->bindParam(':articulo', $articulo['kid_articulo']);
                            $stmt->execute();
                            $idArticulo = $stmt->fetchColumn();

                            if ($idArticulo) {
                                $articulos[$index]['kid_articulo'] = $idArticulo;
                            } else {
                                throw new Exception("El artículo '{$articulo['kid_articulo']}' no existe en la base de datos.");
                            }
                        }
                    }

                    try {
                        // Iniciar transacción
                        $conexion->beginTransaction();

                        // Insertar la lista de compras
                        $columns = implode(", ", array_keys($newformDataJson));
                        $placeholders = ":" . implode(", :", array_keys($newformDataJson));
                        $query = "INSERT INTO $tabla ($columns) VALUES ($placeholders)";
                        $stmt = $conexion->prepare($query);
                        $stmt->execute($newformDataJson);
                        $lastInsertId = $conexion->lastInsertId();

                        // Insertar los detalles de los artículos
                        foreach ($articulos as $articulo) {
                            $articulo['kid_lista_compras'] = $lastInsertId;
                            $articulo['fecha_creacion'] = date('Y-m-d H:i:s');
                            $articulo['kid_creacion'] = $_SESSION["s_id"];
                            $articulo['kid_estatus'] = 1;

                            $columns = implode(", ", array_keys($articulo));
                            $placeholders = ":" . implode(", :", array_keys($articulo));
                            $query = "INSERT INTO detalles_listas_compras ($columns) VALUES ($placeholders)";
                            $stmt = $conexion->prepare($query);
                            $stmt->execute($articulo);
                        }

                        // Confirmar transacción
                        $conexion->commit();
                        echo json_encode(['status' => 'success', 'message' => 'Lista de compras creada exitosamente', 'id_lista_compra' => $lastInsertId]);
                        $data = ['status' => 'success', 'message' => 'Lista de compras creada exitosamente', 'id_lista_compra' => $lastInsertId];
                        exit; // Finalizar el script inmediatamente después de enviar la respuesta JSON
                    } catch (Exception $e) {
                        // Revertir transacción en caso de error
                        $conexion->rollBack();
                        $data = ['status' => 'error', 'message' => 'Error al guardar la lista de compras: ' . $e->getMessage()];
                        echo json_encode(['status' => 'error', 'message' => 'Error al guardar la lista de compras: ' . $e->getMessage()]);
                        exit; // Finalizar el script inmediatamente después de enviar la respuesta JSON
                    }
                    break;

                case 'detalles_listas_compras':
                    $tabla = 'detalles_listas_compras';
                    $idcolumn= "id_detalle_lista_compras";

                    /*-------------------- Obtener Tablas Foráneas --------------------*/
                    $formDataJson['kid_articulo'] = isset($formDataJson['kid_articulo']) ? GetIDArticuloByName($formDataJson['kid_articulo']) : null;
                    $formDataJson['kid_lista_compras'] = isset($formDataJson['kid_lista_compras']) ? GetIDListaCompraByName($formDataJson['kid_lista_compras']) : null;
                    /*------------------- Fin Obtener Tablas Foráneas ------------------*/

                    $editformDataJson = CleanJson($formDataJson);
                    //$formDataJson = insertarDespuesDeClave($formDataJson, 'marca', ['fecha_creacion'=>date('Y-m-d H:i:s')]);
                    $newformDataJson = $formDataJson;
                    $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                    $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                    $newformDataJson['kid_estatus'] = 1;
                    $consultaselect = "SELECT dlc.id_detalle_lista_compras,
                        lc.lista_compra AS kid_lista_compras,
                        a.articulo AS kid_articulo,
                        dlc.cantidad,
                        dlc.costo_unitario_total,
                        dlc.costo_unitario_neto,
                        CONCAT(dlc.porcentaje_descuento,' %'),
                        dlc.monto_total,
                        dlc.monto_neto,
                        dlc.fecha_creacion
                    FROM detalles_listas_compras dlc
                    LEFT JOIN listas_compras lc ON dlc.kid_lista_compras = lc.id_lista_compra
                    LEFT JOIN articulos a ON dlc.kid_articulo = a.id_articulo
                    WHERE dlc.kid_estatus != 3 and ".$idcolumn." = :".$idcolumn;

                    $ColumnsCheck = [];
                    break;

                    case 'cotizaciones_compras':
                        $tabla = 'cotizaciones_compras';
                        $idcolumn = "id_cotizacion_compra";
                    
                        /*-------------------- Obtener Tablas Foráneas --------------------*/
                        // Obtener valores por defecto para campos obligatorios
                        $defaultProjectId = GetDefaultProjectId();
                        $defaultTiempoEntregaId = GetDefaultTiempoEntregaId();
                        $defaultTipoPagoId = GetDefaultTipoPagoId();
                    
                        // Asignar valores, usando valores por defecto si son nulos
                        $formDataJson['kid_proyecto'] = $defaultProjectId;
                        $formDataJson['kid_proveedor'] = !empty($formDataJson['kid_proveedor']) ? 
                            GetIDProveedorByName($formDataJson['kid_proveedor']) : null;
                        
                        // Procesar tiempo de entrega y tipo de pago
                        $formDataJson['kid_tiempo_entrega'] = GetIDTiempoEntregaByName($formDataJson['kid_tiempo_entrega']);
                        if (empty($formDataJson['kid_tiempo_entrega'])) {
                            $formDataJson['kid_tiempo_entrega'] = $defaultTiempoEntregaId;
                        }
                        
                        $formDataJson['kid_tipo_pago'] = GetIDTipoPagoByName($formDataJson['kid_tipo_pago']);
                        if (empty($formDataJson['kid_tipo_pago'])) {
                            $formDataJson['kid_tipo_pago'] = $defaultTipoPagoId;
                        }
                        /*------------------- Fin Obtener Tablas Foráneas ------------------*/
                    
                        // Verificar campos requeridos
                        if (empty($formDataJson['kid_proveedor'])) {
                            print json_encode([
                                'status' => 'error',
                                'message' => 'El campo Proveedor es requerido'
                            ], JSON_UNESCAPED_UNICODE);
                            return;
                        }
                    
                        $editformDataJson = CleanJson($formDataJson);
                        $newformDataJson = $formDataJson;
                        
                        if($opcion == 1) { // Insertar nueva cotización
                            try {
                                // Obtener el siguiente número de grupo
                                $consultaselect = "SELECT MAX(grupo) FROM cotizaciones_compras 
                                                 WHERE kid_proveedor = :kid_proveedor AND kid_estatus != 3";
                                $resultado = $conexion->prepare($consultaselect);
                                $resultado->bindParam(':kid_proveedor', $formDataJson['kid_proveedor']);
                                $resultado->execute();
                                $grupo = $resultado->fetchColumn() ?: 0;
                                
                                // Preparar datos para inserción
                                $newformDataJson['grupo'] = $grupo + 1;
                                $newformDataJson['cotizacion_compras'] = $formDataJson['cotizacion'];
                                $newformDataJson['fecha_cotizacion'] = $formDataJson['fecha_cotizacion'];
                                $newformDataJson['especificaciones_adicionales'] = $formDataJson['especificaciones_adicionales'];
                                $newformDataJson['fecha_creacion'] = date('Y-m-d H:i:s');
                                $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                                $newformDataJson['kid_estatus'] = 8;
                                $newformDataJson['kid_autorizo'] = 0;
                                $newformDataJson['kid_proyecto'] = $defaultProjectId;
                    
                                // Eliminar campo que no existe en la tabla
                                unset($newformDataJson['cotizacion']);
                    
                                // Insertar el registro
                                $columns = implode(', ', array_keys($newformDataJson));
                                $values = ':' . implode(', :', array_keys($newformDataJson));
                                
                                $sql = "INSERT INTO $tabla ($columns) VALUES ($values)";
                                $stmt = $conexion->prepare($sql);
                                
                                foreach($newformDataJson as $key => $value) {
                                    $stmt->bindValue(":$key", $value);
                                }
                                
                                $stmt->execute();
                                $lastId = $conexion->lastInsertId();
                    
                                // Obtener el registro insertado para la respuesta
                                $consultaselect = "SELECT 
                                    cc.id_cotizacion_compra,
                                    cc.cotizacion_compras,
                                    cc.grupo,
                                    prov.razon_social AS kid_proveedor,
                                    CASE
    WHEN cc.kid_estatus = 5 THEN 'Finalizado'
    WHEN cc.kid_estatus = 6 THEN 'En Revisión I'
    WHEN cc.kid_estatus = 7 THEN 'En Revisión II'
    WHEN cc.kid_estatus = 8 THEN 'Pendiente'
    ELSE 'Desconocido'
    END AS kid_estatus,
                                    u.email AS kid_creacion,
                                    COALESCE(u2.email, 'Sin Autorizar') AS kid_autorizo,
                                    cc.fecha_creacion
                                FROM cotizaciones_compras cc
                                LEFT JOIN proveedores prov ON cc.kid_proveedor = prov.id_proveedor
                                LEFT JOIN colaboradores u ON cc.kid_creacion = u.id_colaborador
                                LEFT JOIN colaboradores u2 ON cc.kid_autorizo = u2.id_colaborador
                                WHERE cc.id_cotizacion_compra = :id";
                    
                                $resultado = $conexion->prepare($consultaselect);
                                $resultado->bindParam(":id", $lastId);
                                $resultado->execute();
                                $data_resultado = $resultado->fetch(PDO::FETCH_ASSOC);
                    
                                if(isset($fuc_mapping)) {
                                    $data_resultado = array_map($fuc_mapping, [$data_resultado])[0];
                                }
                    
                                print json_encode([
                                    'status' => 'success',
                                    'message' => 'Cotización creada correctamente',
                                    'data' => $data_resultado
                                ], JSON_UNESCAPED_UNICODE);
                                return;
                    
                            } catch(PDOException $e) {
                                print json_encode([
                                    'status' => 'error',
                                    'message' => 'Error al guardar: ' . $e->getMessage()
                                ], JSON_UNESCAPED_UNICODE);
                                return;
                            }
                        }
                    
                        // Función de mapeo para los botones
                        $fuc_mapping = function ($row) {
                            global $data_script, $estatus, $estatus_name;
                            $botones_acciones = $data_script['botones_acciones'];
                    
                            $bloque = 'compras';
                            $modalCRUD = 'update_estatus_cotizaciones_compras';
                            
                            if(!in_array($row['kid_estatus'], [5,6,7])){
                                $nuevo_boton = '<button class="UpdateEstatus btn btn-success" bloque="'. $bloque.'" name="'.$estatus_name[6].'" modalCRUD="'.$modalCRUD.'"><i class="bi bi-check2"></i> Revisar I</button>';
                                array_unshift($botones_acciones,$nuevo_boton);
                            } else if($row['kid_estatus'] == 6){
                                $nuevo_boton = '<button class="UpdateEstatus btn btn-success" bloque="'. $bloque.'" name="'.$estatus_name[7].'" modalCRUD="'.$modalCRUD.'"><i class="bi bi-check2-all"></i> Revisar II</button>';
                                array_unshift($botones_acciones,$nuevo_boton);
                            } else if($row['kid_estatus'] == 7){
                                $nuevo_boton = '<button class="UpdateEstatus btn btn-success" bloque="'. $bloque.'" name="'.$estatus_name[5].'" modalCRUD="'.$modalCRUD.'"><i class="bi bi-check2-circle"></i> Autorizar</button>';
                                array_unshift($botones_acciones,$nuevo_boton);
                            }
                            
                            $hashed_id = codificar($row['id_cotizacion_compra']);
                            $nuevo_boton = '<a href="/rutas/compras.php/detalles_cotizaciones_compras?id=' . $hashed_id . '" class="btn btn-secondary"><i class="bi bi-journal-text"></i> Contenido</a>';
                            array_push($botones_acciones, $nuevo_boton);
                            $nuevo_boton = '<button class="GenerarReporte btn btn-success success" reporte="proveedores_cuadro_comparativo" data-id="'.$hashed_id.'"><i class="bi bi-play-circle"></i> Cuadro Comparativo</button>';
                            array_push($botones_acciones, $nuevo_boton);
                            
                            $row['botones'] = GenerateCustomsButtons($botones_acciones, 'cotizaciones_compras');
                            $row['kid_estatus'] = $estatus[$row['kid_estatus']];
                            return $row;
                        };
                    
                        $ColumnsCheck = [['column'=>"cotizacion_compras","check_similar"=>false]];
                        break;

                case 'update_estatus_cotizaciones_compras':

                    $opcion = 2;
                    $id = $_POST['firstColumnValue'];
                    $consultacotizacion = "SELECT cc.id_cotizacion_compra,
                        cc.cotizacion_compras,
                        cc.grupo,
                        cc.kid_proyecto,
                        cc.kid_proveedor,
                        cc.kid_estatus,
                        cc.kid_autorizo,
                        SUM(dcc.monto_total) AS monto_total,
                        SUM(dcc.monto_neto) AS monto_neto
                    FROM cotizaciones_compras cc
                    LEFT JOIN detalles_cotizaciones_compras dcc ON cc.id_cotizacion_compra = dcc.kid_cotizacion_compra
                    WHERE cc.kid_estatus != 3 AND cc.id_cotizacion_compra = :id
                    GROUP BY cc.id_cotizacion_compra, cc.grupo, cc.kid_proyecto, cc.kid_proveedor, cc.kid_estatus";
                    $resultado = $conexion->prepare($consultacotizacion);
                    $resultado->bindParam(':id', $id);
                    $resultado->execute();
                    $cotizacion = $resultado->fetch(PDO::FETCH_ASSOC);

                    $formDataJson['kid_estatus'] = isset($formDataJson['UpdateEstatus']) ? GetIDEstatusByName($formDataJson['UpdateEstatus']) : null;

                    unset($formDataJson['UpdateEstatus']);

                    $editformDataJson = CleanJson($formDataJson);

                    if (isset($formDataJson['kid_estatus']) && $editformDataJson['kid_estatus'] == 5 && $cotizacion['kid_estatus'] != 5) {
                        try {
                            // Iniciar la transacción
                            $conexion->beginTransaction();

                            $consulordenes = "INSERT INTO ordenes_compras (
                                orden_compras,
                                codigo_externo,
                                grupo_cotizacion,
                                kid_proyecto,
                                kid_proveedor,
                                monto_total,
                                monto_neto,
                                kid_creacion,
                                fecha_creacion,
                                kid_estatus
                            ) VALUES (
                                CONCAT('Orden compra de ', :cotizacion_compras),
                                CONCAT('COMP', DATE_FORMAT(NOW(), '%Y%m%d%H%i%s')),
                                :grupo_cotizacion,
                                :kid_proyecto,
                                :kid_proveedor,
                                :monto_total,
                                :monto_neto,
                                :kid_creacion,
                                :fecha_creacion,
                                :kid_estatus
                            );";
                            $cotizacion['monto_total'] =$cotizacion['monto_total']?$cotizacion['monto_total']:0;
                            $cotizacion['monto_neto'] = $cotizacion['monto_neto']?$cotizacion['monto_neto']:0;
                            
                            // Preparar y ejecutar la consulta
                            $resultado = $conexion->prepare($consulordenes);
                            $resultado->bindParam(':cotizacion_compras', $cotizacion['cotizacion_compras']);
                            $resultado->bindParam(':grupo_cotizacion', $cotizacion['grupo']);
                            $resultado->bindParam(':kid_proyecto', $cotizacion['kid_proyecto']);
                            $resultado->bindParam(':kid_proveedor',$cotizacion['kid_proveedor']);
                            $resultado->bindParam(':monto_total', $cotizacion['monto_total']);
                            $resultado->bindParam(':monto_neto', $cotizacion['monto_neto']);
                            $resultado->bindParam(':kid_creacion', $_SESSION["s_id"]);
                            $resultado->bindValue(':fecha_creacion', date('Y-m-d H:i:s'));
                            $resultado->bindValue(':kid_estatus', 8);
                            
                            if ($resultado->execute()) {
                                $orden_compra_id = $conexion->lastInsertId();
                                // Array para almacenar los IDs de los artículos insertados
                                $insertedItems = [];

                                $consultdetalle = "SELECT id_detalle_cotizacion_compras,
                                    kid_articulo,
                                    cantidad,
                                    costo_unitario_total,
                                    costo_unitario_neto,
                                    monto_total,
                                    monto_neto,
                                    fecha_creacion
                                FROM detalles_cotizaciones_compras
                                WHERE kid_estatus != 3 AND kid_cotizacion_compra  = :id";
                                $resultado = $conexion->prepare($consultdetalle);
                                $resultado->bindParam(':id', $cotizacion['id_cotizacion_compra']);
                                $resultado->execute();
                                $detalles_cotizacion = $resultado->fetchAll(PDO::FETCH_ASSOC);

                                $insertQuery = "INSERT INTO detalles_ordenes_compras (kid_orden_compras,grupo_cotizacion, kid_articulo, cantidad, costo_unitario_total, costo_unitario_neto, monto_total, monto_neto,kid_creacion, fecha_creacion ,kid_estatus) 
                                VALUES (:kid_orden_compras,:grupo_cotizacion, :kid_articulo, :cantidad, :costo_unitario_total, :costo_unitario_neto, :monto_total, :monto_neto, :kid_creacion, :fecha_creacion ,:kid_estatus)";

                                $insertStmt = $conexion->prepare($insertQuery);
                                

                                foreach ($detalles_cotizacion as $detalle) {
                                    $insertStmt->bindParam(':kid_orden_compras', $orden_compra_id);
                                    $insertStmt->bindParam(':grupo_cotizacion', $cotizacion['grupo']);
                                    $insertStmt->bindParam(':kid_articulo', $detalle['kid_articulo']);
                                    $insertStmt->bindParam(':cantidad', $detalle['cantidad']);
                                    $insertStmt->bindParam(':costo_unitario_total', $detalle['costo_unitario_total']);
                                    $insertStmt->bindParam(':costo_unitario_neto', $detalle['costo_unitario_neto']);
                                    $insertStmt->bindParam(':monto_total', $detalle['monto_total']);
                                    $insertStmt->bindParam(':monto_neto', $detalle['monto_neto']);
                                    $insertStmt->bindParam(':kid_creacion', $_SESSION["s_id"]);
                                    $insertStmt->bindValue(':fecha_creacion', date('Y-m-d H:i:s'));
                                    $insertStmt->bindValue(':kid_estatus', 7);
                                    
                                    // Ejecutar la inserción
                                    if ($insertStmt->execute()) {
                                        // Almacenar el ID del artículo insertado
                                        $insertedItems[] = $detalle['kid_articulo'];
                                        
                                    } else {
                                        throw new Exception("Error en la inserción para el artículo: " . $detalle['kid_articulo']);
                                    }
                                }
                            }

                            

                            // Si todas las inserciones fueron exitosas, confirmar la transacción
                            $conexion->commit();
                        // echo "Todas las inserciones se realizaron correctamente.<br>";
                            $editformDataJson['kid_estatus']=5;
                            $editformDataJson['kid_autorizo']=$_SESSION["s_id"];
                            


                        } catch (Exception $e) {
                            // Si hubo un error, revertir la transacción
                            $conexion->rollBack();
                            $respuesta = ['status' => 'error', 'message' => 'Transacción fallida: ' . $e->getMessage()];
                            echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
                            exit;
                        }

                    }

                    $tabla = 'cotizaciones_compras';
                    $idcolumn= "id_cotizacion_compra";

                    $consultaselect = "SELECT cc.id_cotizacion_compra,
                            cc.cotizacion_compras,
                            cc.grupo,
                            p.proyecto AS kid_proyecto,
                            prov.razon_social AS kid_proveedor,
                            cc.kid_estatus,
                            u.email AS kid_creacion,
                            COALESCE(u2.email, 'Sin Autorizar') AS kid_autorizo,
                            cc.fecha_creacion
                        FROM cotizaciones_compras cc
                        LEFT JOIN proyectos p ON cc.kid_proyecto = p.id_proyecto
                        LEFT JOIN proveedores prov ON cc.kid_proveedor = prov.id_proveedor
                        LEFT JOIN colaboradores u ON cc.kid_creacion = u.id_colaborador
                        LEFT JOIN colaboradores u2 ON cc.kid_autorizo = u.id_colaborador
                        WHERE cc.kid_estatus != 3 and ".$idcolumn." = :".$idcolumn;

                        $fuc_mapping = function ($row) {
                            global $data_script, $estatus, $estatus_name;
                            $botones_acciones = $data_script['botones_acciones'];

                            $bloque = 'compras';
                            $modalCRUD =  'update_estatus_cotizaciones_compras';
                            if(!in_array($row['kid_estatus'], [5,6,7])){
                                $nuevo_boton = '<button class="UpdateEstatus btn btn-success" bloque="'. $bloque.'" name="'.$estatus_name[6].'" modalCRUD="'.$modalCRUD.'"><i class="bi bi-check2"></i> Revisar I</button>';
                                array_unshift($botones_acciones,$nuevo_boton);
                            }else if($row['kid_estatus'] == 6){
                                $nuevo_boton = '<button class="UpdateEstatus btn btn-success" bloque="'. $bloque.'" name="'.$estatus_name[7].'" modalCRUD="'.$modalCRUD.'"><i class="bi bi-check2-all"></i> Revisar II</button>';
                                array_unshift($botones_acciones,$nuevo_boton);
                            }else if($row['kid_estatus'] == 7){
                                $nuevo_boton = '<button class="UpdateEstatus btn btn-success" bloque="'. $bloque.'" name="'.$estatus_name[5].'" modalCRUD="'.$modalCRUD.'"><i class="bi bi-check2-circle"></i> Autorizar</button>';
                                array_unshift($botones_acciones,$nuevo_boton);
                            }
                            
                            $hashed_id = codificar($row['id_cotizacion_compra']);
                            $nuevo_boton = '<a href="/rutas/compras.php/detalles_cotizaciones_compras?id=' . $hashed_id . '" class="btn btn-secondary "><i class="bi bi-journal-text"></i> Contenido</a>';
                            array_push($botones_acciones, $nuevo_boton);
                            $nuevo_boton = '<button class="GenerarReporte btn btn-success success" reporte="proveedores_cuadro_comparativo" data-id="'.$hashed_id.'"><i class="bi bi-play-circle"></i> Cuadro Comparativo</button>';
                            array_push($botones_acciones, $nuevo_boton);
                            $row['botones'] = GenerateCustomsButtons($botones_acciones, 'cotizaciones_compras');
                            $row['kid_estatus'] = $estatus[$row['kid_estatus']];
                            return $row;
                        };



                    //debug($editformDataJson);
                    $ColumnsCheck = [];

                    $text_colums_edit = [];
                break;


                case 'detalles_cotizaciones_compras':
                    $tabla = 'detalles_cotizaciones_compras';
                    $idcolumn= "id_detalle_cotizacion_compras";

                    /*-------------------- Obtener Tablas Foráneas --------------------*/
                    $formDataJson['kid_articulo'] = isset($formDataJson['kid_articulo']) ? GetIDArticuloByName($formDataJson['kid_articulo']) : null; 
                    $formDataJson['kid_cotizacion_compra'] = isset($formDataJson['kid_cotizacion_compra']) ? GetIDCotizacionComprasByName($formDataJson['kid_cotizacion_compra']) : null;
                    /*------------------- Fin Obtener Tablas Foráneas ------------------*/

                    $editformDataJson = CleanJson($formDataJson);
                    //$formDataJson = insertarDespuesDeClave($formDataJson, 'marca', ['fecha_creacion'=>date('Y-m-d H:i:s')]);
                    $newformDataJson = $formDataJson;
                    $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                    $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                    $newformDataJson['kid_estatus'] = 1;
                    $consultaselect = "SELECT dcc.id_detalle_cotizacion_compras,
                        cc.cotizacion_compras AS kid_cotizacion_compra,
                        a.articulo AS kid_articulo,
                        dcc.cantidad,
                        dcc.costo_unitario_total,
                        dcc.costo_unitario_neto,
                        dcc.monto_total,
                        dcc.monto_neto,
                        dcc.fecha_creacion
                    FROM detalles_cotizaciones_compras dcc
                    LEFT JOIN cotizaciones_compras cc ON dcc.kid_cotizacion_compra = cc.id_cotizacion_compra 
                    LEFT JOIN articulos a ON dcc.kid_articulo = a.id_articulo
                    WHERE dcc.kid_estatus != 3 and ".$idcolumn." = :".$idcolumn;

                    $ColumnsCheck = [];
                    break;

                    case 'ordenes_compras':
                        $tabla = 'ordenes_compras';
                        $idcolumn= "id_orden_compras";
                        $custombt = true;
        
                        /*-------------------- Obtener Tablas Foráneas --------------------*/
                        $formDataJson['kid_proyecto'] = (isset($formDataJson['kid_proyecto']) && !is_numeric($formDataJson['kid_proyecto']) ? GetIDProyectoByName($formDataJson['kid_proyecto']) : null);
                        $formDataJson['kid_proveedor'] = (isset($formDataJson['kid_proveedor']) && !is_numeric($formDataJson['kid_proveedor'])) ? GetIDProveedorByName($formDataJson['kid_proveedor']) : null;
                        $formDataJson['kid_estatus'] = isset($formDataJson['kid_estatus']) ? GetIDEstatusByName($formDataJson['kid_estatus']) : null;
                        /*------------------- Fin Obtener Tablas Foráneas ------------------*/
        
                        $editformDataJson = CleanJson($formDataJson);
                        //$formDataJson = insertarDespuesDeClave($formDataJson, 'marca', ['fecha_creacion'=>date('Y-m-d H:i:s')]);
                        $newformDataJson = $formDataJson;
                        $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                        $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                        $newformDataJson['kid_estatus'] = 1;
        
                        $consultaselect = "SELECT oc.id_orden_compras ,
                            oc.orden_compras,
                            oc.codigo_externo,
                            oc.grupo_cotizacion,
                            p.proyecto AS kid_proyecto,
                            prov.proveedor AS kid_proveedor,
                            oc.monto_total,
                            oc.monto_neto,
                            oc.kid_estatus,
                            oc.fecha_creacion
                        FROM ordenes_compras oc
                        LEFT JOIN proyectos p ON oc.kid_proyecto = p.id_proyecto
                        LEFT JOIN proveedores prov ON oc.kid_proveedor = prov.id_proveedor
                        WHERE oc.kid_estatus !=3 and ".$idcolumn." = :".$idcolumn;
        
                        
                        $fuc_mapping = function ($row) {
                            global $data_script, $estatus, $estatus_name;
                            $botones_acciones = $data_script['botones_acciones'];
            
                            $bloque = 'compras';
                            $modalCRUD =  'update_estatus_ordenes_compras';
                            if(!in_array($row['kid_estatus'], [5,6,9])){
                                $nuevo_boton = '<button class="UpdateEstatus btn btn-success" bloque="'. $bloque.'" name="'.$estatus_name[6].'" modalCRUD="'.$modalCRUD.'"><i class="bi bi-check2"></i> Revisar</button>';
                                array_unshift($botones_acciones,$nuevo_boton);
                            }else if($row['kid_estatus'] == 6){
                                $nuevo_boton = '<button class="UpdateEstatus btn btn-success" bloque="'. $bloque.'" name="'.$estatus_name[5].'" modalCRUD="'.$modalCRUD.'"><i class="bi bi-check2-circle"></i> Autorizar</button>';
                                array_unshift($botones_acciones,$nuevo_boton);
                            }if($row['kid_estatus'] == 5){
                                $nuevo_boton = '<button class="ModalSetData btn btn-success" bloque="'. $bloque.'" name="'.$estatus_name[9].'" modalCRUD="recepciones_compras"><i class="bi bi-check2-circle"></i> Comprar</button>';
                                array_unshift($botones_acciones,$nuevo_boton);
                            }
                            
                            $hashed_id = codificar($row['id_orden_compras']);
                            $nuevo_boton = '<a href="/rutas/compras.php/detalles_ordenes_compras?id=' . $hashed_id . '" class="btn btn-secondary "><i class="bi bi-journal-text"></i> Contenido</a>';
                            array_push($botones_acciones, $nuevo_boton);
                            $nuevo_boton = '<button class="GenerarReporte btn btn-success success" reporte="proveedores_cuadro_comparativo" data-id="'.$hashed_id.'"><i class="bi bi-play-circle"></i> Cuadro Comparativo</button>';
                            array_push($botones_acciones, $nuevo_boton);
                            $row['botones'] = GenerateCustomsButtons($botones_acciones, 'cotizaciones_compras');
                            $row['kid_estatus'] = $estatus[$row['kid_estatus']];
                            return $row;
                        };
        
                        $ColumnsCheck = [
                            ['column'=>"orden_compras","check_similar"=>false]
                        ];
        
                        $nuevo_boton = '<button class="ModalNewAdd3 btn btn-info info" modalCRUD="'.$modalCRUD.'"><i class="bi bi-file-spreadsheet"></i> Ver Detalles</button>';
                        //array_splice($data_script['botones_acciones'], 0, 0, $nuevo_boton);
                        array_push($data_script['botones_acciones'], $nuevo_boton);
        
                        $modalCRUD = 'ordenes_compras';
                        $newbuttons = [
                            7 =>'<button class="UpdateEstatus btn btn-success success" bloque="compras" name="revisar" modalCRUD="'.$modalCRUD.'"><i class="bi bi-check2"></i> Dar Revisión</button>',
                            6 =>'<button class="UpdateEstatus btn btn-success success" bloque="compras" name="autorizar" modalCRUD="'.$modalCRUD.'"><i class="bi bi-check2-all"></i> Autorizar</button>',
                            5 => '<button class="ModalSetData btn btn-success success" bloque="compras" name="comprar" modalCRUD="recepciones_compras"><i class="bi bi-bag"></i> Confirmar Compra</button>'
                        ];
        
                        if (isset($formDataJson['UpdateEstatus'])) {
                            $statusMap = [
                                'revisar' => 6,
                                'autorizar' => 5
                            ];
                            
                            if (array_key_exists($formDataJson['UpdateEstatus'], $statusMap)) {
                                $editformDataJson['kid_estatus'] = $statusMap[$formDataJson['UpdateEstatus']];
                                $ColumnsCheck = [];
                                unset($editformDataJson['UpdateEstatus']);
                            }
                        }
                        break;

                case 'update_estatus_ordenes_compras':

                    $opcion = 2;
                    $id = $_POST['firstColumnValue'];

                    $formDataJson['kid_estatus'] = isset($formDataJson['UpdateEstatus']) ? GetIDEstatusByName($formDataJson['UpdateEstatus']) : null;
        
                    unset($formDataJson['UpdateEstatus']);
        
                    $editformDataJson = CleanJson($formDataJson);
        
                    $tabla = 'ordenes_compras';
                    $idcolumn= "id_orden_compras";
        
                    $consultaselect = "SELECT oc.id_orden_compras ,
                        oc.orden_compras,
                        oc.codigo_externo,
                        oc.grupo_cotizacion,
                        p.proyecto AS kid_proyecto,
                        prov.proveedor AS kid_proveedor,
                        oc.monto_total,
                        oc.monto_neto,
                        oc.kid_estatus,
                        oc.fecha_creacion
                    FROM ordenes_compras oc
                    LEFT JOIN proyectos p ON oc.kid_proyecto = p.id_proyecto
                    LEFT JOIN proveedores prov ON oc.kid_proveedor = prov.id_proveedor
                    WHERE oc.kid_estatus !=3 and ".$idcolumn." = :".$idcolumn;

                    
                    $fuc_mapping = function ($row) {
                        global $data_script, $estatus, $estatus_name;
                        $botones_acciones = $data_script['botones_acciones'];
        
                        $bloque = 'compras';
                        $modalCRUD =  'update_estatus_ordenes_compras';
                        if(!in_array($row['kid_estatus'], [5,6,9])){
                            $nuevo_boton = '<button class="UpdateEstatus btn btn-success" bloque="'. $bloque.'" name="'.$estatus_name[6].'" modalCRUD="'.$modalCRUD.'"><i class="bi bi-check2"></i> Revisar</button>';
                            array_unshift($botones_acciones,$nuevo_boton);
                        }else if($row['kid_estatus'] == 6){
                            $nuevo_boton = '<button class="UpdateEstatus btn btn-success" bloque="'. $bloque.'" name="'.$estatus_name[5].'" modalCRUD="'.$modalCRUD.'"><i class="bi bi-check2-circle"></i> Autorizar</button>';
                            array_unshift($botones_acciones,$nuevo_boton);
                        }if($row['kid_estatus'] == 5){
                            $nuevo_boton = '<button class="ModalSetData btn btn-success" bloque="'. $bloque.'" name="'.$estatus_name[9].'" modalCRUD="recepciones_compras"><i class="bi bi-check2-circle"></i> Comprar</button>';
                            array_unshift($botones_acciones,$nuevo_boton);
                        }
                        
                        $hashed_id = codificar($row['id_orden_compras']);
                        $nuevo_boton = '<a href="/rutas/compras.php/detalles_ordenes_compras?id=' . $hashed_id . '" class="btn btn-secondary "><i class="bi bi-journal-text"></i> Contenido</a>';
                        array_push($botones_acciones, $nuevo_boton);
                        $nuevo_boton = '<button class="GenerarReporte btn btn-success success" reporte="proveedores_cuadro_comparativo" data-id="'.$hashed_id.'"><i class="bi bi-play-circle"></i> Cuadro Comparativo</button>';
                        array_push($botones_acciones, $nuevo_boton);
                        $row['botones'] = GenerateCustomsButtons($botones_acciones, 'cotizaciones_compras');
                        $row['kid_estatus'] = $estatus[$row['kid_estatus']];
                        return $row;
                    };
        
        
        
                    //debug($editformDataJson);
                    $ColumnsCheck = [];
        
                    $text_colums_edit = [];
                    break;

                case 'detalles_ordenes_compras':
                    $tabla = 'detalles_ordenes_compras';
                    $idcolumn= "id_detalle_orden_compra";

                    /*-------------------- Obtener Tablas Foráneas --------------------*/
                    $formDataJson['kid_articulo'] = isset($formDataJson['kid_articulo']) ? GetIDArticuloByName($formDataJson['kid_articulo']) : null; 
                    $formDataJson['kid_orden_compra'] = isset($formDataJson['kid_orden_compra']) ? GetIDOrdenComprasByName($formDataJson['kid_orden_compra']) : null;
                    /*------------------- Fin Obtener Tablas Foráneas ------------------*/

                    $editformDataJson = CleanJson($formDataJson);
                    //$formDataJson = insertarDespuesDeClave($formDataJson, 'marca', ['fecha_creacion'=>date('Y-m-d H:i:s')]);
                    $newformDataJson = $formDataJson;
                    $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                    $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                    $newformDataJson['kid_estatus'] = 1;
                    $consultaselect = "SELECT doc.id_detalle_orden_compra,
                        oc.orden_compras AS kid_orden_compra,
                        doc.grupo_cotizacion,
                        a.articulo AS kid_articulo,
                        doc.cantidad,
                        doc.costo_unitario_total,
                        doc.costo_unitario_neto,
                        doc.monto_total,
                        doc.monto_neto,
                        doc.fecha_creacion
                    FROM detalles_ordenes_compras doc
                    LEFT JOIN articulos a ON doc.kid_articulo = a.id_articulo
                    LEFT JOIN ordenes_compras oc ON doc.kid_orden_compras = oc.id_orden_compras
                    WHERE doc.kid_estatus  !=3 and ".$idcolumn." = :".$idcolumn;

                    $ColumnsCheck = [];
                    break;

                
                case 'recepciones_compras':
                    $tabla = 'recepciones_compras';
                    $idcolumn= "id_recepcion_compras";
                    $estatus = GetEstatusLabels();
                    /*-------------------- Obtener Tablas Foráneas --------------------*/
                    //$formDataJson['kid_articulo'] = isset($formDataJson['kid_articulo']) ? GetIDArticuloByName($formDataJson['kid_articulo']) : null; 
                    //$formDataJson['kid_orden_compra'] = isset($formDataJson['kid_orden_compra']) ? GetIDOrdenComprasByName($formDataJson['kid_orden_compra']) : null;
                    $formDataJson['kid_proyecto'] = isset($formDataJson['kid_alkid_proyectomacen']) ? GetIDProyectoByName($formDataJson['kid_proyecto']) : null;
                    $formDataJson['kid_proveedor'] = isset($formDataJson['kid_proveedor']) ? GetIDProveedorByName($formDataJson['kid_proveedor']) : null;
                    $formDataJson['kid_orden_compras'] = isset($formDataJson['kid_orden_compras']) ? GetIDOrdenComprasByName($formDataJson['kid_orden_compras']) : null;
                    $formDataJson['kid_almacen'] = isset($formDataJson['kid_almacen']) ? GetIDAlmacenesByName($formDataJson['kid_almacen']) : null;
                    $formDataJson['kid_recibe'] = isset($formDataJson['kid_recibe']) ? GetIDUsuariosByName($formDataJson['kid_recibe']) : null;
                    $formDataJson['kid_reclama'] = isset($formDataJson['kid_reclama']) ? GetIDUsuariosByName($formDataJson['kid_reclama']) : null;
                    $formDataJson['kid_regresa'] = isset($formDataJson['kid_regresa']) ? GetIDUsuariosByName($formDataJson['kid_regresa']) : null;
                    /*------------------- Fin Obtener Tablas Foráneas ------------------*/
                    $editformDataJson = CleanJson($formDataJson);
                    $newformDataJson = $formDataJson;
                    if($opcion == 1){
                        $consulta = "SELECT * FROM ordenes_compras WHERE id_orden_compras = :id";
                        $resultado = $conexion->prepare($consulta);
                        $resultado->bindParam(':id', $_POST['firstColumnValue']);
                        $resultado->execute();
                        $data_result = $resultado->fetch(PDO::FETCH_ASSOC);
                        $newformDataJson['kid_orden_compras'] = $data_result['id_orden_compras'];
                        $newformDataJson['grupo_cotizacion'] = $data_result['grupo_cotizacion'];
                        $newformDataJson['kid_proyecto'] = $data_result['kid_proyecto'];
                        $newformDataJson['kid_proveedor'] = $data_result['kid_proveedor'];
                        $newformDataJson['monto_total'] = $data_result['monto_total'];
                        $newformDataJson['monto_neto'] = $data_result['monto_neto'];

                        $consultdetalle = "SELECT * FROM detalles_cotizaciones_compras WHERE kid_estatus != 3 AND kid_cotizacion_compra  = :id";
                        $resultado = $conexion->prepare($consultdetalle);
                        $resultado->bindParam(':id', $data_result['id_orden_compras']);
                        $resultado->execute();
                        $detalles_orden_compra = $resultado->fetchAll(PDO::FETCH_ASSOC);

                        $add_detalles_table = 'detalles_recepciones_compras';
                        foreach ($detalles_orden_compra as $detalle) {
                            $add_detalles[] =[
                                'kid_articulo' => $detalle['kid_articulo'],
                                'cantidad' => $detalle['cantidad'],
                                'kid_recepcion_compras' => ':id',
                                'costo_unitario_total' => $detalle['costo_unitario_total'],
                                'costo_unitario_neto' => $detalle['costo_unitario_neto'],
                                'monto_total' => $detalle['monto_total'],
                                'monto_neto' => $detalle['monto_neto'],
                                'kid_creacion'=>$_SESSION["s_id"],
                                'fecha_creacion'=>date('Y-m-d H:i:s'),
                                'kid_estatus'=> 7
                            ];
                        }

                        $update_row_consult = "UPDATE ordenes_compras SET kid_estatus = 9 WHERE id_orden_compras = ".$data_result['id_orden_compras'];
                    }
                    
                    
                    $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                    $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                    $newformDataJson['kid_estatus'] = 10;

                
                    
                    $caseEstatus = "CASE \n";
                    foreach ($estatus as $key => $value) {
                        $caseEstatus .= "    WHEN rc.kid_estatus = $key THEN '$value'\n";
                    }
                    $caseEstatus .= "    ELSE 'Desconocido' \nEND AS kid_estatus";
                    $consultaselect = "SELECT rc.id_recepcion_compras,
                        rc.recepcion_compras,
                        rc.codigo_externo,
                        (SELECT proyecto FROM proyectos p WHERE p.id_proyecto =rc.kid_proyecto LIMIT 1) AS kid_proyecto,
                        (SELECT proveedor FROM proveedores prov WHERE prov.id_proveedor = rc.kid_proveedor LIMIT 1) AS kid_proveedor,
                        (SELECT almacen FROM almacenes alm WHERE alm.id_almacen  = rc.kid_almacen LIMIT 1) AS kid_almacen,
                        (SELECT orden_compras FROM ordenes_compras oc WHERE oc.id_orden_compras  = rc.kid_orden_compras LIMIT 1) AS kid_orden_compras,
                        $caseEstatus,
                        fecha_creacion
                    FROM recepciones_compras rc
                    WHERE rc.kid_estatus !=3 and ".$idcolumn." = :".$idcolumn;

                    $ColumnsCheck = [
                        ['column'=>"recepcion_compras","check_similar"=>true],
                        ['column'=>"codigo_externo","check_similar"=>false]
                        ];
                    break;

                case 'detalles_recepciones_compras':
                    $tabla = 'detalles_recepciones_compras';
                    $idcolumn= "id_detalle_recepcion_compras";

                    /*-------------------- Obtener Tablas Foráneas --------------------*/
                    $formDataJson['kid_articulo'] = isset($formDataJson['kid_articulo']) ? GetIDArticuloByName($formDataJson['kid_articulo']) : null; 
                    //$formDataJson['kid_orden_compra'] = isset($formDataJson['kid_orden_compra']) ? GetIDOrdenComprasByName($formDataJson['kid_orden_compra']) : null;
                    /*------------------- Fin Obtener Tablas Foráneas ------------------*/

                    $editformDataJson = CleanJson($formDataJson);
                    //$formDataJson = insertarDespuesDeClave($formDataJson, 'marca', ['fecha_creacion'=>date('Y-m-d H:i:s')]);
                    $newformDataJson = $formDataJson;
                    $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                    $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                    $newformDataJson['kid_estatus'] = 1;
                    $consultaselect = "SELECT drc.id_detalle_recepcion_compras,
                        a.articulo AS kid_articulo,
                        rc.recepcion_compras AS kid_recepcion_compras,
                        drc.cantidad,
                        drc.costo_unitario_total,
                        drc.costo_unitario_neto,
                        drc.monto_total,
                        drc.monto_neto,
                        drc.fecha_creacion
                    FROM detalles_recepciones_compras drc
                    LEFT JOIN articulos a ON drc.kid_articulo = a.id_articulo
                    LEFT JOIN recepciones_compras rc ON drc.kid_recepcion_compras = rc.id_recepcion_compras
                    WHERE drc.kid_estatus  !=3 and ".$idcolumn." = :".$idcolumn;

                    $ColumnsCheck = [];
                    break;

                case 'comentarios_recepciones':
                    $tabla = 'comentarios_recepciones';
                    $idcolumn= "id_comentario_recepcion";

                    /*-------------------- Obtener Tablas Foráneas --------------------*/
                    //$colaboradores = GetUsuariosListById();
                    $formDataJson['kid_recepcion_compras'] = isset($formDataJson['kid_recepcion_compras']) ? GetIDRecepcionCompraByName($formDataJson['kid_recepcion_compras']) : null;
                    $formDataJson['kid_tipo_comentario'] = isset($formDataJson['kid_tipo_comentario']) ? GetIDTipoComentarioByName($formDataJson['kid_tipo_comentario']) : null;  
                    /*------------------- Fin Obtener Tablas Foráneas ------------------*/


                    $editformDataJson = CleanJson($formDataJson);
                    //$formDataJson = insertarDespuesDeClave($formDataJson, 'marca', ['fecha_creacion'=>date('Y-m-d H:i:s')]);
                    $newformDataJson = $formDataJson;
                    $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                    $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                    $newformDataJson['kid_estatus'] = 1;

                    $consultaselect = "SELECT cr.id_comentario_recepcion, 
                        rc.recepcion_compras AS kid_recepcion_compras, 
                        ar.articulo,
                        cr.comentario_recepcion_compras,
                        tc.tipo_comentario AS kid_tipo_comentario,
                        cr.fecha_creacion
                    FROM 
                        $tabla cr
                    LEFT JOIN 
                        recepciones_compras rc ON cr.kid_recepcion_compras = rc.id_recepcion_compras 
                    LEFT JOIN 
                        detalles_recepciones_compras drc ON cr.kid_detalle_recepcion_compras = drc.id_detalle_recepcion_compras 
                    LEFT JOIN 
                        articulos ar ON drc.kid_articulo = ar.id_articulo
                    LEFT JOIN 
                        tipos_comentarios tc ON cr.kid_tipo_comentario = tc.id_tipo_comentario
                    WHERE cr.$idcolumn = :$idcolumn";

                    $ColumnsCheck = [
                        
                    ];
                    break;

                case 'asignacion_viaticos':
                    $tabla = 'asignacion_viaticos';
                    $idcolumn= "id_asignacion_viaticos";

                    /*-------------------- Obtener Tablas Foráneas --------------------*/
                    $formDataJson['kid_tipo_viatico'] = isset($formDataJson['kid_tipo_viatico']) ? GetIDTipoViaticoByName($formDataJson['kid_tipo_viatico']) : null;
                    $formDataJson['kid_responsable'] = isset($formDataJson['kid_responsable']) ? GetIDUsuariosByName($formDataJson['kid_responsable']): null;
                    $formDataJson['kid_proyecto'] = isset($formDataJson['kid_proyecto']) ? GetIDProyectoByName($formDataJson['kid_proyecto']) : null;
                    $formDataJson['kid_detalle_actividad'] = isset($formDataJson['kid_detalle_actividad']) ? GetIDDetalleActividadByName($formDataJson['kid_detalle_actividad']) : null;
                    /*------------------- Fin Obtener Tablas Foráneas ------------------*/


                    $editformDataJson = CleanJson($formDataJson);
                    //$formDataJson = insertarDespuesDeClave($formDataJson, 'marca', ['fecha_creacion'=>date('Y-m-d H:i:s')]);
                    $newformDataJson = $formDataJson;
                    $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                    $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                    $newformDataJson['kid_estatus'] = 1;

                    //debug($newformDataJson);

                    $consultaselect = "SELECT av.id_asignacion_viaticos,
                        tv.tipo_viatico,
                        av.justificacion,
                        av.monto_asignado,
                        av.monto_real,
                        CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) as kid_personal_asignado,
                        p.proyecto,
                        da.actividad,
                        av.fecha_creacion
                    FROM 
                        asignacion_viaticos av
                    LEFT JOIN 
                        tipos_viaticos tv ON av.kid_tipo_viatico = tv.id_tipo_viatico
                    LEFT JOIN 
                        colaboradores u ON av.kid_responsable = u.id_colaborador
                    LEFT JOIN 
                        proyectos p ON av.kid_proyecto = p.id_proyecto
                    LEFT JOIN 
                        detalles_actividades da ON av.kid_detalle_actividad = da.id_detalle_actividad 
                    WHERE av.kid_estatus !=3 AND $idcolumn = :$idcolumn";

                    $ColumnsCheck = [
                        //['column'=>"tipo_viatico","check_similar"=>true],
                    ];
                    break;


                case 'tipos_viaticos':
                    $tabla = 'tipos_viaticos';
                    $idcolumn= "id_tipo_viatico";

                    /*-------------------- Obtener Tablas Foráneas --------------------*/
                    /*------------------- Fin Obtener Tablas Foráneas ------------------*/


                    $editformDataJson = CleanJson($formDataJson);
                    //$formDataJson = insertarDespuesDeClave($formDataJson, 'marca', ['fecha_creacion'=>date('Y-m-d H:i:s')]);
                    $newformDataJson = $formDataJson;
                    $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                    $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                    $newformDataJson['kid_estatus'] = 1;

                    $consultaselect = "SELECT id_tipo_viatico , 
                        tipo_viatico,
                        orden,
                        pordefecto,
                        fecha_creacion
                    FROM 
                        tipos_viaticos
                    WHERE kid_estatus !=3 AND $idcolumn = :$idcolumn";

                    $ColumnsCheck = [
                        ['column'=>"tipo_viatico","check_similar"=>true],
                    ];
                    break;

                case 'tiempos_entregas':
                    $tabla = 'tiempos_entregas';
                    $idcolumn= "id_tiempo_entrega";

                    /*-------------------- Obtener Tablas Foráneas --------------------*/
                    /*------------------- Fin Obtener Tablas Foráneas ------------------*/


                    $editformDataJson = CleanJson($formDataJson);
                    //$formDataJson = insertarDespuesDeClave($formDataJson, 'marca', ['fecha_creacion'=>date('Y-m-d H:i:s')]);
                    $newformDataJson = $formDataJson;
                    $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                    $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                    $newformDataJson['kid_estatus'] = 1;

                    $consultaselect = "SELECT id_tiempo_entrega, 
                        tiempo_entrega,
                        orden,
                        pordefecto,
                        fecha_creacion
                    FROM 
                        tiempos_entregas
                    WHERE kid_estatus !=3 AND $idcolumn = :$idcolumn";

                    $ColumnsCheck = [
                        ['column'=>"tiempo_entrega","check_similar"=>true],
                    ];
                    break;

                case 'tipos_pagos':
                    $tabla = 'tipos_pagos';
                    $idcolumn= "id_tipo_pago";

                    /*-------------------- Obtener Tablas Foráneas --------------------*/
                    /*------------------- Fin Obtener Tablas Foráneas ------------------*/


                    $editformDataJson = CleanJson($formDataJson);
                    //$formDataJson = insertarDespuesDeClave($formDataJson, 'marca', ['fecha_creacion'=>date('Y-m-d H:i:s')]);
                    $newformDataJson = $formDataJson;
                    $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                    $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                    $newformDataJson['kid_estatus'] = 1;

                    $consultaselect = "SELECT id_tipo_pago, 
                        tipo_pago,
                        orden,
                        pordefecto,
                        fecha_creacion
                    FROM 
                        tipos_pagos
                    WHERE kid_estatus !=3 AND $idcolumn = :$idcolumn";

                    $ColumnsCheck = [
                        ['column'=>"tipo_pago","check_similar"=>true],
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
    } catch (Exception $e) {
        $respuesta = ['status' => 'error', 'message' => $e->getMessage()];
        echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// Define the $respuesta variable to avoid undefined variable errors
$respuesta = $data ?? ['status' => 'error', 'message' => 'No response data available'];
// Limpia cualquier salida anterior
if (ob_get_level()) {
    ob_clean();
}

// Asegurar que siempre se devuelva un objeto JSON válido
if (!isset($respuesta)) {
    $respuesta = ['status' => 'error', 'message' => 'Respuesta no definida'];
}

// Enviar la respuesta JSON
header('Content-Type: application/json; charset=utf-8');
echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
exit;
?>