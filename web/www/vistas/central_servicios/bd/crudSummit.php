<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');
ob_start();

$objeto = new Conexion();
$conexion = $objeto->Conectar();

$data = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $modalCRUD = $_POST['modalCRUD'];
    $formDataJson = json_decode($_POST['formDataJson'], true);
    $opcion = $_POST['opcion'];

    $tabla = 'detalles_almacenes';
    $idcolumn = 'id_detalle_almacen';

    switch ($opcion) {
        case 1: // Crear
            $formDataJson['fecha_creacion'] = date('Y-m-d H:i:s');
            $formDataJson['kid_creacion'] = $_SESSION["s_id"];
            $formDataJson['kid_estatus'] = 1;

            $columnas = implode(", ", array_keys($formDataJson));
            $valores = ":" . implode(", :", array_keys($formDataJson));

            $consulta = "INSERT INTO $tabla ($columnas) VALUES ($valores)";
            $resultado = $conexion->prepare($consulta);

            foreach ($formDataJson as $key => $value) {
                $resultado->bindValue(":$key", $value);
            }

            if ($resultado->execute()) {
                $data = ['status' => 'success', 'message' => 'Registro creado correctamente'];
            } else {
                $data = ['status' => 'error', 'message' => 'Error al crear el registro'];
            }
            break;

        case 2: // Editar
            $id = $_POST['firstColumnValue'];
            $setPart = [];
            foreach ($formDataJson as $key => $value) {
                $setPart[] = "$key = :$key";
            }
            $setPart = implode(", ", $setPart);

            $consulta = "UPDATE $tabla SET $setPart WHERE $idcolumn = :id";
            $resultado = $conexion->prepare($consulta);

            foreach ($formDataJson as $key => $value) {
                $resultado->bindValue(":$key", $value);
            }
            $resultado->bindValue(":id", $id);

            if ($resultado->execute()) {
                $data = ['status' => 'success', 'message' => 'Registro actualizado correctamente'];
            } else {
                $data = ['status' => 'error', 'message' => 'Error al actualizar el registro'];
            }
            break;

        case 3: // Eliminar
            $id = $_POST['firstColumnValue'];
            $consulta = "UPDATE $tabla SET kid_estatus = 3 WHERE $idcolumn = :id";
            $resultado = $conexion->prepare($consulta);
            $resultado->bindValue(":id", $id);

            if ($resultado->execute()) {
                $data = ['status' => 'success', 'message' => 'Registro eliminado correctamente'];
            } else {
                $data = ['status' => 'error', 'message' => 'Error al eliminar el registro'];
            }
            break;

        default:
            $data = ['status' => 'error', 'message' => 'Opción no válida'];
            break;
    }
} else {
    $data = ['status' => 'error', 'message' => 'Método no permitido'];
}

echo json_encode($data, JSON_UNESCAPED_UNICODE);
?>