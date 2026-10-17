<?php
// MOSTRAR TODOS LOS ERRORES
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<pre>Iniciando prueba de conexión...\n\n";

require_once "conexion.php";

echo "✔ La conexión a MySQL se realizó correctamente.\n";
echo "</pre>";
?>
