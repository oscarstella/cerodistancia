<?php
require_once "conexion.php";

header("Content-Type: application/json; charset=utf-8");

// 1️⃣ Validar slug
if (!isset($_GET["slug"]) || trim($_GET["slug"]) === "") {
    echo json_encode(["error" => "slug faltante"]);
    exit;
}

$slug = trim($_GET["slug"]);

// 2️⃣ Buscar tienda
$stmt = $conn->prepare("
    SELECT 
        id,
        uid_usuario,
        nombre,
        slug,
        descripcion,
        quienes,
        telefono,
        logo,
        estado,
        url_final,
        opcion1,
        opcion2,
        opcion3,
        titulo_opciones
    FROM tiendas
    WHERE slug = ?
    LIMIT 1
");

if (!$stmt) {
    echo json_encode(["error" => "Error prepare: " . $conn->error]);
    exit;
}

$stmt->bind_param("s", $slug);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["error" => "no encontrada"]);
    exit;
}

$tienda = $result->fetch_assoc();

// 3️⃣ Normalizar estado (MUY IMPORTANTE)
$tienda["estado"] = strtolower(trim($tienda["estado"]));

// 4️⃣ Responder OK
echo json_encode($tienda);
