<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

// 🔗 CONEXIÓN CENTRALIZADA
require_once __DIR__ . "/conexion.php";

// 🟦 ENTRADAS
$id = $_POST["id"] ?? "";
$email = $_POST["email"] ?? "";
$logo = $_POST["logo"] ?? "";

// 🔥 1. OBTENER DATOS ANTES DE BORRAR
$stmt = $conn->prepare("SELECT email, logo, slug, mp_preapproval_id FROM tiendas WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$tienda = $result->fetch_assoc();

if (!$tienda) {
    echo json_encode(["error" => "No se encontró la tienda"]);
    exit();
}

// 🔐 Validación de Permisos (Admin O Dueño de la tienda)
$admins = ["oscarns@gmail.com"];
$esAdmin = in_array($email, $admins);
$esDueño = ($tienda["email"] === $email && !empty($email));

if (!$esAdmin && !$esDueño) {
    echo json_encode(["error" => "No tienes permisos para borrar esta tienda"]);
    exit();
}

$logo = $tienda["logo"];
$slug = $tienda["slug"];
$mp_id = $tienda["mp_preapproval_id"];

// 🔥 2. DAR DE BAJA SUSCRIPCIÓN EN MERCADO PAGO (si existe)
if (!empty($mp_id)) {
    require_once __DIR__ . '/config_mp.php';

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
    $resp_mp = curl_exec($ch);
    curl_close($ch);
}

// 🔥 3. BORRAR LOGO DEL SERVIDOR
if (!empty($logo)) {
    $rutaLogo = dirname(__DIR__) . "/" . $logo;
    if (file_exists($rutaLogo) && is_file($rutaLogo)) {
        unlink($rutaLogo);
    }
}

// 🔥 4. BORRAR CARPETA EN /tiendas/
if (!empty($slug)) {
    $rutaTienda = dirname(__DIR__) . "/tiendas/" . $slug;
    if (is_dir($rutaTienda)) {
        // Función para borrar directorio no vacío
        function rrmdir($dir)
        {
            if (is_dir($dir)) {
                $objects = scandir($dir);
                foreach ($objects as $object) {
                    if ($object != "." && $object != "..") {
                        if (is_dir($dir . "/" . $object))
                            rrmdir($dir . "/" . $object);
                        else
                            unlink($dir . "/" . $object);
                    }
                }
                rmdir($dir);
            }
        }
        rrmdir($rutaTienda);
    }
}

// 🔥 5. BORRAR PRODUCTOS DE LA TIENDA
$stmtProd = $conn->prepare("DELETE FROM productos WHERE tienda_id = ?");
$stmtProd->bind_param("i", $id);
$stmtProd->execute();

// 🔥 6. BORRAR TIENDA DE LA BASE DE DATOS
$sql = "DELETE FROM tiendas WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(["ok" => true]);
} else {
    echo json_encode(["error" => "No se pudo eliminar la tienda de la DB"]);
}
