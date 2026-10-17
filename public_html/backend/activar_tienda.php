<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['preapproval_id'])) {
  echo json_encode(['ok' => false, 'error' => 'preapproval_id faltante']);
  exit;
}

$preapproval_id = $data['preapproval_id'];

require_once __DIR__ . '/conexion.php';

/* Buscar tienda por ID de suscripción */
$stmt = $conn->prepare(
  "SELECT id, slug FROM tiendas WHERE mp_preapproval_id = ? LIMIT 1"
);
$stmt->bind_param("s", $preapproval_id);
$stmt->execute();
$res = $stmt->get_result();
$tienda = $res->fetch_assoc();

if (!$tienda) {
  echo json_encode(['ok' => false, 'error' => 'Tienda no encontrada']);
  exit;
}

/* Activar tienda */
$up = $conn->prepare(
  "UPDATE tiendas SET estado = 'activa' WHERE id = ?"
);
$up->bind_param("i", $tienda['id']);
$up->execute();

echo json_encode([
  'ok' => true,
  'slug' => $tienda['slug']
]);
