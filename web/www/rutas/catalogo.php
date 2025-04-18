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
        case 'colaboradores':
            $vista = 'colaboradores';
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
            $vista = 'categorias';
            break;
        case 'subcategorias':
            $vista = 'subcategorias';
            break;
        case 'dimensiones':
            $vista = 'dimensiones';
            break;
        case 'presentaciones':
            $vista = 'presentaciones';
            break;
        case 'formatos':
            $vista = 'formatos';
            break;
        case 'roles':
            $vista = 'roles';
            break;
        case 'unidades':
            $vista = 'unidades';
            break;
        case 'articulos':
            $vista = 'articulos';
            break;
        case 'estados':
            $vista = 'estados';
            break;
        case 'municipios':
            $vista = 'municipios';
            break;
        case 'empresas':
            $vista = 'empresas';
            break;
        case 'sucursales':
            $vista = 'sucursales';
            break;
        case 'almacenes':
            $vista = 'almacenes';
            break;
            case 'mermas':
                $vista = 'mermas';
                break;
                case 'locaciones':
                    $vista = 'locaciones';
                    break;
        case 'detalles_almacenes':
            $vista = 'detalles_almacenes';
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
        case 'comentarios_almacenes':
            $vista = 'comentarios_almacenes';
            break;
        case 'tipos_comentarios':
            $vista = 'tipos_comentarios';
            break;
        case 'tipos_estados':
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