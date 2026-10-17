<?php
// SIMULADOR DE WEBHOOK
// Este script simula ser MercadoPago enviando una notificación de "subscription_preapproval"
// ÚSALO SOLO PARA TESTEAR QUE TU LOGICA DE BASE DE DATOS FUNCIONA.

$tienda_id = $_GET['id'] ?? 0;

if (!$tienda_id) {
    die("Debes pasar el ID de la tienda: test_webhook.php?id=TU_ID_DE_TIENDA");
}

// 1. URL de tu webhook real
$url = "http://localhost/cerodistancia/backend/webhook_mp.php";
// SI ESTAS EN HOSTING USAR: 
// $url = "https://cerodistancia.click/backend/webhook_mp.php";

// 2. Datos falsos de notificación
$data = [
    "type" => "subscription_preapproval",
    "data" => [
        "id" => "PREAPPROVAL_FAKE_123" // ID Falso que tu webhook intentará consultar a la API Real y FALLARÁ si no existe
    ]
];

// NOTA: Como el ID es falso, el Webhook intentará consultar a la API de MP y fallará (404 Not Found).
// PARA PROBAR, NECESITAS MODIFICAR TEMPORALMENTE TU webhook_mp.php PARA QUE NO CONSULTE A LA API
// O BIEN, HACER UN "MOCK" DENTRO DE ESTE TEST.

echo "<h1>Simulación de Webhook</h1>";
echo "<p>Enviando notificación POST a: $url</p>";
echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>";

// Hacemos el POST
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$response = curl_exec($ch);
curl_close($ch);

echo "<h2>Respuesta del Webhook:</h2>";
echo "<pre>$response</pre>";
echo "<hr>";
echo "<h3>Instrucciones para prueba manual (Bypass de validación MP):</h3>";
echo "Si quieres ver la tienda activarse SIN un pago real, edita backend/webhook_mp.php y comenta la verificación de la API de MercadoPago, forzando la activación.";
