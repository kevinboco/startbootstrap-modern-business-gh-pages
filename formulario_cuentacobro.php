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
    <title>consolidado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">consolidado</h2>
    <form action="subir_cuentacobro.php" method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="fecha" class="form-label">Fecha del viaje</label>
                                <input type="date" class="form-control" id="fecha" name="fecha" required>
                            </div>
                            <div class="mb-3">
                                <label for="nombre_viaje" class="form-label">Nombre del viaje</label>
                                <input type="text" class="form-control" id="nombre_viaje" name="nombre_viaje" required>
                            </div>
                            
                            <!-- 🔄 CAMBIO AQUÍ -->
                            <div class="mb-3">
                                <label class="form-label">Cuenta de cobro para la empresa</label>
                                <input type="file" class="form-control" name="cuenta_empresa" accept=".docx,.pdf,.jpg,.jpeg,.png">
                            </div>

                            <div class="mb-3">
                                <label for="cuenta_cobro" class="form-label">Consolidado (.xlsx, .xls)</label>
                                <input type="file" class="form-control" id="cuenta_cobro" name="cuenta_cobro" accept=".xlsx,.xls" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Cuentas de cobro de los afiliados</label>
                                <input type="file" class="form-control" name="cuentas_trabajadores[]" multiple >
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Captura de pago realizado</label>
                                <input type="file" class="form-control" name="captura_pago_realizado" accept=".pdf,.jpg,.jpeg,.png">
                            </div>

                            <button type="submit" class="btn btn-success">Subir</button>
                            <a href="bienvenida.php" class="btn btn-secondary ms-2">Volver</a>
        </form>

</div>
</body>
</html>
