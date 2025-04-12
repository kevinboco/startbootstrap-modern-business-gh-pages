<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}
require_once 'db.php';

if (!isset($_GET['id'])) {
    echo "ID no válido.";
    exit;
}

$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM cuentas_cobro WHERE id = $id");

if ($result->num_rows === 0) {
    echo "Cuenta no encontrada.";
    exit;
}

$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Cuenta de Cobro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <h1>Actualizar Captura de Pago</h1>
    <form action="procesar_edicion.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $row['id'] ?>">
        
        <div class="mb-3">
            <label>Nombre del viaje:</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($row['nombre_viaje']) ?>" disabled>
        </div>

        <div class="mb-3">
            <label>Subir nueva captura de pago:</label>
            <input type="file" name="captura_pago" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="ver_cuentas_cobro.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
