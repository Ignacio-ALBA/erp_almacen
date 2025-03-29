<?php 

$host = '127.0.0.1'; // Cambia esto si tu servidor de base de datos es diferente
$username = 'alba_erp';
$password = 'alba_erp2024;';
$dbname = 'alba_erp';

// Conexión a la base de datos
$conn = new mysqli($host, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}


$tablas = [
    [1, 'clientes'],
    [2, 'planeaciones_compras'],
    [3, 'almacen_mhe'],
    [4, 'articulos'],
    [5, 'bancos'],
    [6, 'bolsas_proyectos'],
    [7, 'cambios_planeaciones_compras'],
    [8, 'categorias'],
    [9, 'colonias'],
    [10, 'colores_interfaz'],
    [11, 'comentarios_almacenes'],
    [12, 'comentarios_recepciones'],
    [13, 'compras_cuentas_bancarias'],
    [14, 'conceptos'],
    [15, 'configuraciones'],
    [16, 'cotizaciones_compras'],
    [17, 'cuentas_bancarias'],
    [18, 'detalles_almacenes'],
    [19, 'detalles_cotizaciones_compras'],
    [20, 'detalles_cuentas_bancarias'],
    [21, 'detalles_listas_compras'],
    [22, 'detalles_ordenes_compras'],
    [23, 'detalles_planeaciones_actividades'],
    [24, 'detalles_planeaciones_compras'],
    [25, 'detalles_planeaciones_rrhh'],
    [26, 'detalles_proyectos'],
    [27, 'detalles_recepciones_compras'],
    [28, 'dimensiones'],
    [29, 'empresas'],
    [30, 'estados'],
    [31, 'estatus'],
    [32, 'formatos'],
    [33, 'formatos_reportes'],
    [34, 'internos_externos'],
    [35, 'listas_compras'],
    [36, 'marcas'],
    [37, 'modulos'],
    [38, 'municipios'],
    [39, 'ordenes_compras'],
    [40, 'paises'],
    [41, 'planeaciones_actividades'],
    [42, 'planeaciones_rrhh'],
    [43, 'presentaciones'],
    [44, 'proveedores'],
    [45, 'proyectos'],
    [46, 'recepciones_compras'],
    [47, 'regimenes_fiscales'],
    [48, 'sitios'],
    [49, 'subcategorias'],
    [50, 'sucursales'],
    [51, 'tablas'],
    [52, 'tipos_comentarios'],
    [53, 'tipos_costo'],
    [54, 'tipos_cuentas_bancarias'],
    [55, 'tipos_precio_venta'],
    [56, 'tipos_usuario'],
    [57, 'unidades'],
    [58, 'usuarios'],
    [59, 'vinculos'],
    [60, 'actividades'],
    [61,'detalles_actividades'],
    [62, 'justificaciones_actividades'],
    [63, 'evidencia_actividades'],
    [64, 'comentarios_proveedores'],
    [65,'asignacion_viaticos'],
    [66,'tipos_viaticos'],
    [67,'tiempos_entregas'],
    [68,'tipos_pagos'],
    [69,'facturas_clientes'],
    [70,'reportes_cuentas_bancarias'],
    [71,'monedas'],
    [72,'detalles_reportes_cb'],
    [73,'tipos_reportes_cb'],
    [74,'observaciones_reportes_cb'],
    [75,'ocupaciones_th'],
    [76,'adicionales_asistencias_th'],
    [77,'tipos_adicionales_th'],
    [78,'roles'],
    [79,'almacenes'],
    [80,'asistencias_th']
];

try {
    $conn->begin_transaction();

    foreach ($tablas as $tabla) {
        $permisos = ['crear','editar','ver','eliminar'];
        foreach($permisos as $permiso) {
            $sql = "INSERT INTO `permisos` (
                `permiso`, 
                `etiqueta`, 
                `descripcion`, 
                `kid_tabla`, 
                `kid_creacion`, 
                `fecha_creacion`, 
                `kid_estatus`) 
                VALUES 
                (?, ?, ?, ?, ?, current_timestamp(), '1')";
    
            $stmt = $conn->prepare($sql);
    
            $permiso = $permiso.'_'.$tabla[1];
            $etiqueta = ucwords(str_replace("_", " ", $permiso));
            $descripcion = 'Permiso para ' . $etiqueta;
            $kid_tabla = $tabla[0];
            $kid_creacion = 1; // Cambia esto según sea necesario
    
            $stmt->bind_param("sssii", $permiso, $etiqueta, $descripcion, $kid_tabla, $kid_creacion);
    
            $stmt->execute();
    
            echo "Registro insertado con éxito: " . $permiso . "\n";

        }
    }

    $conn-> commit();
} catch (Exception $e) {
    $conn->rollback();
    echo "Error al insertar registros: " . $e->getMessage() . "\n";
}


/*$permisos_list =[];
    echo "[\n";
    foreach ($tablas as $tabla) {
        $permisos = ['crear','editar','ver','eliminar'];
        $permiso_list = [];
        echo "[\n";
        foreach($permisos as $index => $permiso) {
            
                $permiso = $permiso.'_'.$tabla[1];
                $permiso_list[] = $permiso;
                echo '"'.$permiso.'"';
                if($index < 3){
                    echo ",";
                }
                echo "\n";
        }
        echo "],\n";
        
        $permisos_list[]=$permiso_list;
        //echo "  ['" . implode("','", $permiso_list) . "'],\n";
        //echo '';

    }
    echo "\n]";*/


?> 