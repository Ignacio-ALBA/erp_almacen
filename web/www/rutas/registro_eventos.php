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
    $data['data_show']['nombre_modulo'] = 'Registro de eventos';


    switch ($pathResult) {
        case 'registro_eventos':
            $perms = [
                "crear_registro_eventos",
            "editar_registro_eventos",
            "ver_registro_eventos",
            "eliminar_registro_eventos"
               ];
    
                checkPerms($perms);
                $acciones = ['ver_', 'editar_', 'eliminar_'];
                foreach ($acciones as $index => $accion) {
                    if (!checkPerms(preg_grep("/$accion/", $perms), true)) {
                        unset($data_script['botones_acciones'][$index]);
                    }
                }
            $vista = 'registro_eventos';
            break;
            case 'detalles_registro_eventos':
                $perms = [
                    "crear_detalles_registro_eventos",
                    "ver_detalles_registro_eventos",
                    "editar_detalles_registro_eventos",
                    "eliminar_detalles_registro_eventos"
                ];
                checkPerms($perms);
                $acciones = ['ver_', 'editar_', 'eliminar_'];
                foreach ($acciones as $index => $accion) {
                    if (!checkPerms(preg_grep("/$accion/", $perms), true)) {
                        unset($data_script['botones_acciones'][$index]);
                    }
                }

                $vista = 'detalles_registro_eventos';
                break;
                case 'comentarios_registro_eventos':
            $vista = 'comentarios_registro_eventos';
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