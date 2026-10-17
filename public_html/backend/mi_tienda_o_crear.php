<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/conexion.php";

/* =========================
   ENTRADAS
========================= */
$uid         = trim($_POST["uid"] ?? "");
$nombre      = trim($_POST["nombre"] ?? "");
$descripcion = trim($_POST["descripcion"] ?? "");
$quienes     = trim($_POST["quienes"] ?? "");

/* =========================
   UID OBLIGATORIO
========================= */
if ($uid === "") {
  echo json_encode(["ok"=>false, "error"=>"UID faltante"]);
  exit;
}

/* =========================
   1) SI YA EXISTE TIENDA -> DEVOLVERLA
========================= */
$sql = "SELECT id, slug, estado, url_final
        FROM tiendas
        WHERE uid_usuario = ?
        LIMIT 1";
$stmt = $conn->prepare($sql);
if (!$stmt) {
  echo json_encode(["ok"=>false, "error"=>"DB prepare fail"]);
  exit;
}
$stmt->bind_param("s", $uid);
$stmt->execute();
$res = $stmt->get_result();

if ($res && $res->num_rows === 1) {
  $t = $res->fetch_assoc();
  echo json_encode([
    "ok"        => true,
    "existe"    => true,
    "tienda_id" => (int)$t["id"],
    "slug"      => $t["slug"],
    "estado"    => $t["estado"],
    "url_final" => $t["url_final"] ?? null
  ]);
  exit;
}

/* =========================
   2) SI NO EXISTE -> CREAR (REQUIERE NOMBRE + DESCRIPCION)
========================= */
if ($nombre === "" || $descripcion === "") {
  echo json_encode([
    "ok"=>false,
    "existe"=>false,
    "error"=>"No existe tienda. Enviá nombre y descripcion para crear."
  ]);
  exit;
}

/* =========================
   SUBIR LOGO (OPCIONAL)
========================= */
$logo = "";
if (!empty($_FILES["logo"]["name"])) {
  $archivo = $_FILES["logo"];
  $ext = strtolower(pathinfo($archivo["name"], PATHINFO_EXTENSION));
  $permitidas = ["jpg","jpeg","png","webp","avif"];
  if (!in_array($ext, $permitidas)) {
    echo json_encode(["ok"=>false, "error"=>"Formato de imagen no permitido"]);
    exit;
  }
  if (!is_dir("../uploads")) mkdir("../uploads", 0777, true);

  $nuevoNombre = "logo_" . time() . "_" . rand(1000,9999) . "." . $ext;
  $destino = "../uploads/" . $nuevoNombre;

  if (move_uploaded_file($archivo["tmp_name"], $destino)) {
    $logo = "uploads/" . $nuevoNombre;
  }
}

/* =========================
   SLUG
========================= */
$slug = preg_replace('/[^a-z0-9]+/i', '-', strtolower($nombre));
$slug = trim($slug, "-");
$slug = $slug . "-" . rand(1000,9999);

/* =========================
   INSERT
========================= */
$sql2 = "INSERT INTO tiendas
         (uid_usuario, nombre, slug, descripcion, quienes, logo, estado, fecha_creacion)
         VALUES (?, ?, ?, ?, ?, ?, 'demo', NOW())";
$stmt2 = $conn->prepare($sql2);
if (!$stmt2) {
  echo json_encode(["ok"=>false, "error"=>"DB prepare insert fail"]);
  exit;
}

$stmt2->bind_param("ssssss", $uid, $nombre, $slug, $descripcion, $quienes, $logo);

if (!$stmt2->execute()) {
  echo json_encode(["ok"=>false, "error"=>"No se pudo crear la tienda"]);
  exit;
}

$asunto = "🚀 ¡Nueva Tienda Demo Creada!";
$mensaje = "Hola Oscar.\n\nSe ha creado una nueva tienda en modo demo.\nNombre: $nombre\nSlug: $slug\nUID: $uid\n\nPuedes revisarla en el panel de admin.";
@mail("oscarns@gmail.com", $asunto, $mensaje, "From: noreply@cerodistancia.click");

echo json_encode([
  "ok"        => true,
  "existe"    => false,
  "tienda_id" => (int)$stmt2->insert_id,
  "slug"      => $slug,
  "estado"    => "demo",
  "url_final" => null
]);
exit;
