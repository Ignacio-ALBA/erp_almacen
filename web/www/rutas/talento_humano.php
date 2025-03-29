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
    $data['data_show']['nombre_modulo'] = 'Talento Humano';


    switch ($pathResult) {
        case 'colaboradores':
            $vista = 'colaboradores';
            $consultaselect = "SELECT u.id_colaborador, 
                CONCAT(nombre, ' ', apellido_paterno, ' ', apellido_materno) AS nombre,
                ie.internos_externos,
                u.email,
                tu.tipo_usuario AS kid_tipo_usuario,
                CASE 
                    WHEN u.login = 1 THEN 'SÍ'  
                    ELSE 'NO' 
                END,
                u.fecha_creacion
            FROM 
                colaboradores u
            LEFT JOIN tipos_usuario tu ON u.kid_tipo_usuario = tu.id_tipo_usuario
            LEFT JOIN internos_externos ie ON u.kid_internos_externos = ie.id_internos_externos
            WHERE 
                u.kid_estatus != 3";

            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();

            $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
            $data['data_show']['tipos_usuario'] = GetTiposUsuariosListForSelect();
            $data['data_show']['regimenes'] = GetRegimenesListForSelect();
            $data['data_show']['paises'] = GetPaisesListForSelect();
            $data['data_show']['estados'] = GetEstadosListForSelect();
            $data['data_show']['tipo_contrato'] = GetTipoContratoListForSelect();
            $data['data_show']['estado_civil'] = GetEstadoCivilListForSelect();
            $data['data_show']['internos_externos'] = GetInternosExternosListForSelect();
            $data['data_show']['tipo_cantidad'] = GetTiposCostosListForSelect();

            array_unshift($data_script['botones_acciones'], 
            '<button class="ModalDataEdit btn btn-success" modalCRUD="colaboradores-changepwd"><i class="bi bi-key-fill"></i> Cambiar Contraseña</button>');

            $data['data_show']['botones_acciones'] =  $data_script['botones_acciones'];

            
            break;


        case 'ocupaciones_talento_humano':
            $vista = 'ocupaciones_talento_humano';
            $consultaselect = "SELECT o.id_ocupacion_th , 
                CONCAT(c.nombre, ' ', c.apellido_paterno, ' ', c.apellido_materno) AS kid_colaborador,
                p.proyecto,
                o.estampa_inicio,
                o.estampa_fin,
                CASE 
                    WHEN o.finalizado = 1 THEN 'SÍ'  
                    ELSE 'NO' 
                END AS finalizado,
                o.fecha_creacion
            FROM 
                ocupaciones_th o
            LEFT JOIN colaboradores c ON o.kid_colaborador = c.id_colaborador
            LEFT JOIN proyectos p ON o.kid_proyecto = p.id_proyecto
            WHERE 
                o.kid_estatus != 3";

            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();

            $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);

            $data_script['botones_acciones'] = [
                '<button class="ModalDataView btn btn-primary primary" modalCRUD="${modalCRUD}"><i class="bi bi-eye"></i> Ver</button>',
            ];

            $data['data_show']['botones_acciones'] =  $data_script['botones_acciones'];
            $data['data_show']['colaboradores'] =  GetUsuariosListForSelect();
            $data['data_show']['bolsas_proyectos'] = GetBolsaProyectosListForSelect();
            $data['data_show']['proyectos'] = GetProyectosListForSelect();
            $data['data_show']['internos_externos'] = GetInternosExternosListForSelect();
            $data['data_show']['tipo_cantidad'] = GetTiposCostosListForSelect();
            break;


        case 'asistencias_talento_humano':
            $vista = 'asistencias_talento_humano';
            $consultaselect = "SELECT a.id_asistencia_th, 
                CONCAT(c.nombre, ' ', c.apellido_paterno, ' ', c.apellido_materno) AS kid_colaborador,
                a.estampa_entrada,
                a.estampa_salida,
                ie.internos_externos as kid_internos_externos,
                a.fecha_creacion
            FROM 
                asistencias_th a
            LEFT JOIN colaboradores c ON a.kid_colaborador = c.id_colaborador
            LEFT JOIN internos_externos ie ON a.kid_internos_externos = ie.id_internos_externos 
            WHERE 
                a.kid_estatus != 3";

            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();

            $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
            $modalCRUD = 'editar_asistencias_th';

            $data_script['botones_acciones'] = [
                '<button class="ModalDataView btn btn-primary primary" modalCRUD="'.$modalCRUD.'"><i class="bi bi-eye"></i> Ver</button>',
                '<button class="ModalDataEdit btn btn-warning warning" modalCRUD="'.$modalCRUD.'"><i class="bi bi-pencil"></i> Editar</button>',
                '<button class="ModalDataDelete btn btn-danger danger" modalCRUD="'.$modalCRUD.'"><i class="bi bi-trash"></i> Eliminar</button>'
            ];

            $data['data_show']['botones_acciones'] =  $data_script['botones_acciones'];
            $data['data_show']['colaboradores'] =  GetUsuariosListForSelect();
            $data['data_show']['bolsas_proyectos'] = GetBolsaProyectosListForSelect();
            $data['data_show']['proyectos'] = GetProyectosListForSelect();
            $data['data_show']['internos_externos'] = GetInternosExternosListForSelect();
            $data['data_show']['tipo_cantidad'] = GetTiposCostosListForSelect();
            break;


        case 'adicionales_asistencias_talento_humano':
            $vista = 'adicionales_asistencias_talento_humano';
            $consultaselect = "SELECT aa.id_asistencia_th, 
                CONCAT(c.nombre, ' ', c.apellido_paterno, ' ', c.apellido_materno) AS kid_colaborador,
                ta.tipo_adicional_th,
                aa.comentario,
                ie.internos_externos as kid_internos_externos,
                aa.fecha_creacion
            FROM 
                adicionales_asistencias_th aa
            LEFT JOIN colaboradores c ON aa.kid_colaborador = c.id_colaborador
            LEFT JOIN internos_externos ie ON aa.kid_interno_externo = ie.id_internos_externos 
            LEFT JOIN tipos_adicionales_th ta ON aa.kid_tipo_adicional_th = ta.id_tipo_adicional_th
            LEFT JOIN tipos_costo tp ON aa.kid_tipos_cantidad = tp.id_tipo_costo  
            WHERE 
                aa.kid_estatus != 3";

            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();

            $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
            $data['data_show']['colaboradores'] =  GetUsuariosListForSelect();
            $data['data_show']['tipoadicionales'] =  GetTiposAdicionalesListForSelect();
            
            $data['data_show']['internos_externos'] = GetInternosExternosListForSelect();
            $data['data_show']['tipo_cantidad'] = GetTiposCostosListForSelect();
            break;


        case 'tipos_adicionales':
            $vista = 'tipos_adicionales';
            $consultaselect = "SELECT id_tipo_adicional_th,
                orden,
                tipo_adicional_th,
                CASE 
                    WHEN pordefecto = 1 THEN 'SÍ' 
                    ELSE 'NO' 
                END AS pordefecto,
                fecha_creacion
            FROM tipos_adicionales_th
            WHERE kid_estatus != 3";

            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();

            $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
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