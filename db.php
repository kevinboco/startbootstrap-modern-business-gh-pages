<?php
$host = "localhost";
$dbname = "asociacion";
$user = "root";
$pass = "";

// Conexión a la base de datos
$conn = new mysqli($host, $user, $pass, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
