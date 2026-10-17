<?php
require_once __DIR__ . '/conexion.php';

echo "--> Buscando una tienda en estado 'demo'...\n";
$res = $conn->query("SELECT id, nombre, estado FROM tiendas WHERE estado = 'demo' LIMIT 1");

if ($row = $res->fetch_assoc()) {
    $tienda_id = $row['id'];
    echo "✔ Tienda encontrada: [ID: {$tienda_id}] -> {$row['nombre']}\n";
    
    echo "--> Simulando que Mercado Pago envió un estado 'authorized'...\n";
    
    $preapproval_id = "simulacion_" . time();

    // Lógica idéntica al archivo webhook_mp.php
    $stmt = $conn->prepare("
        UPDATE tiendas
        SET 
            estado = 'activa',
            url_final = CONCAT('https://cerodistancia.click/', slug),
            fecha_aceptacion = NOW(),
            mp_preapproval_id = ?
        WHERE id = ?
    ");
    $stmt->bind_param("si", $preapproval_id, $tienda_id);
    
    if ($stmt->execute()) {
        echo "✔ ÉXITO: La tienda {$tienda_id} fue activada correctamente en la base de datos simulando el Webhook.\n";
    } else {
        echo "✘ ERROR al intentar activar tienda {$tienda_id}: " . $stmt->error . "\n";
    }
    $stmt->close();
} else {
    echo "⚠ No se encontró ninguna tienda en estado 'demo' para probar. Por favor crea una tienda primero.\n";
}
echo "--> Fin de simulación.\n";
?>
