<?php 
function AddConditions($condiciones) {
    if ($condiciones) {
        $condiciones_str = [];
        $parametros = [];
        
        foreach ($condiciones as $columna => $valor) {
            //$condiciones_str[] = "$columna = :$columna";
            $condiciones_str[] = "$columna = $valor";
            //$parametros[$columna] = $valor;
        }
        
        if (empty($condiciones_str)) {
            return ['', []];
        } else {
            $cadena_condiciones = implode(" AND ", $condiciones_str);
            if (count($condiciones_str) == 1) {
                $cadena_condiciones = "AND " . $cadena_condiciones;
            }
            return [$cadena_condiciones, $parametros];
        }
    }
    return ['', []];
}


function CleanJson($data) {
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            // Limpiar recursivamente los valores anidados
            $data[$key] = cleanJson($value);
        }
        // Eliminar claves con valores vacíos, null, o arrays vacíos
        if ($value === null || $value === '' || (is_array($value) && empty($value))) {
            unset($data[$key]);
        }
    }
    return $data;
}
/************************************** Funciones BackEnd1 **************************************/
function GetIDPermisoByName($valor){
    global $conexion;
    $consult = "SELECT id_permiso FROM permisos WHERE kid_estatus != 3 AND permiso =:valor";
    $resultado = $conexion->prepare($consult);
    $resultado->bindParam(':valor', $valor);
    $resultado->execute();
    return $resultado->fetchColumn() ?: null;
}

function GetIDProyectoByName($valor){
    global $conexion;
    $consult = "SELECT id_proyecto FROM proyectos WHERE kid_estatus != 3 AND proyecto =:valor";
    $resultado = $conexion->prepare($consult);
    $resultado->bindParam(':valor', $valor);
    $resultado->execute();
    return $resultado->fetchColumn() ?: null;
}

function GetIDTiempoEntregaByName($valor){
    global $conexion;
    $consult = "SELECT id_tiempo_entrega  FROM tiempos_entregas WHERE kid_estatus != 3 AND tiempo_entrega =:valor";
    $resultado = $conexion->prepare($consult);
    $resultado->bindParam(':valor', $valor);
    $resultado->execute();
    return $resultado->fetchColumn() ?: null;
}
function GetIDProveedorByName($valor){
    global $conexion;
    $consult = "SELECT id_proveedor FROM proveedores WHERE kid_estatus != 3 AND proveedor =:valor";
    $resultado = $conexion->prepare($consult);
    $resultado->bindParam(':valor', $valor);
    $resultado->execute();
    return $resultado->fetchColumn() ?: null;
}
function GetIDAlmacenByName($valor){
    global $conexion;
    $consult = "SELECT id_almacen FROM almacenes WHERE kid_estatus != 3 AND almacen =:valor";
    $resultado = $conexion->prepare($consult);
    $resultado->bindParam(':valor', $valor);
    $resultado->execute();
    return $resultado->fetchColumn() ?: null;
}
function GetIDTipoPagoByName($valor){
    global $conexion;
    $consult = "SELECT id_tipo_pago   FROM tipos_pagos WHERE kid_estatus != 3 AND tipo_pago =:valor";
    $resultado = $conexion->prepare($consult);
    $resultado->bindParam(':valor', $valor);
    $resultado->execute();
    return $resultado->fetchColumn() ?: null;
}

function GetIDArticuloByName($valor){
    global $conexion;
    $consult = "SELECT id_articulo FROM articulos WHERE kid_estatus != 3 AND articulo =:valor";
    $resultado = $conexion->prepare($consult);
    $resultado->bindParam(':valor', $valor);
    $resultado->execute();
    return $resultado->fetchColumn() ?: null;
}
function GetIDArticuloByName1($nombre_articulo) {
    global $conexion;
    $consulta = "SELECT id_articulo FROM articulos WHERE articulo = :nombre_articulo AND kid_estatus != 3 LIMIT 1";
    $stmt = $conexion->prepare($consulta);
    $stmt->bindParam(':nombre_articulo', $nombre_articulo);
    $stmt->execute();
    return $stmt->fetchColumn(); // Retorna el ID del artículo
}

function GetIDProduccionByName($valor){
    global $conexion;
    $consult = "SELECT id_produccion FROM produccion WHERE kid_estatus != 3 AND produccion =:valor";
    $resultado = $conexion->prepare($consult);
    $resultado->bindParam(':valor', $valor);
    $resultado->execute();
    return $resultado->fetchColumn() ?: null;
}
function GetIDUbicacionByName($valor){
    global $conexion;
    $consult = "SELECT id_ubicacion FROM ubicacion_almacen WHERE kid_estatus != 3 AND codigo_localizacion =:valor";
    $resultado = $conexion->prepare($consult);
    $resultado->bindParam(':valor', $valor);
    $resultado->execute();
    return $resultado->fetchColumn() ?: null;
}
function GetIDCuentaBancariaByName($valor){
    global $conexion;
    $consult = "SELECT id_cuenta_bancaria FROM cuentas_bancarias WHERE kid_estatus != 3 AND cuenta_bancaria =:valor";
    $resultado = $conexion->prepare($consult);
    $resultado->bindParam(':valor', $valor);
    $resultado->execute();
    return $resultado->fetchColumn() ?: null;
}

function GetIDListaCompraByName($valor){
    global $conexion;
    $consult = "SELECT id_lista_compra FROM listas_compras WHERE kid_estatus != 3 AND lista_compra =:valor";
    $resultado = $conexion->prepare($consult);
    $resultado->bindParam(':valor', $valor);
    $resultado->execute();
    return $resultado->fetchColumn() ?: null;
}

function GetIDCotizacionComprasByName($valor){
    global $conexion;
    $consult = "SELECT id_cotizacion_compra FROM cotizaciones_compras WHERE kid_estatus != 3 AND cotizacion_compras =:valor";
    $resultado = $conexion->prepare($consult);
    $resultado->bindParam(':valor', $valor);
    $resultado->execute();
    return $resultado->fetchColumn() ?: null;
}

