<?php
header("Content-Type: application/json");
require_once __DIR__ . "/conexion.php";
require_once __DIR__ . "/config_mp.php";

$data = json_decode(file_get_contents("php://input"), true);
$target_email = $data["target_email"] ?? "";
$admin_email = $data["admin_email"] ?? "";

$admins = ["oscarns@gmail.com"];
if (!in_array($admin_email, $admins)) {
    echo json_encode(["error" => "No tienes permisos de administrador"]);
    exit;
}

if (empty($target_email)) {
    echo json_encode(["error" => "Email de usuario requerido"]);
    exit;
}

// 1. Obtener todas las tiendas de ese usuario
$stmt = $conn->prepare("SELECT id, slug, mp_preapproval_id FROM tiendas WHERE email = ?");
$stmt->bind_param("s", $target_email);
$stmt->execute();
$res = $stmt->get_result();

$tiendas_borradas = 0;
while ($t = $res->fetch_assoc()) {
    $tid = $t["id"];
    $slug = $t["slug"];
    $mp_id = $t["mp_preapproval_id"];

    // A. Cancelar en MP si existe
    if (!empty($mp_id)) {
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
    }

    // B. Borrar archivos de la tienda (carpeta /tiendas/)
    if (!empty($slug)) {
        $path = dirname(__DIR__) . "/tiendas/" . $slug;
        if (is_dir($path)) {
            // Función simple para borrar dir
            $it = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);
            $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
            foreach($files as $file) {
                if ($file->isDir()){ rmdir($file->getRealPath()); } else { unlink($file->getRealPath()); }
            }
            rmdir($path);
        }
    }

    // C. Borrar productos de la tienda
    $conn->query("DELETE FROM productos WHERE tienda_id = $tid");

    // D. Borrar la tienda de la DB
    $conn->query("DELETE FROM tiendas WHERE id = $tid");
    $tiendas_borradas++;
}

echo json_encode(["ok" => true, "mensaje" => "Se eliminó el usuario '$target_email' y sus $tiendas_borradas tiendas correctamente."]);
exit;
?>
