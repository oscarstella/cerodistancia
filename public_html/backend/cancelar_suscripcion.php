<?php
header("Content-Type: application/json; charset=utf-8");
require_once __DIR__ . "/conexion.php";
require_once __DIR__ . "/config_mp.php";

$data = json_decode(file_get_contents("php://input"), true);
$tienda_id = $data["tienda_id"] ?? "";
$email = $data["email"] ?? ""; // Email del usuario para verificar pertenencia

if (!$tienda_id || !$email) {
    echo json_encode(["error" => "Datos incompletos"]);
    exit;
}

// 1. Verificar que la tienda pertenezca al usuario (o ser Admin)
$stmt = $conn->prepare("SELECT id, email, mp_preapproval_id FROM tiendas WHERE id = ?");
$stmt->bind_param("i", $tienda_id);
$stmt->execute();
$res = $stmt->get_result();
$tienda = $res->fetch_assoc();

if (!$tienda) {
    echo json_encode(["error" => "Tienda no encontrada"]);
    exit;
}

$admins = ["oscarns@gmail.com"];
$esAdmin = in_array($email, $admins);
$esDueño = ($tienda["email"] === $email && !empty($email));

if (!$esAdmin && !$esDueño) {
    echo json_encode(["error" => "No tienes permisos para cancelar esta suscripción"]);
    exit;
}

$mp_id = $tienda["mp_preapproval_id"];

if (empty($mp_id)) {
    echo json_encode(["error" => "No hay una suscripción activa para cancelar"]);
    exit;
}

// 2. Determinar acción
$accion = $data["accion"] ?? "cancelar"; // 'cancelar', 'pausar', 'reanudar'
$nuevo_estado_mp = 'cancelled';
$nuevo_estado_db = 'demo';

if ($accion === 'pausar') {
    $nuevo_estado_mp = 'paused';
    $nuevo_estado_db = 'pausada';
} elseif ($accion === 'reanudar') {
    $nuevo_estado_mp = 'authorized';
    $nuevo_estado_db = 'activa';
}

// 3. Modificar en Mercado Pago (SÓLO SI TIENE ID)
if (!empty($mp_id)) {
    $ch = curl_init('https://api.mercadopago.com/preapproval/' . $mp_id);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'PUT',
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . MP_ACCESS_TOKEN,
            'Content-Type: application/json'
        ],
        CURLOPT_POSTFIELDS => json_encode(['status' => $nuevo_estado_mp]),
    ]);
    $resp_raw = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $resp = json_decode($resp_raw, true);

    if ($http_code !== 200 && $http_code !== 201 && ($resp['status'] ?? '') !== $nuevo_estado_mp) {
        echo json_encode(["error" => "Error al cambiar estado en Mercado Pago", "detail" => $resp]);
        exit;
    }
}

// 4. Actualizar base de datos local
$query = "UPDATE tiendas SET estado = '$nuevo_estado_db'";
if ($nuevo_estado_db === 'demo') {
    $query .= ", url_final = NULL";
}
$query .= " WHERE id = ?";

$stmtUpd = $conn->prepare($query);
$stmtUpd->bind_param("i", $tienda_id);

if ($stmtUpd->execute()) {
    $msg = "La tienda ha sido cambiada a '$nuevo_estado_db'.";
    if (!empty($mp_id)) $msg .= " Suscripción en MP actualizada a '$nuevo_estado_mp'.";
    echo json_encode(["ok" => true, "mensaje" => $msg]);
} else {
    echo json_encode(["error" => "Error al actualizar la base de datos local"]);
}
?>