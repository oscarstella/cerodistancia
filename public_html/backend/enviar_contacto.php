<?php
header("Content-Type: text/html; charset=utf-8");

$nombre = $_POST['nombre'] ?? '';
$email = $_POST['email'] ?? '';
$mensaje = $_POST['mensaje'] ?? '';

if (!$nombre || !$email || !$mensaje) {
    echo "<script>alert('Por favor completa todos los campos'); window.history.back();</script>";
    exit;
}

// Envío de correo a tu cuenta oscarns@gmail.com
$destinatario = 'oscarns@gmail.com';
$asunto = 'Nueva consulta desde CeroDistancia';
$cuerpo = "Nombre: $nombre\nEmail: $email\nMensaje: $mensaje";
$headers = "From: no-reply@cerodistancia.shop\r\nReply-To: $email\r\nX-Mailer: PHP/" . phpversion();

mail($destinatario, $asunto, $cuerpo, $headers);

echo "<script>alert('¡Gracias! Tu mensaje fue enviado con éxito. Nos pondremos en contacto pronto.'); window.location.href='../index.html';</script>";
