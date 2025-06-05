<?php

// Sanitizar la entrada del pathResult
$resultado = processRequest();

if($resultado){
    $pathResult = $resultado['pathResult'];
    $queryParams = $resultado['queryParams'];
    $hash_id = isset($queryParams['id']) ? $queryParams['id'] : null;
    $id = $hash_id ? decodificar($hash_id) : null;


    // Función para cargar la vista correspondiente

    // Controlador de vistas
    $vista = '';
    $data = [];

    $data_script['botones_acciones'] = [
        '<button class="ModalDataView btn btn-primary primary" modalCRUD="${modalCRUD}"><i class="bi bi-eye"></i> Ver</button>',
        '<button class="ModalDataEdit btn btn-warning warning" modalCRUD="${modalCRUD}"><i class="bi bi-pencil"></i> Editar</button>',
        '<button class="ModalDataDelete btn btn-danger danger" modalCRUD="${modalCRUD}"><i class="bi bi-trash"></i> Eliminar</button>'
    ];


    $objeto = new Conexion();
    $conexion = $objeto->Conectar();
    $data['data_show']['nombre_modulo'] = 'Compras';
    $data['data_show']['breadcrumb'] = null;
    $data['data_show']['AllowADDButton'] = true;


    switch ($pathResult) {
       
        case 'recepciones_produccion':
            $perms = [
                "crear_recepciones_prod",
                "editar_recepciones_prod",
                "ver_recepciones_prod",
                "eliminar_recepciones_prod"
            ];

            checkPerms($perms);
            $acciones = ['ver_', 'editar_', 'eliminar_'];
            foreach ($acciones as $index => $accion) {
                if (!checkPerms(preg_grep("/$accion/", $perms), true)) {
                    unset($data_script['botones_acciones'][$index]);
                }
            }

            $vista = 'recepciones_produccion';

            // Extract id_orden_compra from the URL path
            $pathParts = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
            $idOrdenCompra = end($pathParts);
            if (is_numeric($idOrdenCompra)) {
                $data['data_show']['id_orden_compra'] = $idOrdenCompra;
            } else {
                $idOrdenCompra = null;
            }

            $estatus = GetEstatusLabels();
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
            WHERE rc.kid_estatus !=3";

            if ($idOrdenCompra) {
                $consultaselect .= " AND rc.kid_orden_compras = :idOrdenCompra";
            }

            $resultado = $conexion->prepare($consultaselect);
            if ($idOrdenCompra) {
                $resultado->bindParam(':idOrdenCompra', $idOrdenCompra, PDO::PARAM_INT);
            }
            $resultado->execute();

            $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
            $data['data_show']['colaboradores'] = GetUsuariosListForSelect();
            $data['data_show']['almacenes'] = GetAlmacenesListForSelect();

            $modalCRUD = 'detalles_recepciones_compras';
            $nuevo_boton = '
                <button class="ModalNewAdd3 btn btn-info info" modalCRUD="'.$modalCRUD.'"><i class="bi bi-file-spreadsheet"></i> Ver Detalles</button>';
            array_push($data_script['botones_acciones'], $nuevo_boton);
            $data['data_show']['botones_acciones'] = $data_script['botones_acciones'];
            $optionkey = 'NewAdd3';
            $data_script[$optionkey] = ['data_list_column' => []];

            break;
             case 'pesaje_produccion':
            $perms = [
                "crear_pesaje_produccion",
                "editar_pesaje_produccion",
                "ver_pesaje_produccion",
                "eliminar_pesaje_produccion"
            ];

            checkPerms($perms);
            $acciones = ['ver_', 'editar_', 'eliminar_'];
            foreach ($acciones as $index => $accion) {
                if (!checkPerms(preg_grep("/$accion/", $perms), true)) {
                    unset($data_script['botones_acciones'][$index]);
                }
            }

            $vista = 'pesaje_produccion';

            // Extract id_orden_compra from the URL path
            $pathParts = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
            $idOrdenCompra = end($pathParts);
            if (is_numeric($idOrdenCompra)) {
                $data['data_show']['id_orden_compra'] = $idOrdenCompra;
            } else {
                $idOrdenCompra = null;
            }

            $estatus = GetEstatusLabels();
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
            WHERE rc.kid_estatus !=3";

            if ($idOrdenCompra) {
                $consultaselect .= " AND rc.kid_orden_compras = :idOrdenCompra";
            }

            $resultado = $conexion->prepare($consultaselect);
            if ($idOrdenCompra) {
                $resultado->bindParam(':idOrdenCompra', $idOrdenCompra, PDO::PARAM_INT);
            }
            $resultado->execute();

            $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
            $data['data_show']['colaboradores'] = GetUsuariosListForSelect();
            $data['data_show']['almacenes'] = GetAlmacenesListForSelect();

            $modalCRUD = 'detalles_recepciones_compras';
            $nuevo_boton = '
                <button class="ModalNewAdd3 btn btn-info info" modalCRUD="'.$modalCRUD.'"><i class="bi bi-file-spreadsheet"></i> Ver Detalles</button>';
            array_push($data_script['botones_acciones'], $nuevo_boton);
            $data['data_show']['botones_acciones'] = $data_script['botones_acciones'];
            $optionkey = 'NewAdd3';
            $data_script[$optionkey] = ['data_list_column' => []];
            $data['list_js_scripts']['../vistas/almacen_produccion/pesaje_produccion_script'] = ['data' => $data_script];
            break;
        case 'detalles_recepciones_prod':
            $perms = [
                "crear_detalles_recepciones_prod",
                    "editar_detalles_recepciones_prod",
                    "ver_detalles_recepciones_prod",
                    "eliminar_detalles_recepciones_prod"
                   ];
        
                    checkPerms($perms);
                    $acciones = ['ver_', 'editar_', 'eliminar_'];
                    foreach ($acciones as $index => $accion) {
                        if (!checkPerms(preg_grep("/$accion/", $perms), true)) {
                            unset($data_script['botones_acciones'][$index]);
                        }
                    }
            $vista = 'detalles_recepciones_prod';
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
            WHERE drc.kid_estatus  !=3";
            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();

            $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
            $data['data_show']['cotizaciones'] = GetCotizacionesListForSelect();
            $data['data_show']['articulos'] = GetArticulosListForSelect();
            $data['data_show']['tipo_comentario'] = GetTiposComentariosListForSelect();
            $modalCRUD = 'comentarios_recepciones';
            $nuevo_boton = '
                <button class="ModalNewAdd1 btn btn-secondary secondary" modalCRUD="'.$modalCRUD.'"><i class="bi bi-chat-left-text"></i> Comentario</button>
            ';
            array_splice($data_script['botones_acciones'], 1, 0, $nuevo_boton);
            $data['data_show']['botones_acciones'] = $data_script['botones_acciones'];
            
            $data_script['NewAdd1'] =['data_list_column'=>[
                'kid_recepcion_compras-NewAdd1'=>2,
                'kid_detalle_recepcion_compras'=>0
                
            ]];
            break;


              case 'producto_terminado':
            $perms = [
                    "crear_producto_terminado",
                    "editar_producto_terminado",
                    "ver_producto_terminado",
                    "eliminar_producto_terminado"
                   ];
        
                    checkPerms($perms);
                    $acciones = ['ver_', 'editar_', 'eliminar_'];
                    foreach ($acciones as $index => $accion) {
                        if (!checkPerms(preg_grep("/$accion/", $perms), true)) {
                            unset($data_script['botones_acciones'][$index]);
                        }
                    }
            $vista = 'producto_terminado';
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
            WHERE drc.kid_estatus  !=3";
            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();

            $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
            $data['data_show']['cotizaciones'] = GetCotizacionesListForSelect();
            $data['data_show']['articulos'] = GetArticulosListForSelect();
            $data['data_show']['tipo_comentario'] = GetTiposComentariosListForSelect();
            $modalCRUD = 'comentarios_recepciones';
            $nuevo_boton = '
                <button class="ModalNewAdd1 btn btn-secondary secondary" modalCRUD="'.$modalCRUD.'"><i class="bi bi-chat-left-text"></i> Comentario</button>
            ';
            array_splice($data_script['botones_acciones'], 1, 0, $nuevo_boton);
            $data['data_show']['botones_acciones'] = $data_script['botones_acciones'];
            
            $data_script['NewAdd1'] =['data_list_column'=>[
                'kid_recepcion_compras-NewAdd1'=>2,
                'kid_detalle_recepcion_compras'=>0
                
            ]];
            break;
        case 'comentarios_recepciones_prod':
            $perms = [
                "crear_comentarios_recepciones_prod",
                    "editar_comentarios_recepciones_prod",
                    "ver_comentarios_recepciones_prod",
                    "eliminar_comentarios_recepciones_prod"
                   ];
        
                    checkPerms($perms);
                    $acciones = ['ver_', 'editar_', 'eliminar_'];
                    foreach ($acciones as $index => $accion) {
                        if (!checkPerms(preg_grep("/$accion/", $perms), true)) {
                            unset($data_script['botones_acciones'][$index]);
                        }
                    }
            $vista = 'comentarios_recepciones_prod';
            $consultaselect = "SELECT cr.id_comentario_recepcion, 
                rc.recepcion_compras AS kid_recepcion_compras, 
                ar.articulo,
                cr.comentario_recepcion_compras,
                tc.tipo_comentario AS kid_tipo_comentario,
                cr.fecha_creacion
            FROM 
                comentarios_recepciones cr
            LEFT JOIN 
                recepciones_compras rc ON cr.kid_recepcion_compras = rc.id_recepcion_compras 
            LEFT JOIN 
                detalles_recepciones_compras drc ON cr.kid_detalle_recepcion_compras = drc.id_detalle_recepcion_compras 
            LEFT JOIN 
                articulos ar ON drc.kid_articulo = ar.id_articulo
            LEFT JOIN 
                tipos_comentarios tc ON cr.kid_tipo_comentario = tc.id_tipo_comentario
            WHERE cr.kid_estatus !=3";
            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();

            $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
            $data['data_show']['tipo_comentario'] = GetTiposComentariosListForSelect();
            break;

   case 'mermas_prod':
                $perms = [
                        "crear_mermas_prod",
                        "editar_mermas_prod",
                        "ver_mermas_prod",
                        "eliminar_mermas_prod"
                   ];
        
                    checkPerms($perms);
                    $acciones = ['ver_', 'editar_', 'eliminar_'];
                    foreach ($acciones as $index => $accion) {
                        if (!checkPerms(preg_grep("/$accion/", $perms), true)) {
                            unset($data_script['botones_acciones'][$index]);
                        }
                    }
                $vista = 'mermas_prod';
                $consultaselect = "SELECT m.id_merma,
                    p.id_produccion AS kid_produccion,
                    a.articulo AS kid_articulo,
                    m.tipo_merma,
                    m.titulo,
                    m.descripcion,
                    m.cantidad,
                    c.email AS kid_creacion,
                    m.fecha_creacion
                FROM mermas m
                LEFT JOIN produccion p ON m.kid_produccion = p.id_produccion
                LEFT JOIN articulos a ON m.kid_articulo = a.id_articulo
                LEFT JOIN colaboradores c ON m.kid_creacion = c.id_colaborador
                WHERE m.kid_estatus != 3";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->execute();
            
                $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
                $data['data_show']['producciones'] = GetProduccionesListForSelect();
                $data['data_show']['articulos'] = GetArticulosListForSelect();
                $data['data_show']['botones_acciones'] = $data_script['botones_acciones'];
            $optionkey = 'NewAdd3';
                $data_script[$optionkey] = ['data_list_column' => []];
                $data['list_js_scripts']['../vistas/almacen_produccion/mermasp_script'] =['data'=> $data_script];
               
                break;

                case 'produccion' :
            $perms = [
                "crear_produccion",
                "editar_produccion",
                "ver_produccion",
                "eliminar_produccion"
            ];
            checkPerms($perms);
            $acciones = ['ver_', 'editar_', 'eliminar_'];
            foreach ($acciones as $index => $accion) {
                if (!checkPerms(preg_grep("/$accion/", $perms), true)) {
                    unset($data_script['botones_acciones'][$index]);
                }
            }
            $vista = 'produccion';
            /*$consultaselect = "SELECT p.id_produccion,
                p.produccion,
                p.codigo_externo,
                (SELECT proyecto FROM proyectos pr WHERE pr.id_proyecto = p.kid_proyecto LIMIT 1) AS kid_proyecto,
                (SELECT almacen FROM almacenes alm WHERE alm.id_almacen = p.kid_almacen LIMIT 1) AS kid_almacen,
                (SELECT orden_compras FROM ordenes_compras oc WHERE oc.id_orden_compras = p.kid_orden_compras LIMIT 1) AS kid_orden_compras,
                (SELECT email FROM colaboradores c WHERE c.id_colaborador = p.kid_creacion LIMIT 1) AS kid_creacion,
                p.fecha_creacion
            FROM produccion p
            WHERE p.kid_estatus != 3";
            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();
            
            $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
            $data['data_show']['colaboradores'] = GetUsuariosListForSelect();
            $data['data_show']['almacenes'] = GetAlmacenesListForSelect();
            $modalCRUD = 'detalles_produccion';
            $nuevo_boton = '
                <button class="ModalNewAdd3 btn btn-info info" modalCRUD="'.$modalCRUD.'"><i class="bi bi-file-spreadsheet"></i> Ver Detalles</button>
            ';
            array_push($data_script['botones_acciones'], $nuevo_boton);
            $data['data_show']['botones_acciones'] = $data_script['botones_acciones'];
            $optionkey = 'NewAdd3';*/
            $data_script[$optionkey] = ['data_list_column' => []];
            break;

        default:
            $vista = '404'; // Vista de error 404 si no se encuentra la ruta
            break;
    }

    $data['list_js_scripts']['formularios_script'] =$data_script;

    renderView($vista, $data);
}else{
    header("Location: /index.php");
}


?>