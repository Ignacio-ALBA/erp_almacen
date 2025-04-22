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