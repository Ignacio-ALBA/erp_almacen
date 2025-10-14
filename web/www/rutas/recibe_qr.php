<?php
// rutas/recibe_qr.php
// Servicio para recibir códigos QR y guardarlos en la tabla codigos_qr

header('Content-Type: application/json; charset=utf-8');
// CORS básico (ajusta según tus necesidades)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

session_start();

require_once __DIR__ . '/../bd/conexion.php';

function json_response($status, $payload = [], $http_code = 200) {
    http_response_code($http_code);
    echo json_encode(['status' => $status] + $payload, JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        json_response('error', ['message' => 'Método no permitido. Usa POST'], 405);
    }

    // Soportar application/json y x-www-form-urlencoded/multipart
    $contentType = isset($_SERVER['CONTENT_TYPE']) ? strtolower(trim(explode(';', $_SERVER['CONTENT_TYPE'])[0])) : '';

    $input = null;
    if ($contentType === 'application/json') {
        $raw = file_get_contents('php://input');
        $input = json_decode($raw, true);
        if ($input === null && json_last_error() !== JSON_ERROR_NONE) {
            json_response('error', ['message' => 'JSON inválido en el cuerpo de la solicitud'], 400);
        }
    } else {
        $input = $_POST;
    }

    // Normalizar entradas
    $codigo = isset($input['codigo']) ? trim((string)$input['codigo']) : '';
    $tipo   = isset($input['tipo']) ? trim((string)$input['tipo']) : null;
    $fuente = isset($input['fuente']) ? trim((string)$input['fuente']) : null;
    $datos  = isset($input['datos']) ? $input['datos'] : null; // puede ser string o array

    if ($codigo === '') {
        json_response('error', ['message' => 'El parámetro "codigo" es requerido'], 400);
    }

    // Preparar valor para columna JSON "datos"
    $datosForDb = null;
    if ($datos !== null) {
        if (is_array($datos) || is_object($datos)) {
            $datosForDb = json_encode($datos, JSON_UNESCAPED_UNICODE);
        } else {
            // Si es string, intentar parsear como JSON; si no es válido, guardar como string JSON
            $decoded = json_decode((string)$datos, true);
            if ($decoded !== null || json_last_error() === JSON_ERROR_NONE) {
                $datosForDb = (string)$datos;
            } else {
                $datosForDb = json_encode((string)$datos, JSON_UNESCAPED_UNICODE);
            }
        }
    }

    // Obtener usuario de sesión si existe
    $kid_usuario = isset($_SESSION['s_id']) ? (int)$_SESSION['s_id'] : null;

    // Conectar BD
    $objeto = new Conexion();
    $pdo = $objeto->Conectar();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Insertar registro
    $sql = "INSERT INTO codigos_qr (codigo, tipo, fuente, kid_usuario, datos) VALUES (:codigo, :tipo, :fuente, :kid_usuario, :datos)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':codigo', $codigo, PDO::PARAM_STR);
    $stmt->bindValue(':tipo', $tipo, $tipo === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt->bindValue(':fuente', $fuente, $fuente === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    if ($kid_usuario === null) {
        $stmt->bindValue(':kid_usuario', null, PDO::PARAM_NULL);
    } else {
        $stmt->bindValue(':kid_usuario', $kid_usuario, PDO::PARAM_INT);
    }
    if ($datosForDb === null) {
        $stmt->bindValue(':datos', null, PDO::PARAM_NULL);
    } else {
        $stmt->bindValue(':datos', $datosForDb, PDO::PARAM_STR);
    }

    $stmt->execute();
    $insertId = $pdo->lastInsertId();

    json_response('success', [
        'message' => 'Código QR registrado',
        'id' => (int)$insertId,
        'codigo' => $codigo,
        'tipo' => $tipo,
        'fuente' => $fuente,
        'kid_usuario' => $kid_usuario,
    ], 201);

} catch (PDOException $e) {
    json_response('error', ['message' => 'Error de base de datos: '.$e->getMessage()], 500);
} catch (Throwable $e) {
    json_response('error', ['message' => 'Error del servidor: '.$e->getMessage()], 500);
}
