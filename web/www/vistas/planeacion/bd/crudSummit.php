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

        switch ($modalCRUD) {
            case 'clientes':
                $tabla = 'clientes';
                $idcolumn= "id_cliente";

                /*-------------------- Obtener Tablas Foráneas --------------------*/
                $estados = GetEstadosListById();
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
                $consultaselect = "SELECT id_marca, orden, marca, CASE WHEN pordefecto = 1 THEN 'Activado' ELSE 'Desactivado' END AS pordefecto, fecha_creacion 
                    FROM ".$tabla." WHERE ".$idcolumn." = :".$idcolumn;

                $consultaselect = "SELECT id_cliente , 
                                        codigo, 
                                        nombre,
                                        razon_social,
                                        rfc,
                                        email,
                                        fecha_creacion
                                    FROM ".$tabla." WHERE ".$idcolumn." = :".$idcolumn;

                $fuc_mapping = function ($row) {
                    global $data_script, $estatus;
                    $botones_acciones = $data_script['botones_acciones'];
                    $hashed_id = codificar($row['id_cliente']);
                    array_push($botones_acciones, '<a href="/rutas/planeacion.php/planeaciones_actividades?id=' . $hashed_id . '" class="btn btn-info "><i class="bi bi-file-spreadsheet"></i> Actividades</a>');
                    array_push($botones_acciones, '<a href="/rutas/planeacion.php/planeaciones_recursos_humanos?id=' . $hashed_id . '" class="btn btn-info "><i class="bi bi-file-spreadsheet"></i> TH</a>');
                    array_push($botones_acciones, '<a href="/rutas/planeacion.php/planeaciones_compras?id=' . $hashed_id . '" class="btn btn-info "><i class="bi bi-file-spreadsheet"></i> Compras</a>');
                    array_push($botones_acciones, '<a href="/rutas/contabilidad.php/facturas_clientes?id=' . $hashed_id . '" class="btn btn-secondary "><i class="bi bi-receipt"></i> Facturas</a>');
                    $row['botones'] = GenerateCustomsButtons($botones_acciones, 'clientes');
                    return $row;
                };


                $ColumnsCheck = [
                    ['column'=>"codigo","check_similar"=>false],
                    ['column'=>"razon_social","check_similar"=>false],
                    ['column'=>"rfc","check_similar"=>false]
                ];
                $text_colums_edit = [];

                
                break;
            case 'planeaciones_compras':

                
                $tabla = 'planeaciones_compras';
                $idcolumn= "id_planeacion_compras";

                /*-------------------- Obtener Tablas Foráneas --------------------*/
                $formDataJson['kid_bolsa_proyecto'] = isset($formDataJson['kid_bolsa_proyecto']) ? GetIDBolsaProyectosByName($formDataJson['kid_bolsa_proyecto']) : null;
                $formDataJson['kid_proyecto'] = isset($formDataJson['kid_proyecto']) ? GetIDProyectoByName($formDataJson['kid_proyecto']) : null;
                $formDataJson['kid_cliente'] = isset($formDataJson['kid_cliente']) ? GetIDClienteByName($formDataJson['kid_cliente']) : null;
                /*------------------- Fin Obtener Tablas Foráneas ------------------*/

                $editformDataJson = CleanJson($formDataJson);
                

                $newformDataJson = $formDataJson;
                $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                $newformDataJson['kid_estatus'] = 1;
                $caseEstatus = "CASE \n";
                foreach ($estatus as $key => $value) {
                    $caseEstatus .= "    WHEN pc.kid_estatus = $key THEN '$value'\n";
                }
                $caseEstatus .= "    ELSE 'Desconocido' \nEND AS kid_estatus";
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
                WHERE pc.kid_estatus != 3 and ".$idcolumn." = :".$idcolumn;

                $fuc_mapping = function ($row) {
                    global $data_script;
                    $botones_acciones = $data_script['botones_acciones'];
                    $hashed_id = codificar($row['id_planeacion_compras']);
                    array_push($botones_acciones, '<a href="/rutas/planeacion.php/detalles_planeaciones_compras?id=' . $hashed_id . '" class="btn btn-secondary "><i class="bi bi-journal-text"></i> Contenido</a>');
                    $row['botones'] = GenerateCustomsButtons($botones_acciones, 'planeaciones_compras');
                    return $row;
                };


                $ColumnsCheck = [];

                $text_colums_edit = [
                    'kid_bolsa_proyecto' => "Se cambio la bolsa de proyecto para el registro #id_editado del valor: #valor_anterior a #nuevo_valor.",
                    'kid_proyecto' => "Se cambio el proyecto para el registro #id_editado del valor: #valor_anterior a #nuevo_valor.",
                    'kid_cliente' => "Se cambio el cliente para el registro #id_editado del valor: #valor_anterior a #nuevo_valor.",
                    'costo_total_almacen' => "Cambio en el valor del Costo Total en Almacén del valor: #valor_anterior a #nuevo_valor.",
                    'costo_total_a_comprar' => "Cambio en el valor del Costo Total a Comprar del valor: #valor_anterior a #nuevo_valor.",
                    'monto_total' => "Cambio en el valor del Monto Total a Comprar del valor: #valor_anterior a #nuevo_valor.",
                    'registros_almacen' => "Cambio en el valor de Registros en Almacén a Comprar del valor: #valor_anterior a #nuevo_valor.",
                    'registros_a_comprar' => "Cambio en el valor de Registros a Comprar del valor: #valor_anterior a #nuevo_valor.",
                    'registros_total' => "Cambio en el valor de Registros Totales del valor: #valor_anterior a #nuevo_valor."
                    
                ];
                break;

            case 'detalles_planeaciones_compras':
                $tabla = 'detalles_planeaciones_compras';
                $idcolumn= "id_detalle_planeacion_compras";

                /*-------------------- Obtener Tablas Foráneas --------------------*/
                $formDataJson['kid_articulo'] = isset($formDataJson['kid_articulo']) ? GetIDArticuloByName($formDataJson['kid_articulo']) : null;
                /*------------------- Fin Obtener Tablas Foráneas ------------------*/

                $editformDataJson = CleanJson($formDataJson);
                

                $newformDataJson = $formDataJson;
                $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                $newformDataJson['kid_estatus'] = 1;
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
                WHERE dpc.kid_estatus != 3 and ".$idcolumn." = :".$idcolumn;

                $ColumnsCheck = [];


                $text_colums_edit = [
                    'kid_planeacion_compras' => "Se cambio la planeación de compra en el contenido de planeación #id_editado del valor: #valor_anterior a #nuevo_valor.",
                    'kid_articulo' => "Se cambio el articulo en el contenido de planeación #id_editado del valor: #valor_anterior a #nuevo_valor.",
                    'cantidad_solicitada' => "Se cambio la cantidad solicitada en el contenido de planeación #id_editado del valor: #valor_anterior a #nuevo_valor.",
                    'cantidad_en_almacen' => "Se cambio la cantidad en almacén en el contenido de planeación #id_editado del valor: #valor_anterior a #nuevo_valor.",
                    'cantidad_a_comprar' => "Se cambio la cantidad a comprar en el contenido de planeación #id_editado del valor: #valor_anterior a #nuevo_valor.",
                    'costo_total_almacen' => "Se cambio el costo total de almacén en el contenido de planeación #id_editado del valor: #valor_anterior a #nuevo_valor.",
                    'costo_unitario_a_comprar' => "Se cambio el costo unitario a comprar en el contenido de planeación #id_editado del valor: #valor_anterior a #nuevo_valor.",
                    'costo_total_a_comprar' => "Se cambio el costo total a comprar en el contenido de planeación #id_editado del valor: #valor_anterior a #nuevo_valor.",
                    'monto_total' => "Se cambio el monto total en el contenido de planeación #id_editado del valor: #valor_anterior a #nuevo_valor.",
                ];

                break;

            case 'tablas':
                $tabla = 'tablas';
                $idcolumn= "id_tabla";
                /*-------------------- Obtener Tablas Foráneas --------------------*/
                $formDataJson['kid_modulo'] = isset($formDataJson['kid_modulo']) ? GetIDModulosByName($formDataJson['kid_modulo']) : null;
                /*------------------- Fin Obtener Tablas Foráneas ------------------*/
                $editformDataJson = CleanJson($formDataJson);
                $newformDataJson = $formDataJson;
                $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                $newformDataJson['kid_estatus'] = 1;
                $consultaselect = "SELECT t.id_tabla,
                t.tablas,
                m.modulo as kid_modulo,
                t.descripcion,
                CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) as kid_creacion,
                t.fecha_creacion
                FROM tablas t 
                LEFT JOIN colaboradores u ON t.kid_creacion = u.id_colaborador
                LEFT JOIN modulos m ON t.kid_modulo = m.id_modulo
                WHERE t.kid_estatus != 3 and ".$idcolumn." = :".$idcolumn;

                $ColumnsCheck = [
                    ['column'=>"tablas","check_similar"=>true]
                ];

                $text_colums_edit = [
                    'tablas' => "Se cambio el nombre de la tabla #id_editado del valor: #valor_anterior a #nuevo_valor.",
                    'kid_modulo' => "Se cambio el modulo de la tabla #id_editado del valor: #valor_anterior a #nuevo_valor.",
                    'descripcion' => "Se cambio la descripción de la tabla #id_editado del valor: #valor_anterior a #nuevo_valor.",
                ];
                break;

            case 'modulos':
                $tabla = 'modulos';
                $idcolumn= "id_modulo";
                $editformDataJson = CleanJson($formDataJson);
                $newformDataJson = $formDataJson;
                $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                $newformDataJson['kid_estatus'] = 1;
                $consultaselect = "SELECT m.id_modulo,
                m.modulo,
                m.descripcion,
                CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) as kid_creacion,
                m.fecha_creacion
                FROM modulos m 
                LEFT JOIN colaboradores u ON m.kid_creacion = u.id_colaborador
                WHERE m.kid_estatus != 3 and ".$idcolumn." = :".$idcolumn;

                $ColumnsCheck = [
                    ['column'=>"modulo","check_similar"=>true]
                ];

                $text_colums_edit = [
                    'modulo' => "Se cambio el nombre del modulo #id_editado del valor: #valor_anterior a #nuevo_valor.",
                    'descripcion' => "Se cambio la descripción del modulo #id_editado del valor: #valor_anterior a #nuevo_valor.",
                ];
                break;

            case 'planeaciones_recursos_humanos':
                $tabla = 'planeaciones_rrhh';
                $idcolumn= "id_planeacion_rrhh";

                /*-------------------- Obtener Tablas Foráneas --------------------*/
                $formDataJson['kid_bolsa_proyecto'] = isset($formDataJson['kid_bolsa_proyecto']) ? GetIDBolsaProyectosByName($formDataJson['kid_bolsa_proyecto']) : null;
                $formDataJson['kid_proyecto'] = isset($formDataJson['kid_proyecto']) ? GetIDProyectoByName($formDataJson['kid_proyecto']) : null;
                $formDataJson['kid_cliente'] = isset($formDataJson['kid_cliente']) ? GetIDClienteByName($formDataJson['kid_cliente']) : null;
                /*------------------- Fin Obtener Tablas Foráneas ------------------*/

                $editformDataJson = CleanJson($formDataJson);
                

                $newformDataJson = $formDataJson;
                $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                $newformDataJson['kid_estatus'] = 1;
                $caseEstatus = "CASE \n";
                foreach ($estatus as $key => $value) {
                    $caseEstatus .= "    WHEN prh.kid_estatus = $key THEN '$value'\n";
                }
                $caseEstatus .= "    ELSE 'Desconocido' \nEND AS kid_estatus";
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
                WHERE prh.kid_estatus != 3 and ".$idcolumn." = :".$idcolumn;

                $fuc_mapping = function ($row) {
                    global $data_script;
                    $botones_acciones = $data_script['botones_acciones'];
                    $hashed_id = codificar($row['id_planeacion_rrhh']);
                    array_push($botones_acciones, '<a href="/rutas/planeacion.php/detalles_planeaciones_recursos_humanos?id=' . $hashed_id . '" class="btn btn-secondary "><i class="bi bi-journal-text"></i> Contenido</a>');
                    $row['botones'] = GenerateCustomsButtons($botones_acciones, 'planeaciones_recursos_humanos');
                    return $row;
                    return $row;
                };



                $ColumnsCheck = [];

                $text_colums_edit = [
                    'kid_bolsa_proyecto' => "Se cambio la bolsa de proyecto para la planeación de recursos humanos #id_editado del valor: #valor_anterior a #nuevo_valor.",
                    'kid_proyecto' => "Se cambio el proyecto para la planeación de recursos humanos #id_editado del valor: #valor_anterior a #nuevo_valor.",
                    'kid_cliente' => "Se cambio el cliente para la planeación de recursos humanos #id_editado del valor: #valor_anterior a #nuevo_valor.",
                    'cantidad_internos' => "Cambio la cantidad de internos del valor: #valor_anterior a #nuevo_valor.",
                    'costo_cantidad_internos' => "Cambio el costo por cantidad de internos del valor: #valor_anterior a #nuevo_valor.",
                    'cantidad_externos' => "Cambio la cantidad de esternos del valor: #valor_anterior a #nuevo_valor.",
                    'costo_cantidad_externos' => "Cambio el costo por cantidad de externos del valor: #valor_anterior a #nuevo_valor.",
                    'monto_total' => "Cambio el monto total del valor: #valor_anterior a #nuevo_valor."
                    
                ];
                break;


            case 'detalles_planeaciones_rrhh':
                $tabla = 'detalles_planeaciones_rrhh';
                $idcolumn= "id_detalle_planeaciones_rrhh";

                /*-------------------- Obtener Tablas Foráneas --------------------*/
                $formDataJson['kid_personal'] = isset($formDataJson['kid_personal']) ? GetIDUsuariosByName($formDataJson['kid_personal']) : null;
                $formDataJson['kid_tipo_cantidad'] = isset($formDataJson['kid_tipo_cantidad']) ? GetIDTiposCostosByName($formDataJson['kid_tipo_cantidad']) : null;
                $formDataJson['kid_interno_externo'] = isset($formDataJson['kid_interno_externo']) ? GetIDInternosExternosByName($formDataJson['kid_interno_externo']) : null;
                /*------------------- Fin Obtener Tablas Foráneas ------------------*/

                $editformDataJson = CleanJson($formDataJson);
                

                $newformDataJson = $formDataJson;
                $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                $newformDataJson['kid_estatus'] = 1;
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
                WHERE dprh.kid_estatus != 3 and ".$idcolumn." = :".$idcolumn;

                $ColumnsCheck = [];
                
                $text_colums_edit = [
                    'kid_planeaciones_rrhh' => "Se cambio la planeación de recursos humanos para el registro #id_editado del valor: #valor_anterior a #nuevo_valor.",
                    'kid_personal' => "Se cambio el persoanl en el contenido de planeación de recursos humanos #id_editado del valor: #valor_anterior a #nuevo_valor.",
                    'costo' => "Se cambio el costo del contenido de planeación de recursos humanos #id_editado del valor: #valor_anterior a #nuevo_valor.",
                    'cantidad' => "Se cambio la cantidad en el contenido de planeación de recursos humanos #id_editado del valor: #valor_anterior a #nuevo_valor.",
                    'kid_tipo_cantidad' => "Se cambio el tipo de costo del contenido planeación de recursos humanos #id_editado del valor: #valor_anterior a #nuevo_valor.",
                    'kid_interno_externo' => "Se cambio la modalidad del personal en el contenido de planeación de recursos humanos #id_editado del valor: #valor_anterior a #nuevo_valor.",
                    'costo_total' => "Se cambio el costo totalen el contenido de planeación de recursos humanos #id_editado del valor: #valor_anterior a #nuevo_valor.",
                ];

                break;

            case 'internos_externos':
                $tabla = 'internos_externos';
                $idcolumn= "id_internos_externos";
                $editformDataJson = CleanJson($formDataJson);
                $newformDataJson = $formDataJson;
                $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                $newformDataJson['kid_estatus'] = 1;
                $consultaselect = "SELECT id_internos_externos,
                    orden,
                    internos_externos,
                    CASE 
                    WHEN pordefecto = 1 THEN 'SÍ' 
                    ELSE 'NO' 
                    END AS pordefecto,
                    fecha_creacion
                FROM $tabla 
                WHERE kid_estatus != 3 and ".$idcolumn." = :".$idcolumn;

                $ColumnsCheck = [
                    ['column'=>"internos_externos","check_similar"=>true]
                ];

                $text_colums_edit = [
                    'internos_externos' => "Se cambio el nombre de la modadidad de trabajo en el registro #id_editado del valor: #valor_anterior a #nuevo_valor.",
                    'orden' => "Se cambio el numero de orden para la modalidad de trabajo #id_editado del valor: #valor_anterior a #nuevo_valor.",
                    'pordefecto' => "Se cambio el estado por defecto para la modadidad de trabajo #id_editado del valor: #valor_anterior a #nuevo_valor.",
                ];


                break;

            case 'tipos_costo':
                $tabla = 'tipos_costo';
                $idcolumn= "id_tipo_costo";
                $editformDataJson = CleanJson($formDataJson);
                $newformDataJson = $formDataJson;
                $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                $newformDataJson['kid_estatus'] = 1;
                $consultaselect = "SELECT id_tipo_costo,
                    orden,
                    tipo_costo,
                    CASE 
                    WHEN pordefecto = 1 THEN 'SÍ' 
                    ELSE 'NO' 
                    END AS pordefecto,
                    fecha_creacion
                FROM $tabla 
                WHERE kid_estatus != 3 and ".$idcolumn." = :".$idcolumn;

                $ColumnsCheck = [
                    ['column'=>"tipo_costo","check_similar"=>true]
                ];

                $text_colums_edit = [
                    'tipo_costo' => "Se cambio el nombre del tipo de costo en el registro #id_editado del valor: #valor_anterior a #nuevo_valor.",
                    'orden' => "Se cambio el numero de orden para el tipo de costo #id_editado del valor: #valor_anterior a #nuevo_valor.",
                    'pordefecto' => "Se cambio el estado por defecto ppara el tipo de costo #id_editado del valor: #valor_anterior a #nuevo_valor.",
                ];

                break;


            case 'planeaciones_actividades':
                $tabla = 'planeaciones_actividades';
                $idcolumn= "id_planeacion_actividad";

                /*-------------------- Obtener Tablas Foráneas --------------------*/
                $formDataJson['kid_bolsa_proyecto'] = isset($formDataJson['kid_bolsa_proyecto']) ? GetIDBolsaProyectosByName($formDataJson['kid_bolsa_proyecto']) : null;
                $formDataJson['kid_proyecto'] = isset($formDataJson['kid_proyecto']) ? GetIDProyectoByName($formDataJson['kid_proyecto']) : null;
                $formDataJson['kid_cliente'] = isset($formDataJson['kid_cliente']) ? GetIDClienteByName($formDataJson['kid_cliente']) : null;
                /*------------------- Fin Obtener Tablas Foráneas ------------------*/

                $editformDataJson = CleanJson($formDataJson);
                

                $newformDataJson = $formDataJson;
                $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                $newformDataJson['kid_estatus'] = 1;
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
                WHERE pa.kid_estatus != 3 and ".$idcolumn." = :".$idcolumn;

                $fuc_mapping = function ($row) {
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
                };

                $ColumnsCheck = [];

                $text_colums_edit = [
                    'kid_bolsa_proyecto' => "Se cambio la bolsa de proyecto para el registro #id_editado del valor: #valor_anterior a #nuevo_valor.",
                    'kid_proyecto' => "Se cambio el proyecto para el registro #id_editado del valor: #valor_anterior a #nuevo_valor.",
                    'kid_cliente' => "Se cambio el cliente para el registro #id_editado del valor: #valor_anterior a #nuevo_valor.",
                    'cantidad_actividades' => "Cambio el numero de actividades para la planeación de actividades #id_editado del valor: #valor_anterior a #nuevo_valor.",
                    'fecha_inicial' => "Cambio la fecha de inicio para la planeación de actividades #id_editado del valor: #valor_anterior a #nuevo_valor.",
                    'fecha_final' => "Cambio la fecha de fin para la planeación de actividades #id_editado del valor: #valor_anterior a #nuevo_valor.",
                    'dias_totales' => "Cambio el valor de dias totales para la planeación de actividades #id_editado del valor: #valor_anterior a #nuevo_valor.",
                    'cantidad_rrhh' => "Cambio la cantidad de recursos humanos para la planeación de actividades #id_editado del valor: #valor_anterior a #nuevo_valor.",
                    'kid_estatus' => "Cambio el estado de la planeación de actividades #id_editado al nuevo valor: #nuevo_valor."
                ];
                break;

            case 'detalles_planeaciones_actividades':
                $tabla = 'detalles_planeaciones_actividades';
                $idcolumn= "id_detalle_planeacion_actividad";

                /*-------------------- Obtener Tablas Foráneas --------------------*/
                $formDataJson['kid_tipo_actividad'] = isset($formDataJson['kid_tipo_actividad']) ? GetTipoActividadByName($formDataJson['kid_tipo_actividad']) : null;
                /*------------------- Fin Obtener Tablas Foráneas ------------------*/

            $editformDataJson = CleanJson($formDataJson);
                

                $newformDataJson = $formDataJson;
                $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                $newformDataJson['kid_estatus'] = 1;
                $consultaselect = "SELECT dpa.id_detalle_planeacion_actividad, 
                    dpa.kid_planeacion_actividad,
                    dpa.actividad,
                    dpa.fecha_inicial,
                    dpa.fecha_final,
                    dpa.dias_totales,
                    dpa.fecha_creacion
                FROM detalles_planeaciones_actividades dpa WHERE dpa.kid_estatus != 3 and ".$idcolumn." = :".$idcolumn;

                $ColumnsCheck = [];
                $text_colums_edit = [
                    'kid_planeacion_actividad' => "Se cambio la planeación de actividades para el regsitro #id_editado del valor: #valor_anterior a #nuevo_valor.",
                    'actividad' => "Se cambio la actividad en el contenido de planeación de actividades #id_editado del valor: #valor_anterior a #nuevo_valor.",
                    'fecha_inicial' => "Cambio la fecha de inicio para el contenido de planeación de actividades #id_editado del valor: #valor_anterior a #nuevo_valor.",
                    'fecha_final' => "Cambio la fecha de fin para para el contenido de planeación de actividades #id_editado del valor: #valor_anterior a #nuevo_valor.",
                    'dias_totales' => "Cambio el valor de dias totales para el contenido de planeación de actividades #id_editado del valor: #valor_anterior a #nuevo_valor.",
                    'cantidad_rrhh' => "Cambio la cantidad de recursos humanos para el contenido de planeación de actividades #id_editado del valor: #valor_anterior a #nuevo_valor.",
                ];
                break;

            case 'check_planeaciones_actividades':
                if (isset($_POST['firstColumnValue']) && is_numeric($_POST['firstColumnValue'])) {
                    $id = $_POST['firstColumnValue'];
                    $statusMap = [
                        'revisar' => 6,
                        'autorizar' => 5
                    ];
                    if (isset($formDataJson['UpdateEstatus']) && array_key_exists($formDataJson['UpdateEstatus'], $statusMap)) {
                        

                        $select_data = "SELECT * FROM planeaciones_actividades WHERE id_planeacion_actividad = :id";
                        $resultado = $conexion->prepare($select_data);
                        $resultado->bindParam(':id', $id);
                        $resultado->execute();
                        $data_selected = $resultado->fetch();
                        if($data_selected){
                            $select_data_detalle = "SELECT * FROM detalles_planeaciones_actividades WHERE kid_planeacion_actividad = :id";
                            $resultado = $conexion->prepare($select_data_detalle);
                            $resultado->bindParam(':id', $id);
                            $resultado->execute();
                            $data_detalle_selected = $resultado->fetchAll();
                            $consulta_insert = "INSERT INTO actividades (kid_bolsa_proyecto, kid_proyecto, kid_cliente, cantidad_actividades, fecha_inicial, fecha_final, dias_totales,actividades_pendientes, kid_creacion, fecha_creacion, kid_estatus) 
                                VALUES (:kid_bolsa_proyecto, :kid_proyecto, :kid_cliente, :cantidad_actividades, :fecha_inicial, :fecha_final, :dias_totales,:actividades_pendientes, :kid_creacion, :fecha_creacion, :kid_estatus)";
                            try {
                                // Iniciar la transacción
                                $conexion->beginTransaction();
                                // Preparar la consulta
                                $stmt = $conexion->prepare($consulta_insert);
                                $kid_estatus = 1;
                                $fecha_creacion = date('Y-m-d H:i:s');
                                $kid_creacion =  $_SESSION["s_id"];
                                
                                // Asignar valores a los parámetros
                                $stmt->bindParam(':kid_bolsa_proyecto', $data_selected['kid_bolsa_proyecto']);
                                $stmt->bindParam(':kid_proyecto', $data_selected['kid_proyecto']);
                                $stmt->bindParam(':kid_cliente', $data_selected['kid_cliente']);
                                $stmt->bindParam(':cantidad_actividades', $data_selected['cantidad_actividades']);
                                $stmt->bindParam(':fecha_inicial', $data_selected['fecha_inicial']);
                                $stmt->bindParam(':fecha_final', $data_selected['fecha_final']);
                                $stmt->bindParam(':dias_totales', $data_selected['dias_totales']);
                                $stmt->bindParam(':actividades_pendientes', $data_selected['cantidad_actividades']);
                                $stmt->bindParam(':kid_creacion', $kid_creacion);
                                $stmt->bindParam(':fecha_creacion', $fecha_creacion);
                                $stmt->bindParam(':kid_estatus', $kid_estatus);
                                
                                // Ejecutar la consulta
                                $stmt->execute();
                                
                                // Obtener el ID del registro insertado
                                $id_actividad = $conexion->lastInsertId();
                                
                                // Preparar la consulta para insertar en detalles_actividades
                                $consulta_detalle = "INSERT INTO detalles_actividades (kid_actividad, actividad, fecha_inicial, fecha_final, dias_totales, kid_creacion, fecha_creacion, kid_estatus) VALUES (:kid_actividad, :actividad, :fecha_inicial, :fecha_final, :dias_totales, :kid_creacion, :fecha_creacion, :kid_estatus)";
                                
                                
                                
                                // Iterar sobre los registros de $data_detalle_selected
                                foreach ($data_detalle_selected as $detalle) {
                                    // Preparar la consulta para insertar en detalles_actividades
                                    $stmt_detalle = $conexion->prepare($consulta_detalle);
                                    
                                    // Asignar valores a los parámetros
                                    $stmt_detalle->bindParam(':kid_actividad', $id_actividad);
                                    $stmt_detalle->bindParam(':actividad', $detalle['actividad']);
                                    $stmt_detalle->bindParam(':fecha_inicial', $detalle['fecha_inicial']);
                                    $stmt_detalle->bindParam(':fecha_final', $detalle['fecha_final']);
                                    $stmt_detalle->bindParam(':dias_totales', $detalle['dias_totales']);
                                    $stmt_detalle->bindParam(':kid_creacion', $kid_creacion);
                                    $stmt_detalle->bindParam(':fecha_creacion', $fecha_creacion);
                                    $stmt_detalle->bindParam(':kid_estatus', $kid_estatus);
                                    
                                    // Ejecutar la consulta
                                    if (!$stmt_detalle->execute()) {
                                        // Si la consulta falla, hacer un rollback
                                        $conexion->rollBack();
                                        throw new Exception("Error al insertar en la tabla detalles_actividades.");
                                    }
                                }

                                $tabla = 'planeaciones_actividades';
                                $idcolumn= "id_planeacion_actividad";
                                $opcion = 2;
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
                                WHERE pa.kid_estatus != 3 and ".$idcolumn." = :".$idcolumn;

                                $fuc_mapping = function ($row) {
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
                                };

                                $ColumnsCheck = [];

                                $text_colums_edit = [
                                    'kid_estatus' => "Cambio el estado de la planeación de actividades #id_editado al nuevo valor: #nuevo_valor."
                                ];

                                if (isset($formDataJson['UpdateEstatus'])) {
                                    $editformDataJson['kid_estatus'] = $statusMap[$formDataJson['UpdateEstatus']];
                                    $ColumnsCheck = [];
                                    unset($editformDataJson['UpdateEstatus']);
                                    $check_cambios_data = $editformDataJson;
                                    $check_cambios_data['kid_estatus'] = GetEstatusListForSelect()[$check_cambios_data['kid_estatus']]["valor"];
                                }
                                
                                
                                // Confirmar la transacción si todas las inserciones fueron exitosas
                                $conexion->commit();
                            } catch (PDOException $e) {
                                // Manejar errores
                                debug("Error al insertar el registro: " . $e->getMessage());
                            }
                            
                        }
                        
                        /*
                        $estatus = $statusMap[$formDataJson['UpdateEstatus']];
                        $consulta_update = "UPDATE planeaciones_actividades set kid_estatus = :estatus WHERE id_planeacion_actividad = :id";
                        $resultado = $conexion->prepare($consulta_update);
                        $resultado->bindParam(':estatus', $estatus);
                        $resultado->bindParam(':id', $id);
                        $resultado->execute();*/
                    }
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