function GetIDOrdenComprasByName($valor){
    global $conexion;
    $consult = "SELECT id_orden_compras FROM ordenes_compras WHERE kid_estatus != 3 AND orden_compras =:valor";
    $resultado = $conexion->prepare($consult);
    $resultado->bindParam(':valor', $valor);
    $resultado->execute();
    return $resultado->fetchColumn() ?: null;
}

function GetIDEstatusByName($valor){
    global $conexion;
    $consult = "SELECT id_estatus FROM estatus WHERE kid_estatus != 3 AND estatus =:valor";
    $resultado = $conexion->prepare($consult);
    $resultado->bindParam(':valor', $valor);
    $resultado->execute();
    return $resultado->fetchColumn() ?: null;
}

function GetIDAlmacenesByName($valor){
    global $conexion;
    $consulta = "SELECT id_almacen FROM almacenes WHERE kid_estatus != 3 AND almacen =:valor";
    $resultado = $conexion->prepare($consulta);
    $resultado->bindParam(':valor', $valor);
    $resultado->execute();
    return $resultado->fetchColumn() ?: null;
}

function GetIDUsuariosByName($valor){
    global $conexion;
    $consulta = "SELECT id_colaborador FROM colaboradores WHERE kid_estatus != 3 AND email =:valor";
    $resultado = $conexion->prepare($consulta);
    $resultado->bindParam(':valor', $valor);
    $resultado->execute();
    return $resultado->fetchColumn() ?: null;
}
function GetNameColaboradoressByEmail($valor){
    global $conexion;
    $consulta = "SELECT CONCAT(nombre,' ',apellido_paterno,' ',apellido_materno) FROM colaboradores WHERE kid_estatus != 3 AND email =:valor";
    $resultado = $conexion->prepare($consulta);
    $resultado->bindParam(':valor', $valor);
    $resultado->execute();
    return $resultado->fetchColumn() ?: null;
}

function GetIDRecepcionCompraByName($valor){
    global $conexion;
    $consulta = "SELECT id_recepcion_compras FROM recepciones_compras WHERE kid_estatus != 3 AND recepcion_compras =:valor";
    $resultado = $conexion->prepare($consulta);
    $resultado->bindParam(':valor', $valor);
    $resultado->execute();
    return $resultado->fetchColumn() ?: null;
}

function GetIDDettalleRecepcionCompraByName($valor){
    global $conexion;
    $consulta = "SELECT id_detalle_recepcion_compras FROM detalles_recepciones_compras WHERE kid_estatus != 3 AND recepcion_compras =:valor";
    $resultado = $conexion->prepare($consulta);
    $resultado->bindParam(':valor', $valor);
    $resultado->execute();
    return $resultado->fetchColumn() ?: null;
}
function GetIDTipoComentarioByName($valor){
    global $conexion;
    $consulta = "SELECT id_tipo_comentario FROM tipos_comentarios WHERE kid_estatus != 3 AND tipo_comentario =:valor";
    $resultado = $conexion->prepare($consulta);
    $resultado->bindParam(':valor', $valor);
    $resultado->execute();
    return $resultado->fetchColumn() ?: null;
}

function GetIDBolsaProyectosByName($valor){
    global $conexion;
    $consulta = "SELECT id_bolsa_proyecto FROM bolsas_proyectos WHERE kid_estatus != 3 AND bolsa_proyecto =:valor";
    $resultado = $conexion->prepare($consulta);
    $resultado->bindParam(':valor', $valor);
    $resultado->execute();
    return $resultado->fetchColumn() ?: null;
}

function GetIDClienteByName($valor){
    global $conexion;
    $consulta = "SELECT id_cliente FROM clientes WHERE kid_estatus != 3 AND nombre =:valor";
    $resultado = $conexion->prepare($consulta);
    $resultado->bindParam(':valor', $valor);
    $resultado->execute();
    return $resultado->fetchColumn() ?: null;
}


function GetIDInternosExternosByName($valor){
    global $conexion;
    $consulta = "SELECT id_internos_externos FROM internos_externos WHERE kid_estatus != 3 AND internos_externos =:valor";
    $resultado = $conexion->prepare($consulta);
    $resultado->bindParam(':valor', $valor);
    $resultado->execute();
    return $resultado->fetchColumn() ?: null;
}

function GetIDTiposCostosByName($valor){
    global $conexion;
    $consulta = "SELECT id_tipo_costo FROM tipos_costo WHERE kid_estatus != 3 AND tipo_costo =:valor";
    $resultado = $conexion->prepare($consulta);
    $resultado->bindParam(':valor', $valor);
    $resultado->execute();
    return $resultado->fetchColumn() ?: null;
}

function GetIDModulosByName($valor){
    global $conexion;
    $consulta = "SELECT id_modulo FROM modulos WHERE kid_estatus != 3 AND modulo =:valor";
    $resultado = $conexion->prepare($consulta);
    $resultado->bindParam(':valor', $valor);
    $resultado->execute();
    return $resultado->fetchColumn() ?: null;
}

function GetIDTablaNameByName($valor){
    global $conexion;
    $consulta = "SELECT id_tabla FROM tablas WHERE kid_estatus != 3 AND tabla_name =:valor";
    $resultado = $conexion->prepare($consulta);
    $resultado->bindParam(':valor', $valor);
    $resultado->execute();
    return $resultado->fetchColumn() ?: null;
}


function GetIDTipoViaticoByName($valor){
    global $conexion;
    $consulta = "SELECT id_tipo_viatico FROM tipos_viaticos WHERE kid_estatus != 3 AND tipo_viatico =:valor";
    $resultado = $conexion->prepare($consulta);
    $resultado->bindParam(':valor', $valor);
    $resultado->execute();
    return $resultado->fetchColumn() ?: null;
}

