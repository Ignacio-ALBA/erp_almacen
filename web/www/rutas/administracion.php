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


    switch ($pathResult) {
        case 'proyectos':
            $vista = 'proyectos';
            $consultaselect = "SELECT p.id_proyecto , 
                    p.proyecto, 
                    b.bolsa_proyecto as kid_bolsa_proyecto,
                    p.ubicacion, 
                    p.presupuesto,
                    p.objetivo,
                    CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) as kid_responsable,
                    p.fecha_inicio,
                    p.fecha_fin
            FROM proyectos p
            LEFT JOIN bolsas_proyectos b ON p.kid_bolsa_proyecto = b.id_bolsa_proyecto
            LEFT JOIN colaboradores u ON p.kid_responsable = u.id_colaborador 
            WHERE p.kid_estatus = 1";
            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();

            $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);

            $data['data_show']['colaboradores'] = GetUsuariosListForSelect();
            $data['data_show']['bolsa_proyectos'] = GetBolsaProyectosListForSelect();
            break;
        case 'detalles_proyectos':
            $vista = 'detalles_proyectos';
            $consultaselect = "SELECT d.id_detalle_proyecto, 
                    d.detalle_proyecto, 
                    p.proyecto as kid_proyecto,
                    d.presupuesto,
                    d.objetivo,
                    CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) as kid_responsable,
                    d.fecha_inicio,
                    d.fecha_fin
            FROM detalles_proyectos d
            LEFT JOIN proyectos p ON d.kid_proyecto = p.id_proyecto  
            LEFT JOIN colaboradores u ON d.kid_responsable = u.id_colaborador 
            WHERE d.kid_estatus = 1";
            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();

            $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
            $data['data_show']['colaboradores'] = GetUsuariosListForSelect();
            $data['data_show']['proyectos'] = GetProyectosListForSelect();
            break;
        case 'bolsas_proyectos':
            $vista = 'bolsas_proyectos';
            $consultaselect = "SELECT b.id_bolsa_proyecto , 
                    b.bolsa_proyecto, 
                    c.nombre as kid_cliente,
                    b.comentarios,
                    b.fecha_creacion
            FROM bolsas_proyectos b
            LEFT JOIN clientes c ON b.kid_cliente = c.id_cliente  
            WHERE b.kid_estatus = 1";
            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();

            $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
            $data['data_show']['clientes'] = GetClientesListForSelect();

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