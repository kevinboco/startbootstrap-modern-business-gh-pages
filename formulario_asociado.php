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
    <meta charset="UTF-8">
    <title>Formulario Asociado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2>Formulario de Información del Asociado</h2>
    <form action="subir_archivo.php" method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre completo</label>
            <input type="text" class="form-control" id="nombre" name="nombre_persona" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Archivo RUT</label>
            <input type="file" class="form-control" name="rut" accept=".pdf,.jpg,.jpeg,.png" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Certificado Bancario</label>
            <input type="file" class="form-control" name="certificado_bancario" accept=".pdf,.jpg,.jpeg,.png" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Cédula</label>
            <input type="file" class="form-control" name="cedula" accept=".pdf,.jpg,.jpeg,.png" required>
        </div>
        <button type="submit" class="btn btn-primary">Enviar Información</button>
    </form>
    <a href="bienvenida.php" class="btn btn-secondary mt-3">Volver</a>
</div>
</body>
</html>
