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
            case 'proveedores':
                $tabla = 'proveedores';
                $idcolumn= "id_proveedor";

                /*-------------------- Obtener Tablas Foráneas --------------------*/
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
                    case 'comentarios_clientes':
                        $tabla = 'comentarios_clientes';
                        $idcolumn= "id_comentario_cliente";
        
                        /*-------------------- Obtener Tablas Foráneas --------------------*/
                        //$colaboradores = GetUsuariosListById();
                        $formDataJson['kid_cliente'] = isset($formDataJson['kid_cliente']) ? GetIDClienteByName($formDataJson['kid_cliente']) : null;
                        $formDataJson['kid_tipo_comentario'] = isset($formDataJson['kid_tipo_comentario']) ? GetIDTipoComentarioByName($formDataJson['kid_tipo_comentario']) : null;  
                        /*------------------- Fin Obtener Tablas Foráneas ------------------*/
        
        
                        $editformDataJson = CleanJson($formDataJson);
                        //$formDataJson = insertarDespuesDeClave($formDataJson, 'marca', ['fecha_creacion'=>date('Y-m-d H:i:s')]);
                        $newformDataJson = $formDataJson;
                        $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                        $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                        $newformDataJson['kid_estatus'] = 1;
        
                        $consultaselect = "SELECT cc.id_comentario_cliente, 
                            c.nombre AS kid_cliente, 
                            cc.comentario_cliente,
                            tc.tipo_comentario AS kid_tipo_comentario,
                            cc.fecha_creacion
                        FROM 
                            comentarios_clientes cc
                        LEFT JOIN 
                            clientes c ON cc.kid_cliente = c.id_cliente
                        LEFT JOIN 
                            tipos_comentarios tc ON cc.kid_tipo_comentario = tc.id_tipo_comentario
                        WHERE cc.kid_estatus !=3 AND $idcolumn = :$idcolumn";
        
                        $ColumnsCheck = [];
                        break;
            case 'marcas':
                $tabla = 'marcas';
                $idcolumn= "id_marca";
                $editformDataJson = $formDataJson;
                //$formDataJson = insertarDespuesDeClave($formDataJson, 'marca', ['fecha_creacion'=>date('Y-m-d H:i:s')]);
                $newformDataJson = $formDataJson;
                $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                $newformDataJson['kid_estatus'] = 1;
                $consultaselect = "SELECT id_marca, orden, marca, CASE WHEN pordefecto = 1 THEN 'Activado' ELSE 'Desactivado' END AS pordefecto, fecha_creacion 
                    FROM ".$tabla." WHERE ".$idcolumn." = :".$idcolumn;
                /*$ColumnsCheck = [
                    ['column'=>"codigo_interno","check_similar"=>false],
                    ['column'=>"codigo_externo","check_similar"=>false],
                    ['column'=>"articulo","check_similar"=>true]
                ];*/
                $ColumnsCheck = [
                    ['column'=>"marca","check_similar"=>true]
                ];

                
                break;
            case 'categorias':
                $tabla = 'categorias';
                $idcolumn= "id_categoria";
                $editformDataJson = $formDataJson;
                //$formDataJson = insertarDespuesDeClave($formDataJson, 'marca', ['fecha_creacion'=>date('Y-m-d H:i:s')]);
                $newformDataJson = $formDataJson;
                $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                $newformDataJson['kid_estatus'] = 1;
                $consultaselect = "SELECT id_categoria , orden, categoria, CASE WHEN pordefecto = 1 THEN 'Activado' ELSE 'Desactivado' END AS pordefecto, fecha_creacion 
                    FROM ".$tabla." WHERE ".$idcolumn." = :".$idcolumn;

                $ColumnsCheck = [
                    ['column'=>"categoria","check_similar"=>true]
                ];

                break;

            case 'subcategorias':
                $tabla = 'subcategorias';
                $idcolumn= "id_subcategoria";

                /*-------------------- Obtener Tablas Foráneas --------------------*/
                $categorias = GetCategoriasListById();
                /*------------------- Fin Obtener Tablas Foráneas ------------------*/

                if (!empty($formDataJson['kid_categoria']) && isset($categorias[$formDataJson['kid_categoria']])) {
                $formDataJson['kid_categoria'] = $categorias[$formDataJson['kid_categoria']];
                }
                $editformDataJson = $formDataJson;
                //$formDataJson = insertarDespuesDeClave($formDataJson, 'marca', ['fecha_creacion'=>date('Y-m-d H:i:s')]);
                $newformDataJson = $formDataJson;
                $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                $newformDataJson['kid_estatus'] = 1;
                $consultaselect = "SELECT 
                                        s.id_subcategoria, 
                                        s.orden, 
                                        s.subcategoria, 
                                        CASE 
                                            WHEN s.pordefecto = 1 THEN 'Activado' 
                                            ELSE 'Desactivado' 
                                        END AS pordefecto,
                                        c.categoria AS kid_categoria, 
                                        s.fecha_creacion 
                                    FROM $tabla s 
                                    JOIN categorias c ON s.kid_categoria = c.id_categoria 
                                    WHERE $idcolumn = :$idcolumn";

                $ColumnsCheck = [
                    ['column'=>"subcategoria","check_similar"=>true]
                ];

                break;
            case 'dimensiones':
                $tabla = 'dimensiones';
                $idcolumn= "id_dimension";
                $editformDataJson = $formDataJson;
                //$formDataJson = insertarDespuesDeClave($formDataJson, 'marca', ['fecha_creacion'=>date('Y-m-d H:i:s')]);
                $newformDataJson = $formDataJson;
                $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                $newformDataJson['kid_estatus'] = 1;
                $consultaselect = "SELECT id_dimension , orden, dimension, simbolo, CASE WHEN pordefecto = 1 THEN 'Activado' ELSE 'Desactivado' END AS pordefecto, fecha_creacion
                    FROM ".$tabla." WHERE ".$idcolumn." = :".$idcolumn;
                
                $ColumnsCheck = [
                    ['column'=>"dimension","check_similar"=>true]
                ];

                break;

            case 'presentaciones':
                $tabla = 'presentaciones';
                $idcolumn= "id_presentacion";
                
                /*-------------------- Obtener Tablas Foráneas --------------------*/
                $dimensiones = GetDimensionesListById();
                /*------------------- Fin Obtener Tablas Foráneas ------------------*/
                if (!empty($formDataJson['kid_dimension']) && isset($dimensiones[$formDataJson['kid_dimension']])) {
                    $formDataJson['kid_dimension'] = $dimensiones[$formDataJson['kid_dimension']];
                }
                $editformDataJson = $formDataJson;
                //$formDataJson = insertarDespuesDeClave($formDataJson, 'marca', ['fecha_creacion'=>date('Y-m-d H:i:s')]);
                $newformDataJson = $formDataJson;
                $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                $newformDataJson['kid_estatus'] = 1;

                $consultaselect = "SELECT s.id_presentacion , 
                                            s.orden, 
                                            s.presentacion, 
                                            CASE 
                                                WHEN s.pordefecto = 1 THEN 'Activado' 
                                                ELSE 'Desactivado' 
                                            END AS pordefecto,
                                            c.dimension as kid_dimension,  -- Ahora esta columna está después de pordefecto
                                            s.fecha_creacion
                                            FROM $tabla s
                                            JOIN dimensiones c ON s.kid_dimension = c.id_dimension 
                                            WHERE $idcolumn = :$idcolumn";
                
                $ColumnsCheck = [
                    ['column'=>"presentacion","check_similar"=>true]
                ];

                break;

            case 'unidades':
                $tabla = 'unidades';
                $idcolumn= "id_unidad";
                $editformDataJson = $formDataJson;
                //$formDataJson = insertarDespuesDeClave($formDataJson, 'marca', ['fecha_creacion'=>date('Y-m-d H:i:s')]);
                $newformDataJson = $formDataJson;
                $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                $newformDataJson['kid_estatus'] = 1;
                $consultaselect = "SELECT id_unidad , orden, unidad, simbolo, CASE WHEN pordefecto = 1 THEN 'Activado' ELSE 'Desactivado' END AS pordefecto, fecha_creacion
                    FROM ".$tabla." WHERE ".$idcolumn." = :".$idcolumn;

                $ColumnsCheck = [
                    ['column'=>"unidad","check_similar"=>true]
                ];
                
                break;

            case 'formatos':
                $tabla = 'formatos';
                $idcolumn= "id_formato";
                /*-------------------- Obtener Tablas Foráneas --------------------*/
                $presentaciones = GetPresentacionesListById();
                $unidad = GetUnidadListById();
                $dimensiones = GetDimensionesListById();
                /*------------------- Fin Obtener Tablas Foráneas ------------------*/
                if (!empty($formDataJson['kid_presentacion']) && isset($presentaciones[$formDataJson['kid_presentacion']])) {
                    $formDataJson['kid_presentacion'] = $presentaciones[$formDataJson['kid_presentacion']];
                }

                if (!empty($formDataJson['kid_unidad']) && isset($unidad[$formDataJson['kid_unidad']])) {
                    $formDataJson['kid_unidad'] = $unidad[$formDataJson['kid_unidad']];
                }

                if (!empty($formDataJson['kid_dimension']) && isset($dimensiones[$formDataJson['kid_dimension']])) {
                    $formDataJson['kid_dimension'] = $dimensiones[$formDataJson['kid_dimension']];
                }


                $editformDataJson = $formDataJson;
                //$formDataJson = insertarDespuesDeClave($formDataJson, 'marca', ['fecha_creacion'=>date('Y-m-d H:i:s')]);
                $newformDataJson = $formDataJson;
                $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                $newformDataJson['kid_estatus'] = 1;
                $consultaselect = "SELECT f.id_formato, 
                        f.orden, 
                        f.formato, 
                        CASE 
                            WHEN f.pordefecto = 1 THEN 'Activado' 
                            ELSE 'Desactivado' 
                        END AS formatos,
                        CASE 
                            WHEN f.kid_presentacion = -1 THEN 'Sin asignar' 
                            ELSE COALESCE(p.presentacion, 'Sin asignar') 
                        END AS kid_presentacion,
                        CASE 
                            WHEN f.kid_unidad = -1 THEN 'Sin asignar' 
                            ELSE COALESCE(u.unidad, 'Sin asignar') 
                        END AS kid_unidad,
                        CASE 
                            WHEN f.kid_dimension = -1 THEN 'Sin asignar' 
                            ELSE COALESCE(d.dimension, 'Sin asignar') 
                        END AS kid_dimension,
                        f.fecha_creacion
                FROM $tabla f
                LEFT JOIN presentaciones p ON f.kid_presentacion = p.id_presentacion 
                LEFT JOIN unidades u ON f.kid_unidad = u.id_unidad  
                LEFT JOIN dimensiones d ON f.kid_dimension = d.id_dimension  
                WHERE $idcolumn = :$idcolumn";

                $ColumnsCheck = [
                    ['column'=>"formato","check_similar"=>true]
                ];

                break;

            case 'articulos':
                $tabla = 'articulos';
                $idcolumn= "id_articulo";
                
                /*-------------------- Obtener Tablas Foráneas --------------------*/
                $marcas = GetMarcasListById();
                $categorias = GetCategoriasListById();
                $subcategorias = GetSubcategoriasListById();
                $formatos = GetFormatosListById();
                $presentaciones = GetPresentacionesListById();
                $dimensiones = GetDimensionesListById();
                /*------------------- Fin Obtener Tablas Foráneas ------------------*/

                if (!empty($formDataJson['kid_marca']) && isset($marcas[$formDataJson['kid_marca']])) {
                    $formDataJson['kid_marca'] = $marcas[$formDataJson['kid_marca']];
                }
                if (!empty($formDataJson['kid_categoria']) && isset($categorias[$formDataJson['kid_categoria']])) {
                    $formDataJson['kid_categoria'] = $categorias[$formDataJson['kid_categoria']];
                }
                if (!empty($formDataJson['kid_subcategoria']) && isset($subcategorias[$formDataJson['kid_subcategoria']])) {
                    $formDataJson['kid_subcategoria'] = $subcategorias[$formDataJson['kid_subcategoria']];
                }
                if (!empty($formDataJson['kid_formato']) && isset($formatos[$formDataJson['kid_formato']])) {
                    $formDataJson['kid_formato'] = $formatos[$formDataJson['kid_formato']];
                }
                if (!empty($formDataJson['kid_presentacion']) && isset($presentaciones[$formDataJson['kid_presentacion']])) {
                    $formDataJson['kid_presentacion'] = $presentaciones[$formDataJson['kid_presentacion']];
                }
                if (!empty($formDataJson['kid_dimension']) && isset($dimensiones[$formDataJson['kid_dimension']])) {
                    $formDataJson['kid_dimension'] = $dimensiones[$formDataJson['kid_dimension']];
                }
                
                // Asegurarse de que el campo costo esté presente y tenga un valor por defecto si no se proporciona
                if (!isset($formDataJson['costo']) || $formDataJson['costo'] === null || $formDataJson['costo'] === '') {
                    $formDataJson['costo'] = 0; // Establecer un valor predeterminado para el campo costo
                }

                $editformDataJson = $formDataJson;
                //$formDataJson = insertarDespuesDeClave($formDataJson, 'marca', ['fecha_creacion'=>date('Y-m-d H:i:s')]);
                $newformDataJson = $formDataJson;
                $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                $newformDataJson['kid_estatus'] = 1;

                
                $consultaselect = "SELECT a.id_articulo  , 
                            a.codigo_interno, 
                            a.codigo_externo,
                            a.costo, 
                            a.articulo, 
                            m.marca as kid_marca,
                            c.categoria as kid_categoria,
                            s.subcategoria as kid_subcategoria,
                            a.cantidad_formato,
                            f.formato as kid_formato,
                            p.presentacion as kid_presentacion,
                            d.dimension as kid_dimension,
                            a.fecha_creacion
                            FROM $tabla a
                            LEFT JOIN marcas m ON a.kid_marca = m.id_marca
                            LEFT JOIN categorias c ON a.kid_categoria = c.id_categoria
                            LEFT JOIN subcategorias s ON a.kid_subcategoria = s.id_subcategoria
                            LEFT JOIN formatos f ON a.kid_formato = f.id_formato
                            LEFT JOIN presentaciones p ON a.kid_presentacion = p.id_presentacion 
                            LEFT JOIN dimensiones d ON a.kid_dimension = d.id_dimension  -- Suponiendo que tienes una relación con la tabla dimensiones
                            WHERE $idcolumn = :$idcolumn";

                $ColumnsCheck = [
                    ['column'=>"codigo_interno","check_similar"=>false],
                    ['column'=>"articulo","check_similar"=>true]
                ];

                break;
            case 'estados':
                $tabla = 'estados';
                $idcolumn= "id_estados";

                /*-------------------- Obtener Tablas Foráneas --------------------*/
                $paises = GetPaisesListById();
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
                case 'detalles_produccion':
                    $tabla = 'detalle_produccion';
                    $idcolumn = "id_detalle_produccion";
                
                    /*-------------------- Obtener Tablas Foráneas --------------------*/
                    $formDataJson['kid_produccion'] = isset($formDataJson['kid_produccion']) ? GetIDProduccionByName($formDataJson['kid_produccion']) : null;
                    $formDataJson['kid_articulo'] = isset($formDataJson['kid_articulo']) ? GetIDArticuloByName($formDataJson['kid_articulo']) : null;
                    $formDataJson['kid_ubicacion'] = isset($formDataJson['kid_ubicacion']) ? GetIDUbicacionByName($formDataJson['kid_ubicacion']) : null;
                    /*------------------- Fin Obtener Tablas Foráneas ------------------*/
                
                    $editformDataJson = CleanJson($formDataJson);
                    $newformDataJson = $formDataJson;
                    $newformDataJson['fecha_creacion'] = date('Y-m-d H:i:s');
                    $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                    $newformDataJson['kid_estatus'] = 1;
                
                    $consultaselect = "SELECT dp.id_detalle_produccion,
                        p.fecha_produccion AS kid_produccion,
                        a.articulo AS kid_articulo,
                        dp.cantidad_usada,
                        u.codigo_localizacion AS kid_ubicacion,
                        c.email AS kid_creacion,
                        dp.codigo_qr,
                        dp.fecha_creacion
                    FROM detalle_produccion dp
                    LEFT JOIN produccion p ON dp.kid_produccion = p.id_produccion
                    LEFT JOIN articulos a ON dp.kid_articulo = a.id_articulo
                    LEFT JOIN ubicacion_almacen u ON dp.kid_ubicacion = u.id_ubicacion
                    LEFT JOIN colaboradores c ON dp.kid_creacion = c.id_colaborador
                    WHERE dp.kid_estatus != 3 AND $idcolumn = :$idcolumn";
                
                    $ColumnsCheck = [
                        ['column' => "cantidad_usada", "check_similar" => false],
                        ['column' => "codigo_qr", "check_similar" => true],
                    ];
                    break;
                case 'capturar_produccion':
                    $tabla = 'produccion';
                    $idcolumn = "id_produccion";
                
                    /*-------------------- Obtener Tablas Foráneas --------------------*/
                   // $formDataJson['kid_articulo'] = isset($formDataJson['kid_articulo']) ? GetIDArticuloByName($formDataJson['kid_articulo']) : null;
                    $formDataJson['kid_almacen'] = isset($formDataJson['kid_almacen']) ? GetIDAlmacenByName($formDataJson['kid_almacen']) : null;
                    /*------------------- Fin Obtener Tablas Foráneas ------------------*/
                    $formDataJson['kid_articulo'] = isset($formDataJson['kid_articulo']) ? GetIDArticuloByName($formDataJson['kid_articulo']) : null;

                    if (!$formDataJson['kid_articulo']) {
                        echo json_encode(['status' => 'error', 'message' => 'El artículo especificado no es válido.']);
                        exit;
                    }
                    $editformDataJson = CleanJson($formDataJson);
                    $newformDataJson = $formDataJson;
                    $newformDataJson['fecha_creacion'] = date('Y-m-d H:i:s');
                    $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                    $newformDataJson['kid_estatus'] = 1;
                
                    $consultaselect = "SELECT p.id_produccion,
                        p.fecha_produccion,
                        a.articulo AS kid_articulo,
                        p.cantidad_producida,
                        al.almacen AS kid_almacen,
                        c.email AS kid_creacion,
                        p.fecha_creacion
                    FROM produccion p
                    LEFT JOIN articulos a ON p.kid_articulo = a.id_articulo
                    LEFT JOIN almacenes al ON p.kid_almacen = al.id_almacen
                    LEFT JOIN colaboradores c ON p.kid_creacion = c.id_colaborador
                    WHERE p.kid_estatus != 3 AND $idcolumn = :$idcolumn";
                
                    $ColumnsCheck = [
                        ['column' => "fecha_produccion", "check_similar" => false],
                        ['column' => "cantidad_producida", "check_similar" => false],
                    ];
                    break;
                case 'ubicacion_almacen':
                    $tabla = 'ubicacion_almacen';
                    $idcolumn = "id_ubicacion";
                
                    /*-------------------- Obtener Tablas Foráneas --------------------*/
                    $formDataJson['kid_almacen'] = isset($formDataJson['kid_almacen']) ? GetIDAlmacenByName($formDataJson['kid_almacen']) : null;
                    /*------------------- Fin Obtener Tablas Foráneas ------------------*/
                
                    $editformDataJson = CleanJson($formDataJson);
                    $newformDataJson = $formDataJson;
                    $newformDataJson['fecha_creacion'] = date('Y-m-d H:i:s');
                    $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                    $newformDataJson['kid_estatus'] = 1;
                
                    $consultaselect = "SELECT u.id_ubicacion,
                        a.almacen AS kid_almacen,
                        u.codigo_localizacion,
                        u.descripcion,
                        c.email AS kid_creacion,
                        u.fecha_creacion
                    FROM ubicacion_almacen u
                    LEFT JOIN almacenes a ON u.kid_almacen = a.id_almacen
                    LEFT JOIN colaboradores c ON u.kid_creacion = c.id_colaborador
                    WHERE u.kid_estatus != 3 AND $idcolumn = :$idcolumn";
                
                    $ColumnsCheck = [
                        ['column' => "codigo_localizacion", "check_similar" => true],
                        ['column' => "descripcion", "check_similar" => false],
                    ];
                    break;
                case 'mermas':
                    $tabla = 'mermas';
                    $idcolumn = "id_merma";
                
                    /*-------------------- Obtener Tablas Foráneas --------------------*/
                    $formDataJson['kid_produccion'] = isset($formDataJson['kid_produccion']) ? GetIDProduccionByName($formDataJson['kid_produccion']) : null;
                    $formDataJson['kid_articulo'] = isset($formDataJson['kid_articulo']) ? GetIDArticuloByName($formDataJson['kid_articulo']) : null;
                    /*------------------- Fin Obtener Tablas Foráneas ------------------*/
                
                    $editformDataJson = CleanJson($formDataJson);
                    $newformDataJson = $formDataJson;
                    $newformDataJson['fecha_creacion'] = date('Y-m-d H:i:s');
                    $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                    $newformDataJson['kid_estatus'] = 1;
                
                    $consultaselect = "SELECT m.id_merma,
                        p.produccion AS kid_produccion,
                        a.articulo AS kid_articulo,
                        m.tipo_merma,
                        m.titulo,
                        m.descripcion,
                        m.cantidad,
                        u.email AS kid_creacion,
                        m.fecha_creacion
                    FROM mermas m
                    LEFT JOIN producciones p ON m.kid_produccion = p.id_produccion
                    LEFT JOIN articulos a ON m.kid_articulo = a.id_articulo
                    LEFT JOIN usuarios u ON m.kid_creacion = u.id_colaborador
                    WHERE m.kid_estatus != 3 AND $idcolumn = :$idcolumn";
                
                    $ColumnsCheck = [
                        ['column' => "titulo", "check_similar" => true],
                        ['column' => "descripcion", "check_similar" => false],
                    ];
                    break;
            case 'municipios':
                $tabla = 'municipios';
                $idcolumn= "id_municipio";

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
                $colaboradores =  GetUsuariosListById();
                /*------------------- Fin Obtener Tablas Foráneas ------------------*/

                $formDataJson['kid_propietario'] = !empty($formDataJson['kid_propietario']) && isset($colaboradores[$formDataJson['kid_propietario']]) ? $colaboradores[$formDataJson['kid_propietario']] : -1;
                $formDataJson['kid_representante_legal'] = !empty($formDataJson['kid_representante_legal']) && isset($colaboradores[$formDataJson['kid_representante_legal']]) ? $colaborres[$formDataJson['kid_representante_legal']] : -1;
                $formDataJson['kid_representante_tecnico'] = !empty($formDataJson['kid_representante_tecnico']) && isset($colaboradores[$formDataJson['kid_representante_tecnico']]) ? $colaboradores[$formDataJson['kid_representante_tecnico']] : -1;
                $formDataJson['kid_representante_administrativo'] = !empty($formDataJson['kid_representante_administrativo']) && isset($colaboradores[$formDataJson['kid_representante_administrativo']]) ? $colaboradores[$formDataJson['kid_representante_administrativo']] : -1;

                $formDataJson['telefono_contacto'] = isset($formDataJson['telefono_contacto']) && $formDataJson['telefono_contacto'] !== null && $formDataJson['telefono_contacto'] !== "" ? $formDataJson['telefono_contacto'] : null;
                $formDataJson['celular_contacto'] = isset($formDataJson['celular_contacto']) && $formDataJson['celular_contacto'] !== null && $formDataJson['celular_contacto'] !== "" ? $formDataJson['celular_contacto'] : null;
                $formDataJson['telefono_representante_legal'] = isset($formDataJson['telefono_representante_legal']) && $formDataJson['telefono_representante_legal'] !== null && $formDataJson['telefono_representante_legal'] !== "" ? $formDataJson['telefono_representante_legal'] : null;
                $formDataJson['celular_representante_legal'] = isset($formDataJson['celular_representante_legal']) && $formDataJson['celular_representante_legal'] !== null && $formDataJson['celular_representante_legal'] !== "" ? $formDataJson['celular_representante_legal'] : null;
                $formDataJson['telefono_representante_tecnico'] = isset($formDataJson['telefono_representante_tecnico']) && $formDataJson['telefono_representante_tecnico'] !== null && $formDataJson['telefono_representante_tecnico'] !== "" ? $formDataJson['telefono_representante_tecnico'] : null;
                $formDataJson['celular_representante_tecnico'] = isset($formDataJson['celular_representante_tecnico']) && $formDataJson['celular_representante_tecnico'] !== null && $formDataJson['celular_representante_tecnico'] !== "" ? $formDataJson['celular_representante_tecnico'] : null;
                $formDataJson['telefono_representante_administrativo'] = isset($formDataJson['telefono_representante_administrativo']) && $formDataJson['telefono_representante_administrativo'] !== null && $formDataJson['telefono_representante_administrativo'] !== "" ? $formDataJson['telefono_representante_administrativo'] : null;
                $formDataJson['celular_representante_administrativo'] = isset($formDataJson['celular_representante_administrativo']) && $formDataJson['celular_representante_administrativo'] !== null && $formDataJson['celular_representante_administrativo'] !== "" ? $formDataJson['celular_representante_administrativo'] : null;


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

            case 'sucursales':
                $tabla = 'sucursales';
                $idcolumn= "id_sucursal";

                /*-------------------- Obtener Tablas Foráneas --------------------*/
                $colaboradores = GetUsuariosListById();
                $estados = GetEstadosListById();
                /*------------------- Fin Obtener Tablas Foráneas ------------------*/

                $formDataJson['kid_propietario'] = !empty($formDataJson['kid_propietario']) && isset($colaboradores[$formDataJson['kid_propietario']]) ? $colaboradores[$formDataJson['kid_propietario']] : -1;
                $formDataJson['kid_estado'] = !empty($formDataJson['kid_estado']) && isset($estados[$formDataJson['kid_estado']]) ? $estados[$formDataJson['kid_estado']] : -1;
                $formDataJson['kid_representante_legal'] = !empty($formDataJson['kid_representante_legal']) && isset($colaboradores[$formDataJson['kid_representante_legal']]) ? $colaboradores[$formDataJson['kid_representante_legal']] : -1;
                $formDataJson['kid_representante_tecnico'] = !empty($formDataJson['kid_representante_tecnico']) && isset($colaboradores[$formDataJson['kid_representante_tecnico']]) ? $colaboradores[$formDataJson['kid_representante_tecnico']] : -1;
                $formDataJson['kid_representante_administrativo'] = !empty($formDataJson['kid_representante_administrativo']) && isset($colaboradores[$formDataJson['kid_representante_administrativo']]) ? $colaboradores[$formDataJson['kid_representante_administrativo']] : -1;
                /*
                $formDataJson['telefono_contacto'] = isset($formDataJson['telefono_contacto']) && $formDataJson['telefono_contacto'] !== null && $formDataJson['telefono_contacto'] !== "" ? $formDataJson['telefono_contacto'] : null;
                $formDataJson['celular_contacto'] = isset($formDataJson['celular_contacto']) && $formDataJson['celular_contacto'] !== null && $formDataJson['celular_contacto'] !== "" ? $formDataJson['celular_contacto'] : null;
                $formDataJson['telefono_representante_legal'] = isset($formDataJson['telefono_representante_legal']) && $formDataJson['telefono_representante_legal'] !== null && $formDataJson['telefono_representante_legal'] !== "" ? $formDataJson['telefono_representante_legal'] : null;
                $formDataJson['celular_representante_legal'] = isset($formDataJson['celular_representante_legal']) && $formDataJson['celular_representante_legal'] !== null && $formDataJson['celular_representante_legal'] !== "" ? $formDataJson['celular_representante_legal'] : null;
                $formDataJson['telefono_representante_tecnico'] = isset($formDataJson['telefono_representante_tecnico']) && $formDataJson['telefono_representante_tecnico'] !== null && $formDataJson['telefono_representante_tecnico'] !== "" ? $formDataJson['telefono_representante_tecnico'] : null;
                $formDataJson['celular_representante_tecnico'] = isset($formDataJson['celular_representante_tecnico']) && $formDataJson['celular_representante_tecnico'] !== null && $formDataJson['celular_representante_tecnico'] !== "" ? $formDataJson['celular_representante_tecnico'] : null;
                $formDataJson['telefono_representante_administrativo'] = isset($formDataJson['telefono_representante_administrativo']) && $formDataJson['telefono_representante_administrativo'] !== null && $formDataJson['telefono_representante_administrativo'] !== "" ? $formDataJson['telefono_representante_administrativo'] : null;
                $formDataJson['celular_representante_administrativo'] = isset($formDataJson['celular_representante_administrativo']) && $formDataJson['celular_representante_administrativo'] !== null && $formDataJson['celular_representante_administrativo'] !== "" ? $formDataJson['celular_representante_administrativo'] : null;
                $formDataJson['fecha_inicio_operaciones'] = isset($formDataJson['fecha_inicio_operaciones']) && $formDataJson['fecha_inicio_operaciones'] !== null && $formDataJson['fecha_inicio_operaciones'] !== "" ? $formDataJson['fecha_inicio_operaciones'] : null;*/

                //debug($formDataJson);

                
                $editformDataJson = $formDataJson;
                //$formDataJson = insertarDespuesDeClave($formDataJson, 'marca', ['fecha_creacion'=>date('Y-m-d H:i:s')]);
                $newformDataJson = $formDataJson;
                $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                $newformDataJson['kid_estatus'] = 1;
                $consultaselect = "SELECT s.id_sucursal , 
                            s.sucursal, 
                            s.razon_social, 
                            s.rfc, 
                            CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) AS kid_propietario,
                            CONCAT(u2.nombre, ' ', u2.apellido_paterno, ' ', u2.apellido_materno) AS kid_representante_legal,
                            CONCAT(u3.nombre, ' ', u3.apellido_paterno, ' ', u3.apellido_materno) AS kid_representante_tecnico,
                            CONCAT(u4.nombre, ' ', u4.apellido_paterno, ' ', u4.apellido_materno) AS kid_representante_administrativo,
                            s.fecha_creacion
                        FROM 
                            sucursales s
                        LEFT JOIN 
                            colaboradores u ON s.kid_propietario = u.id_colaborador 
                        LEFT JOIN 
                            colaboradores u2 ON s.kid_representante_legal = u2.id_colaborador 
                        LEFT JOIN 
                            colaboradores u3 ON s.kid_representante_tecnico = u3.id_colaborador 
                        LEFT JOIN 
                            colaboradores u4 ON s.kid_representante_administrativo = u4.id_colaborador
                        WHERE $idcolumn = :$idcolumn";

                $ColumnsCheck = [
                    ['column'=>"sucursal","check_similar"=>true],
                    ['column'=>"razon_social","check_similar"=>true],
                    ['column'=>"rfc","check_similar"=>false]
                ];
                break;

            case 'almacenes':
                $tabla = 'almacenes';
                $idcolumn= "id_almacen";

                /*-------------------- Obtener Tablas Foráneas --------------------*/
                $colaboradores = GetUsuariosListById();
                $sucursales = GetSucursalesListById();
                /*------------------- Fin Obtener Tablas Foráneas ------------------*/

                $formDataJson['kid_sucursal'] = !empty($formDataJson['kid_sucursal']) && isset($sucursales[$formDataJson['kid_sucursal']]) ? $sucursales[$formDataJson['kid_sucursal']] : -1;
                $formDataJson['kid_encargado'] = !empty($formDataJson['kid_encargado']) && isset($colaboradores[$formDataJson['kid_encargado']]) ? $colaboradores[$formDataJson['kid_encargado']] : -1;

                $editformDataJson = $formDataJson;
                //$formDataJson = insertarDespuesDeClave($formDataJson, 'marca', ['fecha_creacion'=>date('Y-m-d H:i:s')]);
                $newformDataJson = $formDataJson;
                $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                $newformDataJson['kid_estatus'] = 1;
                $consultaselect = "SELECT a.id_almacen, 
                            a.orden, 
                            a.almacen, 
                            a.ubicacion, 
                            s.sucursal AS kid_sucursal,
                            CASE 
                                WHEN a.pordefecto = 1 THEN 'SÍ' 
                                ELSE 'NO' 
                            END AS pordefecto,
                            s.fecha_creacion
                        FROM 
                            $tabla a
                        LEFT JOIN 
                            sucursales s ON a.kid_sucursal = s.id_sucursal
                        WHERE $idcolumn = :$idcolumn";

                $ColumnsCheck = [
                    ['column'=>"almacen","check_similar"=>true]
                ];
                break;

            case 'detalles_almacenes':
                $tabla = 'detalles_almacenes';
                $idcolumn= "id_detalle_almacen";

                /*-------------------- Obtener Tablas Foráneas --------------------*/
                //$colaboradores = GetUsuariosListById();
                $almacenes = GetAlmacenesListById();
                $articulos = GetArticulosListById();
                /*------------------- Fin Obtener Tablas Foráneas ------------------*/

                $formDataJson['kid_almacen'] = !empty($formDataJson['kid_almacen']) && isset($almacenes[$formDataJson['kid_almacen']]) ? $almacenes[$formDataJson['kid_almacen']] : -1;
                $formDataJson['kid_articulo'] = !empty($formDataJson['kid_articulo']) && isset($articulos[$formDataJson['kid_articulo']]) ? $articulos[$formDataJson['kid_articulo']] : -1;

                $editformDataJson = $formDataJson;
                //$formDataJson = insertarDespuesDeClave($formDataJson, 'marca', ['fecha_creacion'=>date('Y-m-d H:i:s')]);
                $newformDataJson = $formDataJson;
                $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                $newformDataJson['kid_estatus'] = 1;

                $lo_lo = CreateBadgeIcon('danger',['etiqueta'=>'Muy Baja', 'class'=>'danger']);
                $hi_hi = CreateBadgeIcon('danger',['etiqueta'=>'Muy Alta', 'class'=>'danger']);
                $lo = CreateBadgeIcon('warning',['etiqueta'=>'Baja', 'class'=>'warning']);
                $hi = CreateBadgeIcon('warning',['etiqueta'=>'Alta', 'class'=>'warning']);
                $alarmasok = CreateBadgeIcon('success',['etiqueta'=>'Normal', 'class'=>'success']);
                
                $consultaselect = "SELECT ad.id_detalle_almacen, 
                            a.almacen AS kid_almacen, 
                            ar.articulo AS kid_articulo,
                            ad.cantidad,  
                            CASE 
                                WHEN ad.cantidad < ad.lo_lo THEN '$lo_lo' 
                                WHEN ad.cantidad < ad.lo AND ad.cantidad > ad.lo_lo THEN '$lo' 
                                WHEN ad.cantidad > ad.high AND  ad.cantidad < ad.high_high THEN '$hi' 
                                WHEN ad.cantidad > ad.high_high THEN '$hi_hi' 
                                ELSE '$alarmasok' 
                            END AS alarma,
                            ad.fecha_creacion
                        FROM 
                            detalles_almacenes ad
                        LEFT JOIN 
                            articulos ar ON ad.kid_articulo = ar.id_articulo
                        LEFT JOIN 
                            almacenes a ON ad.kid_almacen = a.id_almacen
                        WHERE $idcolumn = :$idcolumn";

                $ColumnsCheck = [
                    ['column'=>"kid_articulo","check_similar"=>false]
                ];
                break;

            case 'comentarios_almacenes':
                $tabla = 'comentarios_almacenes';
                $idcolumn= "id_comentario_almacen";

                /*-------------------- Obtener Tablas Foráneas --------------------*/
                //$colaboradores = GetUsuariosListById();
                $almacenes = GetAlmacenesListById();
                $tipos_comentarios = GetTiposComentariosListById();
                /*------------------- Fin Obtener Tablas Foráneas ------------------*/

                $formDataJson['kid_tipo_comentario'] = !empty($formDataJson['kid_tipo_comentario']) && isset($tipos_comentarios[$formDataJson['kid_tipo_comentario']]) ? $tipos_comentarios[$formDataJson['kid_tipo_comentario']] : -1;
                //$formDataJson['kid_almacen'] = !empty($formDataJson['kid_almacen']) && isset($almacenes[$formDataJson['kid_almacen']]) ? $almacenes[$formDataJson['kid_almacen']] : -1;
                //$formDataJson['kid_articulo'] = !empty($formDataJson['kid_articulo']) && isset($articulos[$formDataJson['kid_articulo']]) ? $articulos[$formDataJson['kid_articulo']] : -1;

                if(isset($formDataJson['almacen']) && $formDataJson['almacen']){
                    $formDataJson['kid_almacen'] = !empty($formDataJson['almacen']) && isset($almacenes[$formDataJson['almacen']]) ? $almacenes[$formDataJson['almacen']] : -1;
                    unset($formDataJson['almacen']);
                }else{
                    $formDataJson['kid_almacen'] = !empty($formDataJson['kid_almacen']) && isset($almacenes[$formDataJson['kid_almacen']]) ? $almacenes[$formDataJson['kid_almacen']] : -1;
                }
                

                $editformDataJson = $formDataJson;
                //$formDataJson = insertarDespuesDeClave($formDataJson, 'marca', ['fecha_creacion'=>date('Y-m-d H:i:s')]);
                $newformDataJson = $formDataJson;
                $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                $newformDataJson['kid_estatus'] = 1;

                $consultaselect = "SELECT ca.id_comentario_almacen, 
                            a.almacen AS kid_almacen, 
                            ar.articulo,
                            ca.comentario_almacen,
                            tc.tipo_comentario AS kid_tipo_comentario,
                            ca.fecha_creacion
                        FROM 
                            $tabla ca
                        LEFT JOIN 
                            almacenes a ON ca.kid_almacen = a.id_almacen
                        LEFT JOIN 
                            detalles_almacenes da ON ca.kid_detalle_almacen = da.id_detalle_almacen
                        LEFT JOIN 
                            articulos ar ON da.kid_articulo = ar.id_articulo
                        LEFT JOIN 
                            tipos_comentarios tc ON ca.kid_tipo_comentario = tc.id_tipo_comentario
                        WHERE ca.$idcolumn = :$idcolumn";

                $ColumnsCheck = [
                    
                ];
                break;

            case 'tipos_comentarios':
                $tabla = 'tipos_comentarios';
                $idcolumn= "id_tipo_comentario";

                $editformDataJson = $formDataJson;
                $newformDataJson = $formDataJson;
                $newformDataJson['fecha_creacion']=date('Y-m-d H:i:s');
                $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                $newformDataJson['kid_estatus'] = 1;
                
                $consultaselect = "SELECT id_tipo_comentario, 
                            orden, 
                            tipo_comentario, 
                            CASE 
                                WHEN pordefecto = 1 THEN 'SÍ' 
                                ELSE 'NO' 
                            END AS pordefecto,
                            fecha_creacion
                        FROM $tabla
                        WHERE $idcolumn = :$idcolumn";

                $ColumnsCheck = [
                    ['column'=>"tipo_comentario","check_similar"=>true]
                ];
                break;

            case 'estatus':
                $tabla = 'estatus';
                $idcolumn = "id_estatus";
                $editformDataJson = $formDataJson;
                $newformDataJson = $formDataJson;
                $newformDataJson['fecha_creacion'] = date('Y-m-d H:i:s');
                $newformDataJson['kid_creacion'] = $_SESSION["s_id"];
                $newformDataJson['kid_estatus'] = 1;
            
                $consultaselect = "
                    SELECT 
                        id_estatus,
                        estatus,
                        CONCAT('<span class=\"badge\" style=\"background-color:', estatus_color, ';\">', estatus_color, '</span>') AS estatus_color,
                        fecha_creacion
                    FROM 
                        estatus
                    WHERE 
                        kid_estatus = 1 AND $idcolumn = :$idcolumn
                ";
            
                $ColumnsCheck = [
                    ['column' => "estatus", "check_similar" => true]
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

                        // Verificar si el registro existe antes de intentar actualizarlo
                        $consulta_check = "SELECT * FROM ".$tabla." WHERE " . $idcolumn . " = :id";
                        $resultado_check = $conexion->prepare($consulta_check);
                        $resultado_check->bindParam(':id', $id);
                        $resultado_check->execute();
                        
                        if($resultado_check->rowCount() > 0) {
                            // El registro existe, proceder con la actualización
                            $consulta = "UPDATE ".$tabla." SET kid_estatus = :kid_estatus WHERE " . $idcolumn . " = :id";
                            $resultado = $conexion->prepare($consulta);
                            $kid_estatus = 3; // Asignar el estatus de eliminado
                            $resultado->bindParam(':kid_estatus', $kid_estatus);
                            $resultado->bindParam(':id', $id);
                            
                            if ($resultado->execute()) {
                                // Verificar que se haya actualizado correctamente
                                $consulta_verify = "SELECT * FROM " . $tabla . " WHERE " . $idcolumn . " = :id AND kid_estatus = 3";
                                $resultado_verify = $conexion->prepare($consulta_verify);
                                $resultado_verify->bindParam(':id', $id);
                                $resultado_verify->execute();
                                
                                if($resultado_verify->rowCount() > 0) {
                                    // La actualización del estatus fue exitosa
                                    $data = true;
                                } else {
                                    // No se actualizó el estatus correctamente
                                    $data = false;
                                    print json_encode(['status' => 'error', 'message' => 'Error al eliminar el registro. No se pudo actualizar el estatus.'], JSON_UNESCAPED_UNICODE);
                                    exit;
                                }
                            } else {
                                $data = false;
                                print json_encode(['status' => 'error', 'message' => 'Error en la consulta de eliminación.'], JSON_UNESCAPED_UNICODE);
                                exit;
                            }
                        } else {
                            $data = false;
                            print json_encode(['status' => 'error', 'message' => 'El registro que intenta eliminar no existe.'], JSON_UNESCAPED_UNICODE);
                            exit;
                        }
                    } else {
                        print json_encode(['status' => 'error', 'message' => 'ID no válido o no especificado.'], JSON_UNESCAPED_UNICODE);
                        exit;
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