function GetIDDetalleActividadByName($valor){
    global $conexion;
    $consulta = "SELECT id_detalle_actividad FROM detalles_actividades WHERE kid_estatus != 3 AND actividad =:valor";
    $resultado = $conexion->prepare($consulta);
    $resultado->bindParam(':valor', $valor);
    $resultado->execute();
    return $resultado->fetchColumn() ?: null;
}

function GetIDTiposAdicionalesByName($valor){
    global $conexion;
    $consulta = "SELECT id_tipo_adicional_th FROM tipos_adicionales_th WHERE kid_estatus != 3 AND tipo_adicional_th =:valor";
    $resultado = $conexion->prepare($consulta);
    $resultado->bindParam(':valor', $valor);
    $resultado->execute();
    return $resultado->fetchColumn() ?: null;
}

function GetIDMonedasByName($valor){
    global $conexion;
    $consulta = "SELECT id_moneda FROM monedas WHERE kid_estatus != 3 AND moneda =:valor";
    $resultado = $conexion->prepare($consulta);
    $resultado->bindParam(':valor', $valor);
    $resultado->execute();
    return $resultado->fetchColumn() ?: null;
}

function GetIDTipoReporteByName($valor){
    global $conexion;
    $consulta = "SELECT id_tipo_reporte_cb FROM tipos_reportes_cb WHERE kid_estatus != 3 AND tipo_reporte_cb =:valor";
    $resultado = $conexion->prepare($consulta);
    $resultado->bindParam(':valor', $valor);
    $resultado->execute();
    return $resultado->fetchColumn() ?: null;
}

function GetTipoActividadByName($valor){
    $data = [
        'Actividad Normal'=>1,
        'Actividad Extraordinaria'=>2,
    ];
    return $data[$valor];
}

/************************************** Fin Funciones BackEnd1 **************************************/

/************************************** Funciones BackEnd **************************************/
function GetUnidadListById(){
    global $conexion;
    $consult = "SELECT id_unidad, unidad FROM unidades WHERE kid_estatus != 3";
    $resultado = $conexion->prepare($consult);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_column($data, 'id_unidad', 'unidad');
    return $data;
}

function GetMarcasListById(){
    global $conexion;
    $consult = "SELECT id_marca, marca FROM marcas WHERE kid_estatus != 3";
    $resultado = $conexion->prepare($consult);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_column($data, 'id_marca', 'marca');
    return $data;
}

function GetCategoriasListById(){
    global $conexion;
    $consult = "SELECT id_categoria, categoria FROM categorias WHERE kid_estatus != 3";
    $resultado = $conexion->prepare($consult);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_column($data, 'id_categoria', 'categoria');
    return $data;
}

function GetSubcategoriasListById(){
    global $conexion;
    $consult = "SELECT id_subcategoria, subcategoria FROM subcategorias WHERE kid_estatus != 3";
    $resultado = $conexion->prepare($consult);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_column($data, 'id_subcategoria', 'subcategoria');
    return $data;
}

function GetFormatosListById(){
    global $conexion;
    $consult = "SELECT id_formato, formato FROM formatos WHERE kid_estatus != 3";
    $resultado = $conexion->prepare($consult);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_column($data, 'id_formato', 'formato');
    return $data;
}

function GetPresentacionesListById(){
    global $conexion;
    $consult = "SELECT id_presentacion, presentacion FROM presentaciones WHERE kid_estatus != 3";
    $resultado = $conexion->prepare($consult);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_column($data, 'id_presentacion', 'presentacion');
    return $data;
}

function GetDimensionesListById(){
    global $conexion;
    $consult = "SELECT id_dimension, dimension FROM dimensiones WHERE kid_estatus != 3";
    $resultado = $conexion->prepare($consult);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_column($data, 'id_dimension', 'dimension');
    return $data;
}

function GetPaisesListById(){
    global $conexion;
    $consulta = "SELECT id_pais, pais FROM paises WHERE kid_estatus != 3";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_column($data, 'id_pais', 'pais');
    return $data;
}

function GetAlmacenesListById(){
    global $conexion;
    $consulta = "SELECT id_almacen, almacen FROM almacenes WHERE kid_estatus != 3";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_column($data, 'id_almacen', 'almacen');
    return $data;
}

function GetArticulosListById(){
    global $conexion;
    $consulta = "SELECT id_articulo, articulo FROM articulos WHERE kid_estatus != 3";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_column($data, 'id_articulo', 'articulo');
    return $data;
}


function GetEstadosListById(){
    global $conexion;
    $consulta = "SELECT id_estados, estado FROM estados WHERE kid_estatus != 3";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_column($data, 'id_estados', 'estado');
    return $data;
}

function GetSucursalesListById(){
    global $conexion;

    $consulta = "SELECT id_sucursal, sucursal FROM sucursales WHERE kid_estatus != 3";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_column($data, 'id_sucursal', 'sucursal');
    return $data;
}

function GetTiposComentariosListById(){
    global $conexion;

    $consulta = "SELECT id_tipo_comentario, tipo_comentario FROM tipos_comentarios WHERE kid_estatus != 3";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_column($data, 'id_tipo_comentario', 'tipo_comentario');
    return $data;
}

function GetUsuariosListById(){
    global $conexion;
    $consulta = "SELECT id_colaborador, email FROM colaboradores WHERE kid_estatus != 3";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_column($data, 'id_colaborador', 'email');
    return $data;
}

function GetTiposUsuariosListById(){
    global $conexion;
    $consulta = "SELECT id_tipo_usuario, tipo_usuario FROM tipos_usuario WHERE kid_estatus != 3";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_column($data, 'id_tipo_usuario', 'tipo_usuario');
    return $data;
}

function GetClientesListById(){
    global $conexion;
    $consulta = "SELECT id_cliente, nombre FROM clientes WHERE kid_estatus != 3";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_column($data, 'id_cliente', 'nombre');
    return $data;
}
function GetBolsaProyectosListById(){
    global $conexion;
    $consulta = "SELECT id_bolsa_proyecto, bolsa_proyecto FROM bolsas_proyectos WHERE kid_estatus != 3";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_column($data, 'id_bolsa_proyecto', 'bolsa_proyecto');
    return $data;
}

