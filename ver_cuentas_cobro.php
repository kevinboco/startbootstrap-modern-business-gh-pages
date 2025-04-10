<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}
require_once 'db.php'; // Conexión a la base de datos
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ver Cuentas de Cobro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-control {
            max-width: 300px;
        }
    </style>
</head>
<body class="bg-light">
<div class="container mt-4">
    <h1 class="mb-4">Cuentas de Cobro</h1>

    <div class="mb-3 d-flex gap-3 flex-wrap">
        <input type="text" id="buscarNombre" class="form-control" placeholder="Buscar por nombre del viaje">
        <input type="date" id="buscarFecha" class="form-control">
        <a href="bienvenida.php" class="btn btn-secondary">Volver</a>
    </div>

    <table class="table table-bordered table-hover" id="tablaCuentas">
        <thead class="table-dark">
            <tr>
                <th>Nombre del viaje</th>
                <th>Fecha</th>
                <th>Archivo</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $result = $conn->query("SELECT * FROM cuentas_cobro ORDER BY fecha DESC");
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['nombre_viaje']) . "</td>";
            echo "<td>" . htmlspecialchars($row['fecha']) . "</td>";
            echo "<td><a href='" . htmlspecialchars($row['archivo_path']) . "' target='_blank'>Ver archivo</a></td>";
            echo "</tr>";
        }
        $conn->close();
        ?>
        </tbody>
    </table>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const buscarNombre = document.getElementById('buscarNombre');
    const buscarFecha = document.getElementById('buscarFecha');
    const tabla = document.getElementById('tablaCuentas').getElementsByTagName('tbody')[0];

    function filtrar() {
        const nombre = buscarNombre.value.toLowerCase();
        const fecha = buscarFecha.value;
        
        Array.from(tabla.rows).forEach(row => {
            const nombreViaje = row.cells[0].textContent.toLowerCase();
            const fechaViaje = row.cells[1].textContent;

            const coincideNombre = nombreViaje.startsWith(nombre);
            const coincideFecha = fecha === '' || fechaViaje === fecha;

            row.style.display = (coincideNombre && coincideFecha) ? '' : 'none';
        });
    }

    buscarNombre.addEventListener('input', filtrar);
    buscarFecha.addEventListener('input', filtrar);
});
</script>
</body>
</html>
