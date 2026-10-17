<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/config_mp.php';

/* ===============================
   LEER NOTIFICACIÓN DE MERCADO PAGO
================================= */
$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
  http_response_code(200);
  exit;
}

/* ===============================
   SOLO SUSCRIPCIONES
================================= */
if (($data['type'] ?? '') !== 'subscription_preapproval') {
  http_response_code(200);
  exit;
}

$preapproval_id = $data['data']['id'] ?? null;
if (!$preapproval_id) {
  http_response_code(200);
  exit;
}

/* ===============================
   ACCESS TOKEN DE PRUEBA (SANDBOX)
================================= */
/* ===============================
   CONSULTAR ESTADO EN MP
   ================================= */
// USAR EL ACCESS TOKEN DE PRODUCCION (O SANDBOX SI ESTAS PROBANDO)
// EN PRODUCCION USAR $MP_ACCESS_TOKEN DEFINIDO EN VARIABLES O CONF


$ch = curl_init("https://api.mercadopago.com/preapproval/$preapproval_id");
curl_setopt_array($ch, [
  CURLOPT_HTTPHEADER => [
    "Authorization: Bearer " . MP_ACCESS_TOKEN

  ],
  CURLOPT_RETURNTRANSFER => true
]);

$response = curl_exec($ch);
curl_close($ch);

$mp = json_decode($response, true);

/* ===============================
   ACTIVAR TIENDA SI ESTÁ AUTORIZADA
   ================================= */
$status = $mp['status'] ?? '';
$ref = $mp['external_reference'] ?? '';
$tienda_id = (strpos($ref, 'tienda_') === 0) ? intval(substr($ref, 7)) : 0;

if ($status === 'authorized' && $tienda_id > 0) {
    // 1. ACTIVAR EN DB
    $stmt = $conn->prepare("UPDATE tiendas SET estado = 'activa', url_final = CONCAT('https://cerodistancia.click/', slug), fecha_aceptacion = NOW(), mp_preapproval_id = ? WHERE id = ?");
    $stmt->bind_param("si", $preapproval_id, $tienda_id);
    $stmt->execute();
    $stmt->close();

    // 2. ENVIAR AVISO AL ADMIN
    $asunto = "🚀 ¡Nueva Tienda Activada! ID: $tienda_id";
    $mensaje = "Hola Oscar.\n\nSe ha activado exitosamente una suscripción.\nID Tienda: $tienda_id\nFirma MP: $preapproval_id\nEstado: AUTORIZADO\n\nEl sistema ya habilitó la URL pública de esta tienda.";
    mail("oscarns@gmail.com", $asunto, $mensaje, "From: noreply@cerodistancia.click");

} elseif ($tienda_id > 0) {
    // 1. DESACTIVAR EN DB (BAJAR A DEMO)
    $stmt = $conn->prepare("UPDATE tiendas SET estado = 'demo', url_final = NULL WHERE id = ?");
    $stmt->bind_param("i", $tienda_id);
    $stmt->execute();
    $stmt->close();

    // 2. ENVIAR AVISO DE FALLO/BAJA AL ADMIN
    $asunto = "⚠️ Alerta: Pago Fallido o Baja - ID: $tienda_id";
    $mensaje = "Atención Oscar.\n\nHa ocurrido un evento de baja o pago rechazado.\nID Tienda: $tienda_id\nEstado MP detectado: $status\n\nLa tienda ha sido bajada a modo DEMO automáticamente.";
    mail("oscarns@gmail.com", $asunto, $mensaje, "From: noreply@cerodistancia.click");
}

http_response_code(200);
exit;
