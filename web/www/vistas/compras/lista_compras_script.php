<?php
/**
 * Este archivo ahora solo genera variables PHP necesarias y carga el JavaScript desde un archivo externo
 * Este enfoque evita que el analizador PHP Intelephense genere errores al analizar código JavaScript
 */

// Preparamos las variables para el JavaScript
$nonce_value = isset($nonce) ? htmlspecialchars($nonce) : '';

// Preparamos el código de opciones de artículos si existe
$articulos_options = '';
if(isset($articulos)) {
    foreach($articulos as $articulo) {
        $articulos_options .= '<option value="'.htmlspecialchars($articulo['valor']).'">'.htmlspecialchars($articulo['valor']).'</option>';
    }
}

// Preparamos las opciones de proyectos si existen
$proyectos_options = '';
if(isset($proyectos)) {
    foreach($proyectos as $proyecto) {
        $proyectos_options .= '<option value="'.htmlspecialchars($proyecto['valor']).'" '.($proyecto['pordefecto'] == 1 ? 'selected' : '').'>'.htmlspecialchars($proyecto['valor']).'</option>';
    }
}

// Preparamos las opciones de cuentas bancarias si existen
$cuentas_bancarias_options = '';
if(isset($cuentas_bancarias)) {
    foreach($cuentas_bancarias as $cuenta) {
        $cuentas_bancarias_options .= '<option value="'.htmlspecialchars($cuenta['valor']).'" '.($cuenta['pordefecto'] == 1 ? 'selected' : '').'>'.htmlspecialchars($cuenta['valor']).'</option>';
    }
}

// Preparamos las opciones de estados si existen
$estatus_options = '';
if(isset($estatus)) {
    foreach($estatus as $est) {
        $estatus_options .= '<option value="'.htmlspecialchars($est['valor']).'" '.($est['pordefecto'] == 1 ? 'selected' : '').'>'.htmlspecialchars($est['valor']).'</option>';
    }
}
?>

<!-- Script con variables generadas por PHP -->
<script nonce="<?php echo $nonce_value; ?>">
// Estas variables se usarán en el JavaScript externo
window.articulosOptions = `<?php echo $articulos_options; ?>`;
window.proyectosOptions = `<?php echo $proyectos_options; ?>`;
window.cuentasBancariasOptions = `<?php echo $cuentas_bancarias_options; ?>`;
window.estatusOptions = `<?php echo $estatus_options; ?>`;
</script>

<!-- Cargar el archivo JavaScript externo -->
<script src="/assets/js/compras/lista_compras.js?v=<?php echo time(); ?>"></script>
<?php 
// Cierre del archivo PHP - esto evita errores de análisis en PHP
?>