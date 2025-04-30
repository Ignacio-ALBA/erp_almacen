<script nonce="<?php echo $nonce; ?>">
    $(document).ready(function() {
        // Asociar el evento click al botón
        $('#btactividades').on('click', function() {
        // Redirigir a la URL especificada
        window.location.href = '/rutas/produccion.php/capturar_produccion';
        });

        // Asociar el evento click al botón
        $('#btproyectos').on('click', function() {
        // Redirigir a la URL especificada
        window.location.href = '/rutas/central_servicios.php/central_mp';
        });
        // Asociar el evento click al botón
        $('#btcuentas').on('click', function() {
        // Redirigir a la URL especificada
        window.location.href = '/rutas/central_servicios.php/central_productos';
        });
        // Asociar el evento click al botón
        $('#btclientes').on('click', function() {
        // Redirigir a la URL especificada
        window.location.href = '/rutas/compras.php/listas_compras';
        });
        // Asociar el evento click al botón
        $('#btproveedores').on('click', function() {
        // Redirigir a la URL especificada
        window.location.href = '/rutas/compras.php/ordenes_compras';
        });
        // Asociar el evento click al botón
        $('#btcomentariosproveedores').on('click', function() {
        // Redirigir a la URL especificada
        window.location.href = '/rutas/compras.php/cotizaciones_compras';
        });
    });
</script>