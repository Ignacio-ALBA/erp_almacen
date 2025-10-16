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

    // Normalizar entradas (nuevo esquema)
    // Aceptamos application/json o form-data fields
    $idOrdenCompra = isset($input['idOrdenCompra']) && $input['idOrdenCompra'] !== '' ? (int)$input['idOrdenCompra'] : null;
    $insumo = isset($input['insumo']) ? trim((string)$input['insumo']) : null;
    $proveedor = isset($input['proveedor']) ? trim((string)$input['proveedor']) : null;
    $fechaHora = isset($input['fecha_hora']) ? trim((string)$input['fecha_hora']) : null; // expected ISO or 'Y-m-d H:i:s'
    $pesoKg = isset($input['peso_kg']) && $input['peso_kg'] !== '' ? (float)$input['peso_kg'] : null;
    $pesoTarima = isset($input['peso_tarima']) && $input['peso_tarima'] !== '' ? (float)$input['peso_tarima'] : null;


    // Validaciones mínimas
    if ($insumo === null || $insumo === '') {
        json_response('error', ['message' => 'El parámetro "insumo" es requerido'], 400);
    }
    if ($proveedor === null || $proveedor === '') {
        json_response('error', ['message' => 'El parámetro "proveedor" es requerido'], 400);
    }
    if ($fechaHora === null || $fechaHora === '') {
        json_response('error', ['message' => 'El parámetro "fechaHora" es requerido'], 400);
    }
    // Normalizar formato de fecha: convertir "a.m." a "am" y "p.m." a "pm"
    $fechaHora = str_replace(['a.m.', 'p.m.'], ['am', 'pm'], $fechaHora);
    // Parsear fecha usando DateTime::createFromFormat para formato 'd/m/Y, H:i:s a'
    $format = 'd/m/Y, H:i:s a';
    $dateTime = DateTime::createFromFormat($format, $fechaHora);
    if ($dateTime === false) {
        json_response('error', ['message' => 'El parámetro "fechaHora" no tiene un formato de fecha válido'], 400);
    }
    // Normalizar a formato MySQL DATETIME
    $fechaHoraFormatted = $dateTime->format('Y-m-d H:i:s');

    // Conectar BD
    $objeto = new Conexion();
    $pdo = $objeto->Conectar();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Insertar registro en la nueva estructura
    $sql = "INSERT INTO codigos_qr (idOrdenCompra, insumo, proveedor, fechaHora, pesoKg, pesoTarima) 
            VALUES (:idOrdenCompra, :insumo, :proveedor, :fechaHora, :pesoKg, :pesoTarima)";
    $stmt = $pdo->prepare($sql);

    // Vincular parámetros
    if ($idOrdenCompra === null) {
        $stmt->bindValue(':idOrdenCompra', null, PDO::PARAM_NULL);
    } else {
        $stmt->bindValue(':idOrdenCompra', $idOrdenCompra, PDO::PARAM_INT);
    }
    $stmt->bindValue(':insumo', $insumo, PDO::PARAM_STR);
    $stmt->bindValue(':proveedor', $proveedor, PDO::PARAM_STR);
    $stmt->bindValue(':fechaHora', $fechaHoraFormatted, PDO::PARAM_STR);
    if ($pesoKg === null) {
        $stmt->bindValue(':pesoKg', null, PDO::PARAM_NULL);
    } else {
        $stmt->bindValue(':pesoKg', $pesoKg);
    }
    if ($pesoTarima === null) {
        $stmt->bindValue(':pesoTarima', null, PDO::PARAM_NULL);
    } else {
        $stmt->bindValue(':pesoTarima', $pesoTarima);
    }

    $stmt->execute();
    $insertId = $pdo->lastInsertId();

    json_response('success', [
        'message' => 'Registro recibido y almacenado',
        'id' => (int)$insertId,
        'idOrdenCompra' => $idOrdenCompra,
        'insumo' => $insumo,
        'proveedor' => $proveedor,
        'fechaHora' => $fechaHoraFormatted,
        'pesoKg' => $pesoKg,
        'pesoTarima' => $pesoTarima
    ], 201);

} catch (PDOException $e) {
    json_response('error', ['message' => 'Error de base de datos: '.$e->getMessage()], 500);
} catch (Throwable $e) {
    json_response('error', ['message' => 'Error del servidor: '.$e->getMessage()], 500);
}
