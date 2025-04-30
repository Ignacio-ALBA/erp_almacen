<?php

// Sanitizar la entrada del pathResult
$resultado = processRequest();

if($resultado){
    $pathResult = $resultado['pathResult'];
    $queryParams = $resultado['queryParams'];

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
    $data['data_show']['nombre_modulo'] = 'Producción';


    switch ($pathResult) {
        case 'capturar_produccion':
            $perms = [
                "ver_capturar_produccion",
                  "editar_capturar_produccion",
                  "eliminar_capturar_produccion",
                  "crear_capturar_produccion"
               ];
    
                checkPerms($perms);
                $acciones = ['ver_', 'editar_', 'eliminar_'];
                foreach ($acciones as $index => $accion) {
                    if (!checkPerms(preg_grep("/$accion/", $perms), true)) {
                        unset($data_script['botones_acciones'][$index]);
                    }
                }
            $vista = 'capturar_produccion';
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
            WHERE p.kid_estatus != 3";
            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();
        
            $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
            $data['data_show']['articulos'] = GetArticulosListForSelect();
            $data['data_show']['almacenes'] = GetAlmacenesListForSelect();
            $data['data_show']['botones_acciones'] = $data_script['botones_acciones'];
            break;
            case 'reporte_produccion':
                $perms = [
                    "ver_reporte_produccion",
                    "editar_reporte_produccion",
                    "eliminar_reporte_produccion",
                    "crear_reporte_produccion"
                   ];
        
                    checkPerms($perms);
                    $acciones = ['ver_', 'editar_', 'eliminar_'];
                    foreach ($acciones as $index => $accion) {
                        if (!checkPerms(preg_grep("/$accion/", $perms), true)) {
                            unset($data_script['botones_acciones'][$index]);
                        }
                    }
                $vista = 'reporte_produccion';
                
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
                WHERE dp.kid_estatus != 3";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->execute();
            
                $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
                $data['data_show']['producciones'] = GetProduccionesListForSelect();
                $data['data_show']['articulos'] = GetArticulosListForSelect();
                $data['data_show']['ubicaciones'] = GetUbicacionListForSelect();
                $data['data_show']['botones_acciones'] = $data_script['botones_acciones'];
            
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