function GetProyectosListById(){
    global $conexion;
    $consulta = "SELECT id_proyecto , proyecto  FROM proyectos WHERE kid_estatus != 3";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_column($data, 'id_proyecto', 'proyecto');
    return $data;
}

function GetTipoContratoListById(){
    $data = [
        'Indeterminado'=>1,
        'Determinado'=>2
    ];
    return $data;
}

function GetEstadoCivilListById(){
    $data = [
        'Soltero(a)'=>1,
        'Casado(a)'=>2,
        'Conviviente civil'=>3,
        'Separado(a) judicialmente'=>4,
        'Divorciado(a)'=>5,
        'Viudo(a)'=>6
    ];
    return $data;
}

function GetTipoActividadListById(){
    $data = [
        'Actividad Normal'=>1,
        'Actividad Extraordinaria'=>2,
    ];
    return $data;
}

function GetBancosListById(){
    global $conexion;
    $consulta = "SELECT id_banco, banco  FROM bancos WHERE kid_estatus != 3";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_column($data, 'id_banco', 'banco');
    return $data;
}

function GetTiposCuentasBancariasListById(){
    global $conexion;
    $consulta = "SELECT id_tipo_cuenta_bancaria, tipo_cuenta_bancaria  FROM tipos_cuentas_bancarias WHERE kid_estatus != 3";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_column($data, 'id_tipo_cuenta_bancaria', 'tipo_cuenta_bancaria');
    return $data;
}

function GetCuentasBancariasListById(){
    global $conexion;
    $consulta = "SELECT id_cuenta_bancaria, cuenta_bancaria  FROM cuentas_bancarias WHERE kid_estatus != 3";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_column($data, 'id_cuenta_bancaria', 'cuenta_bancaria');
    return $data;
}

function GetListaComprasListById(){
    global $conexion;
    $consulta = "SELECT id_lista_compra, lista_compra FROM listas_compras WHERE kid_estatus != 3";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_column($data, 'id_lista_compra', 'lista_compra');
    return $data;
}

function GetProveedoresListById(){
    global $conexion;
    $consulta = "SELECT id_proveedor, proveedor FROM proveedores WHERE kid_estatus != 3";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_column($data, 'id_proveedor', 'proveedor');
    return $data;
}
/************************************** Fin Funciones BackEnd **************************************/

/************************************** Funciones FrontEnd **************************************/
function GetUnidadListForSelect($condiciones  = []){
    global $conexion;
    list($condiciones_str, $parametros) = AddConditions($condiciones);
    $consult = "SELECT id_unidad, unidad FROM unidades WHERE kid_estatus != 3 $condiciones_str";
    $resultado = $conexion->prepare($consult);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_map(fn($item) => [
        'valor' => $item['marca'],
        'pordefecto' => $item['pordefecto']
    ], $data);
    return $data;
}

function GetMarcasListForSelect($condiciones  = []){
    global $conexion;
    list($condiciones_str, $parametros) = AddConditions($condiciones);
    $consult = "SELECT marca,pordefecto FROM marcas WHERE kid_estatus != 3  $condiciones_str ORDER BY pordefecto DESC, orden ASC, marca ASC";
    $resultado = $conexion->prepare($consult);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_map(fn($item) => [
        'valor' => $item['marca'],
        'pordefecto' => $item['pordefecto']
    ], $data);
    return $data;
}

function GetCategoriasListForSelect($condiciones  = []){
    global $conexion;
    list($condiciones_str, $parametros) = AddConditions($condiciones);
    $consult = "SELECT categoria,pordefecto FROM categorias WHERE kid_estatus != 3 $condiciones_str ORDER BY pordefecto DESC, orden ASC, categoria ASC";
    $resultado = $conexion->prepare($consult);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_map(fn($item) => [
        'valor' => $item['categoria'],
        'pordefecto' => $item['pordefecto']
    ], $data);
    return $data;
}

function GetSubcategoriasListForSelect($condiciones  = []){
    global $conexion;
    list($condiciones_str, $parametros) = AddConditions($condiciones);
    $consult = "SELECT subcategoria,pordefecto FROM subcategorias WHERE kid_estatus != 3 $condiciones_str ORDER BY pordefecto DESC, orden ASC, subcategoria ASC";
    $resultado = $conexion->prepare($consult);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_map(fn($item) => [
        'valor' => $item['subcategoria'],
        'pordefecto' => $item['pordefecto']
    ], $data);
    return $data;
}

function GetFormatosListForSelect($condiciones  = []){
    global $conexion;
    list($condiciones_str, $parametros) = AddConditions($condiciones);
    $consult = "SELECT formato,pordefecto FROM formatos WHERE kid_estatus != 3 $condiciones_str ORDER BY pordefecto DESC, orden ASC, formato ASC";
    $resultado = $conexion->prepare($consult);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_map(fn($item) => [
        'valor' => $item['formato'],
        'pordefecto' => $item['pordefecto']
    ], $data);
    return $data;
}

function GetPresentacionesListForSelect($condiciones  = []){
    global $conexion;
    list($condiciones_str, $parametros) = AddConditions($condiciones);
    $consult = "SELECT presentacion,pordefecto FROM presentaciones WHERE kid_estatus != 3 $condiciones_str ORDER BY pordefecto DESC, orden ASC, presentacion ASC";
    $resultado = $conexion->prepare($consult);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_map(fn($item) => [
        'valor' => $item['presentacion'],
        'pordefecto' => $item['pordefecto']
    ], $data);
    return $data;
}

function GetDimensionesListForSelect($condiciones  = []){
    global $conexion;
    list($condiciones_str, $parametros) = AddConditions($condiciones);
    $consulta = "SELECT dimension,pordefecto FROM dimensiones WHERE kid_estatus != 3 $condiciones_str ORDER BY pordefecto DESC, orden ASC, dimension ASC";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_map(fn($item) => [
        'valor' => $item['dimension'],
        'pordefecto' => $item['pordefecto']
    ], $data);
    return $data;
}

