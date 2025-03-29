<?php
session_start();
unset($_SESSION["s_usuario"]);
unset($_SESSION["s_id"]);
unset($_SESSION["s_id_tipoUsuario"]);
unset($_SESSION["s_usuario"]);
unset($_SESSION["s_tipo_usuario"]);
session_destroy();
header("Location: /index.php");
?>