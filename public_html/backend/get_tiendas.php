<?php
header("Content-Type: application/json; charset=utf-8");
require_once __DIR__ . "/conexion.php";

$data = json_decode(file_get_contents("php://input"), true);
$uid = $data["uid"] ?? "";
$email = $data["email"] ?? "";

if (!$uid && !$email) {
    echo json_encode([]);
    exit;
}

$sql = "
SELECT 
    id,
    nombre,
    slug,
    descripcion,
    estado,
    url_final,
    mp_preapproval_id
FROM tiendas
WHERE uid_usuario = ? OR (email = ? AND email != '')
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $uid, $email);
$stmt->execute();

$result = $stmt->get_result();
$tiendas = [];

while ($t = $result->fetch_assoc()) {
    $activa = ($t["estado"] === "activa");
    $slug = $t["slug"];

    // Formatear URL pública: Si ya es una URL completa la dejamos, si no, la armamos relativa
    $url_publica = $t["url_final"];
    if (empty($url_publica) || strpos($url_publica, 'http') === false) {
        $url_publica = "/" . $slug;
    }

    $tiendas[] = [
        "id" => (int) $t["id"],
        "nombre" => $t["nombre"],
        "slug" => $slug,
        "descripcion" => $t["descripcion"],
        "activa" => $activa,
        "url_publica" => $url_publica,
        "mp_id" => $t["mp_preapproval_id"]
    ];
}

echo json_encode($tiendas);
exit;
