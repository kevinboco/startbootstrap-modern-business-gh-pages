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
                <th>cuenta de cobro enviada a la empresa</th>
                <th>Fecha</th>
                <th>Cuenta de Cobro</th>
                <th>Captura de Pago</th>
                <th>Modificar</th>
                <th>eliminar registro</th>
                <th>Archivos de Trabajadores</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $result = $conn->query("SELECT * FROM cuentas_cobro ORDER BY fecha DESC");
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['nombre_viaje']) . "</td>";
            if (!empty($row['cuenta_empresa'])) {
                $rutaCaptura = '/proyecto/informacion/' . htmlspecialchars($row['cuenta_empresa']);
                echo "<td><a href='" . $rutaCaptura . "' target='_blank'>" . basename($rutaCaptura) . "</a></td>";
            } else {
                echo "<td>No disponible</td>";
            }
            echo "<td>" . htmlspecialchars($row['fecha']) . "</td>";

            // Ruta al archivo de cuenta de cobro
            if (!empty($row['cuenta_cobro'])) {
                $rutaCuenta = '/proyecto/' . htmlspecialchars($row['cuenta_cobro']);
                echo "<td><a href='" . $rutaCuenta . "' target='_blank'>" . basename($rutaCuenta) . "</a></td>";
            } else {
                echo "<td>No disponible</td>";
            }

            // Ruta a la captura de pago
            if (!empty($row['captura_pago_realizado'])) {
                $rutaCaptura = '/proyecto/informacion/' . htmlspecialchars($row['captura_pago_realizado']);
                echo "<td><a href='" . $rutaCaptura . "' target='_blank'>" . basename($rutaCaptura) . "</a></td>";
            } else {
                echo "<td>No disponible</td>";
            }

            // Botón para editar la cuenta de cobro
            echo "<td><a href='editar_cuenta.php?id=" . urlencode($row['id']) . "' class='btn btn-sm btn-primary'>Modificar</a></td>";
            echo "<td><a href='eliminar_cuenta.php?id=" . urlencode($row['id']) . "' class='btn btn-sm btn-danger'>eliminar</a></td>";
            // Archivos de los trabajadores asociados a la cuenta de cobro
            $idCuenta = $row['id'];
            $subquery = $conn->prepare("SELECT archivo FROM cuentas_trabajadores WHERE cuenta_cobro_id = ?");
            $subquery->bind_param("i", $idCuenta);
            $subquery->execute();
            $resultTrabajadores = $subquery->get_result();

            echo "<td>";
            if ($resultTrabajadores->num_rows > 0) {
                while ($trabajador = $resultTrabajadores->fetch_assoc()) {
                    $rutaArchivoTrabajador = '/proyecto/informacion/' . htmlspecialchars($trabajador['archivo']);
                    echo "<a href='" . $rutaArchivoTrabajador . "' target='_blank'>" . basename($rutaArchivoTrabajador) . "</a><br>";
                }
            } else {
                echo "No disponible";
            }
            echo "</td>";

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
