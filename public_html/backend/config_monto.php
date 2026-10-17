<?php
header("Content-Type: application/json");
require_once __DIR__ . "/conexion.php";

// Crear tabla si no existe
$conn->query("CREATE TABLE IF NOT EXISTS configuracion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(50) UNIQUE NOT NULL,
    valor VARCHAR(255) NOT NULL
)");

// Asegurar que exista el monto_suscripcion inicial si no está
$check = $conn->query("SELECT id FROM configuracion WHERE clave = 'monto_suscripcion'");
if ($check->num_rows === 0) {
    $conn->query("INSERT INTO configuracion (clave, valor) VALUES ('monto_suscripcion', '100')");
}

$data = json_decode(file_get_contents("php://input"), true);
$accion = $_GET["accion"] ?? "get";

if ($accion === "get") {
    $res = $conn->query("SELECT valor FROM configuracion WHERE clave = 'monto_suscripcion'");
    $row = $res->fetch_assoc();
    echo json_encode(["monto" => $row["valor"]]);
    exit;
}

if ($accion === "set") {
    $admin_email = $data["admin_email"] ?? "";
    if ($admin_email !== "oscarns@gmail.com") {
        echo json_encode(["error" => "No autorizado"]);
        exit;
    }
    
    $nuevo_monto = $data["monto"] ?? "100";
    $stmt = $conn->prepare("UPDATE configuracion SET valor = ? WHERE clave = 'monto_suscripcion'");
    $stmt->bind_param("s", $nuevo_monto);
    if ($stmt->execute()) {
        echo json_encode(["ok" => true]);
    } else {
        echo json_encode(["error" => "Error al guardar"]);
    }
    exit;
}
?>
