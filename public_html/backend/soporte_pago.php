<?php
header("Content-Type: application/json; charset=utf-8");

// ==========================
// CONFIG
// ==========================
$EMAIL_DESTINO = "oscarns@gmail.com";
$MAX_SIZE = 5 * 1024 * 1024; // 5 MB

// ==========================
// VALIDAR DATOS
// ==========================
$nombre   = $_POST["nombre"]   ?? "";
$email    = $_POST["email"]    ?? "";
$mensaje  = $_POST["mensaje"]  ?? "";

if (!$nombre || !$email || !$mensaje) {
  echo json_encode(["ok" => false, "error" => "Datos incompletos"]);
  exit;
}

if (!isset($_FILES["comprobante"])) {
  echo json_encode(["ok" => false, "error" => "Falta comprobante"]);
  exit;
}

$archivo = $_FILES["comprobante"];

if ($archivo["error"] !== UPLOAD_ERR_OK) {
  echo json_encode(["ok" => false, "error" => "Error al subir archivo"]);
  exit;
}

if ($archivo["size"] > $MAX_SIZE) {
  echo json_encode(["ok" => false, "error" => "Archivo demasiado grande"]);
  exit;
}

// ==========================
// PHPMailer (SMTP)
// ==========================
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . "/lib/PHPMailer/src/Exception.php";
require __DIR__ . "/lib/PHPMailer/src/PHPMailer.php";
require __DIR__ . "/lib/PHPMailer/src/SMTP.php";

$mail = new PHPMailer(true);

try {
  // SMTP Gmail
  $mail->isSMTP();
  $mail->Host       = "smtp.gmail.com";
  $mail->SMTPAuth   = true;
  $mail->Username   = "oscarns@gmail.com";
  $mail->Password   = "uiqn ptew vphk cdss"; // ⬅️ CLAVE DE APLICACIÓN
  $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
  $mail->Port       = 587;

  // Headers
  $mail->setFrom("no-reply@cerodistancia.click", "CeroDistancia");
  $mail->addAddress($EMAIL_DESTINO);
  $mail->addReplyTo($email, $nombre);

  // Contenido
  $mail->Subject = "CeroDistancia — Pago realizado pero tienda no activa";
  $mail->Body =
    "PROBLEMA DE ACTIVACIÓN DE TIENDA\n\n" .
    "Nombre: $nombre\n" .
    "Email: $email\n\n" .
    "Mensaje:\n$mensaje";

  // Adjuntar comprobante
  $mail->addAttachment(
    $archivo["tmp_name"],
    $archivo["name"]
  );

  $mail->send();

  echo json_encode(["ok" => true]);

} catch (Exception $e) {
  echo json_encode([
    "ok" => false,
    "error" => $mail->ErrorInfo
  ]);
}
