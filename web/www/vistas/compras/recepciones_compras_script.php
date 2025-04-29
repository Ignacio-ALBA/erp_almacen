<?php
/**
 * Este archivo ahora solo genera variables PHP necesarias y carga el JavaScript desde un archivo externo
 * Este enfoque evita que el analizador PHP Intelephense genere errores al analizar código JavaScript
 */

// Preparamos las variables para el JavaScript
$nonce_value = isset($nonce) ? htmlspecialchars($nonce) : '';

// Ruta del logotipo
$logo_path = $_SERVER['DOCUMENT_ROOT'] . '/assets/img/logoi.JPG';

// Verifica si el archivo existe y conviértelo a Base64
if (file_exists($logo_path)) {
    $imageData = file_get_contents($logo_path);
    $imageType = pathinfo($logo_path, PATHINFO_EXTENSION);
    $logoBase64 = 'data:image/' . $imageType . ';base64,' . base64_encode($imageData);
} else {
    error_log("El logotipo no se pudo cargar desde la ruta: " . $logo_path);
    $logoBase64 = ''; // Imagen por defecto o vacío
}
?>
<script nonce="<?php echo $nonce_value; ?>">
    // Pasar el logotipo como una variable global en formato Base64
    const logoBase64 = "<?php echo $logoBase64; ?>";
</script>


?>
<script src="https://cdn.jsdelivr.net/npm/pdf-lib/dist/pdf-lib.min.js" nonce="<?php echo $nonce; ?>"></script>

<script src="/assets/js/qrcode.min.js"></script>
<script src="/assets/js/jspdf.umd.min.js"></script>



<!-- Cargar el archivo JavaScript externo -->
<script  type="module" src="/assets/js/recepciones_compras.js?v=<?php echo time(); ?>"></script>
<!--<script src="/assets/js/codigoqr.js?v=<?php echo time(); ?>"></script>-->
<?php 
// Cierre del archivo PHP - esto evita errores de análisis en PHP
?>