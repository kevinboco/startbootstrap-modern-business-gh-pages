<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}
require_once 'db.php';

$totalAsociados = $conn->query("SELECT COUNT(*) as total FROM info_asociados")->fetch_assoc()['total'];

$cuentasPorDia = [];
$labels = [];
$counts = [];
if (isset($_GET['mes'])) {
    $mes = $_GET['mes'];
    $stmt = $conn->prepare("SELECT DATE(fecha) as dia, COUNT(*) as cantidad FROM cuentas_cobro WHERE DATE_FORMAT(fecha, '%Y-%m') = ? GROUP BY dia ORDER BY dia ASC");
    $stmt->bind_param("s", $mes);
    $stmt->execute();
    $resultado = $stmt->get_result();
    while ($fila = $resultado->fetch_assoc()) {
        $cuentasPorDia[] = $fila;
        $labels[] = $fila['dia'];
        $counts[] = $fila['cantidad'];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estadísticas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .mini-card {
            font-size: 1.2rem;
            padding: 1rem;
            background-color: #f8f9fa;
            border-left: 4px solid #0d6efd;
            border-radius: 0.5rem;
        }
        canvas {
            max-height: 250px;
        }
    </style>
</head>
<body class="bg-light">
<div class="container py-4">
    <h3 class="mb-4">Estadísticas</h3>

    <div class="mini-card mb-3">
        <strong>Total de Asociados Registrados:</strong> <?php echo $totalAsociados; ?>
    </div>

    <div class="card mb-4">
        <div class="card-body p-3">
            <h5 class="mb-3">Cuentas de Cobro por Día</h5>

            <form method="get" class="mb-3">
                <label for="mes" class="form-label">Seleccionar Mes:</label>
                <input type="month" name="mes" id="mes" class="form-control form-control-sm" onchange="this.form.submit()" value="<?php echo isset($_GET['mes']) ? $_GET['mes'] : ''; ?>">
            </form>

            <?php if (isset($_GET['mes'])): ?>
                <?php if (count($cuentasPorDia) > 0): ?>
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Día</th>
                                <th>Cuentas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cuentasPorDia as $fila): ?>
                                <tr>
                                    <td><?php echo $fila['dia']; ?></td>
                                    <td><?php echo $fila['cantidad']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <canvas id="graficoCobros"></canvas>
                    <script>
                        const ctx = document.getElementById('graficoCobros').getContext('2d');
                        new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: <?php echo json_encode($labels); ?>,
                                datasets: [{
                                    label: 'Cuentas por Día',
                                    data: <?php echo json_encode($counts); ?>,
                                    backgroundColor: 'rgba(13, 110, 253, 0.6)',
                                    borderColor: 'rgba(13, 110, 253, 1)',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                plugins: { legend: { display: false } },
                                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                            }
                        });
                    </script>
                <?php else: ?>
                    <div class="alert alert-info small">No hay cuentas de cobro para ese mes.</div>
                <?php endif; ?>
            <?php endif; ?>

            <a href="bienvenida.php" class="btn btn-outline-secondary btn-sm mt-3">← Volver</a>
        </div>
    </div>
</div>
</body>
</html>

