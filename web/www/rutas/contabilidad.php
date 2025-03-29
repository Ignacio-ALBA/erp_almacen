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
    $data['data_show']['nombre_modulo'] = 'Contabilidad';
    $data['data_show']['breadcrumb'] = null;

    switch ($pathResult) {
        case 'bancos':
            $vista = 'bancos';
            $consultaselect = "SELECT id_banco,
                orden,
                banco,
                CASE 
                    WHEN pordefecto = 1 THEN 'SÍ' 
                    ELSE 'NO' 
                END,
                fecha_creacion
            FROM bancos
            WHERE kid_estatus = 1";
            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();

            $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
            break;
        case 'tipos_cuentas_bancarias':
            $vista = 'tipos_cuentas_bancarias';
            $consultaselect = "SELECT id_tipo_cuenta_bancaria,
                orden,
                tipo_cuenta_bancaria,
                CASE 
                    WHEN pordefecto = 1 THEN 'SÍ' 
                    ELSE 'NO' 
                END,
                fecha_creacion
            FROM tipos_cuentas_bancarias
            WHERE kid_estatus = 1";
            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();

            $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
            $data['data_show']['colaboradores'] = GetUsuariosListForSelect();
            $data['data_show']['proyectos'] = GetProyectosListForSelect();
            break;
        case 'cuentas_bancarias':
            $vista = 'cuentas_bancarias';
            $consultaselect = "SELECT cb.id_cuenta_bancaria,
                cb.cuenta_bancaria,
                b.banco AS kid_banco,
                cb.numero_cuenta_bancaria,
                cb.cable,
                cb.tarjeta,
                tcb.tipo_cuenta_bancaria,
                cb.saldo,
                cb.deuda,
                cb.fecha_creacion
            FROM cuentas_bancarias cb
            LEFT JOIN bancos b ON cb.kid_banco = b.id_banco
            LEFT JOIN tipos_cuentas_bancarias tcb ON cb.kid_tipo_cuenta_bancaria = tcb.id_tipo_cuenta_bancaria 
            WHERE b.kid_estatus = 1";
            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();
            $cuentas = $resultado->fetchAll(PDO::FETCH_ASSOC);

            $cuentas = array_map(function ($cuenta) {
                global $data_script;
                $botones_acciones = $data_script['botones_acciones'];
                $hashed_id = codificar($cuenta['id_cuenta_bancaria']);
                
                array_push($botones_acciones, '<button class="btn btn-success" modalCRUD="${modalCRUD}"><i class="bi bi-file-earmark-plus"></i> Crear Reporte</button>');
                array_push($botones_acciones, '<a href="/rutas/contabilidad.php/reportes_cuentas_bancarias?id=' . $hashed_id . '" class="btn btn-info "><i class="bi bi-journals"></i> Reportes</a>');
                array_push($botones_acciones, '<a href="/rutas/contabilidad.php/detalles_cuentas_bancarias?id=' . $hashed_id . '" class="btn btn-secondary "><i class="bi bi-journal-text"></i> Contenido</a>');
                $cuenta['botones'] = GenerateCustomsButtons($botones_acciones, 'cuentas_bancarias');
                return $cuenta;
            }, $cuentas);

            $data['data_show']['data'] = $cuentas;
            $data['data_show']['bancos'] = GetBancosListForSelect();
            $data['data_show']['tipos_cuentas_bancarias'] = GetTiposCuentasBancariasListForSelect();

            break;

        case 'detalles_cuentas_bancarias':
            $vista = 'detalles_cuentas_bancarias';
            if($id !=null){
                $data['data_show']['PageSection'] = "Contenido de Cuenta Bancaria";

                $consultaselect = "SELECT dcb.id_detalle_cuenta_bancaria,
                    dcb.detalle_cuenta_bancaria,
                    cb.cuenta_bancaria AS kid_cuenta_bancaria,
                    p.proyecto AS kid_proyecto,
                    dcb.monto_asignado,
                    dcb.monto_disponible,
                    dcb.monto_adeudado,
                    dcb.monto_gastado,
                    dcb.fecha_creacion
                FROM detalles_cuentas_bancarias dcb
                LEFT JOIN cuentas_bancarias cb ON dcb.kid_cuenta_bancaria = cb.id_cuenta_bancaria
                LEFT JOIN proyectos p ON dcb.kid_proyecto = p.id_proyecto 
                WHERE dcb.kid_estatus = 1 AND dcb.kid_cuenta_bancaria = $id";

                $resultado = $conexion->prepare("SELECT cuenta_bancaria FROM cuentas_bancarias WHERE id_cuenta_bancaria = $id");
                $resultado->execute();
                $cuenta = $resultado->fetch(PDO::FETCH_ASSOC);

                $breadcrumb = '
                <li class="breadcrumb-item"><a href="/rutas/contabilidad.php/cuentas_bancarias">Cuentas Bancarias</a></li>
                <li class="breadcrumb-item active">Contenido '.$cuenta['cuenta_bancaria'].'</li>';
                $data['data_show']['breadcrumb'] = $breadcrumb;
                $data['data_show']['AllowADDButton'] = true;
                $data['data_show']['cuentas_bancarias'] = GetCuentasBancariasListForSelect(['id_cuenta_bancaria'=>$id]);
            }else{
                $data['data_show']['PageSection'] = "Contenidos de Cuentas Bancarias";
                $consultaselect = "SELECT dcb.id_detalle_cuenta_bancaria,
                    dcb.detalle_cuenta_bancaria,
                    cb.cuenta_bancaria AS kid_cuenta_bancaria,
                    p.proyecto AS kid_proyecto,
                    dcb.monto_asignado,
                    dcb.monto_disponible,
                    dcb.monto_adeudado,
                    dcb.monto_gastado,
                    dcb.fecha_creacion
                FROM detalles_cuentas_bancarias dcb
                LEFT JOIN cuentas_bancarias cb ON dcb.kid_cuenta_bancaria = cb.id_cuenta_bancaria
                LEFT JOIN proyectos p ON dcb.kid_proyecto = p.id_proyecto 
                WHERE dcb.kid_estatus = 1";
                $data['data_show']['AllowADDButton'] = false;
                $data['data_show']['cuentas_bancarias'] = GetCuentasBancariasListForSelect();
            }
            
            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();

            $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
            $data['data_show']['proyectos'] = GetProyectosListForSelect();
            

            break;

        case 'compras_cuentas_bancarias':
            $vista = 'compras_cuentas_bancarias';
            $consultaselect = "SELECT ccb.id_compra_cuenta_bancaria ,
                cb.cuenta_bancaria AS kid_cuenta_bancaria,
                p.proyecto AS kid_proyecto,
                ccb.monto_total,
                ccb.monto_neto,
                ccb.fecha_creacion
            FROM compras_cuentas_bancarias ccb
            LEFT JOIN cuentas_bancarias cb ON ccb.kid_cuenta_bancaria = cb.id_cuenta_bancaria
            LEFT JOIN proyectos p ON ccb.kid_proyecto = p.id_proyecto 
            WHERE ccb.kid_estatus = 1";
            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();

            $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
            $data['data_show']['proyectos'] = GetProyectosListForSelect();
            $data['data_show']['cuentas_bancarias'] = GetCuentasBancariasListForSelect();

            break;

        case 'facturas_clientes':
            $vista = 'facturas_clientes';
            if($id !=null){
                $consultaselect = "SELECT fc.id_factura_cliente,
                c.nombre,
                p.proyecto,
                cb.cuenta_bancaria,
                monto_total,
                monto_neto,
                CASE
                    WHEN fc.archivo_pdf IS NOT NULL THEN 
                        CONCAT(
                            '<div class=\"btn-group\" role=\"group\" style=\"width:100%;\">',
                            '<button class=\"ViewDocument btn btn-primary\" href=\"', fc.archivo_pdf, '\"><i class=\"bi bi-file-earmark-fill\"></i> Ver</button>',
                            '<button class=\"DownloadDocument btn btn-secondary\" href=\"', fc.archivo_pdf, '\"><i class=\"bi bi-file-earmark-arrow-down-fill\"></i> Descargar</button>',
                            '</div>'
                        )
                    ELSE 'Sin Archivo'
                END AS archivo_pdf,
                CASE
                    WHEN fc.archivo_xml IS NOT NULL THEN 
                        CONCAT(
                            '<div class=\"btn-group\" role=\"group\" style=\"width:100%;\">',
                            '<button class=\"ViewDocument btn btn-primary\" href=\"', fc.archivo_xml, '\"><i class=\"bi bi-file-earmark-fill\"></i> Ver</button>',
                            '<button class=\"DownloadDocument btn btn-secondary\" href=\"', fc.archivo_xml, '\"><i class=\"bi bi-file-earmark-arrow-down-fill\"></i> Descargar</button>',
                            '</div>'
                        )
                    ELSE 'Sin Archivo'
                END AS archivo_xml,
                fecha_factura
                FROM facturas_clientes fc
                LEFT JOIN proyectos p ON fc.kid_proyecto = p.id_proyecto 
                LEFT JOIN clientes c ON fc.kid_cliente = c.id_cliente 
                LEFT JOIN cuentas_bancarias cb ON fc.kid_cuenta_bancaria = cb.id_cuenta_bancaria
                WHERE fc.kid_estatus !=3 AND fc.kid_cliente = $id";

                $resultado = $conexion->prepare("SELECT id_bolsa_proyecto  FROM bolsas_proyectos WHERE kid_cliente = $id");
                $resultado->execute();
                $bolsas_proyectos = $resultado->fetchAll(PDO::FETCH_ASSOC);

                $proyectos = [];
                $cuentas_bancarias = [];
                foreach ($bolsas_proyectos as $bolsa_proyecto) {
                    $proyectos = array_merge($proyectos, GetProyectosListForSelect(["kid_bolsa_proyecto"=>$bolsa_proyecto['id_bolsa_proyecto']]));

                }

                $data['data_show']['clientes'] = GetClientesListForSelect(["id_cliente"=>$id]);
                $data['data_show']['proyectos'] =  $proyectos;
                $data['data_show']['cuentas_bancarias'] = GetCuentasBancariasListForSelect();
            }else{
                $consultaselect = "SELECT fc.id_factura_cliente,
                c.nombre,
                p.proyecto,
                cb.cuenta_bancaria,
                monto_total,
                monto_neto,
                fecha_factura,
                CASE
                    WHEN fc.archivo_pdf IS NOT NULL THEN 
                        CONCAT(
                            '<div class=\"btn-group\" role=\"group\" style=\"width:100%;\">',
                            '<button class=\"ViewDocument btn btn-primary\" href=\"', fc.archivo_pdf, '\"><i class=\"bi bi-file-earmark-fill\"></i> Ver</button>',
                            '<button class=\"DownloadDocument btn btn-secondary\" href=\"', fc.archivo_pdf, '\"><i class=\"bi bi-file-earmark-arrow-down-fill\"></i> Descargar</button>',
                            '</div>'
                        )
                    ELSE 'Sin Archivo'
                END AS archivo_pdf,
                CASE
                    WHEN fc.archivo_xml IS NOT NULL THEN 
                        CONCAT(
                            '<div class=\"btn-group\" role=\"group\" style=\"width:100%;\">',
                            '<button class=\"ViewDocument btn btn-primary\" href=\"', fc.archivo_xml, '\"><i class=\"bi bi-file-earmark-fill\"></i> Ver</button>',
                            '<button class=\"DownloadDocument btn btn-secondary\" href=\"', fc.archivo_xml, '\"><i class=\"bi bi-file-earmark-arrow-down-fill\"></i> Descargar</button>',
                            '</div>'
                        )
                    ELSE 'Sin Archivo'
                END AS archivo_xml
                FROM facturas_clientes fc
                LEFT JOIN proyectos p ON fc.kid_proyecto = p.id_proyecto 
                LEFT JOIN clientes c ON fc.kid_cliente = c.id_cliente 
                LEFT JOIN cuentas_bancarias cb ON fc.kid_cuenta_bancaria = cb.id_cuenta_bancaria
                WHERE fc.kid_estatus !=3";
                $data['data_show']['clientes'] = GetClientesListForSelect();
                $data['data_show']['proyectos'] = GetProyectosListForSelect();
                $data['data_show']['cuentas_bancarias'] = GetCuentasBancariasListForSelect();
            }
            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();

            $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
            break;

        case 'reportes_cuentas_bancarias':
            $vista = 'reportes_cuentas_bancarias';
            if($id !=null){
                $consultaselect = "SELECT
                rcb.id_reporte_cuenta_bancaria,
                cb.cuenta_bancaria,
                CONCAT(rcb.mes_reporte, ' ', rcb.anno_reporte),
                CONCAT(rcb.saldo_inicial,' ', m.codigo),
                CONCAT(rcb.saldo_final,' ', m.codigo),
                CONCAT(rcb.total_debitos,' ', m.codigo),
                CONCAT(rcb.total_creditos,' ', m.codigo),
                rcb.fecha_creacion
                FROM reportes_cuentas_bancarias rcb
                LEFT JOIN monedas m ON rcb.kid_moneda = m.id_moneda
                LEFT JOIN cuentas_bancarias cb ON rcb.kid_cuenta_bancaria = cb.id_cuenta_bancaria 
                WHERE rcb.kid_estatus != 3 AND rcb.kid_cuenta_bancaria = $id";

                $resultado = $conexion->prepare("SELECT cuenta_bancaria FROM cuentas_bancarias WHERE id_cuenta_bancaria = $id");
                $resultado->execute();
                $cuenta = $resultado->fetch(PDO::FETCH_ASSOC);
                $breadcrumb = '
                <li class="breadcrumb-item"><a href="/rutas/contabilidad.php/cuentas_bancarias">Cuentas Bancarias</a></li>
                <li class="breadcrumb-item active">Reporte '.$cuenta['cuenta_bancaria'].'</li>';
                $data['data_show']['breadcrumb'] = $breadcrumb;
            }else{
                $consultaselect = "SELECT
                rcb.id_reporte_cuenta_bancaria,
                cb.cuenta_bancaria,
                CONCAT(rcb.mes_reporte, ' ', rcb.anno_reporte),
                CONCAT(rcb.saldo_inicial,' ', m.codigo),
                CONCAT(rcb.saldo_final,' ', m.codigo),
                CONCAT(rcb.total_debitos,' ', m.codigo),
                CONCAT(rcb.total_creditos,' ', m.codigo),
                rcb.fecha_creacion
                FROM reportes_cuentas_bancarias rcb
                LEFT JOIN monedas m ON rcb.kid_moneda = m.id_moneda
                LEFT JOIN cuentas_bancarias cb ON rcb.kid_cuenta_bancaria = cb.id_cuenta_bancaria 
                WHERE rcb.kid_estatus != 3";
            }
            

            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();

            $reportes = $resultado->fetchAll(PDO::FETCH_ASSOC);

            $reportes = array_map(function ($reporte) {
                global $data_script;
                $botones_acciones = $data_script['botones_acciones'];
                $hashed_id = codificar($reporte['id_reporte_cuenta_bancaria']);
                array_push($botones_acciones, '<a href="/rutas/contabilidad.php/detalles_reportes_cb?id=' . $hashed_id . '" class="btn btn-secondary "><i class="bi bi-journals"></i>Detalles</a>');
                array_push($botones_acciones, '<a href="/rutas/contabilidad.php/observaciones_reportes_cb?id=' . $hashed_id . '" class="btn btn-info "><i class="bi bi-chat-right-text-fill"></i> Observación</a>');
                $reporte['botones'] = GenerateCustomsButtons($botones_acciones, 'cuentas_bancarias');
                return $reporte;
            }, $reportes);
            $data_script['NewAdd1'] =['data_list_column'=>[
                'kid_reporte_cuenta_bancaria'=>0,
                
            ]];

            $data['data_show']['data'] = $reportes;
            break;

        case 'monedas':
            $vista = 'monedas';
            $consultaselect = "SELECT
            id_moneda,
            orden,
            moneda,
            simbolo,
            codigo,
            CASE 
                WHEN pordefecto = 1 THEN 'SÍ' 
                ELSE 'NO' 
            END,
            fecha_creacion
            FROM monedas WHERE kid_estatus !=3";
            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();

            $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
            break;


        case 'detalles_reportes_cb':
            $vista = 'detalles_reportes_cb';

            if($id !=null){
                $data['data_show']['PageSection'] = "Contenido de Reporte de Cuenta Bancaria";
                $consultaselect = "SELECT
                drcb.id_detalle_reporte_cb,
                drcb.kid_reporte_cuenta_bancaria,
                drcb.fecha_transaccion,
                drcb.descripcion,
                drcb.numero_referencia,
                trcb.tipo_reporte_cb,
                drcb.monto_total,
                drcb.monto_neto
                FROM detalles_reportes_cb drcb
                LEFT JOIN tipos_reportes_cb trcb ON drcb.kid_tipo_reporte_cb = trcb.id_tipo_reporte_cb
                LEFT JOIN reportes_cuentas_bancarias rcb ON drcb.kid_reporte_cuenta_bancaria = rcb.id_reporte_cuenta_bancaria 
                WHERE drcb.kid_estatus !=3 AND drcb.kid_reporte_cuenta_bancaria = $id";

                $resultado = $conexion->prepare("SELECT cb.cuenta_bancaria AS cuenta_bancaria
                FROM reportes_cuentas_bancarias rcb
                LEFT JOIN cuentas_bancarias cb ON rcb.kid_cuenta_bancaria = cb.id_cuenta_bancaria
                WHERE id_reporte_cuenta_bancaria = $id");
                $resultado->execute();
                $cuenta = $resultado->fetch(PDO::FETCH_ASSOC);
                $hashed_id = codificar($id);

                $breadcrumb = '
                <li class="breadcrumb-item"><a href="/rutas/contabilidad.php/cuentas_bancarias">Cuentas Bancarias</a></li>
                <li class="breadcrumb-item"><a href="/rutas/contabilidad.php/reportes_cuentas_bancarias?id=' . $hashed_id . '">Reporte</a></li>
                <li class="breadcrumb-item active">Detalle '.$cuenta['cuenta_bancaria'].'</li>';
                $data['data_show']['breadcrumb'] = $breadcrumb;

            }else{
                $data['data_show']['PageSection'] = "Contenidos de Reportes de Cuentas Bancarias";
                $consultaselect = "SELECT
                drcb.id_detalle_reporte_cb,
                drcb.kid_reporte_cuenta_bancaria,
                drcb.fecha_transaccion,
                drcb.descripcion,
                drcb.numero_referencia,
                trcb.tipo_reporte_cb,
                drcb.monto_total,
                drcb.monto_neto
                FROM detalles_reportes_cb drcb
                LEFT JOIN tipos_reportes_cb trcb ON drcb.kid_tipo_reporte_cb = trcb.id_tipo_reporte_cb
                LEFT JOIN reportes_cuentas_bancarias rcb ON drcb.kid_reporte_cuenta_bancaria = rcb.id_reporte_cuenta_bancaria
                WHERE drcb.kid_estatus !=3";
            }
            
            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();

            $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
            break;

        case 'tipos_reportes_cb':
            $vista = 'tipos_reportes_cb';
            $consultaselect = "SELECT
                id_tipo_reporte_cb,
                orden,
                tipo_reporte_cb,
                CASE 
                    WHEN pordefecto = 1 THEN 'SÍ' 
                    ELSE 'NO' 
                END,
                fecha_creacion
                FROM tipos_reportes_cb WHERE kid_estatus !=3";
            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();
            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();

            $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
            break;


        case 'observaciones_reportes_cb':
            $vista = 'observaciones_reportes_cb';
            if($id !=null){
                $consultaselect = "SELECT
                id_observacion_reporte_cb,
                kid_reporte_cuenta_bancaria,
                observacion,
                fecha_creacion
                FROM observaciones_reportes_cb WHERE kid_estatus !=3 AND kid_reporte_cuenta_bancaria = $id";
                $data['data_show']['AllowADDButton'] = true;
                $data['data_show']['reporteCB'] = $id;

                $resultado = $conexion->prepare("SELECT cb.cuenta_bancaria, cb.id_cuenta_bancaria
                FROM reportes_cuentas_bancarias rcb
                LEFT JOIN cuentas_bancarias cb ON rcb.kid_cuenta_bancaria = cb.id_cuenta_bancaria
                WHERE id_reporte_cuenta_bancaria = $id");
                $resultado->execute();
                $cuenta = $resultado->fetch(PDO::FETCH_ASSOC);
                $hashed_id = codificar($id);

                $breadcrumb = '
                <li class="breadcrumb-item"><a href="/rutas/contabilidad.php/cuentas_bancarias">Cuentas Bancarias</a></li>
                <li class="breadcrumb-item"><a href="/rutas/contabilidad.php/reportes_cuentas_bancarias?id=' . codificar($cuenta['id_cuenta_bancaria']) . '">Reporte</a></li>
                <li class="breadcrumb-item">'.$cuenta['cuenta_bancaria'].'</li>
                <li class="breadcrumb-item active">Observaciones de Reporte '.$id.'</li>';
                $data['data_show']['breadcrumb'] = $breadcrumb;


                
            }else{
                $consultaselect = "SELECT
                id_observacion_reporte_cb,
                kid_reporte_cuenta_bancaria,
                observacion,
                fecha_creacion
                FROM observaciones_reportes_cb WHERE kid_estatus !=3";
                $data['data_show']['AllowADDButton'] = false;
                $data['data_show']['reporteCB'] = '';
            }
            
            $resultado = $conexion->prepare($consultaselect);
            $resultado->execute();

            $data['data_show']['data'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
            $data['data_show']['reportes_cuentas_bancarias'] = $resultado->fetchAll(PDO::FETCH_ASSOC);
            
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