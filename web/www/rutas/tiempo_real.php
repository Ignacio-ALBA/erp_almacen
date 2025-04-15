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
    $data['data_show']['nombre_modulo'] = 'Tiempo real';


    switch ($pathResult) {
        case 'tiempo_real':
            $vista = 'tiempo_real';
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