

<?php
	//include $_SERVER['DOCUMENT_ROOT'].'/helpers/main.php'; 
	$objeto = new Conexion();
	$conexion = $objeto->Conectar();
	$passw = trim($_POST['password1']);
	$email=trim($_SESSION["s_usuario"]);
	$id_creo = $_SESSION['s_id'];
	$creo = $_SESSION['s_usuario'];
	date_default_timezone_set('America/Mexico_City');
	$fecha_creacion = date('Y:m:d H:i:s');

	$temp=$passw.$email;
	$hash = password_hash($temp, PASSWORD_DEFAULT);
    $consulta = "UPDATE colaboradores SET password='".$hash."' WHERE id_colaborador=".$id_creo;
    $resultado = $conexion->prepare($consulta);
    $resultado->execute(); 

    $evento="Personal: Cambio password ".$email;
    /*$consulta = "INSERT INTO log (evento, id_colaborador, usuario, fecha_creacion) VALUES ('$evento','$id_creo','$creo','$fecha_creacion')";
    //echo $consulta;   
    $resultado = $conexion->prepare($consulta);
    $resultado->execute(); */

	echo'<script type="text/javascript"> alert("Contraseña actualizada"); window.location.href="/rutas/dashboard.php/cambiarpass"; </script>';
?>