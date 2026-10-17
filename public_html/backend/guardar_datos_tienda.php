<?php
header("Content-Type: application/json; charset=utf-8");
require_once __DIR__ . "/conexion.php";

$tienda_id = $_POST["tienda_id"] ?? "";
$nombre = $_POST["nombre"] ?? "";
$descripcion = $_POST["descripcion"] ?? "";

// Validación mínima
if (!$tienda_id || !$nombre) {
    echo json_encode(["ok" => false, "msg" => "Datos faltantes"]);
    exit();
}

$logo_ruta = null;

// Si viene una imagen nueva
if (isset($_FILES["logo"]) && $_FILES["logo"]["error"] === 0) {

    $nombreArchivo = time() . "_" . basename($_FILES["logo"]["name"]);
    $destino = "../uploads/" . $nombreArchivo;

    if (move_uploaded_file($_FILES["logo"]["tmp_name"], $destino)) {
        $logo_ruta = "uploads/" . $nombreArchivo;
    }
}

// 🔹 Opciones personalizadas
$titulo_opciones = $_POST["titulo_opciones"] ?? "";
$opcion1 = $_POST["opcion1"] ?? "";
$opcion2 = $_POST["opcion2"] ?? "";
$opcion3 = $_POST["opcion3"] ?? "";

// QUERY BASE
$sql = "UPDATE tiendas SET nombre=?, descripcion=?, titulo_opciones=?, opcion1=?, opcion2=?, opcion3=?";
$types = "ssssss";
$params = [$nombre, $descripcion, $titulo_opciones, $opcion1, $opcion2, $opcion3];

if ($logo_ruta) {
    $sql .= ", logo=?";
    $types .= "s";
    $params[] = $logo_ruta;
}

$sql .= " WHERE id=?";
$types .= "i";
$params[] = $tienda_id;

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    echo json_encode(["ok" => true, "logo_ruta" => $logo_ruta ?: null]);
} else {
    echo json_encode(["error" => "No se pudo actualizar la tienda"]);
}
