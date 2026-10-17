<?php
declare(strict_types=1);

ini_set('display_errors', '1');
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

/* =========================
   CREDENCIALES MP
========================= */
require_once __DIR__ . '/config_mp.php';

/* =========================
   INPUT
========================= */
$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['tienda_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'tienda_id requerido']);
    exit;
}

$tienda_id = (int) $data['tienda_id'];

/* =========================
   DB
========================= */
require_once __DIR__ . '/conexion.php';

/*
  ACA ESTÁ LA CLAVE REAL:
  - El email SALE DE tiendas.email
  - NO de usuarios
  - NO de Firebase en este paso
*/
$stmt = $conn->prepare(
    "SELECT nombre, email FROM tiendas WHERE id = ? LIMIT 1"
);
$stmt->bind_param("i", $tienda_id);
$stmt->execute();
$res = $stmt->get_result();
$datos = $res->fetch_assoc();

if (!$datos || empty($datos['email'])) {
    http_response_code(400);
    echo json_encode(['error' => 'La tienda no tiene email asociado']);
    exit;
}

/* =========================
   CARGAR MONTO DINAMICO
========================= */
$res_monto = $conn->query("SELECT valor FROM configuracion WHERE clave = 'monto_suscripcion'");
$monto_final = 100; // Fallback
if ($res_monto && $res_monto->num_rows > 0) {
    $row_monto = $res_monto->fetch_assoc();
    $monto_final = (float) $row_monto["valor"];
}

/* =========================
   PAYLOAD MP
========================= */
$payload = [
    'reason' => 'Suscripción Mensual - ' . $datos['nombre'],
    'payer_email' => $datos['email'], // ← EMAIL GUARDADO AL CREAR TIENDA
    'auto_recurring' => [
        'frequency' => 1,
        'frequency_type' => 'months',
        'transaction_amount' => $monto_final,
        'currency_id' => 'ARS'
    ],
    'back_url' => 'https://cerodistancia.click/pago-gracias.html',
    'external_reference' => 'tienda_' . $tienda_id,
    'notification_url' => 'https://cerodistancia.click/backend/webhook_mp.php'
];

/* =========================
   CURL
========================= */
$ch = curl_init('https://api.mercadopago.com/preapproval');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . MP_ACCESS_TOKEN,

        'Content-Type: application/json'
    ],
    CURLOPT_POSTFIELDS => json_encode($payload),
]);

$response_raw = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$response = json_decode($response_raw, true);

/* =========================
   RESPUESTA
========================= */
if (!empty($response['init_point'])) {
    // 🔹 NUEVO: Guardar el ID de la suscripción antes de enviar al usuario a pagar
    if (!empty($response['id'])) {
        $up = $conn->prepare("UPDATE tiendas SET mp_preapproval_id = ? WHERE id = ?");
        $up->bind_param("si", $response['id'], $tienda_id);
        $up->execute();
        $up->close();
    }

    // 🔹 NUEVO: Avisar a Oscar que se solicitó la suscripción
    $asunto_admin = "📢 Suscripción Solicitada - " . $datos['nombre'];
    $mensaje_admin = "Hola Oscar.\n\nUn usuario ha solicitado la suscripción (botón pagar presionado).\nTienda: " . $datos['nombre'] . "\nID Tienda: " . $tienda_id . "\nEmail Payer: " . $datos['email'] . "\nMonto: " . $monto_final . " ARS\n\nEl usuario está siendo redirigido a Mercado Pago.";
    @mail("oscarns@gmail.com", $asunto_admin, $mensaje_admin, "From: noreply@cerodistancia.click");

    echo json_encode([
        'ok' => true,
        'init_point' => $response['init_point']
    ]);
    exit;
}

http_response_code($http_code ?: 500);
echo json_encode([
    'ok' => false,
    'http_code' => $http_code,
    'mp_response' => $response
]);
