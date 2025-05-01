
<?php
session_start();

$objeto = new Conexion();
$conexion = $objeto->Conectar();

//Real
date_default_timezone_set('America/Mexico_City');
$fecha_creacion = date('Y:m:d H:i:s');

// Recepción de datos enviados mediante POST desde AJAX
$usuario = isset($_POST['usuario']) ? $_POST['usuario'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$id_tipoUsuario = isset($_POST['id_tipoUsuario']) ? $_POST['id_tipoUsuario'] : '';
$usuario = trim($usuario);
$password = trim($password);

$data=null;
$pass = $password;   #AGREGAR SEED
// Uso de consultas preparadas
$consulta = "SELECT * FROM colaboradores WHERE email = :email AND kid_tipo_usuario = :id_tipoUsuario AND login = 1 AND kid_estatus  = 1";
$resultado = $conexion->prepare($consulta);
$resultado->bindParam(':email', $usuario);
$resultado->bindParam(':id_tipoUsuario', $id_tipoUsuario);
$resultado->execute();

$temp=$pass.$usuario;
if($resultado->rowCount() >= 1){
	$data=$resultado->fetchAll(PDO::FETCH_ASSOC);
	$hash = $data[0]["password"];
	if(password_verify($temp, $hash)){
	    //$data = $resultado->fetchAll(PDO::FETCH_ASSOC);
		$consulta = "SELECT * FROM tipos_usuario WHERE id_tipo_usuario  = :id_tipo_usuario  AND kid_estatus  = 1";
		$resultado = $conexion->prepare($consulta);
		$resultado->bindParam(':id_tipo_usuario', $id_tipoUsuario);
		$resultado->execute();
		$tipo_usuario=$resultado->fetch(PDO::FETCH_ASSOC);

	    $_SESSION["s_id"] = $data[0]['id_colaborador'];
	    $_SESSION["s_usuario"] = $data[0]['email'];
	    $_SESSION["s_id_tipoUsuario"] = $data[0]['kid_tipo_usuario'];
		$_SESSION["s_usuario"] = $data[0]['email'];
	    $_SESSION["s_tipo_usuario"] = $tipo_usuario["tipo_usuario"];
		
		$_SESSION["permisos"] =  $_SESSION["s_id_tipoUsuario"] == 1 ? ["all"] : GetAllowPermsList($_SESSION["s_id_tipoUsuario"]) ;
		$nombre = $data[0]['nombre'];
		debug ($_SESSION["permisos"]);
		if($data[0]['apellido_paterno']){$nombre .= " ".$data[0]['apellido_paterno'];}
		if($data[0]['apellido_materno']){$nombre .= " ".$data[0]['apellido_materno'];}
		$_SESSION["s_nombre"] = $nombre;

		//Evento
        /*$evento="Login: Ingreso exitoso: ".$data[0]['email']." (".$data[0]['id_colaborador'].")";
        $consulta = "INSERT INTO log (evento, id_colaborador, usuario, fecha_creacion) VALUES ('$evento','$data[0]['email']','$data[0]['id_colaborador']','$fecha_creacion')";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();*/
        //Evento 
	}else{
	    $_SESSION["s_id"] = null;
	    $_SESSION["s_usuario"] = null;
	    $_SESSION["s_id_tipoUsuario"] = null;
		$_SESSION["s_tipo_usuario"] = null;
		$_SESSION["s_nombre"] = null;
		$_SESSION["permisos"] = [];
	    $data=null;

		/*
		//Evento
        $evento="Login: Ingreso fallido (Contraseña inválida): ".$usuario." (".substr($password,0,1)."***".substr($password,-2)." - ".$id_tipoUsuario.")";
        $consulta = "INSERT INTO log (evento, id_colaborador, usuario, fecha_creacion) VALUES ('$evento',3,'N.A.','$fecha_creacion')";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();
        //Evento */
	}
}else{
    $_SESSION["s_id"] = null;
    $_SESSION["s_usuario"] = null;
    $_SESSION["s_id_tipoUsuario"] = null;
	$_SESSION["s_tipo_usuario"] = null;
	$_SESSION["s_nombre"] = null;
	$_SESSION["permisos"] = [];
	$data=null;
	/*
	//Evento
	$evento="Login: Ingreso fallido (No existe usuario con ese tipo de usuario): ".$usuario." (".substr($password,0,1)."***".substr($password,-2)." - ".$id_tipoUsuario.")";
	$consulta = "INSERT INTO log (evento, id_colaborador, usuario, fecha_creacion) VALUES ('$evento',3,'N.A.','$fecha_creacion')";
	$resultado = $conexion->prepare($consulta);
	$resultado->execute();
	//Evento */
}

print json_encode($data);
$conexion=null;
//reload();

//colaboradores de pruebaen la base de datos
//usuario:admin pass:Alba2024#
