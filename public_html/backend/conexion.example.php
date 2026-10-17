<?php
$host = 'localhost';
mysqli_report(MYSQLI_REPORT_OFF);
$user = 'TU_USUARIO_DB';
$password = 'TU_PASSWORD_DB';
$database = 'cerodistancia';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>
