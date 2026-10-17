<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/conexion.php";

// 🔹 Datos del formulario
$uid = $_POST["uid"] ?? "";
$email = $_POST["email"] ?? "";   // 👈 NUEVO
$nombre = $_POST["nombre"] ?? "";
$descripcion = $_POST["descripcion"] ?? "";
$quienes = $_POST["quienes"] ?? "";

// 🔹 Validaciones
if (!$uid || !$email || !$nombre || !$descripcion) {
    echo json_encode(["error" => "Faltan datos obligatorios"]);
    exit();
}

// -------------------------------------------------------
// 🔹 SUBIR LOGO — opcional
// -------------------------------------------------------
$logo = "";

if (isset($_FILES["logo"]) && !empty($_FILES["logo"]["name"])) {

    $archivo = $_FILES["logo"];
    $ext = strtolower(pathinfo($archivo["name"], PATHINFO_EXTENSION));

    $permitidas = ["jpg", "jpeg", "png", "webp"];
    if (!in_array($ext, $permitidas)) {
        echo json_encode(["error" => "Formato de imagen no permitido"]);
        exit();
    }

    $nuevoNombre = "logo_" . time() . "_" . rand(1000, 9999) . "." . $ext;
    $destino = "../uploads/" . $nuevoNombre;

    if (!is_dir("../uploads")) {
        mkdir("../uploads", 0777, true);
    }

    if (move_uploaded_file($archivo["tmp_name"], $destino)) {
        $logo = "uploads/" . $nuevoNombre;
    }
}

// -------------------------------------------------------
// 🔹 GENERAR SLUG
// -------------------------------------------------------
$slug = preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower($nombre));
$slug = trim($slug, "-");
$slug .= "-" . rand(1000, 9999);

// -------------------------------------------------------
// 🔹 INSERTAR TIENDA
// -------------------------------------------------------
$sql = "INSERT INTO tiendas (
    uid_usuario,
    email,
    nombre,
    slug,
    descripcion,
    quienes,
    logo,
    estado,
    fecha_creacion
) VALUES (?, ?, ?, ?, ?, ?, ?, 'demo', NOW())";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "sssssss",
    $uid,
    $email,
    $nombre,
    $slug,
    $descripcion,
    $quienes,
    $logo
);

if ($stmt->execute()) {
    $asunto = "🚀 ¡Nueva Tienda Demo Creada!";
    $mensaje = "Hola Oscar.\n\nSe ha creado una nueva tienda en modo demo.\nNombre: $nombre\nSlug: $slug\nEmail: $email\nUID: $uid\n\nPuedes revisarla en el panel de admin.";
    @mail("oscarns@gmail.com", $asunto, $mensaje, "From: noreply@cerodistancia.click");

    echo json_encode([
        "ok" => true,
        "slug" => $slug
    ]);
} else {
    echo json_encode([
        "error" => "No se pudo crear la tienda"
    ]);
}
