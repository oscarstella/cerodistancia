<?php
header("Content-Type: application/json; charset=utf-8");
require_once __DIR__ . "/conexion.php";

$uid = $_GET["uid"] ?? "";

if (!$uid) {
  echo json_encode(["tiene_tienda" => false]);
  exit;
}

$sql = "SELECT id, nombre, slug, descripcion, quienes, logo, estado, url_final, mp_preapproval_id FROM tiendas WHERE uid_usuario = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $uid);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
  echo json_encode(["tiene_tienda" => false]);
  exit;
}

$t = $res->fetch_assoc();

echo json_encode([
  "tiene_tienda" => true,
  "tienda_id" => (int)$t["id"],
  "nombre" => $t["nombre"],
  "slug" => $t["slug"],
  "descripcion" => $t["descripcion"],
  "quienes" => $t["quienes"],
  "logo" => $t["logo"],
  "estado" => $t["estado"],
  "url_final" => $t["url_final"],
  "mp_id" => $t["mp_preapproval_id"]
]);
