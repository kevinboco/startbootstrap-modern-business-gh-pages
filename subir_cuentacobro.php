<?php
require_once 'db.php'; // conexión a la base de datos

// Verificamos si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha = $_POST['fecha'];
    $nombre_viaje = $_POST['nombre_viaje'];

    // Validamos y subimos la cuenta de cobro
    if (isset($_FILES['cuenta_cobro']) && $_FILES['cuenta_cobro']['error'] === UPLOAD_ERR_OK) {
        $archivo = $_FILES['cuenta_cobro'];
        $ext = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $nombre_archivo = uniqid('cuenta_') . '.' . $ext;
        $ruta_destino = 'informacion/' . $nombre_archivo;

        if (move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {

            // Validamos y subimos la captura de pago realizado
            if (isset($_FILES['captura_pago_realizado']) && $_FILES['captura_pago_realizado']['error'] === UPLOAD_ERR_OK) {
                $captura = $_FILES['captura_pago_realizado'];
                $ext_captura = pathinfo($captura['name'], PATHINFO_EXTENSION);
                $nombre_captura = uniqid('pago_') . '.' . $ext_captura;
                $ruta_captura = 'informacion/' . $nombre_captura;

                if (move_uploaded_file($captura['tmp_name'], $ruta_captura)) {
                    // Guardamos en la base de datos
                    $stmt = $conn->prepare("INSERT INTO cuentas_cobro (fecha, nombre_viaje, archivo_path, captura_pago_realizado) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("ssss", $fecha, $nombre_viaje, $ruta_destino, $ruta_captura);

                    if ($stmt->execute()) {
                        echo "Cuenta de cobro subida exitosamente. <a href='bienvenida.php'>Volver</a>";
                    } else {
                        echo "Error al guardar en la base de datos: " . $stmt->error;
                    }

                    $stmt->close();
                } else {
                    echo "Error al mover la captura de pago.";
                }
            } else {
                echo "Error al subir la captura de pago.";
            }
        } else {
            echo "Error al mover el archivo de cuenta de cobro.";
        }
    } else {
        echo "Error al subir el archivo de cuenta de cobro.";
    }

    $conn->close();
} else {
    header("Location: formulario_cuentacobro.php");
    exit();
}
?>

