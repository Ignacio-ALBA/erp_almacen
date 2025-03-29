<script nonce="<?php echo $nonce; ?>">
    $(document).ready(function() {
        // Asociar el evento click al botón
        $('#btactividades').on('click', function() {
        // Redirigir a la URL especificada
        window.location.href = '/rutas/ingenieria_servicios.php/actividades';
        });

        // Asociar el evento click al botón
        $('#btproyectos').on('click', function() {
        // Redirigir a la URL especificada
        window.location.href = '/rutas/administracion.php/proyectos';
        });
        // Asociar el evento click al botón
        $('#btcuentas').on('click', function() {
        // Redirigir a la URL especificada
        window.location.href = '/rutas/contabilidad.php/cuentas_bancarias';
        });
        // Asociar el evento click al botón
        $('#btclientes').on('click', function() {
        // Redirigir a la URL especificada
        window.location.href = '/rutas/planeacion.php/clientes';
        });
        // Asociar el evento click al botón
        $('#btproveedores').on('click', function() {
        // Redirigir a la URL especificada
        window.location.href = '/rutas/compras.php/proveedores';
        });
        // Asociar el evento click al botón
        $('#btcomentariosproveedores').on('click', function() {
        // Redirigir a la URL especificada
        window.location.href = '/rutas/compras.php/comentarios_proveedores';
        });
    });
</script>