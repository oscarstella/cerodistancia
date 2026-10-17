<?php
header("Content-Type: application/json; charset=utf-8");
require_once __DIR__ . "/conexion.php";

$data = json_decode(file_get_contents("php://input"), true);
$email = $data["email"] ?? "";

// 🔹 LISTA DE ADMINS (Podés agregar más acá)
$admins = ["oscarns@gmail.com"];

if (!in_array($email, $admins)) {
    echo json_encode(["error" => "No tienes permisos de administrador"]);
    exit;
}

// Chequear si existe la columna fecha_modificacion
$resCols = $conn->query("SHOW COLUMNS FROM tiendas LIKE 'fecha_modificacion'");
$has_mod = ($resCols && $resCols->num_rows > 0);
$mod_str = $has_mod ? ", fecha_modificacion" : "";

$sql = "
SELECT 
    id,
    nombre,
    slug,
    descripcion,
    estado,
    url_final,
    email as usuario_email,
    mp_preapproval_id,
    fecha_creacion
    $mod_str
FROM tiendas
ORDER BY id DESC
";

$result = $conn->query($sql);
$tiendas = [];

while ($t = $result->fetch_assoc()) {
    $activa = ($t["estado"] === "activa");
    $slug = $t["slug"];

    $url_publica = $t["url_final"];
    if (empty($url_publica) || strpos($url_publica, 'http') === false) {
        $url_publica = "/" . $slug;
    }

    $tiendas[] = [
        "id" => (int) $t["id"],
        "nombre" => $t["nombre"],
        "slug" => $slug,
        "descripcion" => $t["descripcion"],
        "usuario_email" => $t["usuario_email"],
        "mp_preapproval_id" => $t["mp_preapproval_id"],
        "activa" => $activa,
        "estado" => $t["estado"],
        "url_publica" => $url_publica,
        "fecha" => $t["fecha_creacion"],
        "modificacion" => $has_mod ? ($t["fecha_modificacion"] ?? null) : null
    ];
}

echo json_encode($tiendas);
exit;
