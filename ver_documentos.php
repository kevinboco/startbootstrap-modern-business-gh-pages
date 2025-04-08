<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}
require_once 'db.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Documentos Subidos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script>
    function filtrarTabla() {
        let input = document.getElementById("buscador").value.toLowerCase();
        let rows = document.querySelectorAll("#documentTable tbody tr");

        rows.forEach(row => {
            let nombre = row.querySelector("td").textContent.toLowerCase();
            // Solo muestra si el nombre INICIA con la secuencia exacta
            if (nombre.indexOf(input) === 0) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    }
    </script>
</head>
<body class="bg-light">
    <div class="container mt-4">
        <h2>Documentos Subidos</h2>

        <div class="mb-3">
            <label for="buscador" class="form-label">Buscar por nombre:</label>
            <input type="text" id="buscador" onkeyup="filtrarTabla()" class="form-control" placeholder="Escribe un nombre...">
        </div>

        <table class="table table-bordered table-hover" id="documentTable">
            <thead class="table-dark">
                <tr>
                    <th>Nombre</th>
                    <th>RUT</th>
                    <th>Certificado Bancario</th>
                    <th>Cédula</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT * FROM info_asociados ORDER BY id DESC");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['nombre_persona']) . "</td>";
                    echo "<td><a href='" . htmlspecialchars($row['rut_path']) . "' target='_blank'>Ver</a></td>";
                    echo "<td><a href='" . htmlspecialchars($row['certificado_bancario_path']) . "' target='_blank'>Ver</a></td>";
                    echo "<td><a href='" . htmlspecialchars($row['cedula_path']) . "' target='_blank'>Ver</a></td>";
                    echo "</tr>";
                }
                $conn->close();
                ?>
            </tbody>
        </table>

        <a href="bienvenida.php" class="btn btn-secondary mt-3">Volver</a>
    </div>
</body>
</html>
