<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>ASOCIACIÓNES DE TRANSPORTISTAS ZONA NORTE WUINPUMUIN</title>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
<div class="container mt-5">
    <h1 class="mb-4">Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?>!</h1>

    <div class="d-grid gap-3 col-6 mx-auto">
        <a href="formulario_asociado.php" class="btn btn-primary btn-lg">Formulario de Información del Asociado</a>
        <a href="ver_documentos.php" class="btn btn-secondary btn-lg">Ver Documentos Subidos</a>
        <a href="index.html" class="btn btn-danger btn-lg">Volver al Inicio</a>
    </div>
</div>
</body>
</html>
