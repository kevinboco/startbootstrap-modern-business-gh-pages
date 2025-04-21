<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}
require_once 'db.php';

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
    <h1>Editar Cuenta de Cobro</h1>
    <form action="procesar_edicion.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $row['id'] ?>">

        <div class="mb-3">
            <label>Nombre del viaje:</label>
            <input type="text" name="nombre_viaje" class="form-control" value="<?= htmlspecialchars($row['nombre_viaje']) ?>" required>
        </div>

        <div class="mb-3">
            <label>Fecha:</label>
            <input type="date" name="fecha" class="form-control" value="<?= $row['fecha'] ?>" required>
        </div>

        <div class="mb-3">
            <label>Cuenta de cobro enviada a la empresa:</label>
            <?php if (!empty($row['cuenta_empresa'])): ?>
                <p>Archivo actual: <a href="/asociacion/<?= htmlspecialchars($row['cuenta_empresa']) ?>" target="_blank"><?= basename($row['cuenta_empresa']) ?></a></p>
            <?php endif; ?>
            <input type="file" name="cuenta_empresa" class="form-control">
        </div>

        <div class="mb-3">
            <label>Cuenta de cobro:</label>
            <?php if (!empty($row['cuenta_cobro'])): ?>
                <p>Archivo actual: <a href="/asociacion/<?= htmlspecialchars($row['cuenta_cobro']) ?>" target="_blank"><?= basename($row['cuenta_cobro']) ?></a></p>
            <?php endif; ?>
            <input type="file" name="cuenta_cobro" class="form-control">
        </div>

        <div class="mb-3">
            <label>Captura de pago realizado:</label>
            <?php if (!empty($row['captura_pago_realizado'])): ?>
                <p>Archivo actual: <a href="/asociacion/informacion/<?= htmlspecialchars($row['captura_pago_realizado']) ?>" target="_blank"><?= basename($row['captura_pago_realizado']) ?></a></p>
            <?php endif; ?>
            <input type="file" name="captura_pago_realizado" class="form-control">
        </div>

        <button type="submit" class="btn btn-success">Guardar cambios</button>
        <a href="ver_cuentas_cobro.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
