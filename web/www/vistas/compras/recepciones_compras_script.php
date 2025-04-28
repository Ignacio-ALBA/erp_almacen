<?php
/**
 * Este archivo ahora solo genera variables PHP necesarias y carga el JavaScript desde un archivo externo
 * Este enfoque evita que el analizador PHP Intelephense genere errores al analizar código JavaScript
 */

// Preparamos las variables para el JavaScript
$nonce_value = isset($nonce) ? htmlspecialchars($nonce) : '';


?>

<!-- Script con variables generadas por PHP -->
<script nonce="<?php echo $nonce_value; ?>">

</script>

<!-- Cargar el archivo JavaScript externo -->
<script src="/assets/js/recepciones_compras.js?v=<?php echo time(); ?>"></script>
<?php 
// Cierre del archivo PHP - esto evita errores de análisis en PHP
?>