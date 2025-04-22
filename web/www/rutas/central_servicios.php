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
    $data['data_show']['nombre_modulo'] = 'Central de servicios';


    switch ($pathResult) {
        case 'central_mp':
            $perms = [
                "crear_detalles_almacenes",
                "editar_detalles_almacenes",
                "ver_detalles_almacenes",
                "eliminar_detalles_almacenes"
              ];
   
               checkPerms($perms);
               $acciones = ['ver_', 'editar_', 'eliminar_'];
               foreach ($acciones as $index => $accion) {
                   if (!checkPerms(preg_grep("/$accion/", $perms), true)) {
                       unset($data_script['botones_acciones'][$index]);
                   }
               }
            $vista = 'central_mp';
            $modalCRUD = 'comentarios_almacenes';
            $nuevo_boton = '
                <button class="ModalNewAdd1 btn btn-secondary secondary" modalCRUD="'.$modalCRUD.'"><i class="bi bi-chat-left-text"></i> Comentario</button>
            ';
            array_splice($data_script['botones_acciones'], 1, 0, $nuevo_boton);
            $data['data_show'] = $data_script;

            $data_script['NewAdd1'] =['data_list_column'=>[
                'almacen'=>1,
                'kid_detalle_almacen'=>0
            ]];
            break;

            case 'central_pedidos_mp':
                $perms = [
                    "crear_detalles_almacenes",
                    "editar_detalles_almacenes",
                    "ver_detalles_almacenes",
                    "eliminar_detalles_almacenes"
                  ];
       
                   checkPerms($perms);
                   $acciones = ['ver_', 'editar_', 'eliminar_'];
                   foreach ($acciones as $index => $accion) {
                       if (!checkPerms(preg_grep("/$accion/", $perms), true)) {
                           unset($data_script['botones_acciones'][$index]);
                       }
                   }
                $vista = 'central_pedidos_mp';
                  // Consulta para obtener los datos de la tabla `detalles_almacenes`
            $consultaselect = "SELECT 
            da.id_detalle_almacen,
            a.almacen AS kid_almacen,
            ar.articulo AS kid_articulo,
            da.cantidad,
            da.peso,
            da.lo_lo,
            da.lo,
            da.high,
            da.high_high,
            CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) AS kid_creacion,
            da.fecha_creacion,
            e.estatus AS kid_estatus
        FROM detalles_almacenes da
        LEFT JOIN almacenes a ON da.kid_almacen = a.id_almacen
        LEFT JOIN articulos ar ON da.kid_articulo = ar.id_articulo
        LEFT JOIN colaboradores u ON da.kid_creacion = u.id_colaborador
        LEFT JOIN estatus e ON da.kid_estatus = e.id_estatus
        WHERE da.kid_estatus != 3";

        $resultado = $conexion->prepare($consultaselect);
        $resultado->execute();

        $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
        $data['data_show']['almacenes'] = GetAlmacenesListForSelect();
        $data['data_show']['articulos'] = GetArticulosListForSelect();
        $data['data_show']['estatus'] = GetEstatusListForSelect();

        // Configuración de los botones de acción
        $modalCRUD = 'detalles_almacenes';
        $nuevo_boton = '<button class="ModalNewAdd3 btn btn-info info" modalCRUD="' . $modalCRUD . '"><i class="bi bi-file-spreadsheet"></i> Ver Detalles</button>';
        array_push($data_script['botones_acciones'], $nuevo_boton);
        $data['data_show']['botones_acciones'] = $data_script['botones_acciones'];
        $optionkey = 'NewAdd3';
        $data_script[$optionkey] = ['data_list_column' => []];
                break;

                case 'central_productos':
                    $perms = [
                        "crear_detalles_almacenes",
                        "editar_detalles_almacenes",
                        "ver_detalles_almacenes",
                        "eliminar_detalles_almacenes"
                      ];
           
                       checkPerms($perms);
                       $acciones = ['ver_', 'editar_', 'eliminar_'];
                       foreach ($acciones as $index => $accion) {
                           if (!checkPerms(preg_grep("/$accion/", $perms), true)) {
                               unset($data_script['botones_acciones'][$index]);
                           }
                       }
                    $vista = 'central_productos';
                      // Consulta para obtener los datos de la tabla `detalles_almacenes`
            $consultaselect = "SELECT 
            da.id_detalle_almacen,
            a.almacen AS kid_almacen,
            ar.articulo AS kid_articulo,
            da.cantidad,
            da.peso,
            da.lo_lo,
            da.lo,
            da.high,
            da.high_high,
            CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) AS kid_creacion,
            da.fecha_creacion,
            e.estatus AS kid_estatus
        FROM detalles_almacenes da
        LEFT JOIN almacenes a ON da.kid_almacen = a.id_almacen
        LEFT JOIN articulos ar ON da.kid_articulo = ar.id_articulo
        LEFT JOIN colaboradores u ON da.kid_creacion = u.id_colaborador
        LEFT JOIN estatus e ON da.kid_estatus = e.id_estatus
        WHERE da.kid_estatus != 3";

        $resultado = $conexion->prepare($consultaselect);
        $resultado->execute();

        $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
        $data['data_show']['almacenes'] = GetAlmacenesListForSelect();
        $data['data_show']['articulos'] = GetArticulosListForSelect();
        $data['data_show']['estatus'] = GetEstatusListForSelect();

        // Configuración de los botones de acción
        $modalCRUD = 'detalles_almacenes';
        $nuevo_boton = '<button class="ModalNewAdd3 btn btn-info info" modalCRUD="' . $modalCRUD . '"><i class="bi bi-file-spreadsheet"></i> Ver Detalles</button>';
        array_push($data_script['botones_acciones'], $nuevo_boton);
        $data['data_show']['botones_acciones'] = $data_script['botones_acciones'];
        $optionkey = 'NewAdd3';
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