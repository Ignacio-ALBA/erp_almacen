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
        case 'proveedores':
            $perms = [
                "crear_proveedores",
                    "editar_proveedores",
                    "ver_proveedores",
                    "eliminar_proveedores"
                    ];
        
                    checkPerms($perms);
                    $acciones = ['ver_', 'editar_', 'eliminar_'];
                    foreach ($acciones as $index => $accion) {
                        if (!checkPerms(preg_grep("/$accion/", $perms), true)) {
                            unset($data_script['botones_acciones'][$index]);
                        }
                    }
            $vista = 'proveedores';
            $consultaselect = "SELECT id_proveedor,
                orden,
                codigo,
                proveedor,
                CONCAT(calificacion,' <i class=\"bi bi-star-fill\"></i>') AS calificacion,
                razon_social,
                rfc,
                email1,
                CASE 
                    WHEN pordefecto = 1 THEN 'SÍ' 
                    ELSE 'NO' 
                END AS pordefecto,  -- Se añadió un alias aquí
                fecha_creacion
            FROM proveedores
            WHERE kid_estatus != 3 
            ORDER BY calificacion DESC";
            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();
            $modalCRUD = 'comentarios_proveedores';
            $nuevo_boton = '
                <button class="ModalNewAdd1 btn btn-secondary secondary" modalCRUD="'.$modalCRUD.'"><i class="bi bi-chat-left-text"></i> Comentario</button>
            ';
            array_splice($data_script['botones_acciones'], 1, 0, $nuevo_boton);
            $data['data_show']['botones_acciones'] = $data_script['botones_acciones'];
            
            $data_script['NewAdd1'] =['data_list_column'=>[
                'kid_proveedor'=>5,
                
            ]];

            $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
            $data['data_show']['regimenes'] = GetRegimenesListForSelect();
            $data['data_show']['paises'] = GetPaisesListForSelect();
            $data['data_show']['estados'] = GetEstadosListForSelect();
            $data['data_show']['proveedores'] = GetProvedoresListForSelect();
            $data['data_show']['tipo_comentario'] = GetTiposComentariosListForSelect();

            break;

        case 'comentarios_proveedores':
            $perms = [
                "crear_comentarios_proveedores",
                "editar_comentarios_proveedores",
                "ver_comentarios_proveedores",
                "eliminar_comentarios_proveedores"
                ];
    
                checkPerms($perms);
                $acciones = ['ver_', 'editar_', 'eliminar_'];
                foreach ($acciones as $index => $accion) {
                    if (!checkPerms(preg_grep("/$accion/", $perms), true)) {
                        unset($data_script['botones_acciones'][$index]);
                    }
                }
            $vista = 'comentarios_proveedores';
            $consultaselect = "SELECT cp.id_comentario_proveedor , 
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
            WHERE cp.kid_estatus !=3";
            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();

            $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
            $data['data_show']['tipo_comentario'] = GetTiposComentariosListForSelect();
            $data['data_show']['proveedores'] = GetProvedoresListForSelect();
            break;

        case 'clientes':
            $perms = [
                "crear_clientes",
            "editar_clientes",
            "ver_clientes",
            "eliminar_clientes"
            ];

            checkPerms($perms);
            $acciones = ['ver_', 'editar_', 'eliminar_'];
            foreach ($acciones as $index => $accion) {
                if (!checkPerms(preg_grep("/$accion/", $perms), true)) {
                    unset($data_script['botones_acciones'][$index]);
                }
            }
            $vista = 'clientes';
            $consultaselect = "SELECT c.id_cliente, 
                        c.codigo, 
                        c.nombre,
                        c.razon_social,
                        c.rfc,
                        c.email,
                        c.fecha_creacion
                    FROM 
                        clientes c
                    WHERE 
                        c.kid_estatus != 3";

            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();

            $clientes = $resultado->fetchAll(PDO::FETCH_ASSOC);

            $clientes = array_map(function ($row) {
                global $data_script, $estatus;
                $botones_acciones = $data_script['botones_acciones'];
                $hashed_id = codificar($row['id_cliente']);
                array_push($botones_acciones, '<a href="/rutas/planeacion.php/planeaciones_actividades?id=' . $hashed_id . '" class="btn btn-info "><i class="bi bi-file-spreadsheet"></i> Actividades</a>');
                array_push($botones_acciones, '<a href="/rutas/planeacion.php/planeaciones_recursos_humanos?id=' . $hashed_id . '" class="btn btn-info "><i class="bi bi-file-spreadsheet"></i> TH</a>');
                array_push($botones_acciones, '<a href="/rutas/planeacion.php/planeaciones_compras?id=' . $hashed_id . '" class="btn btn-info "><i class="bi bi-file-spreadsheet"></i> Compras</a>');
                array_push($botones_acciones, '<a href="/rutas/contabilidad.php/facturas_clientes?id=' . $hashed_id . '" class="btn btn-secondary "><i class="bi bi-receipt"></i> Facturas</a>');
                $row['botones'] = GenerateCustomsButtons($botones_acciones, 'clientes');
                return $row;
            }, $clientes);

            $data['data_show']['data'] = $clientes;

            $data['data_show']['regimenes'] = GetRegimenesListForSelect();
            $data['data_show']['paises'] = GetPaisesListForSelect();
            $data['data_show']['estados'] = GetEstadosListForSelect();
            $data['data_show']['bolsas_proyectos'] = GetBolsaProyectosListForSelect();
            break;

            case 'comentarios_clientes':
                $perms = [
                    "crear_comentarios_clientes",
                    "editar_comentarios_clientes",
                    "ver_comentarios_clientes",
                    "eliminar_comentarios_clientes"
                    ];
        
                    checkPerms($perms);
                    $acciones = ['ver_', 'editar_', 'eliminar_'];
                    foreach ($acciones as $index => $accion) {
                        if (!checkPerms(preg_grep("/$accion/", $perms), true)) {
                            unset($data_script['botones_acciones'][$index]);
                        }
                    }
                $vista = 'comentarios_clientes';
                $consultaselect = "SELECT cc.id_comentario_cliente , 
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
                WHERE cc.kid_estatus !=3";
                $resultado = $conexion->prepare($consultaselect);
                $resultado->execute();
    
                $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
                $data['data_show']['tipo_comentario'] = GetTiposComentariosListForSelect();
                $data['data_show']['clientes'] = GetClientesListForSelect();
                break;
        case 'marcas':
            $perms = [
                "crear_marcas",
                    "editar_marcas",
                    "ver_marcas",
                    "eliminar_marcas"
               ];
    
                checkPerms($perms);
                $acciones = ['ver_', 'editar_', 'eliminar_'];
                foreach ($acciones as $index => $accion) {
                    if (!checkPerms(preg_grep("/$accion/", $perms), true)) {
                        unset($data_script['botones_acciones'][$index]);
                    }
                }
            $vista = 'marcas';
            break;
        case 'categorias':
            $perms = [
                "crear_categorias",
                "editar_categorias",
                "ver_categorias",
                "eliminar_categorias"
               ];
    
                checkPerms($perms);
                $acciones = ['ver_', 'editar_', 'eliminar_'];
                foreach ($acciones as $index => $accion) {
                    if (!checkPerms(preg_grep("/$accion/", $perms), true)) {
                        unset($data_script['botones_acciones'][$index]);
                    }
                }
            $vista = 'categorias';
            break;
        case 'subcategorias':
            $perms = [
                "crear_subcategorias",
                    "editar_subcategorias",
                    "ver_subcategorias",
                    "eliminar_subcategorias"
               ];
    
                checkPerms($perms);
                $acciones = ['ver_', 'editar_', 'eliminar_'];
                foreach ($acciones as $index => $accion) {
                    if (!checkPerms(preg_grep("/$accion/", $perms), true)) {
                        unset($data_script['botones_acciones'][$index]);
                    }
                }
            $vista = 'subcategorias';
            break;
        case 'dimensiones':
            $perms = [
                "crear_dimensiones",
                    "editar_dimensiones",
                    "ver_dimensiones",
                    "eliminar_dimensiones"
               ];
    
                checkPerms($perms);
                $acciones = ['ver_', 'editar_', 'eliminar_'];
                foreach ($acciones as $index => $accion) {
                    if (!checkPerms(preg_grep("/$accion/", $perms), true)) {
                        unset($data_script['botones_acciones'][$index]);
                    }
                }
            $vista = 'dimensiones';
            break;
        case 'presentaciones':
            $perms = [
                "crear_presentaciones",
                    "editar_presentaciones",
                    "ver_presentaciones",
                    "eliminar_presentaciones"
               ];
    
                checkPerms($perms);
                $acciones = ['ver_', 'editar_', 'eliminar_'];
                foreach ($acciones as $index => $accion) {
                    if (!checkPerms(preg_grep("/$accion/", $perms), true)) {
                        unset($data_script['botones_acciones'][$index]);
                    }
                }
            $vista = 'presentaciones';
            break;
        case 'formatos':
            $perms = [
                "crear_formatos",
                    "editar_formatos",
                    "ver_formatos",
                    "eliminar_formatos"
               ];
    
                checkPerms($perms);
                $acciones = ['ver_', 'editar_', 'eliminar_'];
                foreach ($acciones as $index => $accion) {
                    if (!checkPerms(preg_grep("/$accion/", $perms), true)) {
                        unset($data_script['botones_acciones'][$index]);
                    }
                }
            $vista = 'formatos';
            break;
        case 'roles':

            $vista = 'roles';
            break;
        case 'unidades':
            $perms = [
                "crear_unidades",
                    "editar_unidades",
                    "ver_unidades",
                    "eliminar_unidades"
               ];
    
                checkPerms($perms);
                $acciones = ['ver_', 'editar_', 'eliminar_'];
                foreach ($acciones as $index => $accion) {
                    if (!checkPerms(preg_grep("/$accion/", $perms), true)) {
                        unset($data_script['botones_acciones'][$index]);
                    }
                }
            $vista = 'unidades';
            break;
        case 'articulos':
            $perms = [
                "crear_articulos",
                    "editar_articulos",
                    "ver_articulos",
                    "eliminar_articulos"
               ];
    
                checkPerms($perms);
                $acciones = ['ver_', 'editar_', 'eliminar_'];
                foreach ($acciones as $index => $accion) {
                    if (!checkPerms(preg_grep("/$accion/", $perms), true)) {
                        unset($data_script['botones_acciones'][$index]);
                    }
                }
            $vista = 'articulos';
            break;
        case 'estados':
            $perms = [
                "crear_estados",
                    "editar_estados",
                    "ver_estados",
                    "eliminar_estados"
               ];
    
                checkPerms($perms);
                $acciones = ['ver_', 'editar_', 'eliminar_'];
                foreach ($acciones as $index => $accion) {
                    if (!checkPerms(preg_grep("/$accion/", $perms), true)) {
                        unset($data_script['botones_acciones'][$index]);
                    }
                }
            $vista = 'estados';
            break;
        case 'municipios':
            $perms = [
                "crear_municipios",
                    "editar_municipios",
                    "ver_municipios",
                    "eliminar_municipios"
                ];
                checkPerms($perms);
                $acciones = ['ver_', 'editar_', 'eliminar_'];
                foreach ($acciones as $index => $accion) {
                    if (!checkPerms(preg_grep("/$accion/", $perms), true)) {
                        unset($data_script['botones_acciones'][$index]);
                    }
                }
            $vista = 'municipios';
            break;
        case 'empresas':
            $perms = [
                "crear_empresas",
                    "editar_empresas",
                    "ver_empresas",
                    "eliminar_empresas"
                ];
                checkPerms($perms);
                $acciones = ['ver_', 'editar_', 'eliminar_'];
                foreach ($acciones as $index => $accion) {
                    if (!checkPerms(preg_grep("/$accion/", $perms), true)) {
                        unset($data_script['botones_acciones'][$index]);
                    }
                }
            $vista = 'empresas';
            break;
        case 'sucursales':
            $perms = [
                "crear_sucursales",
                    "editar_sucursales",
                    "ver_sucursales",
                    "eliminar_sucursales"
                ];
                checkPerms($perms);
                $acciones = ['ver_', 'editar_', 'eliminar_'];
                foreach ($acciones as $index => $accion) {
                    if (!checkPerms(preg_grep("/$accion/", $perms), true)) {
                        unset($data_script['botones_acciones'][$index]);
                    }
                }
            $vista = 'sucursales';
            break;
        case 'almacenes':
            $perms = [
                "crear_almacenes",
                    "editar_almacenes",
                    "ver_almacenes",
                    "eliminar_almacenes"
               ];
    
                checkPerms($perms);
                $acciones = ['ver_', 'editar_', 'eliminar_'];
                foreach ($acciones as $index => $accion) {
                    if (!checkPerms(preg_grep("/$accion/", $perms), true)) {
                        unset($data_script['botones_acciones'][$index]);
                    }
                }
            $vista = 'almacenes';
            break;
            case 'mermas':
                $vista = 'mermas';
                break;
                case 'locaciones':
                    $vista = 'locaciones';
                    break;
      
        case 'comentarios_almacenes':
            $perms = [
                "crear_comentarios_almacenes",
                    "editar_comentarios_almacenes",
                    "ver_comentarios_almacenes",
                    "eliminar_comentarios_almacenes"
               ];
    
                checkPerms($perms);
                $acciones = ['ver_', 'editar_', 'eliminar_'];
                foreach ($acciones as $index => $accion) {
                    if (!checkPerms(preg_grep("/$accion/", $perms), true)) {
                        unset($data_script['botones_acciones'][$index]);
                    }
                }
            $vista = 'comentarios_almacenes';
            break;
        case 'tipos_comentarios':
            $perms = [
                "crear_tipos_comentarios",
                    "editar_tipos_comentarios",
                    "ver_tipos_comentarios",
                    "eliminar_tipos_comentarios"
               ];
    
                checkPerms($perms);
                $acciones = ['ver_', 'editar_', 'eliminar_'];
                foreach ($acciones as $index => $accion) {
                    if (!checkPerms(preg_grep("/$accion/", $perms), true)) {
                        unset($data_script['botones_acciones'][$index]);
                    }
                }
            $vista = 'tipos_comentarios';
            break;
        case 'tipos_estados':
            $perms = [
                "crear_estatus",
                "editar_estatus",
                "ver_estatus",
                "eliminar_estatus"
               ];
    
                checkPerms($perms);
                $acciones = ['ver_', 'editar_', 'eliminar_'];
                foreach ($acciones as $index => $accion) {
                    if (!checkPerms(preg_grep("/$accion/", $perms), true)) {
                        unset($data_script['botones_acciones'][$index]);
                    }
                }
            $vista = 'tipos_estados';
            $consultaselect = "SELECT id_estatus,
                estatus,
                estatus_color,
                fecha_creacion
            FROM estatus
            WHERE kid_estatus = 1";
            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();
            $consulta_data = $resultado->fetchAll(PDO::FETCH_ASSOC);
            foreach ($consulta_data as &$fila) { // Usar referencia para modificar el array original
                $fila['estatus_color'] = CreateBadge([
                    'etiqueta' => $fila['estatus_color'] ? $fila['estatus_color'] : 'Sin Color',
                    'style' => $fila['estatus_color'] ? ('background-color:' . $fila['estatus_color'].';') : 'color:black;' // Cambiar $data a $fila
                ]);
            }
            $data['data_show']['data'] = $consulta_data;
            array_pop($data_script['botones_acciones']);
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