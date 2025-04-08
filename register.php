<?php
require_once 'db.php'; // ← Importa la conexión

// Validar método HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Método no permitido.";
    exit;
}

// Recoger los datos del formulario
$username = trim($_POST['username']);
$nombre = trim($_POST['nombre']);
$password = $_POST['password'];

// Validación básica (opcional pero recomendable)
if (empty($username) || empty($nombre) || empty($password)) {
    echo "Todos los campos son obligatorios. <a href='register.html'>Volver</a>";
    exit;
}

// Verificar si ya existe el usuario
$check = $conn->prepare("SELECT id FROM login WHERE username = ?");
$check->bind_param("s", $username);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo "El nombre de usuario ya está registrado. <a href='register.html'>Volver</a>";
    $check->close();
    $conn->close();
    exit();
}
$check->close();

// Hashear la contraseña
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insertar nuevo usuario
$sql = "INSERT INTO login (username, password, nombre_completo, rol) VALUES (?, ?, ?, 'usuario')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $username, $hashedPassword, $nombre);

if ($stmt->execute()) {
    echo "✅ Registro exitoso. <a href='login.html'>Iniciar sesión</a>";
} else {
    echo "❌ Error al registrar: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>

