<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

require_once 'db.php';

$id = intval($_POST['id']);
$nombre_viaje = trim($_POST['nombre_viaje']);
$fecha = $_POST['fecha'];

// Obtener los datos actuales para conservar archivos no reemplazados
$actual = $conn->query("SELECT * FROM cuentas_cobro WHERE id = $id")->fetch_assoc();
if (!$actual) {
    echo "Cuenta no encontrada.";
    exit;
}

// Función para guardar archivo y devolver nombre si se subió
function guardarArchivo($inputName, $directorioDestino) {
    if (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] === UPLOAD_ERR_OK) {
        $nombreArchivo = basename($_FILES[$inputName]['name']);
        $rutaDestino = $directorioDestino . $nombreArchivo;
        move_uploaded_file($_FILES[$inputName]['tmp_name'], $rutaDestino);
        return $nombreArchivo;
    }
    return null;
}

// Guardar archivos si se subieron
$nuevaCuentaEmpresa = guardarArchivo('cuenta_empresa', 'asociacion/');
$nuevaCuentaCobro = guardarArchivo('cuenta_cobro', 'asociacion/');
$nuevaCapturaPago = guardarArchivo('captura_pago_realizado', 'asociacion/informacion/');

// Mantener los archivos anteriores si no se subió uno nuevo
$cuenta_empresa = $nuevaCuentaEmpresa ?? $actual['cuenta_empresa'];
$cuenta_cobro = $nuevaCuentaCobro ?? $actual['cuenta_cobro'];
$captura_pago_realizado = $nuevaCapturaPago ?? $actual['captura_pago_realizado'];

// Actualizar en la base de datos
$stmt = $conn->prepare("UPDATE cuentas_cobro SET nombre_viaje=?, fecha=?, cuenta_empresa=?, cuenta_cobro=?, captura_pago_realizado=? WHERE id=?");
$stmt->bind_param("sssssi", $nombre_viaje, $fecha, $cuenta_empresa, $cuenta_cobro, $captura_pago_realizado, $id);

if ($stmt->execute()) {
    header("Location: ver_cuentas_cobro.php?msg=editado");
    exit;
} else {
    echo "Error al actualizar: " . $stmt->error;
}

$conn->close();
?>
