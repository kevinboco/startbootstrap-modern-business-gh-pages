<?php
require_once 'db.php'; // conexión a la base de datos

// Verificamos si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha = $_POST['fecha'];
    $nombre_viaje = $_POST['nombre_viaje'];

    // Validamos y subimos el archivo
    if (isset($_FILES['cuenta_cobro']) && $_FILES['cuenta_cobro']['error'] === UPLOAD_ERR_OK) {
        $archivo = $_FILES['cuenta_cobro'];
        $ext = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $nombre_archivo = uniqid('cuenta_') . '.' . $ext;
        $ruta_destino = 'informacion/' . $nombre_archivo;

        if (move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {
            // Guardamos en la base de datos
            $stmt = $conn->prepare("INSERT INTO cuentas_cobro (fecha, nombre_viaje, archivo_path) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $fecha, $nombre_viaje, $ruta_destino);

            if ($stmt->execute()) {
                echo "Cuenta de cobro subida exitosamente. <a href='bienvenida.php'>Volver</a>";
            } else {
                echo "Error al guardar en la base de datos: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Error al mover el archivo.";
        }
    } else {
        echo "Error al subir el archivo.";
    }

    $conn->close();
} else {
    header("Location: formulario_cuentacobro.php");
    exit();
}
?>
