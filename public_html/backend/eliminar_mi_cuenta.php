<?php
header("Content-Type: application/json");
require_once __DIR__ . "/conexion.php";
require_once __DIR__ . "/config_mp.php";

// Leer datos del POST (JSON)
$data = json_decode(file_get_contents("php://input"), true);
$uid = $data["uid"] ?? "";

if (empty($uid)) {
    echo json_encode(["ok" => false, "error" => "UID requerido"]);
    exit;
}

// 1. Obtener la tienda de este usuario específico por su UID
$stmt = $conn->prepare("SELECT id, slug, mp_preapproval_id FROM tiendas WHERE uid_usuario = ?");
$stmt->bind_param("s", $uid);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(["ok" => false, "error" => "No se encontró ninguna tienda asociada a este usuario."]);
    exit;
}

while ($t = $res->fetch_assoc()) {
    $tid = $t["id"];
    $slug = $t["slug"];
    $mp_id = $t["mp_preapproval_id"];

    // A. Cancelar suscripción en Mercado Pago si tiene ID
    if (!empty($mp_id)) {
        try {
            $ch = curl_init('https://api.mercadopago.com/preapproval/' . $mp_id);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . MP_ACCESS_TOKEN,
                    'Content-Type: application/json'
                ],
                CURLOPT_POSTFIELDS => json_encode(['status' => 'cancelled']),
            ]);
            curl_exec($ch);
            curl_close($ch);
        } catch (Exception $e) {
            // Log error but continue deleting the local data
        }
    }

    // B. Borrar carpeta física si existe
    if (!empty($slug)) {
        $path = dirname(__DIR__) . "/tiendas/" . $slug;
        if (is_dir($path)) {
            $it = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);
            $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
            foreach($files as $file) {
                if ($file->isDir()){ rmdir($file->getRealPath()); } else { unlink($file->getRealPath()); }
            }
            rmdir($path);
        }
    }

    // C. Borrar productos vinculados
    $conn->query("DELETE FROM productos WHERE tienda_id = $tid");

    // D. Borrar la tienda de la base de datos
    $conn->query("DELETE FROM tiendas WHERE id = $tid");
}

echo json_encode(["ok" => true, "mensaje" => "Tu cuenta y tiendas han sido eliminadas correctamente."]);
exit;
