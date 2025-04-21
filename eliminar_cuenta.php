<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

require_once 'db.php';

if (!isset($_GET['id'])) {
    echo "ID no especificado.";
    exit;
}

$id = intval($_GET['id']);

// Obtener datos del registro
$result = $conn->query("SELECT * FROM cuentas_cobro WHERE id = $id");
if ($result->num_rows === 0) {
    echo "Registro no encontrado.";
    exit;
}

$row = $result->fetch_assoc();

// Función para eliminar archivos con ruta absoluta
function eliminarArchivo($archivo) {
    if (empty($archivo)) return;

    $ruta = __DIR__ . "/informacion/" . basename($archivo);
    echo "Intentando borrar: $ruta<br>";

    if (file_exists($ruta)) {
        if (unlink($ruta)) {
            echo "✅ Eliminado: $ruta<br>";
        } else {
            echo "❌ No se pudo eliminar: $ruta<br>";
        }
    } else {
        echo "❌ No existe: $ruta<br>";
    }
}

// Eliminar archivos asociados al registro principal
eliminarArchivo($row['cuenta_empresa']);
eliminarArchivo($row['cuenta_cobro']);
eliminarArchivo($row['captura_pago_realizado']);

// Eliminar archivos de trabajadores asociados
$stmt = $conn->prepare("SELECT archivo FROM cuentas_trabajadores WHERE cuenta_cobro_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultTrabajadores = $stmt->get_result();
while ($trabajador = $resultTrabajadores->fetch_assoc()) {
    eliminarArchivo($trabajador['archivo']);
}
$stmt->close();

// Eliminar registros de trabajadores
$conn->query("DELETE FROM cuentas_trabajadores WHERE cuenta_cobro_id = $id");

// Eliminar registro principal
$conn->query("DELETE FROM cuentas_cobro WHERE id = $id");

$conn->close();

// Redirigir con mensaje
header("Location: ver_cuentas_cobro.php?msg=eliminado");
exit;
?>