function GetPaisesListForSelect($condiciones  = []){
    global $conexion;
    list($condiciones_str, $parametros) = AddConditions($condiciones);
    $consulta = "SELECT pais FROM paises WHERE kid_estatus != 3 $condiciones_str ORDER BY pordefecto DESC, orden ASC, pais ASC";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_map(fn($item) => [
        'valor' => $item['pais'],
        'pordefecto' => 0
    ], $data);
    return $data;
}

function GetAlmacenesListForSelect($condiciones  = []){
    global $conexion;
    list($condiciones_str, $parametros) = AddConditions($condiciones);
    $consulta = "SELECT almacen FROM almacenes WHERE kid_estatus != 3 $condiciones_str";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_map(fn($item) => [
        'valor'=> $item['almacen'],
        'pordefecto' => 0
    ], $data);
    return $data;
}

function GetArticulosListForSelect2($condiciones  = []){
    global $conexion;
    list($condiciones_str, $parametros) = AddConditions($condiciones);
    $consulta = "SELECT id_articulo, articulo FROM articulos WHERE kid_estatus != 3 $condiciones_str";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_map(fn($item) => [
        'valor' => $item['id_articulo'],
        'text' => $item['articulo'],
        'pordefecto' => 0
    ], $data);
    return $data;
}
function GetArticulosListForSelect($condiciones  = []){
    global $conexion;
    list($condiciones_str, $parametros) = AddConditions($condiciones);
    $consulta = "SELECT id_articulo, articulo FROM articulos WHERE kid_estatus != 3 $condiciones_str";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_map(fn($item) => [
        'valor' => $item['id_articulo'],
        'text' => $item['articulo'],
        'pordefecto' => 0
    ], $data);
    return $data;
}
function GetProduccionesListForSelect($condiciones  = []){
    global $conexion;
    // Construir las condiciones y parámetros
    list($condiciones_str, $parametros) = AddConditions($condiciones);

    // Consulta para obtener las producciones activas
    $consulta = "SELECT id_produccion, fecha_produccion 
                 FROM produccion 
                 WHERE kid_estatus != 3 $condiciones_str";

    $resultado = $conexion->prepare($consulta);
    $resultado->execute($parametros);

    // Mapear los datos al formato esperado
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_map(fn($item) => [
        'valor' => $item['id_produccion'],
        'text' => date('Y-m-d H:i:s', strtotime($item['fecha_produccion'])), // Formatear la fecha
        'pordefecto' => 0
    ], $data);

    return $data;
}

function GetUbicacionListForSelect($condiciones  = []){
    global $conexion;
    // Construir las condiciones y parámetros
    list($condiciones_str, $parametros) = AddConditions($condiciones);

    // Consulta para obtener las producciones activas
    $consulta = "SELECT id_ubicacion, codigo_localizacion 
                 FROM ubicacion_almacen
                 WHERE kid_estatus != 3 $condiciones_str";

    $resultado = $conexion->prepare($consulta);
    $resultado->execute($parametros);

    // Mapear los datos al formato esperado
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_map(fn($item) => [
        'valor' => $item['id_ubicacion'],
        'text' => $item['codigo_localizacion'],
        'pordefecto' => 0
    ], $data);

    return $data;
}

function GetEstadosListForSelect($condiciones  = []){
    global $conexion;
    list($condiciones_str, $parametros) = AddConditions($condiciones);
    $consulta = "SELECT e.estado,p.simbolo ,e.pordefecto  FROM estados e JOIN paises p ON e.kid_pais = p.id_pais WHERE e.kid_estatus != 3 $condiciones_str ORDER BY e.pordefecto DESC, e.orden ASC,  e.estado ASC";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_map(fn($item) => [
        'valor'=> $item['estado'],
        'text' => trim(implode('-', array_filter([$item['estado'], $item['simbolo']]))),
        'pordefecto' => $item['pordefecto']
    ], $data);
    return $data;
}


function GetSucursalesListForSelect($condiciones  = []){
    global $conexion;
    list($condiciones_str, $parametros) = AddConditions($condiciones);
    $consulta = "SELECT sucursal FROM sucursales WHERE kid_estatus != 3 $condiciones_str";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_map(fn($item) => [
        'valor' => $item['sucursal'],
        'pordefecto' => 0
    ], $data);
    return $data;
}

function GetTiposComentariosListForSelect($condiciones  = []){
    global $conexion;
    list($condiciones_str, $parametros) = AddConditions($condiciones);
    $consulta = "SELECT tipo_comentario FROM tipos_comentarios WHERE kid_estatus != 3 $condiciones_str";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_map(fn($item) => [
        'valor' => $item['tipo_comentario'],
        'pordefecto' => 0
    ], $data);
    return $data;
}

function GetUsuariosListForSelect($condiciones  = []){
    global $conexion;
    list($condiciones_str, $parametros) = AddConditions($condiciones);
    $consulta = "SELECT email, nombre,apellido_paterno, apellido_materno  FROM colaboradores WHERE kid_estatus != 3 $condiciones_str ORDER BY nombre ASC";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_map(fn($item) => [
        'valor'=> $item['email'],
        'text' => trim(implode(' ', array_filter([$item['nombre'], $item['apellido_paterno'], $item['apellido_materno']]))),
        'pordefecto' => 0
    ], $data);
    return $data;
}

function GetTiposUsuariosListForSelect($condiciones  = []){
    global $conexion;
    list($condiciones_str, $parametros) = AddConditions($condiciones);
    $consulta = "SELECT tipo_usuario,pordefecto FROM tipos_usuario WHERE kid_estatus != 3 $condiciones_str ORDER BY pordefecto DESC, tipo_usuario ASC";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_map(fn($item) => [
        'valor' => $item['tipo_usuario'],
        'pordefecto' => $item['pordefecto']
    ], $data);
    return $data;
}

