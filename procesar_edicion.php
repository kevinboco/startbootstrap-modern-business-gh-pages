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

// Ruta física en el servidor
define('CARPETA_FISICA', 'C:/xampp/htdocs/proyecto/informacion/');

// Obtener datos actuales
$actual = $conn->query("SELECT * FROM cuentas_cobro WHERE id = $id")->fetch_assoc();
if (!$actual) {
    echo "Cuenta no encontrada.";
    exit;
}

// Función para guardar archivo y eliminar el anterior si es necesario
function guardarYReemplazar($inputName, $carpeta, $anterior, $prefijo) {
    if (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] === UPLOAD_ERR_OK) {
        $nombreOriginal = basename($_FILES[$inputName]['name']);
        $nombreUnico = $prefijo . uniqid() . '_' . $nombreOriginal;
        $rutaDestino = $carpeta . $nombreUnico;

        if (move_uploaded_file($_FILES[$inputName]['tmp_name'], $rutaDestino)) {
            // Eliminar el archivo anterior si existe
            if (!empty($anterior)) {
                $rutaAnterior = $carpeta . basename($anterior);
                if (file_exists($rutaAnterior)) {
                    unlink($rutaAnterior);
                }
            }
            return $nombreUnico;
        }
    }
    return $anterior;
}

// Guardar nuevos archivos (y reemplazar si aplica)
$cuenta_empresa = guardarYReemplazar('cuenta_empresa', CARPETA_FISICA, $actual['cuenta_empresa'], 'empresa_');
$cuenta_cobro = guardarYReemplazar('cuenta_cobro', CARPETA_FISICA, $actual['cuenta_cobro'], 'cuenta_');
$captura_pago_realizado = guardarYReemplazar('captura_pago_realizado', CARPETA_FISICA, $actual['captura_pago_realizado'], 'captura_');

// Actualizar en la base de datos
$stmt = $conn->prepare("UPDATE cuentas_cobro SET nombre_viaje=?, fecha=?, cuenta_empresa=?, cuenta_cobro=?, captura_pago_realizado=? WHERE id=?");
$stmt->bind_param("sssssi", $nombre_viaje, $fecha, $cuenta_empresa, $cuenta_cobro, $captura_pago_realizado, $id);

if ($stmt->execute()) {
    header("Location: ver_cuentas_cobro.php?msg=editado");
    exit;
} else {
    echo "Error al actuali: " . $stmt->error;
}

$conn->close();
?>
