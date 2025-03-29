<?php
if (strpos($_SERVER['PHP_SELF'], '/phpmyadmin/') !== false) {
    // Si es phpMyAdmin, no hacer nada
    return;
}else{
    include 'vistas_funciones.php'; 
    include 'helper.php'; 
    include 'elements.php'; 
    include_once $_SERVER['DOCUMENT_ROOT'].'/bd/conexion.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/bd/funciones_db.php';
    date_default_timezone_set('America/Mexico_City');
    $excepciones = ['index.php', 'login.php'];
    if (!in_array(basename($_SERVER['PHP_SELF']), $excepciones)) {
        // Llama a la función validateSession()
        validateSession();
    }
}
?>