function GetRegimenesListForSelect($condiciones  = []){
    global $conexion;
    list($condiciones_str, $parametros) = AddConditions($condiciones);
    $consulta = "SELECT regimen, descripcion  FROM regimenes_fiscales WHERE kid_estatus != 3 $condiciones_str ORDER BY pordefecto DESC, regimen ASC";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_map(fn($item) => [
        'valor'=> $item['regimen'],
        'text' => trim(implode('-', array_filter([$item['regimen'], $item['descripcion']]))),
        'pordefecto' => 0
    ], $data);
    return $data;
}

function GetClientesListForSelect($condiciones  = []){
    global $conexion;
    list($condiciones_str, $parametros) = AddConditions($condiciones);
    $consulta = "SELECT nombre FROM clientes WHERE kid_estatus != 3  $condiciones_str ORDER BY nombre ASC";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_map(fn($item) => [
        'valor'=> $item['nombre'],
        'pordefecto' => 0
    ], $data);
    return $data;
}

function GetReportesCBListForSelect($condiciones  = []){
    global $conexion;
    list($condiciones_str, $parametros) = AddConditions($condiciones);
    $consulta = "SELECT id_reporte_cuenta_bancaria  FROM reportes_cuentas_bancarias WHERE kid_estatus != 3  $condiciones_str ORDER BY nombre ASC";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_map(fn($item) => [
        'valor'=> $item['id_reporte_cuenta_bancaria'],
        'pordefecto' => 0
    ], $data);
    return $data;
}

function GetBolsaProyectosListForSelect($condiciones  = []){
    global $conexion;
    list($condiciones_str, $parametros) = AddConditions($condiciones);
    $consulta = "SELECT bolsa_proyecto FROM bolsas_proyectos WHERE kid_estatus != 3 $condiciones_str ORDER BY bolsa_proyecto ASC";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_map(fn($item) => [
        'valor'=> $item['bolsa_proyecto'],
        'pordefecto' => 0
    ], $data);
    return $data;
}

function GetProyectosListForSelect($condiciones  = []){
    global $conexion;
    list($condiciones_str, $parametros) = AddConditions($condiciones);
    $consulta = "SELECT proyecto FROM proyectos WHERE kid_estatus != 3 $condiciones_str ORDER BY proyecto ASC";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_map(fn($item) => [
        'valor'=> $item['proyecto'],
        'pordefecto' => 0
    ], $data);
    return $data;
}

function GetTipoContratoListForSelect(){
    $data = [
        ['valor'=>'Indeterminado','pordefecto'=>0],
        ['valor'=>'Determinado','pordefecto'=>0]
    ];
    return $data;
}

function GetEstadoCivilListForSelect(){
    $data = [
        ['valor'=>'Soltero(a)','pordefecto'=>0],
        ['valor'=>'Casado(a)','pordefecto'=>0],
        ['valor'=>'Conviviente civil','pordefecto'=>0],
        ['valor'=>'Separado(a) judicialmente','pordefecto'=>0],
        ['valor'=>'Divorciado(a)','pordefecto'=>0],
        ['valor'=>'Viudo(a)','pordefecto'=>0]
    ];
    return $data;
}
function GetTiposActividadesListForSelect(){
    $data = [
        ['valor'=>'Actividad Normal','pordefecto'=>0],
        ['valor'=>'Actividad Extraordinaria','pordefecto'=>0],
    ];
    return $data;
}

function GetBancosListForSelect($condiciones  = []){
    global $conexion;
    list($condiciones_str, $parametros) = AddConditions($condiciones);
    $consulta = "SELECT banco, pordefecto FROM bancos WHERE kid_estatus != 3 $condiciones_str ORDER BY banco ASC";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_map(fn($item) => [
        'valor'=> $item['banco'],
        'pordefecto' => $item['pordefecto']
    ], $data);
    return $data;
}

function GetTiposCuentasBancariasListForSelect($condiciones  = []){
    global $conexion;
    list($condiciones_str, $parametros) = AddConditions($condiciones);
    $consulta = "SELECT tipo_cuenta_bancaria, pordefecto FROM tipos_cuentas_bancarias WHERE kid_estatus != 3 $condiciones_str ORDER BY tipo_cuenta_bancaria ASC";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_map(fn($item) => [
        'valor'=> $item['tipo_cuenta_bancaria'],
        'pordefecto' => $item['pordefecto']
    ], $data);
    return $data;
}

function GetCuentasBancariasListForSelect($condiciones  = []){
    global $conexion;
    list($condiciones_str, $parametros) = AddConditions($condiciones);
    $consulta = "SELECT cuenta_bancaria FROM cuentas_bancarias WHERE kid_estatus != 3 $condiciones_str ORDER BY cuenta_bancaria ASC";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_map(fn($item) => [
        'valor'=> $item['cuenta_bancaria'],
        'pordefecto' => 0
    ], $data);
    return $data;
}

function GetListaComprasForSelect($condiciones  = []){
    global $conexion;
    list($condiciones_str, $parametros) = AddConditions($condiciones);
    $consulta = "SELECT lista_compra FROM listas_compras WHERE kid_estatus != 3 $condiciones_str ORDER BY lista_compra ASC";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_map(fn($item) => [
        'valor'=> $item['lista_compra'],
        'pordefecto' => 0
    ], $data);
    return $data;
}

function GetProvedoresListForSelect($condiciones  = []){
    global $conexion;
    list($condiciones_str, $parametros) = AddConditions($condiciones);
    $consulta = "SELECT pordefecto, proveedor  FROM proveedores WHERE kid_estatus != 3 $condiciones_str ORDER BY pordefecto DESC,orden ASC, proveedor ASC";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_map(fn($item) => [
        'valor'=> $item['proveedor'],
        'pordefecto' => $item['pordefecto']
    ], $data);
    return $data;
}

