<?php
$host = "mysql.hostinger.com";
$dbname = "u648222299_zonanorte";  // ← nombre completo de la base de datos
$user = "u648222299_keboco1";        // ← usuario completo con prefijo
$pass = "Bucaramanga3011";

// Conexión a la base de datos
$conn = new mysqli($host, $user, $pass, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
