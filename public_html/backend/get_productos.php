<?php
require_once "conexion.php";

header("Content-Type: application/json; charset=utf-8");

if (!isset($_GET["tienda_id"])) {
    echo json_encode([]);
    exit;
}

$tienda_id = intval($_GET["tienda_id"]);

$stmt = $conn->prepare("SELECT id, nombre, descripcion, precio, imagen, activo 
                        FROM productos 
                        WHERE tienda_id = ? 
                        ORDER BY id DESC");

if (!$stmt) {
    echo json_encode(["error" => "Error prepare: " . $conn->error]);
    exit;
}

$stmt->bind_param("i", $tienda_id);

if (!$stmt->execute()) {
    echo json_encode(["error" => "Error execute: " . $stmt->error]);
    exit;
}

$res = $stmt->get_result();

$productos = [];

while ($row = $res->fetch_assoc()) {
    $productos[] = $row;
}

echo json_encode($productos);
