<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json; charset=utf-8");

// ✅ USAR CONEXIÓN CENTRALIZADA
require_once __DIR__ . "/conexion.php";

// 🔹 ID obligatorio
$id = $_POST["id"] ?? "";
$imagen = $_POST["imagen"] ?? "";

// Validación
if (!$id) {
    echo json_encode(["error" => "ID faltante"]);
    exit();
}

// --------------------------------------------------
// 🔹 Borrar imagen del servidor si es válida
// --------------------------------------------------
if (!empty($imagen)) {
    // Evita rutas peligrosas
    $imagen = basename($imagen);

    $ruta = "../uploads/" . $imagen;

    if (file_exists($ruta)) {
        unlink($ruta);
    }
}

// --------------------------------------------------
// 🔹 Eliminar producto de la base
// --------------------------------------------------
$sql = "DELETE FROM productos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(["ok" => true]);
} else {
    echo json_encode(["error" => "No se pudo eliminar"]);
}

exit();
?>