function GetCotizacionesListForSelect($condiciones  = []){
    global $conexion;
    list($condiciones_str, $parametros) = AddConditions($condiciones);
    $consulta = "SELECT cotizacion_compras FROM cotizaciones_compras WHERE kid_estatus != 3 $condiciones_str ORDER BY cotizacion_compras ASC";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_map(fn($item) => [
        'valor'=> $item['cotizacion_compras'],
        'pordefecto' => 0
    ], $data);
    return $data;
}

function GetEstatusLabels($condiciones  = []){
    global $conexion;
    list($condiciones_str, $parametros) = AddConditions($condiciones);
    $consult = "SELECT id_estatus, estatus_color, estatus FROM estatus WHERE kid_estatus != 3 $condiciones_str";
    $resultado = $conexion->prepare($consult);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    
    // Crear un array para almacenar los resultados
    $estatus = [];
    
    // Iterar sobre los resultados y crear las etiquetas
    foreach ($data as $fila) {
        $estatus[$fila['id_estatus']] = CreateBadge([
            'etiqueta' => $fila['estatus'],
            'style' => 'background-color:' . $fila['estatus_color'] . ';' // Cambiar a $fila
        ]);
    }
    
    return $estatus;
}

function GetEstatusListForSelect($condiciones  = []){
    global $conexion;
    list($condiciones_str, $parametros) = AddConditions($condiciones);
    $consulta = "SELECT estatus FROM estatus WHERE kid_estatus != 3 $condiciones_str AND id_estatus NOT IN (1, 2, 3) ORDER BY estatus ASC;";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_map(fn($item) => [
        'valor'=> $item['estatus'],
        'pordefecto' => 0
    ], $data);
    return $data;
}

function GetEstatusList($condiciones  = []){
    global $conexion;
    list($condiciones_str, $parametros) = AddConditions($condiciones);
    $consulta = "SELECT id_estatus, estatus FROM estatus WHERE kid_estatus != 3 $condiciones_str AND id_estatus NOT IN (1, 2, 3) ORDER BY estatus ASC;";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_column($data, 'estatus', 'id_estatus');
    return $data;
}

function GetTiemposEntregaListForSelect($condiciones  = []){
    global $conexion;
    list($condiciones_str, $parametros) = AddConditions($condiciones);
    $consulta = "SELECT tiempo_entrega, pordefecto  FROM tiempos_entregas WHERE kid_estatus != 3 $condiciones_str ORDER BY tiempo_entrega ASC;";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_map(fn($item) => [
        'valor'=> $item['tiempo_entrega'],
        'pordefecto' => $item['pordefecto']
    ], $data);
    return $data;
}

function GetTiposPagoListForSelect($condiciones  = []){
    global $conexion;
    list($condiciones_str, $parametros) = AddConditions($condiciones);
    $consulta = "SELECT tipo_pago, pordefecto  FROM tipos_pagos WHERE kid_estatus != 3 $condiciones_str ORDER BY tipo_pago ASC;";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_map(fn($item) => [
        'valor'=> $item['tipo_pago'],
        'pordefecto' => $item['pordefecto']
    ], $data);
    return $data;
}

function GetPlaneacionesComprasListForSelect($condiciones  = []){
    global $conexion;
    list($condiciones_str, $parametros) = AddConditions($condiciones);
    $consulta = "SELECT id_planeacion_compras  FROM planeaciones_compras WHERE kid_estatus != 3 $condiciones_str ORDER BY id_planeacion_compras ASC;";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_map(fn($item) => [
        'valor'=> $item['id_planeacion_compras'],
        'pordefecto' => 0
    ], $data);
    return $data;
}

function GetPlaneacionesRRHHListForSelect($condiciones  = []){
    global $conexion;
    list($condiciones_str, $parametros) = AddConditions($condiciones);
    $consulta = "SELECT id_planeacion_rrhh FROM planeaciones_rrhh WHERE kid_estatus != 3 $condiciones_str ORDER BY id_planeacion_rrhh ASC;";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_map(fn($item) => [
        'valor'=> $item['id_planeacion_rrhh'],
        'pordefecto' => 0
    ], $data);
    return $data;
}

function GetPlaneacionesActividadesListForSelect($condiciones  = []){
    global $conexion;
    list($condiciones_str, $parametros) = AddConditions($condiciones);
    $consulta = "SELECT id_planeacion_actividad FROM planeaciones_actividades WHERE kid_estatus != 3 $condiciones_str ORDER BY id_planeacion_actividad ASC;";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_map(fn($item) => [
        'valor'=> $item['id_planeacion_actividad'],
        'pordefecto' => 0
    ], $data);
    return $data;
}

function GetInternosExternosListForSelect($condiciones  = []){
    global $conexion;
    list($condiciones_str, $parametros) = AddConditions($condiciones);
    $consulta = "SELECT internos_externos FROM internos_externos WHERE kid_estatus != 3 $condiciones_str ORDER BY internos_externos ASC;";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_map(fn($item) => [
        'valor'=> $item['internos_externos'],
        'pordefecto' => 0
    ], $data);
    return $data;
}

function GetTiposCostosListForSelect($condiciones  = []){
    global $conexion;
    list($condiciones_str, $parametros) = AddConditions($condiciones);
    $consulta = "SELECT tipo_costo FROM tipos_costo WHERE kid_estatus != 3 $condiciones_str ORDER BY tipo_costo ASC;";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_map(fn($item) => [
        'valor'=> $item['tipo_costo'],
        'pordefecto' => 0
    ], $data);
    return $data;
}

function GetModulosListForSelect($condiciones  = []){
    global $conexion;
    list($condiciones_str, $parametros) = AddConditions($condiciones);
    $consulta = "SELECT modulo FROM modulos WHERE kid_estatus != 3 $condiciones_str ORDER BY modulo ASC";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_map(fn($item) => [
        'valor'=> $item['modulo'],
        'pordefecto' => 0
    ], $data);
    return $data;
}

