<?php
require_once('../vendor/tecnickcom/tcpdf/tcpdf.php');

// Sanitizar la entrada del pathResult
$resultado = processRequest();

if ($resultado) {
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
    $data['data_show']['nombre_modulo'] = 'Planeación';
    switch ($pathResult) {
        case 'proveedores_cuadro_comparativo':
            $vista = 'proveedores_cuadro_comparativo';
            /*$consultaselect = "WITH detalles_cotizacion_compra_1 AS (
                SELECT kid_articulo, cantidad
                FROM detalles_cotizaciones_compras
                WHERE kid_cotizacion_compra = $id
                ),
                cotizaciones_compras_similares AS (
                SELECT c.id_cotizacion_compra, COUNT(d.id_detalle_cotizacion_compras) AS num_detalles
                FROM cotizaciones_compras c
                JOIN detalles_cotizaciones_compras d ON c.id_cotizacion_compra = d.kid_cotizacion_compra
                WHERE c.kid_proyecto = (SELECT kid_proyecto FROM cotizaciones_compras WHERE id_cotizacion_compra = $id)
                AND c.id_cotizacion_compra != $id
                GROUP BY c.id_cotizacion_compra
                HAVING COUNT(d.id_detalle_cotizacion_compras) = (SELECT COUNT(*) FROM detalles_cotizaciones_compras WHERE kid_cotizacion_compra = $id)
                ),
                detalles_cotizaciones_compras_similares AS (
                SELECT c.id_cotizacion_compra, d.kid_articulo, d.cantidad
                FROM cotizaciones_compras c
                JOIN detalles_cotizaciones_compras d ON c.id_cotizacion_compra = d.kid_cotizacion_compra
                WHERE c.id_cotizacion_compra IN (SELECT id_cotizacion_compra FROM cotizaciones_compras_similares)
                )
                SELECT 
                a.articulo AS kid_articulo,
                p.razon_social AS kid_proveedor,
                d.kid_cotizacion_compra,
                d.id_detalle_cotizacion_compras,
                d.cantidad,
                d.costo_unitario_total,
                d.costo_unitario_neto,
                d.monto_total,
                d.monto_neto,
                c.kid_tiempo_entrega,
                c.kid_tipo_pago,
                c.especificaciones_adicionales,
                c.fecha_cotizacion
                FROM cotizaciones_compras c
                JOIN detalles_cotizaciones_compras d ON c.id_cotizacion_compra = d.kid_cotizacion_compra
                JOIN proveedores p ON c.kid_proveedor = p.id_proveedor
                JOIN articulos a ON d.kid_articulo = a.id_articulo";*/

                

            /*$resultado = $conexion->prepare($consultaselect);
            $resultado->execute();
            $cotizaciones = $resultado->fetchAll(PDO::FETCH_ASSOC);
*/
$cotizaciones = [];
            $resultado = [];
            $proveedores = ['proveedor_1', 'proveedor_2', 'proveedor_3'];

            foreach ($cotizaciones as $cotizacion) {
                $kidArticulo = $cotizacion['kid_articulo'];
                $kidCotizacionCompra = $cotizacion['kid_cotizacion_compra'];
                if (!isset($resultado[$kidArticulo])) {
                    $resultado[$kidArticulo] = [
                        'kid_articulo' =>$cotizacion['kid_articulo'],
                        'cantidad' =>$cotizacion['cantidad'],
                        'proveedor_1' => null,
                        'proveedor_2' => null,
                        'proveedor_3' => null,
                    ];
                }

                // Buscar el primer proveedor disponible
                foreach ($proveedores as $i => $proveedor) {
                    if ($resultado[$kidArticulo][$proveedor] === null) {
                        $resultado[$kidArticulo][$proveedor] = [
                            'kid_proveedor' => $cotizacion['kid_proveedor'],
                            'id_detalle_cotizacion_compras' => $cotizacion['id_detalle_cotizacion_compras'],
                            'kid_cotizacion_compra' => $kidCotizacionCompra,
                            'costo_unitario_total' =>$cotizacion['costo_unitario_total'],
                            'monto_total' =>$cotizacion['monto_total'],
                            'costo_unitario_neto' =>$cotizacion['costo_unitario_neto'],
                            'monto_neto' =>$cotizacion['monto_neto'],
                            'kid_tiempo_entrega' =>$cotizacion['kid_tiempo_entrega'],
                            'kid_tipo_pago' =>$cotizacion['kid_tipo_pago'],
                            'especificaciones_adicionales' =>$cotizacion['especificaciones_adicionales'],
                            'fecha_cotizacion' =>date('d/m/Y', strtotime($cotizacion['fecha_cotizacion']))
                        ];
                        break;
                    }
                }
            }

            // Reordenar los proveedores
            foreach ($resultado as &$articulo) {
                $proveedoresOrdenados = [];
                
                foreach ($proveedores as $proveedor) {
                    if ($articulo[$proveedor] !== null) {
                        $proveedoresOrdenados[] = $articulo[$proveedor];
                    }
                }
                $articulo = [
                    'kid_articulo' => $articulo['kid_articulo'],
                    'cantidad' =>$articulo['cantidad'],
                    'proveedor_1' => $proveedoresOrdenados[0] ?? null,
                    'proveedor_2' => $proveedoresOrdenados[1] ?? null,
                    'proveedor_3' => $proveedoresOrdenados[2] ?? null,
                ];
                
                usort($proveedoresOrdenados, function($a, $b) {
                    return $a['kid_proveedor'] <=> $b['kid_proveedor'];
                });
                $articulo = [
                    'kid_articulo' => $articulo['kid_articulo'],
                    'cantidad' =>$articulo['cantidad'],
                    'proveedor_1' => $proveedoresOrdenados[0] ?? null,
                    'proveedor_2' => $proveedoresOrdenados[1] ?? null,
                    'proveedor_3' => $proveedoresOrdenados[2] ?? null,
                ];
                //debug($articulo);
            }
            $resultado = array_values($resultado);
            $data['data_show']['data'] = [];
            $data['data_show']['additionalData'] = $_POST ? $_POST : [];
            //debug($resultado);
            break;
        default:
            $vista = '404'; // Vista de error 404 si no se encuentra la ruta
            break;
    }

    $data['list_js_scripts']['formularios_script'] = $data_script;

    renderView($vista, $data);
} else {
    header("Location: /index.php");
}


?>