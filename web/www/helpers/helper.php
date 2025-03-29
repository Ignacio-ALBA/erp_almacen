<?php

// helper.php

// ---------------------------------------------
// Configuración de Visualización de Errores
// ---------------------------------------------

// Activar la visualización de errores para facilitar la depuración durante el desarrollo
ini_set('display_errors', 1); // Muestra errores en la pantalla
ini_set('display_startup_errors', 1); // Muestra errores que ocurren durante el inicio
error_reporting(E_ALL); // Reporta todos los tipos de errores, advertencias y avisos

// ---------------------------------------------
// Manejador de Errores Personalizado
// ---------------------------------------------

function customErrorHandler($errno, $errstr, $errfile, $errline) {
    // Verificar si el error es manejable
    if (!(error_reporting() & $errno)) {
        // Este código de error no está incluido en error_reporting
        return;
    }

    // Formato HTML para mostrar el error
    echo '<div style="border: 1px solid #f00; background-color: #ffe6e6; color: #d8000c; padding: 10px; margin: 10px 0; border-radius: 5px;">';
    echo '<strong>Error:</strong> [' . $errno . '] ' . htmlspecialchars($errstr) . '<br>';
    echo '<strong>Archivo:</strong> ' . htmlspecialchars($errfile) . '<br>';
    echo '<strong>Línea:</strong> ' . htmlspecialchars($errline);
    echo '</div>';

    // Detener la ejecución del script si es un error fatal
    if ($errno === E_ERROR) {
        exit();
    }
}

// Establecer el manejador de errores personalizado
set_error_handler("customErrorHandler");

// Manejar errores fatales
function shutdownHandler() {
    $error = error_get_last();
    if ($error) {
        customErrorHandler(E_ERROR, $error['message'], $error['file'], $error['line']);
    }
}

// Registrar el manejador de apagado
register_shutdown_function('shutdownHandler');

// ---------------------------------------------
// Fin de la Configuración de Errores
// ---------------------------------------------

// Ejemplo de código que genera un error para probar
// Uncomment the line below to see the error handling in action
// echo $undefined_variable;

// Puedes agregar aquí otras configuraciones o funciones auxiliares que necesites
// Por ejemplo, funciones de utilidad, configuraciones de base de datos, etc.

// Ejemplo de una función auxiliar
function debug($data) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

function getServerUrl() {
    return "http://" . $_SERVER['SERVER_NAME'];
}

function getBaseUrl() {
    return getServerUrl().$_SERVER['SCRIPT_NAME'];
}

function hexToRgb($hex) {
    $hex = str_replace("#", "", $hex);
    if (strlen($hex) == 6) {
        list($r, $g, $b) = array($hex[0].$hex[1], $hex[2].$hex[3], $hex[4].$hex[5]);
    } elseif (strlen($hex) == 3) {
        list($ $r, $g, $b) = array($hex[0].$hex[0], $hex[1].$hex[1], $hex[2].$hex[2]);
    } else {
        return '0, 0, 0'; // Color por defecto si el formato es incorrecto
    }
    return intval($r, 16) . ', ' . intval($g, 16) . ', ' . intval($b, 16);
}

$hashkey = 'TMTfaqn6Q2u0oWjIb3FkyHRgWN';

function codificar($id) {
    global $hashkey;
    $codificacion = base64_encode($id . $hashkey);
    return $codificacion;
}

function decodificar($codificacion) {
    global $hashkey;
    $id_decodificado = base64_decode($codificacion);
    $id_decodificado = substr($id_decodificado, 0, -strlen($hashkey));
    return $id_decodificado;
}

?>