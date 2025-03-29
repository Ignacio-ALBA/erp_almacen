<?php
session_start();
include '../helpers/main.php'; 
if($_SESSION["s_usuario"] == null){
    header("Location: index.php");
}/*elseif ($_SESSION["s_id_tipoUsuario"] == 2) {
    require_once "vistas/parte_superior_sup.php";
}elseif ($_SESSION["s_id_tipoUsuario"] == 3) {
    require_once "vistas/parte_superior_admin.php";
}elseif ($_SESSION["s_id_tipoUsuario"] == 6) {
    require_once "vistas/parte_superior_aud.php";
}elseif ($_SESSION["s_id_tipoUsuario"] == 7) {
    require_once "vistas/parte_superior_plan.php";
}elseif ($_SESSION["s_id_tipoUsuario"] == 10) {
    require_once "vistas/parte_superior_ger.php";
}elseif ($_SESSION["s_id_tipoUsuario"] == 11) {
    require_once "vistas/parte_superior_cal.php";
}
else{
    header("Location: index.php");
}*/
require_once "catalogo/categorias.php";
//require_once "vistas/parte_inferior.php";
?>