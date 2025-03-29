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
    $default_error = ['status' => 'error', 'message' => 'No se encontraron datos'];
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
            case 'bancos':
                $tabla = 'bancos';
                $idcolumn= "id_banco";
                $editformDataJson = $formDataJson;
                //$formDataJson = insertarDespuesDeClave($formDataJson, 'marca', ['fecha_creacion'=>date('Y-m-d H:i:s')]);
                $newformDataJson = $formDataJson;
                $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                $newformDataJson['kid_estatus'] = 1;
                $consultaselect = "SELECT id_banco,
                    orden,
                    banco,
                    CASE 
                        WHEN pordefecto = 1 THEN 'SÍ' 
                        ELSE 'NO' 
                    END,
                    fecha_creacion
                FROM bancos
                WHERE kid_estatus = 1 and ".$idcolumn." = :".$idcolumn;

                $ColumnsCheck = [
                    ['column'=>"banco","check_similar"=>true]
                ];

                
                break;

            case 'tipos_cuentas_bancarias':
                $tabla = 'tipos_cuentas_bancarias';
                $idcolumn= "id_tipo_cuenta_bancaria";

                $editformDataJson = $formDataJson;
                //$formDataJson = insertarDespuesDeClave($formDataJson, 'marca', ['fecha_creacion'=>date('Y-m-d H:i:s')]);
                $newformDataJson = $formDataJson;
                $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                $newformDataJson['kid_estatus'] = 1;
                $consultaselect = "SELECT id_tipo_cuenta_bancaria,
                    orden,
                    tipo_cuenta_bancaria,
                    CASE 
                        WHEN pordefecto = 1 THEN 'SÍ' 
                        ELSE 'NO' 
                    END,
                    fecha_creacion
                FROM tipos_cuentas_bancarias
                WHERE kid_estatus = 1 AND ".$idcolumn." = :".$idcolumn;
                $ColumnsCheck = [
                    ['column'=>"tipo_cuenta_bancaria","check_similar"=>true]
                ];

                
                break;
            case 'cuentas_bancarias':
                $tabla = 'cuentas_bancarias';
                $idcolumn= "id_cuenta_bancaria";

                /*-------------------- Obtener Tablas Foráneas --------------------*/
                $bancos = GetBancosListById();
                $tipos_cuentas_bancarias = GetTiposCuentasBancariasListById();
                /*------------------- Fin Obtener Tablas Foráneas ------------------*/

                if (!empty($formDataJson['kid_banco']) && isset($bancos[$formDataJson['kid_banco']])) {
                    $formDataJson['kid_banco'] = $bancos[$formDataJson['kid_banco']];
                }
                if (!empty($formDataJson['kid_tipo_cuenta_bancaria']) && isset($tipos_cuentas_bancarias[$formDataJson['kid_tipo_cuenta_bancaria']])) {
                    $formDataJson['kid_tipo_cuenta_bancaria'] = $tipos_cuentas_bancarias[$formDataJson['kid_tipo_cuenta_bancaria']];
                }
                $editformDataJson = $formDataJson;
                //$formDataJson = insertarDespuesDeClave($formDataJson, 'marca', ['fecha_creacion'=>date('Y-m-d H:i:s')]);
                $newformDataJson = $formDataJson;
                $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                $newformDataJson['kid_estatus'] = 1;
                $consultaselect = "SELECT cb.id_cuenta_bancaria,
                    cb.cuenta_bancaria,
                    b.banco AS kid_banco,
                    cb.numero_cuenta_bancaria,
                    cb.cable,
                    cb.tarjeta,
                    tcb.tipo_cuenta_bancaria,
                    cb.saldo,
                    cb.deuda,
                    cb.fecha_creacion
                FROM cuentas_bancarias cb
                LEFT JOIN bancos b ON cb.kid_banco = b.id_banco
                LEFT JOIN tipos_cuentas_bancarias tcb ON cb.kid_tipo_cuenta_bancaria = tcb.id_tipo_cuenta_bancaria 
                WHERE b.kid_estatus = 1 AND ".$idcolumn." = :".$idcolumn;

                $ColumnsCheck = [
                    ['column'=>"cuenta_bancaria","check_similar"=>true],
                    ['column'=>"numero_cuenta_bancaria","check_similar"=>false],
                    ['column'=>"cable","check_similar"=>false],
                    ['column'=>"tarjeta","check_similar"=>false],
                ];

                break;

            case 'detalles_cuentas_bancarias':
                $tabla = 'detalles_cuentas_bancarias';
                $idcolumn= "id_detalle_cuenta_bancaria";

                /*-------------------- Obtener Tablas Foráneas --------------------*/
                $cuentas = GetCuentasBancariasListById();
                $proyectos = GetProyectosListById();
                /*------------------- Fin Obtener Tablas Foráneas ------------------*/

                if (!empty($formDataJson['kid_cuenta_bancaria']) && isset($cuentas[$formDataJson['kid_cuenta_bancaria']])) {
                    $formDataJson['kid_cuenta_bancaria'] = $cuentas[$formDataJson['kid_cuenta_bancaria']];
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
                $consultaselect = "SELECT dcb.id_detalle_cuenta_bancaria,
                    dcb.detalle_cuenta_bancaria,
                    cb.cuenta_bancaria AS kid_cuenta_bancaria,
                    p.proyecto AS kid_proyecto,
                    dcb.monto_asignado,
                    dcb.monto_disponible,
                    dcb.monto_adeudado,
                    dcb.monto_gastado,
                    dcb.fecha_creacion
                FROM detalles_cuentas_bancarias dcb
                LEFT JOIN cuentas_bancarias cb ON dcb.kid_cuenta_bancaria = cb.id_cuenta_bancaria
                LEFT JOIN proyectos p ON dcb.kid_proyecto = p.id_proyecto 
                WHERE dcb.kid_estatus = 1 AND ".$idcolumn." = :".$idcolumn;

                $ColumnsCheck = [
                    ['column'=>"detalle_cuenta_bancaria","check_similar"=>true]
                    ];

                break;

            case 'compras_cuentas_bancarias':
                $tabla = 'compras_cuentas_bancarias';
                $idcolumn= "id_compra_cuenta_bancaria";

                /*-------------------- Obtener Tablas Foráneas --------------------*/
                $cuentas = GetCuentasBancariasListById();
                $proyectos = GetProyectosListById();
                /*------------------- Fin Obtener Tablas Foráneas ------------------*/

                if (!empty($formDataJson['kid_cuenta_bancaria']) && isset($cuentas[$formDataJson['kid_cuenta_bancaria']])) {
                    $formDataJson['kid_cuenta_bancaria'] = $cuentas[$formDataJson['kid_cuenta_bancaria']];
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
                $consultaselect = "SELECT ccb.id_compra_cuenta_bancaria ,
                    cb.cuenta_bancaria AS kid_cuenta_bancaria,
                    p.proyecto AS kid_proyecto,
                    ccb.monto_total,
                    ccb.monto_neto,
                    ccb.fecha_creacion
                FROM compras_cuentas_bancarias ccb
                LEFT JOIN cuentas_bancarias cb ON ccb.kid_cuenta_bancaria = cb.id_cuenta_bancaria
                LEFT JOIN proyectos p ON ccb.kid_proyecto = p.id_proyecto 
                WHERE ccb.kid_estatus = 1 AND ".$idcolumn." = :".$idcolumn;

                $ColumnsCheck = [];

                break;

            case 'facturas_clientes':
                $tabla = 'facturas_clientes';
                $idcolumn= "id_factura_cliente";
                /*-------------------- Obtener Tablas Foráneas --------------------*/
                $formDataJson['kid_cliente'] = isset($formDataJson['kid_cliente']) ? GetIDClienteByName($formDataJson['kid_cliente']) : null;
                $formDataJson['kid_proyecto'] = isset($formDataJson['kid_proyecto']) ? GetIDProyectoByName($formDataJson['kid_proyecto']) : null;
                $formDataJson['kid_cuenta_bancaria'] = isset($formDataJson['kid_cuenta_bancaria']) ? GetIDCuentaBancariaByName($formDataJson['kid_cuenta_bancaria']) : null;
                /*------------------- Fin Obtener Tablas Foráneas ------------------*/
                if ($_FILES) {
                    $pdfFolder  = dirname(__DIR__, 3)."/archivos/facturas/cliente/pdf/";
                    $xmlFolder  = dirname(__DIR__, 3)."/archivos/facturas/cliente/xml/";
                
                    // Verificar si las carpetas existen
                    if (!is_dir($pdfFolder)) {
                        mkdir($pdfFolder, 0777, true);
                    }
                    if (!is_dir($xmlFolder)) {
                        mkdir($xmlFolder, 0777, true);
                    }
                
                    // Subir archivos
                    try {
                        if (isset($_FILES['archivo_pdf']) && $_FILES['archivo_pdf']['error'] == 0) {
                            $pdfName = $_FILES['archivo_pdf']['name'];
                            $pdfTmp = $_FILES['archivo_pdf']['tmp_name'];
                            $pdfPath = $pdfFolder . $pdfName;
                            if (!move_uploaded_file($pdfTmp, $pdfPath)) {
                                throw new Exception("Error al subir el archivo PDF");
                            }
                            debug($pdfPath);
                            $formDataJson['archivo_pdf'] = str_replace('/opt/lampp/htdocs', '', $pdfPath); // Guardar la ruta del PDF
                        }
                
                        if (isset($_FILES['archivo_xml']) && $_FILES['archivo_xml']['error'] == 0) {
                            $xmlName = $_FILES['archivo_xml']['name'];
                            $xmlTmp = $_FILES['archivo_xml']['tmp_name'];
                            $xmlPath = $xmlFolder . $xmlName;
                            if (!move_uploaded_file($xmlTmp, $xmlPath)) {
                                throw new Exception("Error al subir el archivo XML");
                            }
                            debug($xmlPath);
                            $formDataJson['archivo_xml'] = str_replace('/opt/lampp/htdocs', '', $xmlPath);
                        }
                    } catch (Exception $e) {
                        // Manejar la excepción
                        echo "Error: " . $e->getMessage();
                        // Puedes agregar más código para manejar la excepción
                    }
                }

                
                $editformDataJson = CleanJson($formDataJson);
                debug($editformDataJson);
                //$formDataJson = insertarDespuesDeClave($formDataJson, 'marca', ['fecha_creacion'=>date('Y-m-d H:i:s')]);
                $newformDataJson = $formDataJson;
                $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                $newformDataJson['kid_estatus'] = 1;
                $consultaselect = "SELECT fc.id_factura_cliente,
                c.nombre,
                p.proyecto,
                cb.cuenta_bancaria,
                monto_total,
                monto_neto,
                CASE
                    WHEN fc.archivo_pdf IS NOT NULL THEN 
                        CONCAT(
                            '<div class=\"btn-group\" role=\"group\" style=\"width:100%;\">',
                            '<button class=\"ViewDocument btn btn-primary\" href=\"', fc.archivo_pdf, '\"><i class=\"bi bi-file-earmark-fill\"></i> Ver</button>',
                            '<button class=\"DownloadDocument btn btn-secondary\" href=\"', fc.archivo_pdf, '\"><i class=\"bi bi-file-earmark-arrow-down-fill\"></i> Descargar</button>',
                            '</div>'
                        )
                    ELSE 'Sin Archivo'
                END AS archivo_pdf,
                CASE
                    WHEN fc.archivo_xml IS NOT NULL THEN 
                        CONCAT(
                            '<div class=\"btn-group\" role=\"group\" style=\"width:100%;\">',
                            '<button class=\"ViewDocument btn btn-primary\" href=\"', fc.archivo_xml, '\"><i class=\"bi bi-file-earmark-fill\"></i> Ver</button>',
                            '<button class=\"DownloadDocument btn btn-secondary\" href=\"', fc.archivo_xml, '\"><i class=\"bi bi-file-earmark-arrow-down-fill\"></i> Descargar</button>',
                            '</div>'
                        )
                    ELSE 'Sin Archivo'
                END AS archivo_xml,
                fecha_factura
                FROM facturas_clientes fc
                LEFT JOIN proyectos p ON fc.kid_proyecto = p.id_proyecto 
                LEFT JOIN clientes c ON fc.kid_cliente = c.id_cliente 
                LEFT JOIN cuentas_bancarias cb ON fc.kid_cuenta_bancaria = cb.id_cuenta_bancaria
                WHERE fc.kid_estatus !=3 AND ".$idcolumn." = :".$idcolumn;

                $ColumnsCheck = [];
                break;

            case 'monedas':
                $tabla = 'monedas';
                $idcolumn= "id_moneda";

                $editformDataJson = $formDataJson;
                //$formDataJson = insertarDespuesDeClave($formDataJson, 'marca', ['fecha_creacion'=>date('Y-m-d H:i:s')]);
                $newformDataJson = $formDataJson;
                $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                $newformDataJson['kid_estatus'] = 1;
                $consultaselect = "SELECT
                id_moneda,
                orden,
                moneda,
                simbolo,
                codigo,
                CASE 
                    WHEN pordefecto = 1 THEN 'SÍ' 
                    ELSE 'NO' 
                END,
                fecha_creacion
                FROM $tabla WHERE kid_estatus !=3 AND ".$idcolumn." = :".$idcolumn;
                $ColumnsCheck = [
                    ['column'=>"moneda","check_similar"=>true]
                ];
                break;

            case 'tipos_reportes_cb':
                $tabla = 'tipos_reportes_cb';
                $idcolumn= "id_tipo_reporte_cb";

                $editformDataJson = $formDataJson;
                //$formDataJson = insertarDespuesDeClave($formDataJson, 'marca', ['fecha_creacion'=>date('Y-m-d H:i:s')]);
                $newformDataJson = $formDataJson;
                $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                $newformDataJson['kid_estatus'] = 1;
                $consultaselect = "SELECT
                id_tipo_reporte_cb,
                orden,
                tipo_reporte_cb,
                CASE 
                    WHEN pordefecto = 1 THEN 'SÍ' 
                    ELSE 'NO' 
                END,
                fecha_creacion
                FROM tipos_reportes_cb WHERE kid_estatus !=3 AND ".$idcolumn." = :".$idcolumn;
                $ColumnsCheck = [
                    ['column'=>"tipo_reporte_cb","check_similar"=>true]
                ];
                break;

            case 'observaciones_reportes_cb':
                $tabla = 'observaciones_reportes_cb';
                $idcolumn= "id_observacion_reporte_cb";

                $editformDataJson = $formDataJson;
                //$formDataJson = insertarDespuesDeClave($formDataJson, 'marca', ['fecha_creacion'=>date('Y-m-d H:i:s')]);
                $newformDataJson = $formDataJson;
                $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                $newformDataJson['kid_estatus'] = 1;
                $consultaselect = "SELECT
                id_observacion_reporte_cb,
                kid_reporte_cuenta_bancaria,
                observacion,
                fecha_creacion
                FROM observaciones_reportes_cb WHERE kid_estatus !=3 AND ".$idcolumn." = :".$idcolumn;
                $ColumnsCheck = [];
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
                        try {
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
                        } catch (PDOException $e) {
                                switch($e->getCode()){
                                    case '45000':
                                        $default_error = ['status' => 'error', 'message' => 'Ya existe una cuenta maestra asignada.'];
                                        break;
                                    default:
                                    $default_error = ['status' => 'error', 'message' => 'Error al ejecutar la consulta: ' . $e->getMessage()];
                                        break;
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
                            try {
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
                            } catch (PDOException $e) {
                                switch($e->getCode()){
                                    case '45000':
                                        $default_error = ['status' => 'error', 'message' => 'Ya existe una cuenta maestra asignada.'];
                                        break;
                                    default:
                                    $default_error = ['status' => 'error', 'message' => 'Error al ejecutar la consulta: ' . $e->getMessage()];
                                        break;
                                }
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
                print json_encode($default_error, JSON_UNESCAPED_UNICODE);
            }
        }

    }else{
        print json_encode(['status' => 'error', 'message' => 'Faltan datos requeridos'], JSON_UNESCAPED_UNICODE);
    } 

} else {
    print json_encode(['status' => 'error', 'message' => 'Método no permitido'], JSON_UNESCAPED_UNICODE);
}
?>