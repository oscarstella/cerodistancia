<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/conexion.php";

$response = ["ok" => false];

// ==========================
// VALIDACIÓN
// ==========================
$tienda_id = $_POST["tienda_id"] ?? "";
$nombre = trim($_POST["nombre"] ?? "");
$descripcion = trim($_POST["descripcion"] ?? "");
$precio = $_POST["precio"] ?? "";

if (!$tienda_id || !$nombre || !$precio) {
    echo json_encode(["error" => "Faltan datos obligatorios"]);
    exit;
}

$tienda_id = intval($tienda_id);
$precio = floatval($precio);

$imagen_ruta = null;

// ==========================
// SUBIR IMAGEN (opcional)
// ==========================
if (!empty($_FILES["imagen"]["name"])) {

    $archivo = $_FILES["imagen"];
    $ext = strtolower(pathinfo($archivo["name"], PATHINFO_EXTENSION));
    $permitidas = ["jpg", "jpeg", "png", "webp", "avif"];

    if (!in_array($ext, $permitidas)) {
        echo json_encode(["error" => "Formato no permitido"]);
        exit;
    }

    $nuevoNombre = "prod_" . time() . "_" . rand(1000, 9999) . "." . $ext;
    $destino = "../uploads/" . $nuevoNombre;

    if (!is_dir("../uploads")) {
        mkdir("../uploads", 0777, true);
    }

    if (move_uploaded_file($archivo["tmp_name"], $destino)) {
        $imagen_ruta = "uploads/" . $nuevoNombre;
    }
}

// ==========================
// STATUS (activo)
// ==========================
$activo = isset($_POST["activo"]) ? intval($_POST["activo"]) : 1;

// ==========================
// INSERTAR PRODUCTO
// ==========================
$sql = "INSERT INTO productos (tienda_id, nombre, descripcion, precio, imagen, activo)
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "issdsi",
    $tienda_id,
    $nombre,
    $descripcion,
    $precio,
    $imagen_ruta,
    $activo
);

if ($stmt->execute()) {
    $response["ok"] = true;
    $response["id"] = $stmt->insert_id;
} else {
    $response["error"] = "Error al guardar producto";
}

echo json_encode($response);
exit;
