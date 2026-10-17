<?php
header("Content-Type: application/json; charset=utf-8");
require_once __DIR__ . "/conexion.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . "/lib/PHPMailer/src/Exception.php";
require_once __DIR__ . "/lib/PHPMailer/src/PHPMailer.php";
require_once __DIR__ . "/lib/PHPMailer/src/SMTP.php";

$data = json_decode(file_get_contents("php://input"), true);
$admin_email = $data["admin_email"] ?? "";
$asunto      = trim($data["asunto"] ?? "");
$mensaje     = trim($data["mensaje"] ?? "");
$destinatario = trim($data["destinatario"] ?? "todos"); // "todos" o un email individual

$admins = ["oscarns@gmail.com"];
if (!in_array($admin_email, $admins)) {
    echo json_encode(["ok" => false, "error" => "No tienes permisos de administrador."]);
    exit;
}

if (empty($asunto) || empty($mensaje)) {
    echo json_encode(["ok" => false, "error" => "El asunto y el mensaje son obligatorios."]);
    exit;
}

// Obtener la lista de emails de los clientes dueños de tiendas
$destinatarios = [];

if ($destinatario !== "todos" && filter_var($destinatario, FILTER_VALIDATE_EMAIL)) {
    $destinatarios[] = $destinatario;
} else {
    $res = $conn->query("SELECT DISTINCT email FROM tiendas WHERE email IS NOT NULL AND email != ''");
    while ($row = $res->fetch_assoc()) {
        $em = trim($row["email"]);
        if (filter_var($em, FILTER_VALIDATE_EMAIL) && !in_array($em, $destinatarios)) {
            $destinatarios[] = $em;
        }
    }
}

if (empty($destinatarios)) {
    echo json_encode(["ok" => false, "error" => "No se encontraron clientes con email registrado para enviar."]);
    exit;
}

$enviados = 0;
$fallidos = 0;
$errores = [];

foreach ($destinatarios as $targetEmail) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = "smtp.gmail.com";
        $mail->SMTPAuth   = true;
        $mail->Username   = "oscarns@gmail.com";
        $mail->Password   = "uiqn ptew vphk cdss"; // Clave de aplicación SMTP
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = "UTF-8";
        $mail->Timeout    = 15;

        // Opciones SSL para compatibilidad con servidores cPanel/OpenSSL
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        // En Gmail SMTP el remitente debe ser la misma cuenta autenticada para evitar rechazos
        $mail->setFrom("oscarns@gmail.com", "CeroDistancia");
        $mail->addAddress($targetEmail);
        $mail->addReplyTo("oscarns@gmail.com", "CeroDistancia Soporte");

        $mail->Subject = $asunto;
        $mail->isHTML(true);

        // Plantilla HTML elegante de CeroDistancia
        $mail->Body = "
        <div style='background-color:#0b0f17; color:#f9fafb; font-family:sans-serif; padding:30px; border-radius:12px; max-width:600px; margin:auto;'>
            <div style='text-align:center; margin-bottom:20px;'>
                <h2 style='color:#10b981; margin:0;'>CeroDistancia</h2>
                <p style='color:#9ca3af; font-size:14px; margin-top:5px;'>Novedades y Actualizaciones para tu Tienda Online</p>
            </div>
            <div style='background-color:#111827; padding:25px; border-radius:10px; border:1px solid #1f2937;'>
                <div style='font-size:15px; line-height:1.6; color:#e5e7eb; white-space:pre-line;'>
                    " . nl2br(htmlspecialchars($mensaje)) . "
                </div>
            </div>
            <div style='text-align:center; margin-top:25px; color:#6b7280; font-size:12px;'>
                <p>Estás recibiendo este correo porque sos cliente de cerodistancia.click</p>
            </div>
        </div>";

        $enviadoExito = false;

        // Intento 1: Gmail SMTP Puerto 587 TLS
        try {
            $mail->send();
            $enviadoExito = true;
        } catch (Exception $e1) {
            // Intento 2: Gmail SMTP Puerto 465 SSL directo (muchos cPanel bloquean el 587 y permiten el 465)
            try {
                $mail2 = new PHPMailer(true);
                $mail2->isSMTP();
                $mail2->Host       = "smtp.gmail.com";
                $mail2->SMTPAuth   = true;
                $mail2->Username   = "oscarns@gmail.com";
                $mail2->Password   = "uiqn ptew vphk cdss";
                $mail2->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail2->Port       = 465;
                $mail2->CharSet    = "UTF-8";
                $mail2->Timeout    = 15;
                $mail2->SMTPOptions = [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    ]
                ];
                $mail2->setFrom("oscarns@gmail.com", "CeroDistancia");
                $mail2->addAddress($targetEmail);
                $mail2->addReplyTo("oscarns@gmail.com", "CeroDistancia Soporte");
                $mail2->Subject = $asunto;
                $mail2->isHTML(true);
                $mail2->Body = $mail->Body;
                $mail2->AltBody = $mensaje;

                $mail2->send();
                $enviadoExito = true;
            } catch (Exception $e2) {
                // Intento 3: mail() nativo de PHP del servidor si el firewall del hosting tiene bloqueados los puertos externos de Gmail
                $headers  = "MIME-Version: 1.0\r\n";
                $headers .= "Content-type: text/html; charset=UTF-8\r\n";
                $headers .= "From: CeroDistancia <oscarns@gmail.com>\r\n";
                $headers .= "Reply-To: oscarns@gmail.com\r\n";
                $headers .= "X-Mailer: PHP/" . phpversion();

                if (@mail($targetEmail, $asunto, $mail->Body, $headers)) {
                    $enviadoExito = true;
                } else {
                    $errores[] = $targetEmail . ": (587: " . $mail->ErrorInfo . ") (465: " . $mail2->ErrorInfo . ")";
                }
            }
        }

        if ($enviadoExito) {
            $enviados++;
        } else {
            $fallidos++;
        }
    } catch (Exception $e) {
        $fallidos++;
        $errores[] = $targetEmail . ": " . $e->getMessage();
    }
}

$detalleMsg = "Envío finalizado: $enviados correos enviados con éxito.";
if ($fallidos > 0) {
    $detalleMsg = "Se enviaron $enviados correos. Fallaron $fallidos (" . implode(", ", $errores) . ")";
}

echo json_encode([
    "ok" => ($enviados > 0),
    "mensaje" => $detalleMsg,
    "enviados" => $enviados,
    "fallidos" => $fallidos,
    "errores" => $errores
]);
