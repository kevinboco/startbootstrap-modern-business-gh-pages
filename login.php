<?php
session_start();
require_once 'db.php'; // Conexión a la base de datos

// Validar método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Método no permitido.";
    exit;
}

// Recoger datos
$username = trim($_POST['username']);
$password = $_POST['password'];

// Verificar usuario
$sql = "SELECT id, username, password, nombre_completo FROM login WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    
    // Verificar contraseña
    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nombre'] = $user['nombre_completo'];

        // Redirigir a bienvenida
        header("Location: bienvenida.php");
        exit;
    } else {
        echo "Contraseña incorrecta. <a href='login.html'>Volver</a>";
    }
} else {
    echo "Usuario no encontrado. <a href='login.html'>Volver</a>";
}

$stmt->close();
$conn->close();
?>
