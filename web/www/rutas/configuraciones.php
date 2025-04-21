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
    
    $data['data_show']['nombre_modulo'] = 'Configuraciones';
    $data['data_show']['breadcrumb'] = null;
    $data['data_show']['AllowADDButton'] = true;



    switch ($pathResult) {
        case 'permisos':
            $perms = [
                "ver_permisos",
              "editar_permisos"
             ];
  
              checkPerms($perms);
              $acciones = ['ver_', 'editar_', 'eliminar_'];
              foreach ($acciones as $index => $accion) {
                  if (!checkPerms(preg_grep("/$accion/", $perms), true)) {
                      unset($data_script['botones_acciones'][$index]);
                  }
              }
            $vista = 'permisos';
            $consulta = "SELECT 
                p.permiso,
                p.etiqueta,
                t.tablas AS tabla,
                m.modulo,
                p.kid_estatus
            FROM permisos p
            INNER JOIN tablas t ON p.kid_tabla = t.id_tabla
            INNER JOIN modulos m ON t.kid_modulo = m.id_modulo
            WHERE p.kid_estatus != 3
            ORDER BY m.modulo, t.tablas;";
            $resultado = $conexion->prepare($consulta);
            $resultado->execute();
            $permisos = $resultado->fetchAll(PDO::FETCH_ASSOC);

            $estatus = GetEstatusLabels();

            $permisos = array_map(function ($row, $index) {
                global  $estatus;
                $botones_acciones = [];
                array_unshift($row, $index + 1);
                $bloque = 'configuraciones';
                $modalCRUD = 'permisos';
                if($row['kid_estatus'] == 1){
                    $boton = '<button class="UpdateEstatus btn btn-danger" bloque="'. $bloque.'" name="desactivar/'.$row['permiso'].'" modalCRUD="'.$modalCRUD.'"><i class="bi bi-ban"></i> Desactivar</button>';
                }else{
                    $boton = '<button class="UpdateEstatus btn btn-success" bloque="'. $bloque.'" name="activar/'.$row['permiso'].'" modalCRUD="'.$modalCRUD.'"><i class="bi bi-check2-circle"></i> Activar</button>';
                }
                array_push($botones_acciones, $boton);
                $row['botones'] = GenerateCustomsButtons($botones_acciones, 'permisos');
                $row['kid_estatus'] = $estatus[$row['kid_estatus']];
                unset($row['permiso']);
                return $row;
            }, $permisos, array_keys($permisos));


            $data['data_show']['data'] = $permisos;

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