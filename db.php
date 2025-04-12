<?php
$host = "mysql.hostinger.com";
$dbname = "zonanorte";
$user = "keboco1";
$pass = "Bucaramanga3011";

// Conexión a la base de datos
$conn = new mysqli($host, $user, $pass, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
