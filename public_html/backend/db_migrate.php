<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    mysqli_report(MYSQLI_REPORT_OFF);
    require_once "conexion.php";
    if (isset($conn) && $conn->connect_error) {
        throw new Exception($conn->connect_error);
    }
} catch (Throwable $e) {
    die("<h2 style='color:red;'>Error de conexión: " . $e->getMessage() . "</h2>");
}

echo "<h2>Iniciando migración de base de datos...</h2>";

// 1. Crear tabla tiendas si no existe
$conn->query("CREATE TABLE IF NOT EXISTS tiendas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uid_usuario VARCHAR(100),
    email VARCHAR(100),
    nombre VARCHAR(100),
    slug VARCHAR(100) UNIQUE,
    descripcion TEXT,
    quienes TEXT,
    telefono VARCHAR(50),
    logo VARCHAR(255),
    estado VARCHAR(20) DEFAULT 'demo',
    url_final VARCHAR(255),
    titulo_opciones VARCHAR(100),
    opcion1 VARCHAR(100),
    opcion2 VARCHAR(100),
    opcion3 VARCHAR(100),
    mp_preapproval_id VARCHAR(100),
    fecha_creacion DATETIME,
    fecha_aceptacion DATETIME
)");

// 2. Función auxiliar para agregar columnas
function agregarColumna($conn, $tabla, $columna, $definicion)
{
    $res = $conn->query("SHOW COLUMNS FROM $tabla LIKE '$columna'");
    if ($res && $res->num_rows == 0) {
        if ($conn->query("ALTER TABLE $tabla ADD $columna $definicion")) {
            echo "<p style='color:green;'>✔ Columna '$columna' agregada a '$tabla'.</p>";
        } else {
            echo "<p style='color:red;'>✘ Error agregando '$columna': " . $conn->error . "</p>";
        }
    } else {
        echo "<p style='color:blue;'>ℹ La columna '$columna' ya existe en '$tabla'.</p>";
    }
}

// 3. Agregar columnas faltantes a tiendas
agregarColumna($conn, 'tiendas', 'email', 'VARCHAR(100) AFTER uid_usuario');
agregarColumna($conn, 'tiendas', 'quienes', 'TEXT AFTER descripcion');
agregarColumna($conn, 'tiendas', 'titulo_opciones', 'VARCHAR(100) AFTER url_final');
agregarColumna($conn, 'tiendas', 'mp_preapproval_id', 'VARCHAR(100) AFTER opcion3');
agregarColumna($conn, 'tiendas', 'fecha_modificacion', 'DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER fecha_aceptacion');

// 4. Crear tabla productos si no existe
$conn->query("CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tienda_id INT,
    nombre VARCHAR(100),
    descripcion TEXT,
    precio DECIMAL(10,2),
    imagen VARCHAR(255),
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP
)");

// 5. Agregar columnas faltantes a productos (por si acaso)
agregarColumna($conn, 'productos', 'activo', 'TINYINT(1) DEFAULT 1 AFTER imagen');

echo "<h3>Migración finalizada con éxito.</h3>";
echo "<p><a href='../panel.html'>Ir al Panel</a></p>";
?>