<?php
// backend/generar_tienda_final.php
// Ahora SOLO marca la tienda como "activa" y devuelve la URL tienda.php?slug=...

header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);
error_reporting(E_ALL);

set_error_handler(function($s, $m, $f, $l){
    throw new ErrorException($m, 0, $s, $f, $l);
});

try {
    require_once __DIR__ . '/conexion.php';

    if (!$conn || $conn->connect_error) {
        throw new Exception("Error al conectar a la base: " . $conn->connect_error);
    }

    // 1) Leer JSON
    $raw  = file_get_contents("php://input");
    $data = json_decode($raw, true);

    if (!is_array($data) || empty($data['tienda_id'])) {
        echo json_encode(["ok"=>false, "error"=>"Falta tienda_id"]);
        exit;
    }

    $tienda_id = (int)$data['tienda_id'];

    // 2) Buscar tienda
    $sql = "SELECT id, nombre, slug, estado 
            FROM tiendas
            WHERE id = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    if (!$stmt) throw new Exception("Error en prepare SELECT");

    $stmt->bind_param("i", $tienda_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        echo json_encode(["ok"=>false, "error"=>"Tienda no encontrada"]);
        exit;
    }

    $t = $res->fetch_assoc();
    $stmt->close();

    $slug   = trim($t['slug'] ?? '', " /");
    $estado = $t['estado'] ?? '';

    if ($slug === '') {
        throw new Exception("La tienda no tiene slug definido.");
    }

    // 3) Si ya está activa, no hacemos nada raro
    $url_final = "tienda.php?slug=" . $slug;

    if ($estado === 'activa') {
        echo json_encode([
            "ok"        => true,
            "mensaje"   => "La tienda ya estaba activa.",
            "url_final" => $url_final
        ]);
        exit;
    }

    // 4) Marcar como ACTIVA
    $sql2 = "UPDATE tiendas
             SET estado = 'activa',
                 url_final = ?,
                 fecha_aceptacion = NOW()
             WHERE id = ?";
    $stmt2 = $conn->prepare($sql2);
    if (!$stmt2) throw new Exception("Error en prepare UPDATE");

    $stmt2->bind_param("si", $url_final, $tienda_id);
    $stmt2->execute();
    $stmt2->close();

    echo json_encode([
        "ok"        => true,
        "mensaje"   => "Tienda activada correctamente",
        "url_final" => $url_final
    ]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        "ok"    => false,
        "error" => $e->getMessage()
    ]);
}
