<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json; charset=utf-8");

// 🔗 CONEXIÓN CENTRALIZADA
require_once __DIR__ . "/conexion.php";

$tienda_id = $_POST["tienda_id"] ?? "";
$telefono  = $_POST["telefono"] ?? "";

// Validaciones mínimas
if (!$tienda_id || !$telefono) {
    echo json_encode(["error" => "Faltan datos"]);
    exit();
}

// Sanitizar teléfono: solo números + +
$telefono = preg_replace('/[^0-9+]/', '', $telefono);

// Consulta
$sql = "UPDATE tiendas SET telefono = ? WHERE id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["error" => "Error SQL: " . $conn->error]);
    exit();
}

$stmt->bind_param("si", $telefono, $tienda_id);

if ($stmt->execute()) {
    echo json_encode(["ok" => true]);
} else {
    echo json_encode(["error" => "No se pudo guardar el teléfono"]);
}

exit();
?>
