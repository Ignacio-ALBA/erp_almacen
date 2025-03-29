<?php
session_start();
include '../helpers/main.php'; 



if($_SESSION["s_usuario"] == null){
    header("Location: ../index.php");
}elseif ($_SESSION["s_id_tipoUsuario"] == 3) {
    require_once "catalogo/roles.php";
}else{
    header("Location: ../index.php");
}
?>