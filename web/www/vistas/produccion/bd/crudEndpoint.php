<?php
$objeto = new Conexion();
$conexion = $objeto->Conectar();

$data = []; // Inicializa la variable $data

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['modalCRUD']) && isset($_POST['firstColumnValue'])) {
        $modalCRUD = $_POST['modalCRUD'];
        $elementID = $_POST['firstColumnValue'];

        switch ($modalCRUD) {
            case 'proveedores':
                $consultaselect = "SELECT p.*,
                e.estado AS kid_estado
                FROM proveedores p
                LEFT JOIN estados e ON p.kid_estado = e.id_estados
                WHERE p.kid_estatus != 3 AND p.id_proveedor  = :id";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':id', $elementID);
                $resultado->execute();
                $data = $resultado->fetch(PDO::FETCH_ASSOC);
                $data['id_proveedor'] = null;

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'comentarios_proveedores':
                if(isset($_POST['opcion'])) {
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
                    WHERE cp.kid_estatus !=3 AND cp.kid_proveedor  = :id";
                    $resultado = $conexion->prepare($consultaselect);
                    $resultado->bindParam(':id', $elementID);
                    $resultado->execute();
                    $data['data'] = $resultado->fetchAll(PDO::FETCH_NUM);
                
                    $consultaselect = "SELECT a.articulo
                        FROM ordenes_compras oc
                        LEFT JOIN detalles_ordenes_compras doc ON oc.id_orden_compras = doc.kid_orden_compras
                        LEFT JOIN articulos a ON doc.kid_articulo = a.id_articulo
                        WHERE a.kid_estatus != 3 AND doc.kid_estatus != 3 AND oc.kid_estatus = 6
                        AND oc.kid_proyecto = (
                            SELECT cc.kid_proyecto 
                            FROM cotizaciones_compras cc 
                            WHERE doc.kid_orden_compras = :id
                        ) 
                        ORDER BY a.articulo ASC;";
                    $resultado = $conexion->prepare($consultaselect);
                    $resultado->bindParam(':id', $elementID);
                    $resultado->execute();
                    $select = $resultado->fetchAll(PDO::FETCH_ASSOC);

                    $data['options']['kid_articulo'] = array_map(fn($item) => [
                        'valor'=> $item['articulo'],
                        'pordefecto' => 0,
                    ], $select);

                }else{
                    $consultaselect = "SELECT p.nombre_comercial AS kid_proveedor, 
                        cp.comentario_proveedor,
                        tc.tipo_comentario AS kid_tipo_comentario
                    FROM 
                        comentarios_proveedores cp
                    LEFT JOIN 
                        proveedores p ON cp.kid_proveedor = p.id_proveedor
                    LEFT JOIN 
                        tipos_comentarios tc ON cp.kid_tipo_comentario = tc.id_tipo_comentario
                    WHERE cp.kid_estatus !=3 AND cp.id_comentario_proveedor  = :id";
                    $resultado = $conexion->prepare($consultaselect);
                    $resultado->bindParam(':id', $elementID);
                    $resultado->execute();
                    $data = $resultado->fetch(PDO::FETCH_ASSOC);
                    $data['id_detalle_cotizacion_compras'] = null;
                }

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;
                case 'clientes':
                    $consultaselect = "SELECT c.*,
                        e.estado AS kid_estado
                    FROM clientes c
                    LEFT JOIN estados e ON c.kid_estado = e.id_estados 
                    WHERE id_cliente = :idCliente";
    
                    $resultado = $conexion->prepare($consultaselect);
                    $resultado->bindParam(':idCliente', $elementID);
                    $resultado->execute();
                    $data = $resultado->fetch(PDO::FETCH_ASSOC);
                    $data['id_cliente']=null;
    
                    // Verifica si se encontraron datos
                    if ($data) {
                        print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                    } else {
                        print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                    }
                    break;
                    case 'mermas':
                        if (isset($_POST['opcion'])) {
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
                            WHERE m.kid_estatus != 3 AND m.kid_produccion = :id";
                            $resultado = $conexion->prepare($consultaselect);
                            $resultado->bindParam(':id', $elementID);
                            $resultado->execute();
                            $data['data'] = $resultado->fetchAll(PDO::FETCH_NUM);
                    
                            $consultaselect = "SELECT a.articulo
                                FROM articulos a
                                WHERE a.kid_estatus != 3
                                ORDER BY a.articulo ASC;";
                            $resultado = $conexion->prepare($consultaselect);
                            $resultado->execute();
                            $select = $resultado->fetchAll(PDO::FETCH_ASSOC);
                    
                            $data['options']['kid_articulo'] = array_map(fn($item) => [
                                'valor' => $item['articulo'],
                                'pordefecto' => 0,
                            ], $select);
                        } else {
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
                            WHERE m.kid_estatus != 3 AND m.id_merma = :id";
                            $resultado = $conexion->prepare($consultaselect);
                            $resultado->bindParam(':id', $elementID);
                            $resultado->execute();
                            $data = $resultado->fetch(PDO::FETCH_ASSOC);
                        }
                    
                        if ($data) {
                            print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                        } else {
                            print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                        }
                        break;
                        case 'ubicacion_almacen':
                            if (isset($_POST['opcion'])) {
                                $consultaselect = "SELECT u.id_ubicacion,
                                    a.almacen AS kid_almacen,
                                    u.codigo_localizacion,
                                    u.descripcion,
                                    c.email AS kid_creacion,
                                    u.fecha_creacion
                                FROM ubicacion_almacen u
                                LEFT JOIN almacenes a ON u.kid_almacen = a.id_almacen
                                LEFT JOIN colaboradores c ON u.kid_creacion = c.id_colaborador
                                WHERE u.kid_estatus != 3 AND u.kid_almacen = :id";
                                $resultado = $conexion->prepare($consultaselect);
                                $resultado->bindParam(':id', $elementID);
                                $resultado->execute();
                                $data['data'] = $resultado->fetchAll(PDO::FETCH_NUM);
                        
                                $consultaselect = "SELECT a.almacen
                                    FROM almacenes a
                                    WHERE a.kid_estatus != 3
                                    ORDER BY a.almacen ASC;";
                                $resultado = $conexion->prepare($consultaselect);
                                $resultado->execute();
                                $select = $resultado->fetchAll(PDO::FETCH_ASSOC);
                        
                                $data['options']['kid_almacen'] = array_map(fn($item) => [
                                    'valor' => $item['almacen'],
                                    'pordefecto' => 0,
                                ], $select);
                            } else {
                                $consultaselect = "SELECT u.id_ubicacion,
                                    a.almacen AS kid_almacen,
                                    u.codigo_localizacion,
                                    u.descripcion,
                                    c.email AS kid_creacion,
                                    u.fecha_creacion
                                FROM ubicacion_almacen u
                                LEFT JOIN almacenes a ON u.kid_almacen = a.id_almacen
                                LEFT JOIN colaboradores c ON u.kid_creacion = c.id_colaborador
                                WHERE u.kid_estatus != 3 AND u.id_ubicacion = :id";
                                $resultado = $conexion->prepare($consultaselect);
                                $resultado->bindParam(':id', $elementID);
                                $resultado->execute();
                                $data = $resultado->fetch(PDO::FETCH_ASSOC);
                            }
                        
                            if ($data) {
                                print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                            } else {
                                print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                            }
                            break;
                    case 'comentarios_clientes':
                        if(isset($_POST['opcion'])) {
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
                            WHERE cc.kid_estatus !=3 AND cc.kid_cliente  = :id";
                            $resultado = $conexion->prepare($consultaselect);
                            $resultado->bindParam(':id', $elementID);
                            $resultado->execute();
                            $data['data'] = $resultado->fetchAll(PDO::FETCH_NUM);
                        
                            $consultaselect = "SELECT a.articulo
                                FROM ordenes_compras oc
                                LEFT JOIN detalles_ordenes_compras doc ON oc.id_orden_compras = doc.kid_orden_compras
                                LEFT JOIN articulos a ON doc.kid_articulo = a.id_articulo
                                WHERE a.kid_estatus != 3 AND doc.kid_estatus != 3 AND oc.kid_estatus = 6
                                AND oc.kid_proyecto = (
                                    SELECT cc.kid_proyecto 
                                    FROM cotizaciones_compras cc 
                                    WHERE doc.kid_orden_compras = :id
                                ) 
                                ORDER BY a.articulo ASC;";
                            $resultado = $conexion->prepare($consultaselect);
                            $resultado->bindParam(':id', $elementID);
                            $resultado->execute();
                            $select = $resultado->fetchAll(PDO::FETCH_ASSOC);
        
                            $data['options']['kid_articulo'] = array_map(fn($item) => [
                                'valor'=> $item['articulo'],
                                'pordefecto' => 0,
                            ], $select);
        
                        }else{
                            $consultaselect = "SELECT c.nombre AS kid_cliente, 
                                cc.comentario_cliente,
                                tc.tipo_comentario AS kid_tipo_comentario
                            FROM 
                                comentarios_clientes cc
                            LEFT JOIN 
                                clientes c ON cc.kid_cliente = c.id_cliente
                            LEFT JOIN 
                                tipos_comentarios tc ON cc.kid_tipo_comentario = tc.id_tipo_comentario
                            WHERE cc.kid_estatus !=3 AND cc.id_comentario_cliente  = :id";
                            $resultado = $conexion->prepare($consultaselect);
                            $resultado->bindParam(':id', $elementID);
                            $resultado->execute();
                            $data = $resultado->fetch(PDO::FETCH_ASSOC);
                            $data['id_detalle_cotizacion_compras'] = null;
                        }
        
                        // Verifica si se encontraron datos
                        if ($data) {
                            print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                        } else {
                            print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                        }
                        break;
            case 'colaboradores':
                $consultaselect = "SELECT u.*,
                    tu.tipo_usuario AS kid_tipo_usuario,
                    e.estado AS kid_estado
                FROM colaboradores u
                LEFT JOIN tipos_usuario tu ON u.kid_tipo_usuario = tu.id_tipo_usuario
                LEFT JOIN estados e ON u.kid_estado = e.id_estados 
                WHERE id_colaborador = :idUsuario";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':idUsuario', $elementID);
                $resultado->execute();
                $data = $resultado->fetch(PDO::FETCH_ASSOC);
                $data['id_colaborador']=null;

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;
            
            case 'marcas':
                $consultaselect = "SELECT id_marca, orden, marca,pordefecto, fecha_creacion FROM marcas WHERE id_marca = :id_marca";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':id_marca', $elementID);
                $resultado->execute();
                $data = $resultado->fetch(PDO::FETCH_ASSOC);

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;
            case 'categorias':
                $consultaselect = "SELECT id_categoria , orden, categoria, fecha_creacion FROM categorias WHERE id_categoria = :id_marca";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':id_marca', $elementID);
                $resultado->execute();
                $data = $resultado->fetch(PDO::FETCH_ASSOC);

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;
            case 'subcategorias':
                $consultaselect = "SELECT s.id_subcategoria, 
                          s.orden, 
                          s.subcategoria, 
                          s.pordefecto,
                          c.categoria AS kid_categoria,  -- Cambiamos el alias a kid_categoria
                          s.fecha_creacion  
                    FROM subcategorias s
                    JOIN categorias c ON s.kid_categoria = c.id_categoria
                    WHERE s.kid_estatus = 1 AND s.id_subcategoria = :idSubcategoria";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':idSubcategoria', $elementID);
                $resultado->execute();
                $data = $resultado->fetch(PDO::FETCH_ASSOC);

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;
            
            case 'dimensiones':
                $consultaselect = "SELECT id_dimension , orden, dimension, simbolo, pordefecto,fecha_creacion FROM dimensiones WHERE id_dimension = :id_dimension";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':id_dimension', $elementID);
                $resultado->execute();
                $data = $resultado->fetch(PDO::FETCH_ASSOC);

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'presentaciones':
                $consultaselect = "SELECT s.id_presentacion , 
                            s.orden, 
                            s.presentacion, 
                            s.pordefecto,
                            c.dimension as kid_dimension,  -- Ahora esta columna está después de pordefecto
                            s.fecha_creacion
                    FROM presentaciones s
                    JOIN dimensiones c ON s.kid_dimension = c.id_dimension 
                    WHERE s.kid_estatus = 1 AND s.id_presentacion = :idPresentacion";

                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':idPresentacion', $elementID);
                $resultado->execute();
                $data = $resultado->fetch(PDO::FETCH_ASSOC);

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'unidades':
                $consultaselect = "SELECT id_unidad , orden, unidad, simbolo, pordefecto,fecha_creacion FROM unidades WHERE id_unidad = :idUnidad";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':idUnidad', $elementID);
                $resultado->execute();
                $data = $resultado->fetch(PDO::FETCH_ASSOC);

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;


            case 'presentaciones':
                $consultaselect = "SELECT s.id_presentacion , 
                            s.orden, 
                            s.presentacion, 
                            s.pordefecto,
                            c.dimension as kid_dimension,  -- Ahora esta columna está después de pordefecto
                            s.fecha_creacion
                    FROM presentaciones s
                    JOIN dimensiones c ON s.kid_dimension = c.id_dimension 
                    WHERE s.kid_estatus = 1 AND s.id_presentacion = :idPresentacion";

                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':idFormato', $elementID);
                $resultado->execute();
                $data = $resultado->fetch(PDO::FETCH_ASSOC);

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'formatos':
                //$consultaselect = "SELECT id_formato, orden, formato,pordefecto FROM formatos WHERE id_formato = :idFormato";
                $consultaselect = "SELECT 
                        f.id_formato, 
                        f.orden, 
                        f.formato, 
                        f.pordefecto,
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
                FROM formatos f
                LEFT JOIN presentaciones p ON f.kid_presentacion = p.id_presentacion 
                LEFT JOIN unidades u ON f.kid_unidad = u.id_unidad  
                LEFT JOIN dimensiones d ON f.kid_dimension = d.id_dimension  
                WHERE f.id_formato = :idFormato";

                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':idFormato', $elementID);
                $resultado->execute();
                $data = $resultado->fetch(PDO::FETCH_ASSOC);

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'articulos':
                $consultaselect = "SELECT a.id_articulo  , 
                          a.codigo_interno, 
                          a.articulo,
                          a.no_serie, 
                          a.modelo, 
                          m.marca as kid_marca,
                          c.categoria as kid_categoria,
                          s.subcategoria as kid_subcategoria,
                          a.cantidad_formato,
                          f.formato as kid_formato,
                          p.presentacion as kid_presentacion,
                          d.dimension as kid_dimension,
                          a.fecha_creacion
                    FROM articulos a
                    LEFT JOIN marcas m ON a.kid_marca = m.id_marca
                    LEFT JOIN categorias c ON a.kid_categoria = c.id_categoria
                    LEFT JOIN subcategorias s ON a.kid_subcategoria = s.id_subcategoria
                    LEFT JOIN formatos f ON a.kid_formato = f.id_formato
                    LEFT JOIN presentaciones p ON a.kid_presentacion = p.id_presentacion 
                    LEFT JOIN dimensiones d ON a.kid_dimension = d.id_dimension  -- Suponiendo que tienes una relación con la tabla dimensiones
                    WHERE a.id_articulo = :idArticulo";

                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':idArticulo', $elementID);
                $resultado->execute();
                $data = $resultado->fetch(PDO::FETCH_ASSOC);

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'estados':
                $consultaselect = "SELECT e.id_estados , 
                        e.orden, 
                        e.estado, 
                        e.simbolo, 
                        e.pordefecto,
                        p.pais AS kid_pais ,  -- Cambiamos el alias a kid_categoria
                        e.fecha_creacion  
                    FROM estados e
                    JOIN paises p ON e.kid_pais  = p.id_pais
                    WHERE e.kid_estatus = 1 AND e.id_estados  = :idEstados ";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':idEstados', $elementID);
                $resultado->execute();
                $data = $resultado->fetch(PDO::FETCH_ASSOC);

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;


            case 'municipios':
                $consultaselect = "SELECT m.id_municipio   , 
                    m.orden, 
                    m.municipio, 
                    m.pordefecto,
                    e.estado as kid_estado,
                    p.pais as pais,  -- Ahora esta columna está después de pordefecto
                    m.fecha_creacion
                FROM municipios m
                JOIN estados e ON m.kid_estado = e.id_estados
                JOIN paises p ON e.kid_pais = p.id_pais
                WHERE m.kid_estatus = 1 AND m.id_municipio   = :idMunicipio ";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':idMunicipio', $elementID);
                $resultado->execute();
                $data = $resultado->fetch(PDO::FETCH_ASSOC);

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'empresas':
                /*$consultaselect = "SELECT id_estado FROM 
                        estados WHERE kid_estatus = 1 AND id_empresa   = :idEmpresa ";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':idEmpresa', $elementID);
                $resultado->execute();
                $data = $resultado->fetch(PDO::FETCH_ASSOC);*/


                $consultaselect = "SELECT e.*,
                        u.email as kid_propietario,
                        u2.email as kid_representante_legal,
                        u3.email as kid_representante_tecnico,
                        u4.email as kid_representante_administrativo,
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
                    WHERE e.kid_estatus = 1 AND e.id_empresa   = :idEmpresa ";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':idEmpresa', $elementID);
                $resultado->execute();
                $data = $resultado->fetch(PDO::FETCH_ASSOC);

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'sucursales':
                /*$consultaselect = "SELECT id_estado FROM 
                        estados WHERE kid_estatus = 1 AND id_sucursal   = :idSucursal ";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':idEmpresa', $elementID);
                $resultado->execute();
                $data = $resultado->fetch(PDO::FETCH_ASSOC);*/


                $consultaselect = "SELECT s.*,
                            u.email as kid_propietario,
                            u2.email as kid_representante_legal,
                            u3.email as kid_representante_tecnico,
                            u4.email as kid_representante_administrativo,
                            e.estado as kid_estado
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
                        LEFT JOIN
                            estados e ON s.kid_estado = e.id_estados 
                        WHERE s.kid_estatus = 1 AND s.id_sucursal   = :idSucursal ";
                    $resultado = $conexion->prepare($consultaselect);
                    $resultado->bindParam(':idSucursal', $elementID);
                    $resultado->execute();
                    $data = $resultado->fetch(PDO::FETCH_ASSOC);

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'almacenes':
                $consultaselect = "SELECT a.*, 
                            s.sucursal AS kid_sucursal,
                            u.email as kid_encargado
                        FROM 
                            almacenes a
                        LEFT JOIN 
                            sucursales s ON a.kid_sucursal = s.id_sucursal
                        LEFT JOIN 
                            colaboradores u ON a.kid_encargado = u.id_colaborador
                        WHERE a.kid_estatus = 1 AND a.id_almacen   = :idAlmacen ";
                    $resultado = $conexion->prepare($consultaselect);
                    $resultado->bindParam(':idAlmacen', $elementID);
                    $resultado->execute();
                    $data = $resultado->fetch(PDO::FETCH_ASSOC);
                    $data['id_almacen'] = null;

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;
                case 'capturar_produccion':
                    if (isset($_POST['opcion'])) {
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
                        WHERE p.kid_estatus != 3 AND p.kid_almacen = :id";
                        $resultado = $conexion->prepare($consultaselect);
                        $resultado->bindParam(':id', $elementID);
                        $resultado->execute();
                        $data['data'] = $resultado->fetchAll(PDO::FETCH_NUM);
                
                        $consultaselect = "SELECT a.articulo
                            FROM articulos a
                            WHERE a.kid_estatus != 3
                            ORDER BY a.articulo ASC;";
                        $resultado = $conexion->prepare($consultaselect);
                        $resultado->execute();
                        $select = $resultado->fetchAll(PDO::FETCH_ASSOC);
                
                        $data['options']['kid_articulo'] = array_map(fn($item) => [
                            'valor' => $item['articulo'],
                            'pordefecto' => 0,
                        ], $select);
                
                        $consultaselect = "SELECT al.almacen
                            FROM almacenes al
                            WHERE al.kid_estatus != 3
                            ORDER BY al.almacen ASC;";
                        $resultado = $conexion->prepare($consultaselect);
                        $resultado->execute();
                        $select = $resultado->fetchAll(PDO::FETCH_ASSOC);
                
                        $data['options']['kid_almacen'] = array_map(fn($item) => [
                            'valor' => $item['almacen'],
                            'pordefecto' => 0,
                        ], $select);
                    } else {
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
                        WHERE p.kid_estatus != 3 AND p.id_produccion = :id";
                        $resultado = $conexion->prepare($consultaselect);
                        $resultado->bindParam(':id', $elementID);
                        $resultado->execute();
                        $data = $resultado->fetch(PDO::FETCH_ASSOC);
                    }
                
                    if ($data) {
                        print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                    } else {
                        print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                    }
                    break;
                    case 'detalles_produccion':
                        if (isset($_POST['opcion'])) {
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
                            WHERE dp.kid_estatus != 3 AND dp.kid_produccion = :id";
                            $resultado = $conexion->prepare($consultaselect);
                            $resultado->bindParam(':id', $elementID);
                            $resultado->execute();
                            $data['data'] = $resultado->fetchAll(PDO::FETCH_NUM);
                    
                            $consultaselect = "SELECT a.articulo
                                FROM articulos a
                                WHERE a.kid_estatus != 3
                                ORDER BY a.articulo ASC;";
                            $resultado = $conexion->prepare($consultaselect);
                            $resultado->execute();
                            $select = $resultado->fetchAll(PDO::FETCH_ASSOC);
                    
                            $data['options']['kid_articulo'] = array_map(fn($item) => [
                                'valor' => $item['articulo'],
                                'pordefecto' => 0,
                            ], $select);
                    
                            $consultaselect = "SELECT u.codigo_localizacion
                                FROM ubicacion_almacen u
                                WHERE u.kid_estatus != 3
                                ORDER BY u.codigo_localizacion ASC;";
                            $resultado = $conexion->prepare($consultaselect);
                            $resultado->execute();
                            $select = $resultado->fetchAll(PDO::FETCH_ASSOC);
                    
                            $data['options']['kid_ubicacion'] = array_map(fn($item) => [
                                'valor' => $item['codigo_localizacion'],
                                'pordefecto' => 0,
                            ], $select);
                        } else {
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
                            WHERE dp.kid_estatus != 3 AND dp.id_detalle_produccion = :id";
                            $resultado = $conexion->prepare($consultaselect);
                            $resultado->bindParam(':id', $elementID);
                            $resultado->execute();
                            $data = $resultado->fetch(PDO::FETCH_ASSOC);
                        }
                    
                        if ($data) {
                            print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                        } else {
                            print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                        }
                        break;
            case 'detalles_almacenes':
                $consultaselect = "SELECT ad.*, 
                            a.almacen AS kid_almacen, 
                            ar.articulo AS kid_articulo
                        FROM 
                            detalles_almacenes ad
                        LEFT JOIN 
                            articulos ar ON ad.kid_articulo = ar.id_articulo
                        LEFT JOIN 
                            almacenes a ON ad.kid_almacen = a.id_almacen
                        WHERE ad.kid_estatus = 1 AND ad.id_detalle_almacen   = :idDetalleAlmacen ";
                    $resultado = $conexion->prepare($consultaselect);
                    $resultado->bindParam(':idDetalleAlmacen', $elementID);
                    $resultado->execute();
                    $data = $resultado->fetch(PDO::FETCH_ASSOC);
                    $data['id_detalle_almacen'] = null;

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'comentarios_almacenes':
                $consultaselect = "SELECT ca.id_comentario_almacen, 
                                a.almacen AS kid_almacen, 
                                ca.kid_detalle_almacen,
                                ca.comentario_almacen,
                                tc.tipo_comentario AS kid_tipo_comentario,
                                ca.fecha_creacion
                        FROM 
                            comentarios_almacenes ca
                        LEFT JOIN 
                            almacenes a ON ca.kid_almacen = a.id_almacen
                        LEFT JOIN 
                            detalles_almacenes da ON ca.kid_detalle_almacen = da.id_detalle_almacen
                        LEFT JOIN 
                            tipos_comentarios tc ON ca.kid_tipo_comentario = tc.id_tipo_comentario 
                        WHERE ca.kid_estatus = 1 AND ca.id_comentario_almacen   = :idComentarioAlmacen ";
                    $resultado = $conexion->prepare($consultaselect);
                    $resultado->bindParam(':idComentarioAlmacen', $elementID);
                    $resultado->execute();
                    $data = $resultado->fetch(PDO::FETCH_ASSOC);
                    $data['id_comentario_almacen'] = null;

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'tipos_comentarios':
                $consultaselect = "SELECT id_tipo_comentario, 
                                        orden,
                                        tipo_comentario,
                                        pordefecto,
                                        fecha_creacion
                                    FROM 
                                        tipos_comentarios
                                    WHERE kid_estatus = 1 AND id_tipo_comentario   = :idTipoComentario ";
                    $resultado = $conexion->prepare($consultaselect);
                    $resultado->bindParam(':idTipoComentario', $elementID);
                    $resultado->execute();
                    $data = $resultado->fetch(PDO::FETCH_ASSOC);
                    $data['id_tipo_comentario'] = null;

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'estatus':
                $consultaselect = "SELECT estatus,
                    estatus_color
                FROM estatus
                WHERE kid_estatus = 1 AND id_estatus   = :id ";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':id', $elementID);
                $resultado->execute();
                $data = $resultado->fetch(PDO::FETCH_ASSOC);
                $data['id_tipo_comentario'] = null;

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;

            //Otros EndPoints
            case 'GETMunicipios':
                /*-------------------- Obtener Tablas Foráneas --------------------*/
                $consultaselect = "SELECT id_estados FROM estados WHERE estado = :Estado";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':Estado', $elementID);
                $resultado->execute();
                $data = $resultado->fetch(PDO::FETCH_ASSOC);
                $elementID = $data['id_estados'];
                /*------------------- Fin Obtener Tablas Foráneas ------------------*/

                $consultaselect = "SELECT m.municipio, 
                        m.pordefecto, 
                        e.simbolo as simbolo
                    FROM 
                        municipios m
                    LEFT JOIN 
                        estados e ON m.kid_estado = e.id_estados 
                    WHERE m.kid_estatus = 1 AND m.kid_estado = :idEstado 
                    ORDER BY m.pordefecto DESC, m.orden ASC, m.municipio ASC";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':idEstado', $elementID);
                $resultado->execute();
                $data = $resultado->fetchAll(PDO::FETCH_ASSOC);

                $data = array_map(fn($item) => [
                    'valor'=> $item['municipio'],
                    'text' => trim(implode('-', array_filter([$item['municipio'], $item['simbolo']]))),
                    'pordefecto' => $item['pordefecto'],
                ], $data);

                // Verifica si se encontraron datos
                if ($data) {
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'GETColonia':
                $consultaselect = "SELECT colonia FROM colonias WHERE kid_estatus = 1 AND cp = :CP";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->bindParam(':CP', $elementID);
                $resultado->execute();
                $data = $resultado->fetch(PDO::FETCH_ASSOC);
                
                //$data = $item['cp'];
                // Verifica si se encontraron datos
                if ($data) {
                    $respuesta['nombre_colonia'] = $data['colonia'];
                    $data = $respuesta;
                    print json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
                } else {
                    print json_encode(['status' => 'error', 'message' => 'No se encontraron datos'], JSON_UNESCAPED_UNICODE);
                }
                break;

            default:
                print json_encode(['status' => 'error', 'message' => 'Operación no válida'], JSON_UNESCAPED_UNICODE);
                break;
                
        }
    } else {
        print json_encode(['status' => 'error', 'message' => 'Faltan datos requeridos'], JSON_UNESCAPED_UNICODE);
    }
} else {
    print json_encode(['status' => 'error', 'message' => 'Método no permitido'], JSON_UNESCAPED_UNICODE);
}
?>