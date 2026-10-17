<?php
header("Content-Type: application/json; charset=utf-8");
require_once __DIR__ . "/conexion.php";

$id = $_POST["id"] ?? "";
$nombre = $_POST["nombre"] ?? "";
$descripcion = $_POST["descripcion"] ?? "";
$precio = $_POST["precio"] ?? "";

if (!$id || !$nombre || !$precio) {
    echo json_encode(["ok" => false, "msg" => "Datos insuficientes"]);
    exit();
}

$activo = isset($_POST["activo"]) ? intval($_POST["activo"]) : 1;
$nueva_imagen = null;

// Lógica para subir imagen nueva si existe
if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] === 0) {
    $nombreArchivo = "prod_" . time() . "_" . basename($_FILES["imagen"]["name"]);
    $destino = "../uploads/" . $nombreArchivo;
    if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $destino)) {
        $nueva_imagen = "uploads/" . $nombreArchivo;
    }
}

if ($nueva_imagen) {
    $sql = "UPDATE productos SET nombre=?, descripcion=?, precio=?, imagen=?, activo=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdsii", $nombre, $descripcion, $precio, $nueva_imagen, $activo, $id);
} else {
    $sql = "UPDATE productos SET nombre=?, descripcion=?, precio=?, activo=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdii", $nombre, $descripcion, $precio, $activo, $id);
}

$ok = $stmt->execute();
echo json_encode(["ok" => $ok]);