function GetTiposViaticosListForSelect($condiciones  = []){
    global $conexion;
    list($condiciones_str, $parametros) = AddConditions($condiciones);
    $consulta = "SELECT tipo_viatico, pordefecto FROM tipos_viaticos WHERE kid_estatus != 3 $condiciones_str ORDER BY tipo_viatico ASC, pordefecto DESC,orden ASC;";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_map(fn($item) => [
        'valor'=> $item['tipo_viatico'],
        'pordefecto' => $item['pordefecto']
    ], $data);
    return $data;
}

function GetDetallesActividadesListForSelect($condiciones  = [],$id = null){
    global $conexion;
    list($condiciones_str, $parametros) = AddConditions($condiciones);
    if($id != null){
        $consulta = "SELECT actividad FROM detalles_actividades WHERE kid_estatus != 3 AND kid_actividad = $id ORDER BY actividad;";
    }else{
        $consulta = "SELECT actividad FROM detalles_actividades WHERE kid_estatus != 3 $condiciones_str ORDER BY actividad;";
    }
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_map(fn($item) => [
        'valor'=> $item['actividad'],
        'pordefecto' => 0
    ], $data);
    return $data;
}

function GetTiposAdicionalesListForSelect($condiciones  = []){
    global $conexion;
    list($condiciones_str, $parametros) = AddConditions($condiciones);
    $consulta = "SELECT tipo_adicional_th FROM tipos_adicionales_th WHERE kid_estatus != 3 $condiciones_str ORDER BY tipo_adicional_th ASC;";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_map(fn($item) => [
        'valor'=> $item['tipo_adicional_th'],
        'pordefecto' => 0
    ], $data);
    return $data;
}

function GetMonedasListForSelect($condiciones  = []){
    global $conexion;
    list($condiciones_str, $parametros) = AddConditions($condiciones);
    $consulta = "SELECT moneda, pordefecto FROM monedas WHERE kid_estatus != 3 $condiciones_str ORDER BY monedas ASC, orden ASC, pordefecto DESC;";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_map(fn($item) => [
        'valor'=> $item['moneda'],
        'pordefecto' => $item['pordefecto']
    ], $data);
    return $data;
}

function GetTiposReporteCBListForSelect($condiciones  = []){
    global $conexion;
    list($condiciones_str, $parametros) = AddConditions($condiciones);
    $consulta = "SELECT tipo_reporte_cb, pordefecto FROM tipos_reportes_cb WHERE kid_estatus != 3 $condiciones_str ORDER BY tipo_reporte_cb ASC, orden ASC, pordefecto DESC;";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $data = array_map(fn($item) => [
        'valor'=> $item['tipo_reporte_cb'],
        'pordefecto' => $item['pordefecto']
    ], $data);
    return $data;
}

/************************************** Fin Funciones FrontEnd **************************************/

function GetPermsListByModulos($condiciones  = []){
    global $conexion;
    list($condiciones_str, $parametros) = AddConditions($condiciones);
    $consulta = "
        SELECT 
        m.modulo,
        t.tablas AS tabla,
        p.permiso,
        p.etiqueta
    FROM permisos p
    INNER JOIN tablas t ON p.kid_tabla = t.id_tabla
    INNER JOIN modulos m ON t.kid_modulo = m.id_modulo
    WHERE p.kid_estatus NOT IN (3, 12) AND t.kid_estatus = 1 AND m.kid_estatus = 1
    ORDER BY m.modulo, t.tablas;";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);

    // Suponiendo que $data contiene los resultados de la consulta ejecutada
    $result = [];

    // Recorremos los resultados para agruparlos por módulo y tabla
    foreach ($data as $row) {
        $modulo = $row['modulo'];   // Módulo
        $tabla = $row['tabla'];     // Tabla
        $permiso = $row['permiso']; // Permiso
        $etiqueta = $row['etiqueta']; // Etiqueta

        // Verificamos si ya existe el módulo en el arreglo
        $moduloIndex = array_search($modulo, array_column($result, 'nombre'));
        
        if ($moduloIndex === false) {
            // Si no existe el módulo, lo creamos
            $result[] = [
                'nombre' => $modulo,
                'tablas' => [
                    [
                        'tabla' => $tabla,
                        'permisos' => [
                            [
                                'id' => $permiso,
                                'etiqueta' => $etiqueta
                            ]
                        ]
                    ]
                ]
            ];
        } else {
            // Si el módulo existe, buscamos si la tabla ya está registrada
            $tablaIndex = array_search($tabla, array_column($result[$moduloIndex]['tablas'], 'tabla'));
            
            if ($tablaIndex === false) {
                // Si la tabla no existe, la agregamos al módulo
                $result[$moduloIndex]['tablas'][] = [
                    'tabla' => $tabla,
                    'permisos' => [
                        [
                            'id' => $permiso,
                            'etiqueta' => $etiqueta
                        ]
                    ]
                ];
            } else {
                // Si la tabla ya existe, solo agregamos el permiso
                $result[$moduloIndex]['tablas'][$tablaIndex]['permisos'][] = [
                    'id' => $permiso,
                    'etiqueta' => $etiqueta
                ];
            }
        }
    }

    return $result;
}


function GetAllowPermsList($kid_tipo_usuario, $condiciones = []) {
    global $conexion;
    list($condiciones_str, $parametros) = AddConditions($condiciones);
    $consulta = "SELECT DISTINCT p.permiso
    FROM tipos_usuarios_permisos tup
    INNER JOIN permisos p ON tup.kid_permiso = p.id_permiso
    INNER JOIN tablas t ON p.kid_tabla = t.id_tabla
    INNER JOIN modulos m ON t.kid_modulo = m.id_modulo
    WHERE tup.kid_estatus = 1 
    AND tup.kid_tipo_usuario = :kid_tipo_usuario 
    AND p.kid_estatus = 1 
    ORDER BY m.modulo, t.tablas;";

    $resultado = $conexion->prepare($consulta);
    $resultado->bindParam(':kid_tipo_usuario', $kid_tipo_usuario);
    $resultado->execute();
    $data_perms_allow = $resultado->fetchAll(PDO::FETCH_COLUMN);
    return $data_perms_allow;
}

?>